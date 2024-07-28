<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch existing product codes from the database
function fetchExistingProductCodes($conn) {
    $existingProductCodes = [];
    $result = $conn->query("SELECT productCode FROM products");
    while ($row = $result->fetch_assoc()) {
        $existingProductCodes[] = $row['productCode'];
    }
    return $existingProductCodes;
}

// Function to fetch all products in parallel
function fetchAllProductsParallel($apiKey) {
    $allProducts = [];
    $mh = curl_multi_init();

    $handles = [];

    // Initialize multiple cURL handles
    for ($i = 500; $i < 400; $i++) {
        $offset = $i * 100;  // Calculate offset for each iteration
        $url = "https://api.rezdy.com/v1/products/marketplace?apiKey={$apiKey}&offset={$offset}&limit=100";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_multi_add_handle($mh, $ch);
        $handles[] = $ch;
    }

    // Execute the handles
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    // Extract the content and remove the handles
    foreach ($handles as $ch) {
        $response = curl_multi_getcontent($ch);
        $data = json_decode($response, true);
        if (isset($data['products']) && !empty($data['products'])) {
            $allProducts = array_merge($allProducts, $data['products']);
        }
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);

    return $allProducts;
}

// Function to insert product data into database
function insertProductsIntoDB($products, $conn) {
    $stmtInsert = $conn->prepare("INSERT INTO products (name, productCode, shortDescription, productType, itemUrl, city, advertisedPrice, currency) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($products as $product) {
        $name = $product['name'];
        $productCode = $product['productCode'];
        $shortDescription = $product['shortDescription'];
        $productType = $product['productType'];
        $itemUrl = !empty($product['images']) ? $product['images'][0]['itemUrl'] : '';
        $city = $product['locationAddress']['city'] ?? 'All';  // Use null coalescing operator to handle missing city
        $advertisedPrice = $product['advertisedPrice'] ?? 0.0;  // Default to 0.0 if missing
        $currency = $product['currency'] ?? '';  // Use null coalescing operator to handle missing currency

        // Insert the new product
        $stmtInsert->bind_param("ssssssds", $name, $productCode, $shortDescription, $productType, $itemUrl, $city, $advertisedPrice, $currency);
        $stmtInsert->execute();
    }

    $stmtInsert->close();
}

// Fetch existing product codes from the database
$existingProductCodes = fetchExistingProductCodes($conn);

// Fetch products from the API
$apiKey = 'b5a46c6c39624b908c2aef115af33942';
$products = fetchAllProductsParallel($apiKey);

// Filter out products with existing product codes
$newProducts = array_filter($products, function($product) use ($existingProductCodes) {
    return !in_array($product['productCode'], $existingProductCodes);
});

// Insert only new products into the database
insertProductsIntoDB($newProducts, $conn);

// Close the database connection
$conn->close();

echo "New products inserted successfully.";
?>
