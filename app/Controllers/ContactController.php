<?php
/**
 * PackNGo — Contact Controller
 * POST /api/contact/send
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/ContactModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Validator.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/RateLimiter.php';
require_once dirname(__DIR__, 2) . '/helpers/Mailer.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';
require_once dirname(__DIR__, 2) . '/middleware/CsrfMiddleware.php';

class ContactController
{
    private ContactModel $model;

    public function __construct()
    {
        $this->model = new ContactModel();
    }

    public function send(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('contact_send', $ip, 5, 3600);
        CsrfMiddleware::verify();

        $raw = $_POST;
        if (empty($raw)) {
            $raw = (array) json_decode(file_get_contents('php://input'), true);
        }

        // ── Honeypot spam trap ──────────────────────────────
        // "website" is a hidden field — invisible to humans, but bots fill it in.
        // We pretend success so automated scripts don't learn the trap exists,
        // while silently discarding the submission.
        if (!empty($raw['website'])) {
            error_log('[ContactController] Honeypot triggered from IP ' . $ip);
            Response::json(['success' => true, 'message' => 'Your message has been sent. We will reply within 24 hours.']);
            return;
        }

        $v = new Validator($raw);
        $v->required('name')->maxLen('name', 100);
        $v->required('email')->email('email')->maxLen('email', 120);
        $v->maxLen('subject', 200);
        $v->required('message')->minLen('message', 10)->maxLen('message', 2000);

        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        try {
            $this->model->saveMessage([
                'name'    => $raw['name'],
                'email'   => $raw['email'],
                'subject' => $raw['subject'] ?? null,
                'message' => $raw['message'],
                'ip'      => $ip,
            ]);

            // Notify admin
            $config = require dirname(__DIR__, 2) . '/config/app.php';
            try {
                $name    = htmlspecialchars(Sanitizer::name($raw['name']), ENT_QUOTES);
                $email   = htmlspecialchars(Sanitizer::email($raw['email']), ENT_QUOTES);
                $message = htmlspecialchars(Sanitizer::textarea($raw['message'], 2000), ENT_QUOTES);
                $subject = !empty($raw['subject']) ? htmlspecialchars(Sanitizer::string($raw['subject'], 200), ENT_QUOTES) : 'General Enquiry';

                Mailer::sendRaw(
                    $config['mail']['admin_email'],
                    'PackNGo Admin',
                    "New Contact Message — {$subject}",
                    "<h2>New message from {$name}</h2>
                     <p><strong>Email:</strong> {$email}</p>
                     <p><strong>Subject:</strong> {$subject}</p>
                     <p><strong>Message:</strong><br>" . nl2br($message) . "</p>"
                );
            } catch (Throwable $e) {
                error_log('[Contact] Admin notify failed: ' . $e->getMessage());
            }

            Response::json(['success' => true, 'message' => 'Your message has been sent. We will reply within 24 hours.']);
        } catch (Throwable $e) {
            error_log('[ContactController] ' . $e->getMessage());
            Response::json(['success' => false, 'message' => 'Failed to send message. Please try again.'], 500);
        }
    }
}
