<?php
namespace Phppot;

use Phppot\DataSource;

class StripePayment
{

    private $ds;

    function __construct()
    {
        require_once __DIR__ . "/../lib/DataSource.php";
        $this->ds = new DataSource();
    }

    public function insertOrder($orderReferenceId, $unitAmount, $currency, $orderStatus, $name, $email)
    {
        $orderAt = date("Y-m-d H:i:s");

        $insertQuery = "INSERT INTO tbl_order(order_reference_id, amount, currency, order_at, order_status, billing_name, billing_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ";

        $paramValue = array(
            $orderReferenceId,
            $unitAmount,
            $currency,
            $orderAt,
            $orderStatus,
            $name,
            $email
        );

        $paramType = "sdssssss";
        $insertId = $this->ds->insert($insertQuery, $paramType, $paramValue);
        return $insertId;
    }

    public function updateOrder($paymentIntentId, $orderReferenceId, $orderStatus, $paymentStatus)
    {
        $query = "UPDATE tbl_order SET stripe_payment_intent_id = ?, order_status = ?, payment_status = ? WHERE order_reference_id = ?";

        $paramValue = array(
            $paymentIntentId,
            $orderStatus,
            $paymentStatus,
            $orderReferenceId
        );

        $paramType = "ssss";
        $this->ds->execute($query, $paramType, $paramValue);
    }

    public function insertPaymentLog($orderReferenceId, $response)
    {
        $insertQuery = "INSERT INTO tbl_stripe_payment_log(order_id, stripe_payment_response) VALUES (?, ?) ";

        $paramValue = array(
            $orderReferenceId,
            $response
        );

        $paramType = "ss";
        $this->ds->insert($insertQuery, $paramType, $paramValue);
    }
}
?>