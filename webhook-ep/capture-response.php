<?php
namespace Phppot;

use Phppot\StriService;

require_once __DIR__ . "/../lib/StripeService.php";

$stripeService = new StripeService();

$stripeService->captureResponse();

?>