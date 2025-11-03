<?php
require __DIR__ . '/../app/bootstrap.php';

$page = $_GET['page'] ?? (is_logged_in() ? 'dashboard' : 'landing');
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();

switch ($page) {
    case 'landing':
        include __DIR__ . '/../app/Views/landing.php';
        break;
    case 'login':
        if ($method === 'POST') {
            $authController->login();
        } else {
            $authController->showLogin();
        }
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'dashboard':
        if (!is_logged_in()) {
            redirect('?page=login');
        }
        (new DashboardController())->index();
        break;
    case 'residents':
        if (!is_logged_in()) {
            redirect('?page=login');
        }
        (new ResidentController())->index();
        break;
    case 'residents-create':
        (new ResidentController())->create();
        break;
    case 'residents-store':
        (new ResidentController())->store();
        break;
    case 'residents-edit':
        (new ResidentController())->edit();
        break;
    case 'residents-update':
        (new ResidentController())->update();
        break;
    case 'residents-delete':
        (new ResidentController())->destroy();
        break;
    case 'measurements':
        (new MeasurementController())->index();
        break;
    case 'measurements-store':
        (new MeasurementController())->store();
        break;
    case 'immunizations':
        (new ImmunizationController())->index();
        break;
    case 'immunizations-store':
        (new ImmunizationController())->store();
        break;
    case 'immunizations-complete':
        (new ImmunizationController())->markCompleted();
        break;
    case 'reminders':
        (new ReminderController())->index();
        break;
    case 'reminders-store':
        (new ReminderController())->store();
        break;
    case 'reminders-sent':
        (new ReminderController())->markSent();
        break;
    case 'reports':
        (new ReportController())->index();
        break;
    case 'reports-download':
        (new ReportController())->download();
        break;
    case 'users':
        (new UserController())->index();
        break;
    case 'users-store':
        (new UserController())->store();
        break;
    default:
        http_response_code(404);
        include __DIR__ . '/../app/Views/errors/404.php';
}
