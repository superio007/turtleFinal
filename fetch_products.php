<?php
header('Content-Type: application/json');
session_start();
include 'api_helper.php';  // Assuming your product fetching logic is here

$apiKey = "b5a46c6c39624b908c2aef115af33942";
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$productDetails = getRezdyProducts($apiKey, $offset);

echo json_encode($productDetails);
