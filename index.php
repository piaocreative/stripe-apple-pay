<?php
namespace Phppot;

require_once __DIR__ . '/Common/Config.php';
?>
<html>
<title>Stripe Apple Pay integration</title>
<head>
<link href="assets/css/style.css" type="text/css" rel="stylesheet" />
</head>
<body>
	<div class="phppot-container">
		<h1>Stripe Apple Pay integration</h1>
		<div id="payment-box" data-pk="<?php echo Config::STRIPE_PUBLISHIABLE_KEY; ?>" data-return-url="<?php echo Config::RETURN_URL; ?>">
			<input type="hidden" id="unit-price" value="<?php echo Config::PRODUCT_PRICE; ?>" />
			<input type="hidden" id="product-label" value="<?php echo Config::PRODUCT_NAME; ?>" />
			<input type="hidden" id="currency" value="<?php echo Config::CURRENCY; ?>" />
			<input type="hidden" id="shipping-amount" value="<?php echo Config::US_SHIPPING; ?>" />
			<img src="<?php echo Config::PRODUCT_IMAGE; ?>" />
			<h4 class="txt-title"><?php echo Config::PRODUCT_NAME; ?></h4>
			<div class="txt-price">$<?php echo Config::PRODUCT_PRICE; ?></div>
		</div>
		
		<!-- Element target to render Stripe apple pay button -->
		<div id="payment-request-button">
			<!-- A Stripe Element will be inserted here. -->
		</div>
	</div>
	<script src="https://js.stripe.com/v3/"></script>

	<script src="assets/js/payment.js"></script>
</body>
</html>