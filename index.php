    <?php

session_start();

require_once __DIR__ . '/controllers/UserController.php';

$controller = new UserController();

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

        // BACKOFFICE
        case 'index':
            $controller->index();
            break;

        case 'create':
            $controller->create();
            break;

        case 'store':
            $controller->store();
            break;

        case 'edit':
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

        case 'resetPassword':
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->resetPassword();
        break;

        default:
            echo "Action non reconnue.";
            break;

    }