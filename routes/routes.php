<?php
/**
 * PackNGo — Router
 * Lightweight, dependency-free HTTP router.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/Response.php';

class Router
{
    /**
     * Resolve the current request against the route table and call the handler.
     *
     * @param array $routes  ['METHOD /path' => ['Controller', 'method']]
     */
    public static function dispatch(array $routes): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        // Support _method override for HTML forms (PUT/PATCH/DELETE)
        if ($method === 'POST' && !empty($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Get path, strip query string
        $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri  = '/' . trim((string)$uri, '/');

        // Remove a sub-directory prefix if the app lives in a sub-folder
        // e.g. /PackNGo/api/reservations → /api/reservations
        $base = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($base !== '/' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = '/' . ltrim($uri, '/');

        foreach ($routes as $signature => $handler) {
            [$routeMethod, $routePath] = explode(' ', $signature, 2);
            $routeMethod = strtoupper(trim($routeMethod));
            $routePath   = trim($routePath);

            if ($routeMethod !== $method) {
                continue;
            }

            // Convert :param placeholders to named capture groups
            $pattern = preg_replace('/:([a-zA-Z_]+)/', '(?P<$1>[^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            // Extract named parameters
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            [$class, $method_name] = $handler;

            // Auto-require controller
            $controllerFile = dirname(__DIR__) . "/app/Controllers/{$class}.php";
            if (!file_exists($controllerFile)) {
                Response::json(['success' => false, 'message' => "Controller {$class} not found."], 500);
                return;
            }
            require_once $controllerFile;

            if (!class_exists($class)) {
                Response::json(['success' => false, 'message' => "Class {$class} not found."], 500);
                return;
            }

            $controller = new $class();

            if (!method_exists($controller, $method_name)) {
                Response::json(['success' => false, 'message' => "Method {$method_name} not found."], 500);
                return;
            }

            // Call with extracted params (e.g. :id → int)
            if (!empty($params)) {
                $args = array_map(fn($v) => is_numeric($v) ? (int)$v : $v, array_values($params));
                call_user_func_array([$controller, $method_name], $args);
            } else {
                $controller->$method_name();
            }
            return;
        }

        // No route matched
        Response::notFound("Endpoint not found: {$method} {$uri}");
    }
}
