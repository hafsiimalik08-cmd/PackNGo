<?php
/**
 * PackNGo — Reservation OTP Controller
 * Handles email OTP verification for the booking form.
 * No user account required — guest verification only.
 *
 * POST /api/reservation/send-otp   { email }
 * POST /api/reservation/verify-otp { email, otp }
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/RateLimiter.php';
require_once dirname(__DIR__, 2) . '/helpers/Mailer.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';
require_once dirname(__DIR__, 2) . '/helpers/Session.php';
require_once dirname(__DIR__, 2) . '/middleware/CsrfMiddleware.php';

class ReservationOtpController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * POST /api/reservation/send-otp
     * Body: { email }
     * Generates and emails a 6-digit OTP for the given guest email.
     */
    public function sendOtp(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('res_otp_send', $ip, 10, 900); // 10 per 15 min
        CsrfMiddleware::verify();

        $raw   = $this->input();
        $email = Sanitizer::email((string)($raw['email'] ?? ''));

        if (!$email) {
            Response::json(['success' => false, 'message' => 'A valid email address is required.'], 422);
            return;
        }

        // Generate OTP
        $otp     = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $config  = require dirname(__DIR__, 2) . '/config/app.php';
        $expires = date('Y-m-d H:i:s', time() + 900); // 15 minutes

        // Store in reservation_otps table (keyed by email)
        try {
            $this->ensureTable();

            $this->db->query(
                "INSERT INTO reservation_otps (email, otp_hash, expires_at, created_at)
                 VALUES (:email, :hash, :exp, NOW())
                 ON DUPLICATE KEY UPDATE otp_hash=:hash2, expires_at=:exp2, created_at=NOW()",
                [
                    ':email' => $email,
                    ':hash'  => password_hash($otp, PASSWORD_BCRYPT, ['cost' => $config['security']['bcrypt_rounds']]),
                    ':exp'   => $expires,
                    ':hash2' => password_hash($otp, PASSWORD_BCRYPT, ['cost' => $config['security']['bcrypt_rounds']]),
                    ':exp2'  => $expires,
                ]
            );
        } catch (\Throwable $e) {
            error_log('[ReservationOtp] DB error: ' . $e->getMessage());
            Response::json(['success' => false, 'message' => 'Unable to generate OTP. Please try again.'], 500);
            return;
        }

        // Send email
        try {
            $this->sendOtpEmail($email, $otp);
        } catch (\Throwable $e) {
            error_log('[ReservationOtp] Email error: ' . $e->getMessage());
            // Do not fail the request if email fails in dev; log it.
        }

        Response::json([
            'success' => true,
            'message' => 'A 6-digit verification code has been sent to your email. It expires in 15 minutes.',
        ]);
    }

    /**
     * POST /api/reservation/verify-otp
     * Body: { email, otp }
     */
    public function verifyOtp(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('res_otp_verify', $ip, 15, 900); // 15 attempts per 15 min
        CsrfMiddleware::verify();

        $raw   = $this->input();
        $email = Sanitizer::email((string)($raw['email'] ?? ''));
        $otp   = Sanitizer::digits((string)($raw['otp'] ?? ''), 6);

        if (!$email || strlen($otp) !== 6) {
            Response::json(['success' => false, 'message' => 'A valid email and 6-digit code are required.'], 422);
            return;
        }

        try {
            $this->ensureTable();
            $row = $this->db->fetchOne(
                "SELECT otp_hash, expires_at FROM reservation_otps WHERE email = :email LIMIT 1",
                [':email' => $email]
            );
        } catch (\Throwable $e) {
            error_log('[ReservationOtp] DB error: ' . $e->getMessage());
            Response::json(['success' => false, 'message' => 'Verification failed. Please try again.'], 500);
            return;
        }

        if (!$row) {
            Response::json(['success' => false, 'message' => 'No OTP found for this email. Please request a new code.'], 422);
            return;
        }

        if (strtotime($row['expires_at']) < time()) {
            Response::json(['success' => false, 'message' => 'This code has expired. Please request a new one.'], 422);
            return;
        }

        if (!password_verify($otp, $row['otp_hash'])) {
            Response::json(['success' => false, 'message' => 'Invalid code. Please check your email and try again.'], 422);
            return;
        }

        // Mark as verified by setting a verified flag in session
        Session::start();
        $verified = Session::get('verified_reservation_emails', []);
        $verified[$email] = time();
        Session::set('verified_reservation_emails', $verified);

        // Delete the used OTP
        try {
            $this->db->query("DELETE FROM reservation_otps WHERE email = :email", [':email' => $email]);
        } catch (\Throwable $e) {
            // Non-fatal
        }

        Response::json(['success' => true, 'message' => 'Email verified successfully!']);
    }

    // ── Helpers ────────────────────────────────────────────

    private function sendOtpEmail(string $email, string $otp): void
    {
        $boxes = '';
        foreach (str_split($otp) as $digit) {
            $boxes .= "<span style='display:inline-block;width:42px;height:50px;line-height:50px;"
                    . "text-align:center;font-size:22px;font-weight:bold;border:2px solid #D4AF37;"
                    . "border-radius:4px;margin:0 4px;color:#1F3D36;background:#F5EFE6;'>{$digit}</span>";
        }

        $subject = 'PackNGo Booking Verification Code: ' . $otp;
        $html = "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='margin:0;padding:0;background:#f5efe6;font-family:Georgia,serif;'>
  <div style='max-width:520px;margin:40px auto;background:#fff;border-top:4px solid #D4AF37;box-shadow:0 4px 24px rgba(0,0,0,.08);'>
    <div style='background:#1F3D36;padding:28px 36px;text-align:center;'>
      <div style='color:#D4AF37;font-size:26px;letter-spacing:4px;font-weight:bold;'>✈ PackNGo</div>
      <div style='color:rgba(245,239,230,.6);font-size:11px;letter-spacing:2px;margin-top:4px;'>BOOKING VERIFICATION</div>
    </div>
    <div style='padding:36px 40px;'>
      <h2 style='color:#1F3D36;font-size:20px;margin:0 0 16px;'>Verify Your Email</h2>
      <p style='color:#555;font-size:14px;line-height:1.7;margin:0 0 24px;'>Please use the code below to verify your email and complete your reservation. This code expires in <strong>15 minutes</strong>.</p>
      <div style='text-align:center;margin:28px 0;background:#f9f6f0;padding:24px;border-radius:4px;'>{$boxes}</div>
      <p style='color:#888;font-size:13px;'>If you did not attempt to make a booking with PackNGo, please ignore this email. Never share this code with anyone.</p>
    </div>
    <div style='background:#f9f6f0;padding:18px 40px;text-align:center;border-top:1px solid #e2d9c8;'>
      <p style='color:#aaa;font-size:12px;margin:0;'>&copy; 2026 PackNGo &middot; All rights reserved</p>
    </div>
  </div>
</body>
</html>";

        Mailer::sendRaw($email, $email, $subject, $html);
    }

    private function ensureTable(): void
    {
        // Create the table if it doesn't exist (lazy migration)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS reservation_otps (
                email       VARCHAR(120) NOT NULL PRIMARY KEY,
                otp_hash    VARCHAR(255) NOT NULL,
                expires_at  DATETIME     NOT NULL,
                created_at  DATETIME     NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    private function input(): array
    {
        $ct = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($ct, 'application/json')) {
            return (array) json_decode(file_get_contents('php://input'), true);
        }
        return $_POST;
    }
}
