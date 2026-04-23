<?php
// ============================================================
//  app/Core/Controller.php
//  Base Controller - handles views, redirects, validation
// ============================================================

class Controller
{
    protected array $errors = [];
    protected array $oldInput = [];

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = ROOT_PATH . "app/Views/{$view}.php";

        if (!file_exists($viewPath)) {
            die("Erreur : la vue '{$view}' est introuvable ({$viewPath})");
        }

        require $viewPath;
    }

    protected function layout(string $layout, string $content, array $data = []): void
    {
        extract($data);
        $layoutPath = ROOT_PATH . "app/Views/layouts/{$layout}.php";

        if (!file_exists($layoutPath)) {
            die("Erreur : le layout '{$layout}' est introuvable");
        }

        require $layoutPath;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function validate(array $data, array $rules): array|false
    {
        $validator = Validator::make($data, $rules)->validate();

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            $this->oldInput = $data;
            return false;
        }

        return $data;
    }

    protected function getErrors(): array
    {
        return $this->errors;
    }

    protected function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    protected function old(string $field, string $default = ''): string
    {
        return $this->oldInput[$field] ?? $default;
    }

    protected function errorFor(string $field): string
    {
        return $this->errors[$field] ?? '';
    }

    protected function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    protected function sendJson(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function sendSuccess($data = null, string $message = 'OK'): void
    {
        $this->sendJson(['success' => true, 'message' => $message, 'data' => $data]);
    }

    protected function sendError(string $message, int $code = 400): void
    {
        $this->sendJson(['success' => false, 'error' => $message], $code);
    }

    protected function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
