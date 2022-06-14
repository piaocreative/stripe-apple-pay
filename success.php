<?php
namespace Phppot;

require_once __DIR__ . '/Common/Config.php';
?>
<html>
<head>
<title>Payment Response</title>
<link href="./css/style.css" type="text/css" rel="stylesheet" />
</head>
<body>
    <div class="phppot-container">
        <h1>Thank you for shopping with us.</h1>
        <p>You have purchased "<?php echo Config::PRODUCT_NAME; ?>" successfully.</p>
        <p>You have been notified about the payment status of your
            purchase shortly.</p>
    </div>
</body>
</html>