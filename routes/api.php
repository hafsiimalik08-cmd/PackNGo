<?php
/**
 * PackNGo — API Routes (Complete)
 */
declare(strict_types=1);
require_once __DIR__ . '/routes.php';

$routes = [
    // ── CSRF ──
    'GET    /api/auth/csrf'                       => ['AuthController',       'csrf'],

    // ── Auth ──
    'POST   /api/auth/register'                   => ['AuthController',       'register'],
    'POST   /api/auth/verify-otp'                 => ['AuthController',       'verifyOtp'],
    'POST   /api/auth/resend-otp'                 => ['AuthController',       'resendOtp'],
    'POST   /api/auth/login'                      => ['AuthController',       'login'],
    'POST   /api/auth/logout'                     => ['AuthController',       'logout'],
    'GET    /api/auth/verify'                     => ['AuthController',       'verifyEmail'],
    'POST   /api/auth/forgot-password'            => ['AuthController',       'forgotPassword'],
    'POST   /api/auth/reset-password'             => ['AuthController',       'resetPassword'],
    'POST   /api/auth/profile/update'             => ['AuthController',       'updateProfile'],
    'POST   /api/auth/profile/change-password'    => ['AuthController',       'changePassword'],

    // ── Reservations (public) ──
    'POST   /api/reservation/submit'              => ['ReservationController',   'submit'],
    'GET    /api/reservation/check'               => ['ReservationController',   'check'],
    'POST   /api/reservation/send-otp'            => ['ReservationOtpController','sendOtp'],
    'POST   /api/reservation/verify-otp'          => ['ReservationOtpController','verifyOtp'],

    // ── Destinations (public) ──
    'GET    /api/destinations'                    => ['DestinationController','index'],
    'GET    /api/destinations/search'             => ['DestinationController','search'],
    'GET    /api/destinations/:slug'              => ['DestinationController','show'],
    'GET    /api/packages'                        => ['DestinationController','packages'],

    // ── Blog (public) ──
    'GET    /api/blog/posts'                      => ['BlogController',       'posts'],
    'GET    /api/blog/posts/:slug'                => ['BlogController',       'show'],
    'GET    /api/blog/categories'                 => ['BlogController',       'categories'],
    'GET    /api/blog/featured'                   => ['BlogController',       'featured'],

    // ── Gallery (public) ──
    'GET    /api/gallery'                         => ['GalleryController',    'index'],

    // ── Newsletter (public) ──
    'POST   /api/newsletter/subscribe'            => ['NewsletterController', 'subscribe'],
    'GET    /api/newsletter/unsubscribe'          => ['NewsletterController', 'unsubscribe'],

    // ── Contact (public) ──
    'POST   /api/contact/send'                    => ['ContactController',    'send'],

    // ══════════════════════════════════════════
    //  ADMIN ROUTES (all require admin session)
    // ══════════════════════════════════════════

    'GET    /api/admin/dashboard'                 => ['AdminController',      'dashboard'],
    'GET    /api/admin/reports'                   => ['AdminController',      'reports'],

    // Users
    'GET    /api/admin/users'                     => ['AdminController',      'listUsers'],
    'GET    /api/admin/users/:id'                 => ['AdminController',      'getUser'],
    'PUT    /api/admin/users/:id'                 => ['AdminController',      'updateUser'],
    'PATCH  /api/admin/users/:id/toggle-block'    => ['AdminController',      'toggleUserBlock'],
    'DELETE /api/admin/users/:id'                 => ['AdminController',      'deleteUser'],

    // Reservations
    'GET    /api/admin/reservations'              => ['AdminController',      'listReservations'],
    'GET    /api/admin/reservations/:id'          => ['AdminController',      'getReservation'],
    'PATCH  /api/admin/reservations/:id/status'   => ['AdminController',      'updateReservationStatus'],
    'DELETE /api/admin/reservations/:id'          => ['AdminController',      'deleteReservation'],

    // Destinations
    'GET    /api/admin/destinations'              => ['AdminController',      'listDestinations'],
    'POST   /api/admin/destinations'              => ['AdminController',      'createDestination'],
    'PUT    /api/admin/destinations/:id'          => ['AdminController',      'updateDestination'],
    'DELETE /api/admin/destinations/:id'          => ['AdminController',      'deleteDestination'],

    // Packages
    'GET    /api/admin/packages'                  => ['AdminController',      'listPackages'],
    'POST   /api/admin/packages'                  => ['AdminController',      'createPackage'],
    'PUT    /api/admin/packages/:id'              => ['AdminController',      'updatePackage'],
    'DELETE /api/admin/packages/:id'              => ['AdminController',      'deletePackage'],

    // Blog Posts
    'GET    /api/admin/blog/posts'                => ['AdminController',      'listPosts'],
    'GET    /api/admin/blog/posts/:id'            => ['AdminController',      'getPost'],
    'POST   /api/admin/blog/posts'                => ['AdminController',      'createPost'],
    'PUT    /api/admin/blog/posts/:id'            => ['AdminController',      'updatePost'],
    'DELETE /api/admin/blog/posts/:id'            => ['AdminController',      'deletePost'],
    'GET    /api/admin/blog/categories'           => ['AdminController',      'listBlogCategories'],

    // Messages
    'GET    /api/admin/messages'                  => ['AdminController',      'listMessages'],
    'PATCH  /api/admin/messages/:id/read'         => ['AdminController',      'markMessageRead'],
    'POST   /api/admin/messages/:id/reply'        => ['AdminController',      'replyMessage'],
    'DELETE /api/admin/messages/:id'              => ['AdminController',      'deleteMessage'],

    // Newsletter
    'GET    /api/admin/subscribers'               => ['AdminController',      'listSubscribers'],
    'DELETE /api/admin/subscribers/:id'           => ['AdminController',      'deleteSubscriber'],

    // Gallery Uploads
    'POST   /api/admin/upload'                    => ['UploadController',     'image'],
    'DELETE /api/admin/gallery/:id'               => ['UploadController',     'deleteImage'],
];

Router::dispatch($routes);
