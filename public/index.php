<?php
require __DIR__ . '/../app/bootstrap.php';

$page = $_GET['page'] ?? (is_logged_in() ? (user()['role'] === 'pasien' ? 'patient-dashboard' : 'dashboard') : 'landing');
$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();
$productController = new ProductController();
$cartController = new CartController();
$orderController = new OrderController();
$midtransWebhook = new MidtransWebhookController();
$checkoutController = new CheckoutController();
$bookingController = new BookingController();
$medicineController = new MedicineController();
$recommendationController = new RecommendationController();
$fulfillmentController = new FulfillmentController();

if ($action) {
    switch ($action) {
        case 'booking-pay':
            if ($method === 'POST') {
                $bookingController->pay($_GET['code'] ?? '');
            }
            break;
        case 'booking-check-status':
            if ($method === 'POST') {
                $bookingController->checkStatus($_GET['code'] ?? '');
            }
            break;
        case 'create-fulfillment-order':
            if ($method === 'POST') {
                $fulfillmentController->create();
            }
            break;
        case 'pay-fulfillment-order':
            if ($method === 'POST') {
                $fulfillmentController->pay();
            }
            break;
        case 'check-fulfillment-status':
            if ($method === 'POST') {
                $fulfillmentController->checkStatus();
            }
            break;
        default:
            http_response_code(404);
            include __DIR__ . '/../app/Views/errors/404.php';
    }
    return;
}

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
    case 'patient-register':
        if ($method === 'POST') {
            $authController->registerPatient();
        } else {
            $authController->showPatientRegister();
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
    case 'patient-dashboard':
        (new PatientController())->dashboard();
        break;
    case 'patient-profile':
        (new PatientController())->profile();
        break;
    case 'patient-bpjs-update':
        (new PatientController())->updateBpjs();
        break;
    case 'patient-child-store':
        (new PatientController())->storeChild();
        break;
    case 'products':
        $productController->catalog();
        break;
    case 'cart':
        $cartController->index();
        break;
    case 'cart-add':
        if ($method === 'POST') {
            $cartController->add();
        }
        break;
    case 'cart-update':
        if ($method === 'POST') {
            $cartController->update();
        }
        break;
    case 'cart-remove':
        if ($method === 'POST') {
            $cartController->remove();
        }
        break;
    case 'checkout':
        $orderController->checkoutForm();
        break;
    case 'checkout-create-payment':
        if ($method === 'POST') {
            $checkoutController->createPayment();
        }
        break;
    case 'orders-checkout':
        if ($method === 'POST') {
            $orderController->processCheckout();
        }
        break;
    case 'booking-detail':
        $bookingController->show($_GET['code'] ?? '');
        break;
    case 'orders':
        $orderController->patientOrders();
        break;
    case 'order-detail':
        $orderController->orderDetail();
        break;
    case 'patient-recommendations':
        $recommendationController->patientList();
        break;
    case 'recommendation-detail':
        $recommendationController->patientDetail();
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
    case 'admin-products':
        $productController->adminIndex();
        break;
    case 'admin-products-create':
        $productController->create();
        break;
    case 'admin-products-store':
        if ($method === 'POST') {
            $productController->store();
        }
        break;
    case 'admin-products-edit':
        $productController->edit();
        break;
    case 'admin-products-update':
        if ($method === 'POST') {
            $productController->update();
        }
        break;
    case 'admin-products-delete':
        if ($method === 'POST') {
            $productController->destroy();
        }
        break;
    case 'admin-medicines':
        $medicineController->adminIndex();
        break;
    case 'admin-medicines-store':
        if ($method === 'POST') {
            $medicineController->store();
        }
        break;
    case 'admin-medicines-update':
        if ($method === 'POST') {
            $medicineController->update();
        }
        break;
    case 'admin-recommendations':
        $recommendationController->adminIndex();
        break;
    case 'admin-recommendations-create':
        $recommendationController->createForm();
        break;
    case 'admin-recommendations-store':
        if ($method === 'POST') {
            $recommendationController->store();
        }
        break;
    case 'admin-orders':
        $orderController->adminIndex();
        break;
    case 'admin-order-fulfillment':
        if ($method === 'POST') {
            $orderController->updateFulfillment();
        }
        break;
    case 'admin-sales-report':
        $orderController->salesReport();
        break;
    case 'midtrans-webhook':
        if ($method === 'POST') {
            $midtransWebhook->handle();
        }
        break;
    default:
        http_response_code(404);
        include __DIR__ . '/../app/Views/errors/404.php';
}
