<?php
// ============================================================
//  app/Controllers/DashboardController.php
// ============================================================

class DashboardController extends Controller
{
    private RendezVous $rdvModel;
    private Patient $patientModel;
    private Medecin $medecinModel;
    private Hopital $hopitalModel;

    public function __construct()
    {
        $this->rdvModel     = new RendezVous();
        $this->patientModel = new Patient();
        $this->medecinModel = new Medecin();
        $this->hopitalModel = new Hopital();
    }

    public function index(): void
    {
        $stats    = $this->rdvModel->getStats();
        $recent   = $this->rdvModel->getRecent(5);
        $patients = $this->patientModel->findAll();
        $medecins = $this->medecinModel->findAllWithHopital();
        $hopitaux = $this->hopitalModel->findAllWithStats();

        // Count unique specialties
        $specialtyCounts = [];
        foreach ($medecins as $m) {
            $spec = $m['specialite'];
            $specialtyCounts[$spec] = ($specialtyCounts[$spec] ?? 0) + 1;
        }
        arsort($specialtyCounts);

        $this->layout('main', 'dashboard/index', [
            'pageTitle'       => 'Tableau de bord',
            'pageSub'         => "Vue d'ensemble du systeme",
            'activeModule'    => 'dashboard',
            'stats'           => $stats,
            'recent'          => $recent,
            'patientCount'    => count($patients),
            'medecinCount'    => count($medecins),
            'hopitalCount'    => count($hopitaux),
            'specialtyCounts' => $specialtyCounts,
            'regionCount'     => count(array_unique(array_column($hopitaux, 'region'))),
        ]);
    }
}
