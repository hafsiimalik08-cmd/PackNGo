<?php
/**
 * PackNGo — Newsletter Model
 */
declare(strict_types=1);
require_once __DIR__ . '/BaseModel.php';

class NewsletterModel extends BaseModel
{
    protected string $table = 'newsletter_subs';

    public function subscribe(string $email, ?string $name, string $ip): bool
    {
        $existing = $this->db->fetchOne(
            "SELECT id, is_active FROM newsletter_subs WHERE email = :email LIMIT 1",
            [':email' => strtolower(trim($email))]
        );
        if ($existing) {
            if ($existing['is_active']) {
                return false;
            }
            $this->update((int)$existing['id'], [
                'is_active'       => 1,
                'unsubscribed_at' => null,
                'subscribed_at'   => date('Y-m-d H:i:s'),
            ]);
            return true;
        }
        $this->insert([
            'email'      => strtolower(trim($email)),
            'name'       => $name ? trim($name) : null,
            'token'      => bin2hex(random_bytes(20)),
            'ip_address' => $ip,
        ]);
        return true;
    }

    public function unsubscribeByToken(string $token): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM newsletter_subs WHERE token = :token AND is_active = 1 LIMIT 1",
            [':token' => $token]
        );
        if (!$row) {
            return false;
        }
        return $this->update((int)$row['id'], [
            'is_active'       => 0,
            'unsubscribed_at' => date('Y-m-d H:i:s'),
        ]) > 0;
    }
}
