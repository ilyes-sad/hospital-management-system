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
    $uri = parse_url($uri, PHP_URL_PATH);

    $scriptDir  = dirname($_SERVER['SCRIPT_NAME']);
    $scriptName = basename($_SERVER['SCRIPT_NAME']);

    if ($scriptDir !== '/' && $scriptDir !== '.') {
        $uri = preg_replace('#^' . preg_quote($scriptDir, '#') . '#', '', $uri);
    }

    if ($scriptName && strpos($uri, $scriptName) === 0) {
        $uri = substr($uri, strlen($scriptName));
    }

    $uri = rtrim($uri, '/');
    if ($uri === '') $uri = '/';

    if ($uri === '/' && isset($_GET['page'])) {
        $uri = '/' . trim($_GET['page'], '/');
        if (isset($_GET['id'])) {
            $uri .= '/' . $_GET['id'];
        }
    }

    foreach ($this->routes as $route) {
        if ($route['method'] !== $method) continue;

        $pattern = $this->convertToRegex($route['path']);

if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
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

    http_response_code(404);
    echo "404 - Page non trouvee (uri was: '$uri')";
}

    private function convertToRegex(string $path): string
    {
        $path = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $path . '$#';
    }
}
