<?php

require_once __DIR__ . '/controllers/ReclamationController.php';

$controller = new ReclamationController();

$route = $_GET['route'] ?? null;
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$trimmed = trim(str_replace($scriptPath, '', $uri), '/');
$route = $route ?: $trimmed;

if ($route === 'reclamation/new') {
    $controller->showNew();
    return;
}

if ($route === 'reclamation/save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->save();
    return;
}

if ($route === 'admin/reclamations') {
    $controller->adminList();
    return;
}

if (preg_match('#^admin/reclamation/(\d+)/delete$#', $route, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->adminDelete((int)$matches[1]);
    return;
}

if (preg_match('#^admin/reclamation/(\d+)/edit$#', $route, $matches)) {
    $controller->adminEdit((int)$matches[1]);
    return;
}

if (preg_match('#^admin/reclamation/(\d+)$#', $route, $matches)) {
    $controller->adminDetail((int)$matches[1]);
    return;
}

http_response_code(404);
echo '<h1>Page non trouvée</h1>';
