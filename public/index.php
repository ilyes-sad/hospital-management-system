<?php
// ============================================================
//  public/index.php
//  Front controller — all requests go through here
// ============================================================

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

// ---- CORE ----
require ROOT_PATH . 'config/database.php';
require ROOT_PATH . 'app/Core/Database.php';
require ROOT_PATH . 'app/Core/Model.php';
require ROOT_PATH . 'app/Core/Controller.php';
require ROOT_PATH . 'app/Core/Validator.php';
require ROOT_PATH . 'app/Core/Router.php';

// ---- MODELS ----
require ROOT_PATH . 'app/Models/Hopital.php';
require ROOT_PATH . 'app/Models/Medecin.php';
require ROOT_PATH . 'app/Models/Patient.php';
require ROOT_PATH . 'app/Models/RendezVous.php';
require ROOT_PATH . 'app/Models/reclamation/user.php';
require ROOT_PATH . 'app/Models/reclamation/reclamation.php';
require ROOT_PATH . 'app/Models/reclamation/reponseReclamation.php';
require ROOT_PATH . 'app/Models/reclamation/ReclamationNotifier.php';
require ROOT_PATH . 'app/Models/reclamation/PrioriteReclamation.php';
require ROOT_PATH . 'app/Models/reclamation/statutReclamation.php';

// ---- PHPMAILER ----
require ROOT_PATH . 'app/PHPMailer/src/PHPMailer.php';
require ROOT_PATH . 'app/PHPMailer/src/SMTP.php';
require ROOT_PATH . 'app/PHPMailer/src/Exception.php';

// ---- CONTROLLERS ----
require ROOT_PATH . 'app/Controllers/DashboardController.php';
require ROOT_PATH . 'app/Controllers/HopitauxController.php';
require ROOT_PATH . 'app/Controllers/MedecinsController.php';
require ROOT_PATH . 'app/Controllers/PatientsController.php';
require ROOT_PATH . 'app/Controllers/RendezVousController.php';
require ROOT_PATH . 'app/Controllers/PublicController.php';
require ROOT_PATH . 'app/Controllers/reclamation/usercontroller.php';
require ROOT_PATH . 'app/Controllers/reclamation/reclamationController.php';

require ROOT_PATH . 'app/helpers.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router = new Router();

// Test route
$router->get('/test', function() {
    echo "TEST OK - Router is working!";
});

// AUTH ROUTES - direct page serving
$router->get('/login', function() {
    require __DIR__ . '/login_page.php';
});

$router->post('/login', function() {
    // Simple login handler - redirect to appointment page
    $email = trim($_POST['email'] ?? '');
    $motDePasse = trim($_POST['motDePasse'] ?? '');
    
    if (empty($email) || empty($motDePasse)) {
        $_SESSION['error'] = 'Veuillez remplir tous les champs';
        header('Location: /login');
        exit;
    }
    
    // Check credentials - simplified (you can enhance this)
    try {
        require_once ROOT_PATH . 'config/database.php';
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($motDePasse, $user['motDePasse'])) {
            // Login successful
            unset($user['motDePasse']);
            $_SESSION['user'] = $user;
            
            // Redirect to appointment booking
            header('Location: /hospital-management-system-main/public/book');
            exit;
        } else {
            $_SESSION['error'] = 'Email ou mot de passe incorrect';
            header('Location: /login');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erreur de connexion: ' . $e->getMessage();
        header('Location: /login');
        exit;
    }
});

$router->get('/register', function() {
    require __DIR__ . '/register_page.php';
});

$router->post('/register', function() {
    // Simple register handler
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motDePasse = trim($_POST['motDePasse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    
    if (empty($nom) || empty($prenom) || empty($email) || empty($motDePasse)) {
        $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires';
        header('Location: /register');
        exit;
    }
    
    try {
        require_once ROOT_PATH . 'config/database.php';
        $pdo = Database::getInstance();
        
        // Get Patient role ID
        $stmt = $pdo->query("SELECT idRole FROM roles WHERE nomRole = 'Patient' LIMIT 1");
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        $idRole = $role ? $role['idRole'] : 2;
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, motDePasse, telephone, adresse, idRole, statutCompte) VALUES (?, ?, ?, ?, ?, ?, ?, 'actif')");
        $stmt->execute([$nom, $prenom, $email, password_hash($motDePasse, PASSWORD_DEFAULT), $telephone, $adresse, $idRole]);
        
        $_SESSION['success'] = 'Compte créé! Vous pouvez maintenant vous connecter.';
        header('Location: /login');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erreur lors de l\'inscription: ' . $e->getMessage();
        header('Location: /register');
        exit;
    }
});

// Debug: see what URI is being processed
$debugUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptDir !== '/' && $scriptDir !== '\\' && $scriptDir !== '.') {
    $debugUri = preg_replace('#^' . preg_quote($scriptDir, '#') . '#', '', $debugUri);
}
$debugUri = rtrim($debugUri, '/');
if ($debugUri === '') $debugUri = '/';
file_put_contents(__DIR__ . '/debug.log', "URI: $debugUri, Method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);

// ============================================================
// ---- PUBLIC FRONT OFFICE ----
// ============================================================
$router->get('/public', [PublicController::class, 'index']);
$router->get('/public/doctors', [PublicController::class, 'doctors']);
$router->get('/public/book', [PublicController::class, 'book']);
$router->get('/book', [PublicController::class, 'book']);
$router->post('/public/book', [PublicController::class, 'storeRdv']);
$router->post('/book', [PublicController::class, 'storeRdv']);
$router->get('/public/appointment/{id}', [PublicController::class, 'appointment']);
$router->get('/appointment/{id}', [PublicController::class, 'appointment']);
$router->get('/public/portal', [PublicController::class, 'portal']);
$router->post('/public/portal', [PublicController::class, 'portalSearch']);
$router->get('/public/map', [PublicController::class, 'map']);

// ============================================================
// ---- BACK OFFICE ----
// ============================================================
$router->get('/', [UserController::class, 'login']);
$router->post('/', [UserController::class, 'doLogin']);
$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/hopitaux', [HopitauxController::class, 'index']);
$router->get('/hopitaux/create', [HopitauxController::class, 'create']);
$router->post('/hopitaux/store', [HopitauxController::class, 'store']);
$router->get('/hopitaux/edit/{id}', [HopitauxController::class, 'edit']);
$router->post('/hopitaux/update/{id}', [HopitauxController::class, 'update']);
$router->post('/hopitaux/delete/{id}', [HopitauxController::class, 'delete']);
$router->get('/hopitaux/map', [HopitauxController::class, 'map']);

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

// ============================================================
// ---- AUTHENTIFICATION ----
// ============================================================
$router->get('/logout', [UserController::class, 'logout']);
$router->get('/forgot-password', [UserController::class, 'forgotPassword']);
$router->post('/forgot-password', [UserController::class, 'sendResetCode']);
$router->get('/reset-password', [UserController::class, 'resetPassword']);

// ============================================================
// ---- RECLAMATIONS ----
// ============================================================
$router->get('/reclamations/create', [ReclamationController::class, 'create']);
$router->get('/test-reclamation', function() { echo "Reclamation OK"; });
$router->post('/reclamations/store', [ReclamationController::class, 'store']);
$router->get('/reclamations/mes-reclamations', [ReclamationController::class, 'mine']);
$router->get('/reclamations/show/{id}', [ReclamationController::class, 'showMine']);

$router->get('/admin/reclamations', [ReclamationController::class, 'adminIndex']);
$router->get('/admin/reclamations/show/{id}', [ReclamationController::class, 'adminShow']);
$router->post('/admin/reclamations/status/{id}', [ReclamationController::class, 'updateStatus']);
$router->post('/admin/reclamations/respond/{id}', [ReclamationController::class, 'respond']);

// ============================================================
// ---- API ----
// ============================================================
$router->get('/api/rendezvous', [RendezVousController::class, 'apiIndex']);
$router->get('/api/rendezvous/stats', [RendezVousController::class, 'apiStats']);
$router->get('/api/rendezvous/busy-slots', [RendezVousController::class, 'apiBusySlots']);
$router->get('/api/rendezvous/{id}', [RendezVousController::class, 'apiShow']);
$router->post('/api/rendezvous', [RendezVousController::class, 'apiStore']);
$router->put('/api/rendezvous/{id}', [RendezVousController::class, 'apiUpdate']);
$router->delete('/api/rendezvous/{id}', [RendezVousController::class, 'apiDelete']);

$router->get('/api/hopitaux', [HopitauxController::class, 'apiIndex']);
$router->get('/api/hopitaux/search', [HopitauxController::class, 'apiSearch']);
$router->get('/api/hopitaux/regions', [HopitauxController::class, 'apiRegions']);
$router->get('/api/hopitaux/by-region', [HopitauxController::class, 'apiByRegion']);
$router->get('/api/hopitaux/{id}', [HopitauxController::class, 'apiShow']);
$router->post('/api/hopitaux', [HopitauxController::class, 'apiStore']);
$router->put('/api/hopitaux/{id}', [HopitauxController::class, 'apiUpdate']);
$router->delete('/api/hopitaux/{id}', [HopitauxController::class, 'apiDelete']);

$router->get('/api/medecins', [MedecinsController::class, 'apiIndex']);
$router->get('/api/medecins/search', [MedecinsController::class, 'apiSearch']);
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

$router->get('/api/public/doctors', [PublicController::class, 'apiDoctors']);
$router->get('/api/public/busy-slots', [PublicController::class, 'apiBusySlots']);

// ============================================================
// ---- DISPATCH ----
// ============================================================
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir  = dirname($_SERVER['SCRIPT_NAME']);

if ($scriptDir !== '/' && $scriptDir !== '\\' && $scriptDir !== '.') {
    $requestUri = preg_replace('#^' . preg_quote($scriptDir, '#') . '#', '', $requestUri);
}

$scriptName = basename($_SERVER['SCRIPT_NAME']);
if ($scriptName && strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
}

$requestUri = rtrim($requestUri, '/');
if ($requestUri === '') $requestUri = '/';

$router->dispatch($requestUri, $_SERVER['REQUEST_METHOD']);