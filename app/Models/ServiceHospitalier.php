<?php

require_once __DIR__ . '/Database.php';

class ServiceHospitalier
{
    private int $idService;
    private string $nomService;

    private const DEFAULT_SERVICES = [
        'Urgences',
        'Pédiatrie',
        'Cardiologie',
        'Maternité',
    ];

    public function __construct(int $idService, string $nomService)
    {
        $this->idService = $idService;
        $this->nomService = $nomService;
    }

    public function getIdService(): int
    {
        return $this->idService;
    }

    public function getNomService(): string
    {
        return $this->nomService;
    }

    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT idService, nomService FROM service_hospitalier ORDER BY nomService');
        $services = [];

        while ($row = $stmt->fetch()) {
            $services[] = new self((int)$row['idService'], $row['nomService']);
        }

        if (empty($services)) {
            self::seedDefaultServices();
            return self::findAll();
        }

        return $services;
    }

    private static function seedDefaultServices(): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO service_hospitalier (nomService) VALUES (:nomService)');

        foreach (self::DEFAULT_SERVICES as $serviceName) {
            $stmt->execute(['nomService' => $serviceName]);
        }
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT idService, nomService FROM service_hospitalier WHERE idService = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new self((int)$row['idService'], $row['nomService']);
    }

    public function __toString(): string
    {
        return $this->nomService;
    }
}
