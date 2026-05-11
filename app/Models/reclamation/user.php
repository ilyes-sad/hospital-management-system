<?php

require_once ROOT_PATH . 'config/database.php';

class User
{
    private PDO $conn;
    private string $table = 'users';

    public function __construct()
    {
        $this->conn = Database::getInstance();
        $this->ensureRolesTableExists();
    }
    
    private function ensureRolesTableExists(): void
    {
        try {
            $this->conn->query("SELECT 1 FROM roles LIMIT 1");
        } catch (PDOException $e) {
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS roles (
                    idRole INT AUTO_INCREMENT PRIMARY KEY,
                    nomRole VARCHAR(50) NOT NULL UNIQUE
                )
            ");
            $this->conn->exec("INSERT INTO roles (nomRole) VALUES ('admin'), ('Patient'), ('Medecin')");
        }
    }
    

    public function getAll(): array
    {
        $sql = "SELECT u.idUser, u.nom, u.prenom, u.email, u.telephone, u.adresse, 
                       u.statutCompte, u.idRole, r.nomRole
                FROM {$this->table} u
                INNER JOIN role r ON u.idRole = r.idRole
                ORDER BY u.idUser DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    

    public function getByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
public function getById(int $id): ?array
{
    $sql = "SELECT u.*, r.nomRole
            FROM users u
            INNER JOIN role r ON u.idRole = r.idRole
            WHERE u.idUser = :id
            LIMIT 1";

    $stmt = $this->conn->prepare($sql);

    // IMPORTANT : bind + execute séparé
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

    public function create(
        string $nom,
        string $prenom,
        string $email,
        string $motDePasse,
        ?string $telephone,
        ?string $adresse,
        string $statutCompte,
        int $idRole
    ): bool {
        $sql = "INSERT INTO {$this->table}
                    (nom, prenom, email, motDePasse, telephone, adresse, statutCompte, idRole)
                VALUES
                    (:nom, :prenom, :email, :motDePasse, :telephone, :adresse, :statutCompte, :idRole)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':motDePasse' => password_hash($motDePasse, PASSWORD_DEFAULT),
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':statutCompte' => $statutCompte,
            ':idRole' => $idRole
        ]);
    }

    public function update(
        int $id,
        string $nom,
        string $prenom,
        string $email,
        ?string $telephone,
        ?string $adresse,
        string $statutCompte,
        int $idRole
    ): bool {
        $sql = "UPDATE {$this->table}
                SET nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    telephone = :telephone,
                    adresse = :adresse,
                    statutCompte = :statutCompte,
                    idRole = :idRole
                WHERE idUser = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':adresse' => $adresse,
            ':statutCompte' => $statutCompte,
            ':idRole' => $idRole
        ]);
    }
    public function deleteRememberTokens($userId): bool
{
    $stmt = $this->conn->prepare("
        DELETE FROM remember_tokens WHERE user_id = ?
    ");
    return $stmt->execute([$userId]);
}
public function saveRememberToken($userId, $selector, $tokenHash, $expires): bool
{
    $stmt = $this->conn->prepare("
        INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at)
        VALUES (?, ?, ?, ?)
    ");

    return $stmt->execute([
        $userId,
        $selector,
        $tokenHash,
        $expires
    ]);
}
public function updateProfile($id, $nom, $prenom, $email, $telephone): bool
{
    $stmt = $this->conn->prepare("
        UPDATE users
        SET nom = ?, prenom = ?, email = ?, telephone = ?
        WHERE idUser = ?
    ");

    return $stmt->execute([
        $nom,
        $prenom,
        $email,
        $telephone,
        $id
    ]);
}
public static function findByEmail($email)
{
    $db = Database::getInstance();

    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";

    $query = $db->prepare($sql);
    $query->execute([
        'email' => $email
    ]);

    return $query->fetch(PDO::FETCH_ASSOC);
}
        public static function updatePasswordf($email, $password)
{
    $db = Database::getInstance();

    $sql = "UPDATE users SET motDePasse = :password WHERE email = :email";

    $query = $db->prepare($sql);
    $query->execute([
        'password' => $password,
        'email' => $email
    ]);
}

    public function updatePassword(int $id, string $motDePasse): bool
    {
        $sql = "UPDATE {$this->table}
                SET motDePasse = :motDePasse
                WHERE idUser = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':motDePasse' => password_hash($motDePasse, PASSWORD_DEFAULT)
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE idUser = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id
        ]);
    }

    public function login(string $email, string $motDePasse): array|false
    {
        $sql = "SELECT u.idUser, u.nom, u.prenom, u.email, u.motDePasse, 
                       u.statutCompte, u.idRole, r.nomRole
                FROM {$this->table} u
                LEFT JOIN roles r ON u.idRole = r.idRole
                WHERE u.email = :email
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            error_log("User not found for email: $email");
            return false;
        }
        
        error_log("User found, checking password...");
        error_log("Stored hash: " . $user['motDePasse']);

        if (!password_verify($motDePasse, $user['motDePasse'])) {
            return false;
        }

        return $user;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        // Debug
        file_put_contents(__DIR__ . '/../../email_debug.log', "Checking email: $email, excludeId: $excludeId\n", FILE_APPEND);
        
        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*) FROM {$this->table}
                    WHERE email = :email AND idUser != :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        } else {
            $sql = "SELECT COUNT(*) FROM {$this->table}
                    WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        }

        $stmt->execute();
        $result = (int) $stmt->fetchColumn();
        file_put_contents(__DIR__ . '/../../email_debug.log', "Result: $result\n", FILE_APPEND);
        return $result > 0;
    }

    public function getRoles(): array
    {
        $sql = "SELECT idRole, nomRole FROM role ORDER BY nomRole ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getOnlineUsers(): array
{
    $stmt = $this->conn->prepare("
        SELECT idUser, nom, prenom, email, last_activity
        FROM users
        WHERE last_activity >= NOW() - INTERVAL 5 MINUTE
        ORDER BY last_activity DESC
    ");

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
