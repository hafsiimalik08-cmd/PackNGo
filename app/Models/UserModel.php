<?php
/**
 * PackNGo — User Model
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    protected string $table = 'users';

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE email = :email LIMIT 1",
            [':email' => strtolower(trim($email))]
        );
    }

    /**
     * Register a new customer.
     */
    public function register(array $data): int
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $cost   = $config['security']['bcrypt_rounds'];

        return $this->insert([
            'first_name'     => trim($data['first_name']),
            'last_name'      => trim($data['last_name']),
            'email'          => strtolower(trim($data['email'])),
            'password_hash'  => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => $cost]),
            'phone_code'     => $data['phone_code'] ?? '+92',
            'phone'          => $data['phone'] ?? null,
            'role'           => 'customer',
            'email_verified' => 0,
            'verify_token'   => bin2hex(random_bytes(32)),
        ]);
    }

    /**
     * Verify password; returns user array on success, null on failure.
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }
        if (!$user['is_active']) {
            return null;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }
        // Rehash if cost factor changed
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => $config['security']['bcrypt_rounds']])) {
            $this->update($user['id'], [
                'password_hash' => password_hash($password, PASSWORD_BCRYPT, ['cost' => $config['security']['bcrypt_rounds']]),
            ]);
        }
        return $user;
    }

    /**
     * Verify email with token (legacy link-based — kept for backward compat).
     */
    public function verifyEmail(string $token): bool
    {
        $user = $this->db->fetchOne(
            "SELECT id FROM users WHERE verify_token = :token AND email_verified = 0 LIMIT 1",
            [':token' => $token]
        );
        if (!$user) {
            return false;
        }
        return $this->update($user['id'], [
            'email_verified' => 1,
            'verify_token'   => null,
            'otp_code'       => null,
            'otp_expires'    => null,
        ]) > 0;
    }

    /**
     * Generate a new 6-digit OTP for email verification.
     * Stores a bcrypt hash of the OTP with a 15-minute expiry and
     * returns the plain-text OTP so it can be emailed to the user.
     */
    public function generateOtp(int $userId): string
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $otp     = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $this->update($userId, [
            'otp_code'    => password_hash($otp, PASSWORD_BCRYPT, ['cost' => $config['security']['bcrypt_rounds']]),
            'otp_expires' => $expires,
        ]);

        return $otp;
    }

    /**
     * Verify a submitted OTP for the given user.
     * On success: marks email_verified = 1 and clears the OTP (single-use).
     * Returns false if OTP is missing, expired, or doesn't match.
     */
    public function verifyOtp(int $userId, string $otp): bool
    {
        $user = $this->findById($userId);
        if (!$user || empty($user['otp_code']) || empty($user['otp_expires'])) {
            return false;
        }

        // Expiry check
        if (strtotime($user['otp_expires']) < time()) {
            return false;
        }

        // Hash check
        if (!password_verify($otp, $user['otp_code'])) {
            return false;
        }

        // Success: activate account, clear OTP (prevents reuse)
        return $this->update($userId, [
            'email_verified' => 1,
            'verify_token'   => null,
            'otp_code'       => null,
            'otp_expires'    => null,
        ]) > 0;
    }

    /**
     * Create a password reset token (valid 1 hour).
     */
    public function createResetToken(string $email): ?string
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }
        $token = bin2hex(random_bytes(32));
        $this->update($user['id'], [
            'reset_token'   => $token,
            'reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ]);
        return $token;
    }

    /**
     * Reset password using token.
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $user   = $this->db->fetchOne(
            "SELECT id FROM users
              WHERE reset_token = :token
                AND reset_expires > NOW()
              LIMIT 1",
            [':token' => $token]
        );
        if (!$user) {
            return false;
        }
        return $this->update($user['id'], [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => $config['security']['bcrypt_rounds']]),
            'reset_token'   => null,
            'reset_expires' => null,
        ]) > 0;
    }

    /**
     * Update a user's editable profile fields (name, phone).
     * Email and role are intentionally not editable here.
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $allowed = ['first_name', 'last_name', 'phone', 'phone_code'];
        $update  = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) {
            return false;
        }
        return $this->update($userId, $update) > 0;
    }

    /**
     * Return user data safe for session storage (no hashes/tokens).
     */
    public function safeData(array $user): array
    {
        unset(
            $user['password_hash'],
            $user['verify_token'],
            $user['reset_token'],
            $user['reset_expires'],
            $user['otp_code'],
            $user['otp_expires']
        );
        return $user;
    }
}
