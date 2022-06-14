<?php
namespace Phppot;

class Config
{

    const ROOT_PATH = "http://localhost:8080/stripe-apple-pay/";

    /* Stripe API test keys */
    const STRIPE_PUBLISHIABLE_KEY = "pk_test_51JRk8NDVZh2Lq3ZogcoKAvg8G9jLOqUPY1xKWxq9wX1arpW102ru0ezBvA52CkFjBpxK4pZGEn8wbjP3QGIzk1Ue000Lidpbvr";

    const STRIPE_SECRET_KEY = "sk_test_51JRk8NDVZh2Lq3ZoaakDDbVic9C4E2mWOhhsPWiHKe3CtW44BYSxtJUtVFxvgzAGd0qTLp8FS96XeP7kxXFlNtZs00dCleGkpj";

    const STRIPE_WEBHOOK_SECRET = "";

    const RETURN_URL = Config::ROOT_PATH . "/success.php";

    /* PRODUCT CONFIGURATIONS BEGINS */
    const PRODUCT_NAME = 'A6900 MirrorLess Camera';

    const PRODUCT_IMAGE = Config::ROOT_PATH . '/images/camera.jpg';

    const PRODUCT_PRICE = '289.61';

    const CURRENCY = 'usd';

    const US_SHIPPING = 7;

    const UK_SHIPPING = 12;
}
