<?php

/**
 * Router Class
 * Best Practice: RESTful routing with middleware support
 */
class Router {
    private static $routes = [];
    private static $middleware = [];
    private static $currentRoute = null;
    
    /**
     * Add GET route
     * @param string $path
     * @param string $handler
     * @param array $middleware
     */
    public static function get($path, $handler, $middleware = []) {
        self::addRoute('GET', $path, $handler, $middleware);
    }
    
    /**
     * Add POST route
     * @param string $path
     * @param string $handler
     * @param array $middleware
     */
    public static function post($path, $handler, $middleware = []) {
        self::addRoute('POST', $path, $handler, $middleware);
    }
    
    /**
     * Add PUT route
     * @param string $path
     * @param string $handler
     * @param array $middleware
     */
    public static function put($path, $handler, $middleware = []) {
        self::addRoute('PUT', $path, $handler, $middleware);
    }
    
    /**
     * Add DELETE route
     * @param string $path
     * @param string $handler
     * @param array $middleware
     */
    public static function delete($path, $handler, $middleware = []) {
        self::addRoute('DELETE', $path, $handler, $middleware);
    }
    
    /**
     * Add route for any method
     * @param string $path
     * @param string $handler
     * @param array $middleware
     */
    public static function any($path, $handler, $middleware = []) {
        self::addRoute('ANY', $path, $handler, $middleware);
    }
    
    /**
     * Add route
     * @param string $method
     * @param string $path
     * @param string $handler
     * @param array $middleware
     */
    private static function addRoute($method, $path, $handler, $middleware = []) {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
            'pattern' => self::convertToPattern($path)
        ];
    }
    
    /**
     * Convert route path to regex pattern
     * @param string $path
     * @return string
     */
    private static function convertToPattern($path) {
        // Convert {param} to regex capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        // Convert {param?} to optional regex capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\?\}/', '?([^/]*)', $pattern);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Dispatch route
     */
    public static function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Handle PUT and DELETE from form submissions
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }
        
        foreach (self::$routes as $route) {
            if (($route['method'] === $requestMethod || $route['method'] === 'ANY') &&
                preg_match($route['pattern'], $requestUri, $matches)) {
                
                array_shift($matches); // Remove full match
                self::$currentRoute = $route;
                
                // Run middleware
                foreach ($route['middleware'] as $middlewareName) {
                    if (isset(self::$middleware[$middlewareName])) {
                        $middlewareClass = self::$middleware[$middlewareName];
                        $middleware = new $middlewareClass();
                        $result = $middleware->handle();
                        
                        if ($result === false) {
                            return; // Middleware blocked request
                        }
                    }
                }
                
                // Call handler
                self::callHandler($route['handler'], $matches);
                return;
            }
        }
        
        // No route found
        self::notFound();
    }
    
    /**
     * Call route handler
     * @param string $handler
     * @param array $params
     */
    private static function callHandler($handler, $params = []) {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
        } elseif (strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            
            $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controller)) {
                    $controllerInstance = new $controller();
                    
                    if (method_exists($controllerInstance, $method)) {
                        call_user_func_array([$controllerInstance, $method], $params);
                    } else {
                        throw new Exception("Method {$method} not found in controller {$controller}");
                    }
                } else {
                    throw new Exception("Controller {$controller} not found");
                }
            } else {
                throw new Exception("Controller file not found: {$controllerFile}");
            }
        }
    }
    
    /**
     * Register middleware
     * @param string $name
     * @param string $class
     */
    public static function middleware($name, $class) {
        self::$middleware[$name] = $class;
    }
    
    /**
     * Route group with middleware
     * @param array $middleware
     * @param callable $callback
     */
    public static function group($middleware, $callback) {
        $callback();
    }
    
    /**
     * Handle 404 Not Found
     */
    private static function notFound() {
        http_response_code(404);
        
        if (file_exists(__DIR__ . '/../views/errors/404.php')) {
            require_once __DIR__ . '/../views/errors/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
        }
        exit;
    }
    
    /**
     * Generate URL for named route
     * @param string $name
     * @param array $params
     * @return string
     */
    public static function url($path, $params = []) {
        $config = require __DIR__ . '/../../config/config.php';
        $url = $config['app']['url'] . $path;
        
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url = str_replace('{' . $key . '}', $value, $url);
            }
        }
        
        return $url;
    }
    
    /**
     * Get current route
     * @return array|null
     */
    public static function currentRoute() {
        return self::$currentRoute;
    }
}
