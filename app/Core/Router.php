<?php
// ============================================================
//  app/Core/Router.php
//  Simple URL router - works with OR without mod_rewrite
// ============================================================

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

   private function addRoute(string $method, string $path, callable|array $handler): void
{
    $this->routes[] = [
        'method'  => $method,
        'path'    => ($path === '/' ? '/' : rtrim($path, '/')),
        'handler' => $handler,
    ];
}

public function dispatch(string $uri, string $method): void
    {
        // Remove query string from URI
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Handle query string page parameter (for old format ?page=login)
        if ($uri === '/index.php' && isset($_GET['page'])) {
            $uri = '/' . trim($_GET['page'], '/');
            if (isset($_GET['id'])) {
                $uri .= '/' . $_GET['id'];
            }
        }
        
        // Clean up the URI
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';

foreach ($this->routes as $route) {
        if ($route['method'] !== $method) continue;

        $pattern = $this->convertToRegex($route['path']);

        if (preg_match($pattern, $uri, $matches)) {
            $handler = $route['handler'];

            if (is_array($handler)) {
                $controller = new $handler[0]();
                $action     = $handler[1];
                $controller->$action(...array_values($matches));
            } else {
                $handler(...array_values($matches));
            }
            return;
        }
    }

    // Debug removed
    
    http_response_code(404);
    echo "404 - Page non trouvee (uri was: '$uri', method: $method)";
}

    private function convertToRegex(string $path): string
    {
        $path = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $path . '$#';
    }
}
