<?php
require_once __DIR__ . '/../Common/Config.php';

$content = trim(file_get_contents("php://input"));

$jsondecoded = json_decode($content, true);
$country = filter_var($jsondecoded["adress"]["country"], FILTER_SANITIZE_STRING);
if ($country == 'UK') {
    $shippingAmount = Config::UK_SHIPPING;
} else {
    $shippingAmount = Config::US_SHIPPING;
}

$shippingOptions = array(
    "shippingOptions" => array(
        array(
            "id" => 'Edited shipping',
            'label' => "Shipping Costs based on Country",
            'detail' => $detail,
            'amount' => $shippingAmount
        )
    )
);

echo json_encode($shippingOptions);
exit();

?>