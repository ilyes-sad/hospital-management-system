<?php
// ============================================================
//  public/index.php
// ============================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

require ROOT_PATH . 'config/database.php';
require ROOT_PATH . 'app/Core/Database.php';
require ROOT_PATH . 'app/Core/Model.php';
require ROOT_PATH . 'app/Core/Controller.php';
require ROOT_PATH . 'app/Core/Validator.php';
require ROOT_PATH . 'app/Core/Router.php';

require ROOT_PATH . 'app/Models/Hopital.php';
require ROOT_PATH . 'app/Models/Medecin.php';
require ROOT_PATH . 'app/Models/Patient.php';
require ROOT_PATH . 'app/Models/RendezVous.php';

require ROOT_PATH . 'app/Controllers/DashboardController.php';
require ROOT_PATH . 'app/Controllers/HopitauxController.php';
require ROOT_PATH . 'app/Controllers/MedecinsController.php';
require ROOT_PATH . 'app/Controllers/PatientsController.php';
require ROOT_PATH . 'app/Controllers/RendezVousController.php';

require ROOT_PATH . 'app/helpers.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router = new Router();

// ---- WEB ROUTES ----
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/hopitaux', [HopitauxController::class, 'index']);
$router->get('/hopitaux/create', [HopitauxController::class, 'create']);
$router->post('/hopitaux/store', [HopitauxController::class, 'store']);
$router->get('/hopitaux/edit/{id}', [HopitauxController::class, 'edit']);
$router->post('/hopitaux/update/{id}', [HopitauxController::class, 'update']);
$router->post('/hopitaux/delete/{id}', [HopitauxController::class, 'delete']);

$router->get('/medecins', [MedecinsController::class, 'index']);
$router->get('/medecins/create', [MedecinsController::class, 'create']);
$router->post('/medecins/store', [MedecinsController::class, 'store']);
$router->get('/medecins/edit/{id}', [MedecinsController::class, 'edit']);
$router->post('/medecins/update/{id}', [MedecinsController::class, 'update']);
$router->post('/medecins/delete/{id}', [MedecinsController::class, 'delete']);

$router->get('/patients', [PatientsController::class, 'index']);
$router->get('/patients/create', [PatientsController::class, 'create']);
$router->post('/patients/store', [PatientsController::class, 'store']);
$router->get('/patients/edit/{id}', [PatientsController::class, 'edit']);
$router->post('/patients/update/{id}', [PatientsController::class, 'update']);
$router->post('/patients/delete/{id}', [PatientsController::class, 'delete']);

$router->get('/rendezvous', [RendezVousController::class, 'index']);
$router->get('/rendezvous/create', [RendezVousController::class, 'create']);
$router->post('/rendezvous/store', [RendezVousController::class, 'store']);
$router->get('/rendezvous/edit/{id}', [RendezVousController::class, 'edit']);
$router->post('/rendezvous/update/{id}', [RendezVousController::class, 'update']);
$router->post('/rendezvous/delete/{id}', [RendezVousController::class, 'delete']);

// ---- API ROUTES ----
// IMPORTANT: Specific routes BEFORE parameterized routes!

$router->get('/api/rendezvous', [RendezVousController::class, 'apiIndex']);
$router->get('/api/rendezvous/stats', [RendezVousController::class, 'apiStats']);
$router->get('/api/rendezvous/busy-slots', [RendezVousController::class, 'apiBusySlots']);
$router->get('/api/rendezvous/{id}', [RendezVousController::class, 'apiShow']);
$router->post('/api/rendezvous', [RendezVousController::class, 'apiStore']);
$router->put('/api/rendezvous/{id}', [RendezVousController::class, 'apiUpdate']);
$router->delete('/api/rendezvous/{id}', [RendezVousController::class, 'apiDelete']);

$router->get('/api/hopitaux', [HopitauxController::class, 'apiIndex']);
$router->get('/api/hopitaux/regions', [HopitauxController::class, 'apiRegions']);
$router->get('/api/hopitaux/by-region', [HopitauxController::class, 'apiByRegion']);
$router->get('/api/hopitaux/{id}', [HopitauxController::class, 'apiShow']);
$router->post('/api/hopitaux', [HopitauxController::class, 'apiStore']);
$router->put('/api/hopitaux/{id}', [HopitauxController::class, 'apiUpdate']);
$router->delete('/api/hopitaux/{id}', [HopitauxController::class, 'apiDelete']);

$router->get('/api/medecins', [MedecinsController::class, 'apiIndex']);
$router->get('/api/medecins/by-hopital', [MedecinsController::class, 'apiByHopital']);
$router->get('/api/medecins/specialites', [MedecinsController::class, 'apiSpecialites']);
$router->get('/api/medecins/{id}', [MedecinsController::class, 'apiShow']);
$router->post('/api/medecins', [MedecinsController::class, 'apiStore']);
$router->put('/api/medecins/{id}', [MedecinsController::class, 'apiUpdate']);
$router->delete('/api/medecins/{id}', [MedecinsController::class, 'apiDelete']);

$router->get('/api/patients', [PatientsController::class, 'apiIndex']);
$router->get('/api/patients/search', [PatientsController::class, 'apiSearch']);
$router->get('/api/patients/{id}', [PatientsController::class, 'apiShow']);
$router->post('/api/patients', [PatientsController::class, 'apiStore']);
$router->put('/api/patients/{id}', [PatientsController::class, 'apiUpdate']);
$router->delete('/api/patients/{id}', [PatientsController::class, 'apiDelete']);

// Strip /medapp2/public from URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$scriptName = basename($_SERVER['SCRIPT_NAME']);

if ($scriptDir !== '/' && $scriptDir !== '\\' && $scriptDir !== '.') {
    $requestUri = preg_replace('#^' . preg_quote($scriptDir, '#') . '#', '', $requestUri);
}

if ($scriptName && strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
}

$requestUri = rtrim($requestUri, '/');
if ($requestUri === '') $requestUri = '/';

$router->dispatch($requestUri, $_SERVER['REQUEST_METHOD']);