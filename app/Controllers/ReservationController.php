<?php
/**
 * PackNGo — Reservation Controller
 * Handles booking form submissions from Reservation.html
 *
 * Endpoint: POST /api/reservation/submit
 * Response: JSON
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/ReservationModel.php';
require_once dirname(__DIR__) . '/Models/PackageModel.php';
require_once dirname(__DIR__) . '/Models/DestinationModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Validator.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/RateLimiter.php';
require_once dirname(__DIR__, 2) . '/helpers/Mailer.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';
require_once dirname(__DIR__, 2) . '/helpers/Session.php';
require_once dirname(__DIR__, 2) . '/middleware/CsrfMiddleware.php';

class ReservationController
{
    private ReservationModel $model;
    private PackageModel     $packageModel;
    private DestinationModel $destModel;

    public function __construct()
    {
        $this->model        = new ReservationModel();
        $this->packageModel = new PackageModel();
        $this->destModel    = new DestinationModel();
    }

    /**
     * POST /api/reservation/submit
     * Accepts JSON or form-encoded body from Reservation.html
     */
    public function submit(): void
    {
        // ── 1. Rate limit: 10 submissions per hour per IP ──
        $ip = Sanitizer::ip();
        RateLimiter::check('reservation_submit', $ip, 10, 3600);

        // ── 2. CSRF protection ──────────────────────────────
        CsrfMiddleware::verify();

        // ── 3. Parse input ─────────────────────────────────
        $raw = $this->parseInput();

        // ── 3.5. OTP Verification Check ──────────────────────
        Session::start();
        $verifiedEmails = Session::get('verified_reservation_emails', []);
        $email = Sanitizer::email((string)($raw['email'] ?? ''));
        if (!$email || !isset($verifiedEmails[$email])) {
            Response::json([
                'success' => false,
                'message' => 'Your email has not been verified. Please verify your email address via OTP first.'
            ], 403);
            return;
        }

        // ── 4. Validate ─────────────────────────────────────
        $v = new Validator($raw);
        $v->required('first_name')->alpha('first_name')->maxLen('first_name', 50);
        $v->required('last_name')->alpha('last_name')->maxLen('last_name', 50);
        $v->required('email')->email('email')->maxLen('email', 120);
        $v->required('destination')->maxLen('destination', 100);
        $v->required('departure_date')->date('departure_date')->futureDate('departure_date');
        $v->required('travellers');

        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        // ── 5. Resolve package and destination IDs ──────────
        $packageId   = null;
        $packageText = null;
        if (!empty($raw['package'])) {
            $pkgSlug = $this->slugify($raw['package']);
            $pkg     = $this->packageModel->findBySlug($pkgSlug);
            if ($pkg) {
                $packageId   = (int)$pkg['id'];
                $packageText = $pkg['name'];
            } else {
                $packageText = Sanitizer::string($raw['package'], 100);
            }
        }

        $destId   = null;
        $destText = Sanitizer::string($raw['destination'], 100);
        $dest     = $this->destModel->search($destText);
        if (!empty($dest)) {
            $destId = (int)$dest[0]['id'];
        }

        // ── 6. Build record ─────────────────────────────────
        $data = [
            'first_name'       => Sanitizer::name($raw['first_name']),
            'last_name'        => Sanitizer::name($raw['last_name']),
            'email'            => Sanitizer::email($raw['email']),
            'phone_code'       => Sanitizer::string($raw['phone_code'] ?? '+92', 6),
            'phone'            => isset($raw['phone']) ? Sanitizer::digits($raw['phone'], 20) : null,
            'destination_id'   => $destId,
            'destination_text' => $destText,
            'package_id'       => $packageId,
            'package_text'     => $packageText,
            'departure_date'   => $raw['departure_date'],
            'return_date'      => !empty($raw['return_date']) ? $raw['return_date'] : null,
            'travellers'       => Sanitizer::string($raw['travellers'], 30),
            'budget_range'     => !empty($raw['budget']) ? Sanitizer::string($raw['budget'], 50) : null,
            'accommodation'    => !empty($raw['accommodation']) ? Sanitizer::string($raw['accommodation'], 100) : null,
            'special_requests' => !empty($raw['message']) ? Sanitizer::textarea($raw['message'], 1000) : null,
            'status'           => 'pending',
            'ip_address'       => $ip,
            'user_agent'       => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 300),
        ];

        // ── 7. Save to DB ────────────────────────────────────
        try {
            $result = $this->model->createReservation($data);
            // Clear verified email OTP status from session to prevent reuse
            unset($verifiedEmails[$email]);
            Session::set('verified_reservation_emails', $verifiedEmails);
        } catch (Throwable $e) {
            error_log('[ReservationController] DB error: ' . $e->getMessage());
            Response::json(['success' => false, 'message' => 'Unable to save reservation. Please try again.'], 500);
            return;
        }

        // ── 8. Send confirmation emails (non-blocking) ───────
        $fullName = $data['first_name'] . ' ' . $data['last_name'];
        try {
            Mailer::sendReservationConfirmation($data['email'], $fullName, $result['booking_ref'], $data);
            Mailer::sendAdminNotification($result['booking_ref'], $data);
        } catch (Throwable $e) {
            // Email failure should NOT fail the reservation
            error_log('[ReservationController] Email error: ' . $e->getMessage());
        }

        // ── 9. Respond ───────────────────────────────────────
        Response::json([
            'success'     => true,
            'booking_ref' => $result['booking_ref'],
            'message'     => 'Your reservation has been received! Our concierge will contact you within 2 business hours.',
        ], 201);
    }

    /**
     * GET /api/reservation/check?ref=PNG-20260612-0001
     * Let customers look up their booking status.
     */
    public function check(): void
    {
        $ref = Sanitizer::string($_GET['ref'] ?? '', 25);
        if (empty($ref)) {
            Response::json(['success' => false, 'message' => 'Booking reference is required.'], 400);
            return;
        }
        $reservation = $this->model->findByRef($ref);
        if (!$reservation) {
            Response::json(['success' => false, 'message' => 'No reservation found with that reference.'], 404);
            return;
        }
        // Return only safe public fields
        Response::json([
            'success'     => true,
            'booking_ref' => $reservation['booking_ref'],
            'status'      => $reservation['status'],
            'first_name'  => $reservation['first_name'],
            'destination' => $reservation['destination_name'] ?? $reservation['destination_text'],
            'package'     => $reservation['package_name'] ?? $reservation['package_text'],
            'departure'   => $reservation['departure_date'],
            'travellers'  => $reservation['travellers'],
        ]);
    }

    // ── Helpers ────────────────────────────────────────────

    private function parseInput(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $body = file_get_contents('php://input');
            return (array) json_decode($body, true);
        }
        return $_POST;
    }

    private function slugify(string $text): string
    {
        $map = [
            'Luxury Escape — $2,500'       => 'luxury-escape',
            'Honeymoon Special — $4,000'   => 'honeymoon-special',
            'Adventure Trek — $1,800'      => 'adventure-trek',
            'Beach & Nature — $3,200'      => 'beach-nature',
            'Beach &amp; Nature — $3,200'  => 'beach-nature',
        ];
        return $map[$text] ?? strtolower(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)));
    }
}
