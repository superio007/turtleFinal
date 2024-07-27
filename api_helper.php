<?php 
// Inside api_helper.php
function getRezdyProducts($apiKey, $offset) {
    $limit = 100;
    $url = "https://api.rezdy.com/v1/products/marketplace?apiKey=$apiKey&limit=$limit&offset=$offset";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (!$response) {
        return ['error' => curl_error($ch)];
    }
    curl_close($ch);
    return json_decode($response, true);
}
?>