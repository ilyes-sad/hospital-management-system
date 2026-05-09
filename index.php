    <?php

session_start();
require_once __DIR__ . '/controllers/reclamationController.php';
require_once __DIR__ . '/controllers/userController.php';

$controller = new UserController();

$reclamationController = new ReclamationController();

// Récupérer l'action depuis l'URL
$action = $_GET['action'] ?? null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    /* =========================
    ROUTING LOGIC
    ========================= */

    if ($action === null) {
        // Redirection intelligente

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SESSION['user']['nomRole'] === 'admin') {
            header("Location: index.php?action=index");
            exit;
        }

        header("Location: index.php?action=profile");
        exit;
    }

    /* =========================
    ROUTER SWITCH
    ========================= */

    switch ($action) {

        // BACKOFFICE USER
        case 'index':
            $controller->index();
            break;

        case 'create':
            $controller->create();
            break;

        case 'store':
            $controller->store();
            break;
            case 'updateProfile':
    $controller->updateProfile();
    break;
    case 'editProfile':
    $controller->editProfile();
    break;

        case 'edit':
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id <= 0) {
        header("Location: index.php?action=index");
        exit;
    }
    $controller->edit($id);
    break;
        case 'update':
            $controller->update($id);
            break;

        case 'delete':
            $controller->delete($id);
            break;

        case 'show':
            $controller->show();
            break;

        // FRONTOFFICE
        case 'login':
            $controller->login();
            break;

        case 'doLogin':
            $controller->doLogin();
            break;

        case 'register':
            $controller->register();
            break;

        case 'storeRegister':
            $controller->storeRegister();
            break;

        case 'profile':
            $controller->profile();
            break;

        case 'logout':
            $controller->logout();
            break;
        case 'forgotPassword':
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->forgotPassword();
        break;

        case 'sendResetCode':
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->sendResetCode();
        break;
        
        case 'onlineUsers':
        $controller->onlineUsers();
        break;

        case 'resetPassword':
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->resetPassword();
        break;
        //RECLAMATIONS
        case 'newReclamation':
        $reclamationController->create();
        break;

        case 'storeReclamation':
        $reclamationController->store();
        break;

        case 'myReclamations':
        $reclamationController->mine();
        break;

        case 'showMyReclamation':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $reclamationController->showMine($id);
    break;

case 'adminReclamations':
    $reclamationController->adminIndex();
    break;

case 'showReclamation':
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $reclamationController->adminShow($id);
    break;

case 'respondReclamation':
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $reclamationController->respond($id);
    break;
    case 'updateReclamationStatus':
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $reclamationController->updateStatus($id);
    break;

        default:
            echo "Action non reconnue.";
            break;
        
        

    }