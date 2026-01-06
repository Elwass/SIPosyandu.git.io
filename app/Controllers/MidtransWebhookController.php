<?php
class MidtransWebhookController
{
    private Order $orders;
    private Payment $payments;

    public function __construct()
    {
        $this->orders = new Order();
        $this->payments = new Payment();
    }

    public function handle(): void
    {
        $input = file_get_contents('php://input');
        $payload = json_decode($input, true);

        if (!$payload || !isset($payload['order_id'])) {
            http_response_code(400);
            echo 'Invalid payload';
            return;
        }

        $serverKey = config('midtrans.server_key', '');
        if ($serverKey === '') {
            http_response_code(500);
            echo 'Server key missing';
            return;
        }

        $expectedSignature = hash('sha512', $payload['order_id'] . ($payload['status_code'] ?? '') . ($payload['gross_amount'] ?? '') . $serverKey);
        if (($payload['signature_key'] ?? '') !== $expectedSignature) {
            http_response_code(401);
            echo 'Invalid signature';
            return;
        }

        $localOrderId = (int)preg_replace('/^POSYANDU-/', '', $payload['order_id']);
        $order = $this->orders->find($localOrderId);
        if (!$order) {
            http_response_code(404);
            echo 'Order not found';
            return;
        }

        $mappedStatus = $this->mapStatus($payload['transaction_status']);
        $this->payments->record($localOrderId, $payload);
        $this->orders->updatePaymentStatusWithStock($localOrderId, $mappedStatus);

        echo 'OK';
    }

    private function mapStatus(string $midtransStatus): string
    {
        switch ($midtransStatus) {
            case 'settlement':
            case 'capture':
                return 'PAID';
            case 'pending':
                return 'UNPAID';
            case 'expire':
                return 'EXPIRED';
            case 'cancel':
            case 'deny':
                return 'CANCELLED';
            case 'refund':
            case 'chargeback':
                return 'REFUNDED';
            default:
                return 'UNPAID';
        }
    }
}
