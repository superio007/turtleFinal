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

// Function to fetch all products in parallel
function fetchAllProductsParallel($apiKey) {
    $allProducts = [];
    $mh = curl_multi_init();

    $handles = [];

    // Initialize multiple cURL handles
    for ($i = 0; $i < 40; $i++) {
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
    $stmtSelect = $conn->prepare("SELECT COUNT(*) FROM products WHERE productCode = ?");
    $stmtInsert = $conn->prepare("INSERT INTO products (name, productCode, shortDescription, productType, itemUrl) VALUES (?, ?, ?, ?, ?)");

    foreach ($products as $product) {
        $name = $product['name'];
        $productCode = $product['productCode'];
        $shortDescription = $product['shortDescription'];
        $productType = $product['productType'];
        $itemUrl = !empty($product['images']) ? $product['images'][0]['itemUrl'] : '';

        // Check if the product already exists in the database
        $stmtSelect->bind_param("s", $productCode);
        $stmtSelect->execute();
        $stmtSelect->bind_result($count);
        $stmtSelect->fetch();

        if ($count == 0) {
            // Insert the new product if it doesn't exist
            $stmtInsert->bind_param("sssss", $name, $productCode, $shortDescription, $productType, $itemUrl);
            $stmtInsert->execute();
        }
    }

    $stmtSelect->close();
    $stmtInsert->close();
}

// Fetch products and insert into database
$apiKey = 'b5a46c6c39624b908c2aef115af33942';
$products = fetchAllProductsParallel($apiKey);
insertProductsIntoDB($products, $conn);

// Close the database connection
$conn->close();

echo "Products inserted successfully.";
?>
