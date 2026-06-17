<?php
/**
 * PackNGo — Mailer
 *
 * Uses PHPMailer (installed via Composer) over SMTP.
 * Fallback: PHP mail() for local XAMPP testing.
 *
 * Install PHPMailer:  composer require phpmailer/phpmailer
 */
declare(strict_types=1);

class Mailer
{
    private static array $config = [];

    private static function cfg(): array
    {
        if (empty(self::$config)) {
            $app = require dirname(__DIR__) . '/config/app.php';
            self::$config = $app['mail'];
        }
        return self::$config;
    }

    /**
     * Core send method. Returns true on success.
     */
    private static function send(string $to, string $toName, string $subject, string $htmlBody): bool
    {
        $cfg = self::cfg();

        // ── Try PHPMailer if available ──────────────────────
        $phpmailerPath = dirname(__DIR__) . '/vendor/autoload.php';
        if (file_exists($phpmailerPath)) {
            require_once $phpmailerPath;
            try {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = $cfg['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $cfg['username'];
                $mail->Password   = $cfg['password'];
                $mail->SMTPSecure = $cfg['encryption'] === 'ssl'
                    ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = $cfg['port'];
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($cfg['from_address'], $cfg['from_name']);
                $mail->addAddress($to, $toName);
                $mail->addReplyTo($cfg['admin_email'], $cfg['from_name']);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlBody;
                $mail->AltBody = strip_tags($htmlBody);

                return $mail->send();
            } catch (Throwable $e) {
                error_log('[Mailer SMTP] ' . $e->getMessage());
            }
        }

        // ── Fallback: PHP mail() (XAMPP local dev) ──────────
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$cfg['from_name']} <{$cfg['from_address']}>\r\n";
        $headers .= "Reply-To: {$cfg['admin_email']}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return mail($to, $subject, $htmlBody, $headers);
    }

    // ── Email templates ────────────────────────────────────

    /**
     * Send a 6-digit OTP code for email verification.
     */
    public static function sendOtpEmail(string $email, string $name, string $otp): bool
    {
        $subject = 'Your PackNGo Verification Code: ' . $otp;

        $boxes = '';
        foreach (str_split($otp) as $digit) {
            $boxes .= "<span style='display:inline-block;width:42px;height:50px;line-height:50px;"
                    . "text-align:center;font-size:22px;font-weight:bold;border:2px solid #D4AF37;"
                    . "border-radius:4px;margin:0 4px;color:#1F3D36;background:#F5EFE6;'>{$digit}</span>";
        }

        $html = self::wrap("
            <h2 style='color:#1F3D36;'>Verify Your Email ✉️</h2>
            <p>Hello <strong>{$name}</strong>,</p>
            <p>Thanks for joining PackNGo! Enter the verification code below to activate your account. This code expires in <strong>15 minutes</strong>.</p>
            <div style='text-align:center;margin:28px 0;'>{$boxes}</div>
            <p style='color:#888;font-size:13px;'>If you didn't create a PackNGo account, you can safely ignore this email. Never share this code with anyone.</p>
        ");
        return self::send($email, $name, $subject, $html);
    }

    public static function sendReservationConfirmation(
        string $email,
        string $name,
        string $bookingRef,
        array  $data
    ): bool {
        $subject  = "Booking Confirmed — {$bookingRef} | PackNGo";
        $depart   = $data['departure_date'] ?? 'TBD';
        $dest     = htmlspecialchars($data['destination_text'] ?? 'Your destination', ENT_QUOTES);
        $pkg      = htmlspecialchars($data['package_text']     ?? 'Custom', ENT_QUOTES);
        $html = self::wrap("
            <h2 style='color:#1F3D36;'>Your Journey Awaits! ✈️</h2>
            <p>Dear <strong>{$name}</strong>,</p>
            <p>Thank you for choosing PackNGo. Your reservation has been received and our concierge team will contact you within <strong>2 business hours</strong>.</p>
            <table style='width:100%;border-collapse:collapse;margin:24px 0;'>
                <tr><td style='padding:10px;border:1px solid #D4AF37;background:#F5EFE6;width:40%;'><strong>Booking Reference</strong></td><td style='padding:10px;border:1px solid #D4AF37;'>{$bookingRef}</td></tr>
                <tr><td style='padding:10px;border:1px solid #D4AF37;background:#F5EFE6;'><strong>Destination</strong></td><td style='padding:10px;border:1px solid #D4AF37;'>{$dest}</td></tr>
                <tr><td style='padding:10px;border:1px solid #D4AF37;background:#F5EFE6;'><strong>Package</strong></td><td style='padding:10px;border:1px solid #D4AF37;'>{$pkg}</td></tr>
                <tr><td style='padding:10px;border:1px solid #D4AF37;background:#F5EFE6;'><strong>Departure Date</strong></td><td style='padding:10px;border:1px solid #D4AF37;'>{$depart}</td></tr>
            </table>
            <p>Need to make changes? Contact us at <a href='mailto:concierge@packngo.store' style='color:#D4AF37;'>concierge@packngo.store</a> or call <strong>+92 300 123 4567</strong>.</p>
        ");
        return self::send($email, $name, $subject, $html);
    }

    public static function sendAdminNotification(string $bookingRef, array $data): bool
    {
        $cfg      = self::cfg();
        $subject  = "⚡ New Reservation: {$bookingRef}";
        $fullName = htmlspecialchars($data['first_name'] . ' ' . $data['last_name'], ENT_QUOTES);
        $email    = htmlspecialchars($data['email'], ENT_QUOTES);
        $phone    = htmlspecialchars(($data['phone_code'] ?? '') . ' ' . ($data['phone'] ?? ''), ENT_QUOTES);
        $dest     = htmlspecialchars($data['destination_text'] ?? '', ENT_QUOTES);
        $pkg      = htmlspecialchars($data['package_text'] ?? '', ENT_QUOTES);
        $depart   = $data['departure_date'] ?? '';
        $travs    = htmlspecialchars($data['travellers'] ?? '', ENT_QUOTES);
        $notes    = htmlspecialchars($data['special_requests'] ?? 'None', ENT_QUOTES);

        $html = self::wrap("
            <h2 style='color:#1F3D36;'>New Booking Received</h2>
            <p><strong>Reference:</strong> {$bookingRef}</p>
            <table style='width:100%;border-collapse:collapse;margin:16px 0;'>
                <tr><td style='padding:8px;border:1px solid #ccc;background:#f9f9f9;width:35%;'><strong>Name</strong></td><td style='padding:8px;border:1px solid #ccc;'>{$fullName}</td></tr>
                <tr><td style='padding:8px;border:1px solid #ccc;background:#f9f9f9;'><strong>Email</strong></td><td style='padding:8px;border:1px solid #ccc;'>{$email}</td></tr>
                <tr><td style='padding:8px;border:1px solid #ccc;background:#f9f9f9;'><strong>Phone</strong></td><td style='padding:8px;border:1px solid #ccc;'>{$phone}</td></tr>
                <tr><td style='padding:8px;border:1px solid #ccc;background:#f9f9f9;'><strong>Destination</strong></td><td style='padding:8px;border:1px solid #ccc;'>{$dest}</td></tr>
                <tr><td style='padding:8px;border:1px solid #ccc;background:#f9f9f9;'><strong>Package</strong></td><td style='padding:8px;border:1px solid #ccc;'>{$pkg}</td></tr>
                <tr><td style='padding:8px;border:1px solid #ccc;background:#f9f9f9;'><strong>Departure</strong></td><td style='padding:8px;border:1px solid #ccc;'>{$depart}</td></tr>
                <tr><td style='padding:8px;border:1px solid #ccc;background:#f9f9f9;'><strong>Travellers</strong></td><td style='padding:8px;border:1px solid #ccc;'>{$travs}</td></tr>
                <tr><td style='padding:8px;border:1px solid #ccc;background:#f9f9f9;'><strong>Special Requests</strong></td><td style='padding:8px;border:1px solid #ccc;'>{$notes}</td></tr>
            </table>
            <p><a href='https://packngo.store/admin' style='background:#D4AF37;color:#1F3D36;padding:10px 22px;text-decoration:none;font-weight:bold;display:inline-block;'>Open Admin Panel</a></p>
        ");
        return self::send($cfg['admin_email'], 'PackNGo Admin', $subject, $html);
    }

    public static function sendVerificationEmail(string $email, string $name, string $url): bool
    {
        $subject = 'Verify Your Email — PackNGo';
        $html    = self::wrap("
            <h2 style='color:#1F3D36;'>Verify Your Email Address</h2>
            <p>Hello <strong>{$name}</strong>,</p>
            <p>Please click the button below to verify your email address and activate your PackNGo account.</p>
            <p style='text-align:center;margin:30px 0;'><a href='{$url}' style='background:#D4AF37;color:#1F3D36;padding:14px 32px;text-decoration:none;font-weight:bold;display:inline-block;'>Verify Email</a></p>
            <p>This link expires in 24 hours. If you did not create an account, please ignore this email.</p>
        ");
        return self::send($email, $name, $subject, $html);
    }

    public static function sendPasswordReset(string $email, string $name, string $url): bool
    {
        $subject = 'Reset Your Password — PackNGo';
        $html    = self::wrap("
            <h2 style='color:#1F3D36;'>Password Reset Request</h2>
            <p>Hello <strong>{$name}</strong>,</p>
            <p>We received a request to reset your password. Click the button below to set a new password.</p>
            <p style='text-align:center;margin:30px 0;'><a href='{$url}' style='background:#D4AF37;color:#1F3D36;padding:14px 32px;text-decoration:none;font-weight:bold;display:inline-block;'>Reset Password</a></p>
            <p>This link expires in 1 hour. If you did not request a password reset, you can safely ignore this email.</p>
        ");
        return self::send($email, $name, $subject, $html);
    }

    /**
     * Send an admin's reply to a contact/feedback message back to the sender.
     */
    public static function sendContactReply(
        string $toEmail,
        string $toName,
        string $originalSubject,
        string $originalMessage,
        string $replyMessage
    ): bool {
        $subjectLine = $originalSubject !== '' ? $originalSubject : 'Your message to PackNGo';
        $subject     = "Re: {$subjectLine}";

        $safeName    = htmlspecialchars($toName, ENT_QUOTES);
        $safeOrig    = nl2br(htmlspecialchars($originalMessage, ENT_QUOTES));
        $safeReply   = nl2br(htmlspecialchars($replyMessage, ENT_QUOTES));

        $html = self::wrap("
            <h2 style='color:#1F3D36;'>We've Replied to Your Message ✉️</h2>
            <p>Hello <strong>{$safeName}</strong>,</p>
            <p>Thank you for reaching out to PackNGo. Here is our response to your enquiry:</p>
            <div style='background:#F5EFE6;border-left:3px solid #D4AF37;padding:16px 20px;margin:20px 0;'>
                {$safeReply}
            </div>
            <p style='color:#888;font-size:13px;'><strong>Your original message:</strong><br>{$safeOrig}</p>
            <p>If you have any further questions, simply reply to this email or contact our concierge at
               <a href='mailto:concierge@packngo.store' style='color:#D4AF37;'>concierge@packngo.store</a>.</p>
        ");
        return self::send($toEmail, $toName, $subject, $html);
    }

    // ── Shared email wrapper/template ──────────────────────

    /**
     * Generic raw HTML email — used by ContactController etc.
     */
    public static function sendRaw(string $to, string $toName, string $subject, string $htmlBody): bool
    {
        return self::send($to, $toName, $subject, self::wrap($htmlBody));
    }

    private static function wrap(string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>PackNGo</title></head>
<body style="margin:0;padding:0;background:#F5EFE6;font-family:'Times New Roman',Georgia,serif;color:#1F3D36;">
  <table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:40px 20px;">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border:1px solid #D4AF37;border-radius:4px;overflow:hidden;">
      <tr><td style="background:#1F3D36;padding:24px 32px;text-align:center;">
        <span style="color:#D4AF37;font-size:28px;letter-spacing:3px;font-weight:bold;">✈ PackNGo</span>
      </td></tr>
      <tr><td style="padding:36px 40px;">
        {$content}
      </td></tr>
      <tr><td style="background:#1F3D36;padding:18px 32px;text-align:center;">
        <p style="color:rgba(245,239,230,0.65);font-size:12px;margin:0;">
          © 2026 PackNGo · Sargodha, Punjab, Pakistan<br>
          <a href="mailto:concierge@packngo.store" style="color:#D4AF37;text-decoration:none;">concierge@packngo.store</a>
        </p>
      </td></tr>
    </table>
  </td></tr></table>
</body></html>
HTML;
    }
}
