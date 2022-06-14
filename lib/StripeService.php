<?php
namespace Phppot;

use Phppot\StripePayment;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;
require_once __DIR__ . '/../vendor/autoload.php';

class StripeService
{

    private $apiKey;
    
    private $webhookSecret;

    private $stripeService;

    function __construct()
    {
        require_once __DIR__ . '/../Common/Config.php';
        $this->apiKey = Config::STRIPE_SECRET_KEY;
        $this->webhookSecret = Config::STRIPE_WEBHOOK_SECRET;
        $this->stripeService = new Stripe();
        $this->stripeService->setVerifySslCerts(false);
    }

    public function createPaymentIntent($orderReferenceId, $amount, $currency, $email, $customerDetailsArray, $notes, $metaData)
    {
        try {
            $this->stripeService->setApiKey($this->apiKey);

            $paymentIntent = \Stripe\PaymentIntent::create([
                'description' => $notes,
                'shipping' => [
                    'name' => $customerDetailsArray["name"],
                    'address' => [
                        'line1' => $customerDetailsArray["address"],
                        'postal_code' => $customerDetailsArray["postalCode"],
                        'country' => $customerDetailsArray["country"]
                    ]
                ],
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_types' => [
                    'card'
                ],
                'metadata' => $metaData
            ]);
            $output = array(
                "status" => "success",
                "response" => array(
                    'orderHash' => $orderReferenceId,
                    'clientSecret' => $paymentIntent->client_secret
                )
            );
        } catch (\Error $e) {
            $output = array(
                "status" => "error",
                "response" => $e->getMessage()
            );
        }
        return $output;
    }

    public function captureResponse()
    {
        $payload = @file_get_contents('php://input');

        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $this->webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        if (! empty($event)) {

            $eventType = $event->type;

            $orderReferenceId = $event->data->object->metadata->order_id;
            $paymentIntentId = $event->data->object->id;
            $amount = $event->data->object->amount;
            
            require_once __DIR__ . '/../lib/StripePayment.php';
            $stripePayment = new StripePayment();
            
            if ($eventType == "payment_intent.payment_failed") {
                $orderStatus = 'Payement Failure';

                $paymentStatus = 'Unpaid';

                $amount = $amount / 100;

                $stripePayment->updateOrder($paymentIntentId, $orderReferenceId, $orderStatus, $paymentStatus);
                $stripePayment->insertPaymentLog($orderReferenceId, $payload);
            }
            if ($eventType == "payment_intent.succeeded") {
                /*
                 * Json values assign to php variables
                 *
                 */
                $orderStatus = 'Completed';

                $paymentStatus = 'Paid';

                $amount = $amount / 100;

                $stripePayment->updateOrder($paymentIntentId, $orderReferenceId, $orderStatus, $paymentStatus);
                $stripePayment->insertPaymentLog($orderReferenceId, $payload);

                http_response_code(200);
            }
        }
    }
}