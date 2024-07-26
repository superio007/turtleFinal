<?php
require_once 'vendor/autoload.php';
require_once 'secrets/secrets.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);
header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost/TurtleDownUnder/';

// Fetch product details from session storage (assuming they are stored as session variables)
session_start();
$productTitle = isset($_SESSION['productTitle']) ? $_SESSION['productTitle'] : 'Default Product';
$productPrice = isset($_SESSION['productPrice']) ? $_SESSION['productPrice'] : '2000'; // Default price in cents ($20.00)
$productQuantity = isset($_SESSION['productQuantity']) ? $_SESSION['productQuantity'] : 1;

// Create the checkout session with the dynamic product details
$checkout_session = \Stripe\Checkout\Session::create([
  'line_items' => [[
    'price_data' => [
      'currency' => 'usd',
      'product_data' => [
        'name' => $productTitle,
      ],
      'unit_amount' => $productPrice,
    ],
    'quantity' => $productQuantity,
  ]],
  'mode' => 'payment',
  'success_url' => $YOUR_DOMAIN . '/success.html',
  'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
