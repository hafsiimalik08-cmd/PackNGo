<?php
/**
 * PackNGo — Auth Controller
 * POST /api/auth/register
 * POST /api/auth/verify-otp
 * POST /api/auth/resend-otp
 * POST /api/auth/login
 * POST /api/auth/logout
 * POST /api/auth/forgot-password
 * POST /api/auth/reset-password
 * POST /api/auth/profile/update
 * POST /api/auth/profile/change-password
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/UserModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Validator.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/RateLimiter.php';
require_once dirname(__DIR__, 2) . '/helpers/Mailer.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';
require_once dirname(__DIR__, 2) . '/helpers/Session.php';
require_once dirname(__DIR__, 2) . '/middleware/CsrfMiddleware.php';

class AuthController
{
    private UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function register(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('register', $ip, 5, 3600);
        CsrfMiddleware::verify();

        $raw = $this->input();
        $v   = new Validator($raw);
        $v->required('first_name')->alpha('first_name')->maxLen('first_name', 50);
        $v->required('last_name')->alpha('last_name')->maxLen('last_name', 50);
        $v->required('email')->email('email')->maxLen('email', 120);
        $v->required('password')->minLen('password', 8)->maxLen('password', 72);

        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        // Confirm password match (if confirmation field was sent)
        if (array_key_exists('password_confirm', $raw) && $raw['password'] !== $raw['password_confirm']) {
            Response::json(['success' => false, 'errors' => ['password_confirm' => ['Passwords do not match.']]], 422);
            return;
        }

        // Check duplicate email
        if ($this->model->findByEmail($raw['email'])) {
            Response::json(['success' => false, 'message' => 'An account with this email already exists.'], 409);
            return;
        }

        try {
            $userId = $this->model->register($raw);
            $user   = $this->model->findById($userId);

            // Generate OTP and email it (non-fatal if email sending fails)
            $otp = $this->model->generateOtp($userId);
            try {
                Mailer::sendOtpEmail($user['email'], $user['first_name'], $otp);
            } catch (Throwable $e) {
                error_log('[Auth] OTP email failed: ' . $e->getMessage());
            }

            Response::json([
                'success' => true,
                'message' => 'Account created! Please check your email for a 6-digit verification code.',
                'email'   => $user['email'],
            ], 201);
        } catch (Throwable $e) {
            error_log('[Auth] Register error: ' . $e->getMessage());
            Response::json(['success' => false, 'message' => 'Registration failed. Please try again.'], 500);
        }
    }

    /**
     * POST /api/auth/verify-otp
     * Body: { email, otp }
     */
    public function verifyOtp(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('verify_otp', $ip, 10, 900); // 10 attempts / 15 min
        CsrfMiddleware::verify();

        $raw   = $this->input();
        $email = Sanitizer::email((string)($raw['email'] ?? ''));
        $otp   = Sanitizer::digits((string)($raw['otp'] ?? ''), 6);

        if (!$email || strlen($otp) !== 6) {
            Response::json(['success' => false, 'message' => 'A valid email and 6-digit code are required.'], 422);
            return;
        }

        $user = $this->model->findByEmail($email);
        if (!$user) {
            Response::json(['success' => false, 'message' => 'No account found with that email.'], 404);
            return;
        }

        if ((int)$user['email_verified'] === 1) {
            Response::json(['success' => false, 'message' => 'This email is already verified. Please sign in.'], 409);
            return;
        }

        $ok = $this->model->verifyOtp((int)$user['id'], $otp);
        if (!$ok) {
            Response::json(['success' => false, 'message' => 'Invalid or expired code. Please try again or request a new one.'], 422);
            return;
        }

        Response::json(['success' => true, 'message' => 'Email verified successfully! You can now sign in.']);
    }

    /**
     * POST /api/auth/resend-otp
     * Body: { email }
     */
    public function resendOtp(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('resend_otp', $ip, 5, 900); // 5 per 15 min
        CsrfMiddleware::verify();

        $raw   = $this->input();
        $email = Sanitizer::email((string)($raw['email'] ?? ''));

        if (!$email) {
            Response::json(['success' => false, 'message' => 'Email is required.'], 422);
            return;
        }

        $user = $this->model->findByEmail($email);

        // Always respond success to avoid leaking which emails are registered
        if (!$user || (int)$user['email_verified'] === 1) {
            Response::json(['success' => true, 'message' => 'If an unverified account exists for this email, a new code has been sent.']);
            return;
        }

        $otp = $this->model->generateOtp((int)$user['id']);
        try {
            Mailer::sendOtpEmail($user['email'], $user['first_name'], $otp);
        } catch (Throwable $e) {
            error_log('[Auth] Resend OTP email failed: ' . $e->getMessage());
        }

        Response::json(['success' => true, 'message' => 'A new verification code has been sent to your email.']);
    }

    public function login(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('login', $ip, 10, 900); // 10 attempts per 15 min
        CsrfMiddleware::verify();

        $raw = $this->input();
        $v   = new Validator($raw);
        $v->required('email')->email('email');
        $v->required('password');

        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        $user = $this->model->authenticate($raw['email'], $raw['password']);
        if (!$user) {
            // Same message for wrong email AND wrong password (prevent enumeration)
            Response::json(['success' => false, 'message' => 'Invalid email or password.'], 401);
            return;
        }

        // Block login until the email has been verified via OTP
        if ((int)$user['email_verified'] !== 1) {
            // Send a fresh OTP so the user can verify immediately
            $otp = $this->model->generateOtp((int)$user['id']);
            try {
                Mailer::sendOtpEmail($user['email'], $user['first_name'], $otp);
            } catch (Throwable $e) {
                error_log('[Auth] OTP email failed: ' . $e->getMessage());
            }

            Response::json([
                'success'          => false,
                'email_unverified' => true,
                'email'            => $user['email'],
                'message'          => 'Please verify your email first. We just sent a new verification code.',
            ], 403);
            return;
        }

        // Start session
        Session::start();
        session_regenerate_id(true);
        $_SESSION['user']          = $this->model->safeData($user);
        $_SESSION['last_activity'] = time();

        Response::json([
            'success'  => true,
            'message'  => 'Login successful.',
            'user'     => [
                'id'         => $user['id'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'email'      => $user['email'],
                'role'       => $user['role'],
            ],
            'csrf_token' => CsrfMiddleware::generate(),
        ]);
    }

    public function logout(): void
    {
        Session::start();
        Session::destroy();
        Response::json(['success' => true, 'message' => 'Logged out.']);
    }

    public function forgotPassword(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('forgot_password', $ip, 5, 3600);

        $raw = $this->input();
        $v   = new Validator($raw);
        $v->required('email')->email('email');
        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        $token = $this->model->createResetToken($raw['email']);
        if ($token) {
            $config = require dirname(__DIR__, 2) . '/config/app.php';
            $url    = $config['app']['url'] . '/auth/reset-password.php?token=' . $token;
            try {
                $user = $this->model->findByEmail($raw['email']);
                Mailer::sendPasswordReset($raw['email'], $user['first_name'], $url);
            } catch (Throwable $e) {
                error_log('[Auth] Reset email failed: ' . $e->getMessage());
            }
        }
        // Always return success to prevent email enumeration
        Response::json(['success' => true, 'message' => 'If this email exists, a reset link has been sent.']);
    }

    public function resetPassword(): void
    {
        $raw = $this->input();
        $v   = new Validator($raw);
        $v->required('token');
        $v->required('password')->minLen('password', 8)->maxLen('password', 72);
        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        $ok = $this->model->resetPassword($raw['token'], $raw['password']);
        Response::json([
            'success' => $ok,
            'message' => $ok ? 'Password reset successful.' : 'Invalid or expired token.',
        ], $ok ? 200 : 400);
    }

    public function csrf(): void
    {
        Session::start();
        $token = CsrfMiddleware::generate();
        Response::json(['success' => true, 'csrf_token' => $token]);
    }

    public function verifyEmail(): void
    {
        $token = Sanitizer::string($_GET['token'] ?? '', 100);
        $ok    = $this->model->verifyEmail($token);
        // Redirect to the new auth pages
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $base   = $config['app']['url'];
        if ($ok) {
            header('Location: ' . $base . '/auth/login.php?verified=1');
        } else {
            header('Location: ' . $base . '/auth/signup.php?verify_failed=1');
        }
        exit;
    }

    /**
     * POST /api/auth/profile/update
     * Auth required. Body: { first_name, last_name, phone }
     */
    public function updateProfile(): void
    {
        Session::start();
        if (!Session::isLoggedIn()) {
            Response::unauthorized('You must be logged in to update your profile.');
        }
        CsrfMiddleware::verify();

        $raw    = $this->input();
        $userId = (int) Session::user()['id'];

        $v = new Validator($raw);
        $v->required('first_name')->alpha('first_name')->maxLen('first_name', 50);
        $v->required('last_name')->alpha('last_name')->maxLen('last_name', 50);
        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        $data = [
            'first_name' => Sanitizer::name($raw['first_name'], 50),
            'last_name'  => Sanitizer::name($raw['last_name'], 50),
        ];
        if (array_key_exists('phone', $raw)) {
            $phone = Sanitizer::digits((string)$raw['phone'], 20);
            $data['phone'] = $phone !== '' ? $phone : null;
        }

        $this->model->updateProfile($userId, $data);

        // Refresh session copy so the UI reflects the change immediately
        $fresh = $this->model->findById($userId);
        if ($fresh) {
            $_SESSION['user'] = $this->model->safeData($fresh);
        }
        Session::set('last_activity', time());

        Response::json(['success' => true, 'message' => 'Profile updated successfully.']);
    }

    /**
     * POST /api/auth/profile/change-password
     * Auth required. Body: { current_password, new_password }
     */
    public function changePassword(): void
    {
        Session::start();
        if (!Session::isLoggedIn()) {
            Response::unauthorized('You must be logged in to change your password.');
        }
        CsrfMiddleware::verify();

        $raw    = $this->input();
        $userId = (int) Session::user()['id'];

        $v = new Validator($raw);
        $v->required('current_password');
        $v->required('new_password')->minLen('new_password', 8)->maxLen('new_password', 72);
        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        $user = $this->model->findById($userId);
        if (!$user || !password_verify($raw['current_password'], $user['password_hash'])) {
            Response::json(['success' => false, 'message' => 'Your current password is incorrect.'], 401);
            return;
        }

        if (password_verify($raw['new_password'], $user['password_hash'])) {
            Response::json(['success' => false, 'message' => 'New password must be different from the current password.'], 422);
            return;
        }

        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $ok = $this->model->update($userId, [
            'password_hash' => password_hash($raw['new_password'], PASSWORD_BCRYPT, ['cost' => $config['security']['bcrypt_rounds']]),
        ]) > 0;

        Session::set('last_activity', time());

        Response::json([
            'success' => $ok,
            'message' => $ok ? 'Password changed successfully.' : 'Unable to update password. Please try again.',
        ], $ok ? 200 : 500);
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
