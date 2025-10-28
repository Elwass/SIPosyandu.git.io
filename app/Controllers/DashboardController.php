<?php
class DashboardController
{
    private Analytics $analytics;
    private Immunization $immunizations;

    public function __construct()
    {
        $this->analytics = new Analytics();
        $this->immunizations = new Immunization();
    }

    public function index(): void
    {
        require_role(['super_admin', 'admin', 'midwife', 'kader']);

        $residentCounts = $this->analytics->residentCounts();
        $measurementSummary = $this->analytics->measurementSummary();
        $recentMeasurements = $this->analytics->recentMeasurements();
        $upcomingImmunizations = $this->immunizations->upcoming();

        include __DIR__ . '/../Views/dashboard/index.php';
    }
}
