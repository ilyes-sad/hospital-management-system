<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

require_once __DIR__ . '/../models/user.php';

class UserController
{
    private User $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new User();
    }

    /* =========================================
       OUTILS
    
    ========================================= */
    
    
    private function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    private function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    private function isAdmin(): bool
    {
        return isset($_SESSION['user']['nomRole']) && $_SESSION['user']['nomRole'] === 'admin';
    }

    private function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('index.php?action=login');
        }
    }

    private function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('index.php?action=login');
        }
    }

    private function validateUserData(array $data, bool $isCreate = true, ?int $excludeId = null): array
    {
        $errors = [];

        $nom = trim($data['nom'] ?? '');
        $prenom = trim($data['prenom'] ?? '');
        $email = trim($data['email'] ?? '');
        $motDePasse = trim($data['motDePasse'] ?? '');
        $telephone = trim($data['telephone'] ?? '');
        $statutCompte = trim($data['statutCompte'] ?? '');
        $idRole = (int)($data['idRole'] ?? 0);

        if ($nom === '') {
            $errors['nom'] = 'Le nom est obligatoire.';
        }

        if ($prenom === '') {
            $errors['prenom'] = 'Le prénom est obligatoire.';
        }

        if ($email === '') {
            $errors['email'] = 'L’email est obligatoire.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format d’email invalide.';
        } elseif ($this->userModel->emailExists($email, $excludeId)) {
            $errors['email'] = 'Cet email existe déjà.';
        }

        if ($isCreate) {
            if ($motDePasse === '') {
                $errors['motDePasse'] = 'Le mot de passe est obligatoire.';
            } elseif (strlen($motDePasse) < 6) {
                $errors['motDePasse'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
        } else {
            if ($motDePasse !== '' && strlen($motDePasse) < 6) {
                $errors['motDePasse'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
        }

        if ($telephone !== '' && !preg_match('/^[0-9+\s\-]{8,20}$/', $telephone)) {
            $errors['telephone'] = 'Numéro de téléphone invalide.';
        }

        $statutsValides = ['actif', 'bloque', 'en_attente'];
        if ($statutCompte === '' || !in_array($statutCompte, $statutsValides, true)) {
            $errors['statutCompte'] = 'Statut du compte invalide.';
        }

        if ($idRole <= 0) {
            $errors['idRole'] = 'Le rôle est obligatoire.';
        }

        return $errors;
    }

    /* =========================================
       BACKOFFICE
    ========================================= */

    public function index(): void
    {
        $this->requireAdmin();

        $users = $this->userModel->getAll();
        require __DIR__ . '/../views/BackOffice/index.php';
    }

    public function create(): void
    {
        $this->requireAdmin();

        $roles = $this->userModel->getRoles();
        $errors = [];
        $old = [];

        require __DIR__ . '/../views/BackOffice/create.php';
    }

    public function store(): void
    {
        $this->requireAdmin();

        $old = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'statutCompte' => trim($_POST['statutCompte'] ?? ''),
            'idRole' => trim($_POST['idRole'] ?? '')
        ];

        $errors = $this->validateUserData($_POST, true);
        $roles = $this->userModel->getRoles();

        if (!empty($errors)) {
            require __DIR__ . '/../views/BackOffice/create.php';
            return;
        }

        $created = $this->userModel->create(
            trim($_POST['nom']),
            trim($_POST['prenom']),
            trim($_POST['email']),
            trim($_POST['motDePasse']),
            trim($_POST['telephone'] ?? ''),
            trim($_POST['adresse'] ?? ''),
            trim($_POST['statutCompte']),
            (int)$_POST['idRole']
        );

        if ($created) {
            $_SESSION['success'] = 'Utilisateur ajouté avec succès.';
            $this->redirect('index.php?action=index');
        }

        $_SESSION['error'] = 'Erreur lors de l’ajout de l’utilisateur.';
        $this->redirect('index.php?action=create');
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();

        $user = $this->userModel->getById($id);
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            $this->redirect('index.php?action=index');
        }

        $roles = $this->userModel->getRoles();
        $errors = [];
        $old = $user;

        require __DIR__ . '/../views/BackOffice/edit.php';
    }

    public function update(int $id): void
    {
        $this->requireAdmin();

        $user = $this->userModel->getById($id);
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            $this->redirect('index.php?action=index');
        }

        $old = [
            'idUser' => $id,
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'statutCompte' => trim($_POST['statutCompte'] ?? ''),
            'idRole' => trim($_POST['idRole'] ?? '')
        ];

        $errors = $this->validateUserData($_POST, false, $id);
        $roles = $this->userModel->getRoles();

        if (!empty($errors)) {
            require __DIR__ . '/../views/BackOffice/edit.php';
            return;
        }

        $updated = $this->userModel->update(
            $id,
            trim($_POST['nom']),
            trim($_POST['prenom']),
            trim($_POST['email']),
            trim($_POST['telephone'] ?? ''),
            trim($_POST['adresse'] ?? ''),
            trim($_POST['statutCompte']),
            (int)$_POST['idRole']
        );

        if ($updated && !empty($_POST['motDePasse'])) {
            $this->userModel->updatePassword($id, trim($_POST['motDePasse']));
        }

        if ($updated) {
            $_SESSION['success'] = 'Utilisateur modifié avec succès.';
            $this->redirect('index.php?action=index');
        }

        $_SESSION['error'] = 'Erreur lors de la modification.';
        $this->redirect('index.php?action=edit&id=' . $id);
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();

        if ($this->isLoggedIn() && (int)$_SESSION['user']['idUser'] === $id) {
            $_SESSION['error'] = 'Vous ne pouvez pas supprimer votre propre compte.';
            $this->redirect('index.php?action=index');
        }

        $user = $this->userModel->getById($id);
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur introuvable.';
            $this->redirect('index.php?action=index');
        }

        $deleted = $this->userModel->delete($id);

        if ($deleted) {
            $_SESSION['success'] = 'Utilisateur supprimé avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression.';
        }

        $this->redirect('index.php?action=index');
    }
private function getIdFromUrl(): ?int
{
    if (!isset($_GET['id'])) {
        return null;
    }

    $id = (int) $_GET['id'];

    return $id > 0 ? $id : null;
}
  public function show(): void
{
    $this->requireAdmin();

    $id = $this->getIdFromUrl();

    if (!$id) {
        die("ID invalide");
    }

    $user = $this->userModel->getById($id);

    if (!$user) {
        die("Utilisateur introuvable");
    }

    require __DIR__ . '/../views/BackOffice/show.php';
}


    /* =========================================
       FRONTOFFICE
    ========================================= */

    public function register(): void
    {
        $roles = $this->userModel->getRoles();
        $errors = [];
        $old = [];

        require __DIR__ . '/../views/front/user/register.php';
    }

    public function storeRegister(): void
    {
        $patientRoleId = null;
        $roles = $this->userModel->getRoles();

        foreach ($roles as $role) {
            if ($role['nomRole'] === 'Patient') {
                $patientRoleId = (int)$role['idRole'];
                break;
            }
        }

        $data = $_POST;
        $data['idRole'] = $patientRoleId;
        $data['statutCompte'] = 'actif';

        $old = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? '')
        ];

        $errors = [];

        if (!$patientRoleId) {
            $errors['general'] = 'Le rôle Patient est introuvable dans la base.';
        } else {
            $errors = $this->validateUserData($data, true);
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/front/user/register.php';
            return;
        }

        $created = $this->userModel->create(
            trim($_POST['nom']),
            trim($_POST['prenom']),
            trim($_POST['email']),
            trim($_POST['motDePasse']),
            trim($_POST['telephone'] ?? ''),
            trim($_POST['adresse'] ?? ''),
            'actif',
            $patientRoleId
        );

        if ($created) {
            $_SESSION['success'] = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
            $this->redirect('index.php?action=login');
        }

        $_SESSION['error'] = 'Erreur lors de l’inscription.';
        $this->redirect('index.php?action=register');
    }

    public function login(): void
    {
        $errors = [];
        require __DIR__ . '/../views/front/user/login.php';
    }

    public function doLogin(): void
    {
        $email = trim($_POST['email'] ?? '');
        $motDePasse = trim($_POST['motDePasse'] ?? '');
        $errors = [];

        if ($email === '') {
            $errors['email'] = 'L’email est obligatoire.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide.';
        }

        if ($motDePasse === '') {
            $errors['motDePasse'] = 'Le mot de passe est obligatoire.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/front/user/login.php';
            return;
        }

        $user = $this->userModel->login($email, $motDePasse);

        if (!$user) {
            $errors['general'] = 'Email ou mot de passe incorrect.';
            require __DIR__ . '/../views/front/user/login.php';
            return;
        }

        if ($user['statutCompte'] !== 'actif') {
            $errors['general'] = 'Votre compte n’est pas actif.';
            require __DIR__ . '/../views/front/user/login.php';
            return;
        }

        unset($user['motDePasse']);
        $_SESSION['user'] = $user;

        if ($user['nomRole'] === 'admin') {
            $this->redirect('index.php?action=index');
        } else {
            $this->redirect('index.php?action=profile');
        }
    }
    public function forgotPassword()
{
    require 'views/front/user/forgotpassword.php';
}

public function sendResetCode()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once 'models/User.php';

    $email = $_POST['email'];

    $user = User::findByEmail($email);

    if (!$user) {
        $errors['email'] = "Email introuvable";
        require 'views/front/user/forgotpassword.php';
        return;
    }

    $code = rand(100000, 999999);

    $_SESSION['reset_code'] = $code;
    $_SESSION['reset_email'] = $email;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'ademo.belkhiro@gmail.com';
        $mail->Password = 'wmmz daqg mgnd nulo';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('ademo.belkhiro@gmail.com', 'Medicare');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Code de verification';
        $mail->Body = "<h2>Votre code est : <b>$code</b></h2>";

        $mail->send();

        header("Location: index.php?action=resetPassword");
        exit;

    } catch (Exception $e) {
        $errors['general'] = "Erreur mail : " . $mail->ErrorInfo;
        require 'views/front/user/forgotpassword.php';
    }
}

public function resetPassword()
{
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $code = $_POST['code'];
        $newPassword = $_POST['password'];

        if ($code != $_SESSION['reset_code']) {
            $errors['code'] = "Code incorrect";
            require 'views/front/user/resetpassword.php';
            return;
        }

        require_once 'models/User.php';

        User::updatePasswordf(
            $_SESSION['reset_email'],
            password_hash($newPassword, PASSWORD_DEFAULT)
        );

        session_destroy();

        header("Location: index.php?action=login");
        exit;
    }

    require 'views/front/user/resetpassword.php';
}

    public function profile(): void
    {
        $this->requireLogin();

        $user = $this->userModel->getById((int)$_SESSION['user']['idUser']);
        if (!$user) {
            session_destroy();
            $this->redirect('index.php?action=login');
        }

        require __DIR__ . '/../views/front/user/profile.php';
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();

        $this->redirect('index.php?action=login');
    }
    
}