<?php
 include 'header.php';
function getRezdyProducts($apiKey) {
    $url = "https://api.rezdy-staging.com/v1/products/marketplace?apiKey=$apiKey";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        die("Error: Curl request failed: " . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    
    if ($data === null) {
        die("Error: Failed to decode JSON response");
    }
    return $data;
}

$apiKey = "81c3566e60ef42e6afa1c2719e7843fd";

$productType = $_GET['productType'] ?? '';

// Check if productType is provided
if (empty($productType)) {
    die("Error: Product type must be provided.");
}

$product = getRezdyProducts($apiKey);

// Filter products by productType
$availableSessions = array_filter($product['products'], function($session) use ($productType) {
    return isset($session['productType']) && $session['productType'] === $productType;
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rezdy API - Product Results</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        /* Include your CSS styles here */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .header {
            min-height: 100vh;
            background-image: linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url(images/banner_tdu.png);
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .container {
            padding: 0 10%;
        }

        .header h1 {
            font-size: 4vw;
            font-weight: 500;
            color: #ffeb3b; /* Changed the color to yellow for better visibility */
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar {
            background:#B7D3F2;
            width: 45%;
            margin: 0 auto;
            padding: 10px 30px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-bar form input, .search-bar form select {
            border: 0;
            outline: none;
            background: transparent;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            flex: 1;
            min-width: 100px;
        }

        .search-bar form button {
            padding: 12px;
            background: #177E89;
            border-radius: 42px;
            border: 0;
            outline: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-bar form button img {
            width: 20px;
        }

        .location-input {
            flex: 2;
        }

        .search-bar form label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .cal, .guest {
            padding-left: 10px;
            flex: 1;
        }

        /* Center align the form items */
        .form-section {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .form-section form {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: row;
        }

        .form-section .form__group {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-section .input__group {
            margin-right: 10px;
        }

        /* Improve button styling */
        .form-section .btn {
            padding: 10px 20px;
            background: #ffeb3b; /* Yellow color for better visibility */
            color: #000;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        .form-section .btn:hover {
            background: #ffd700; /* Darker yellow on hover */
        }

        .form-section .btn i {
            margin-left: 5px;
        }

        /* Enhanced form styling */
        .form__group {
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 600px;
        }

        .input__group {
            flex: 1;
        }

        .input__group select {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 2px solid #ddd;
            background: #f9f9f9;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .input__group select:focus {
            border-color: #177E89;
            background: #fff;
        }

        .btn {
            padding: 12px 20px;
            background: #177E89;
            color: #fff;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn:hover {
            background: #145f68;
        }

        .btn i {
            margin-left: 10px;
        }

        /* Custom styles for product results */
        .product-results .card {
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .product-results .card img {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .product-results .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .product-results .card-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .product-results .card-text {
            margin-bottom: 10px;
        }

        .product-results .card-text.bg-success {
            background-color: #177E89 !important;
        }

        .product-results .view-details-btn {
            background: #ffeb3b;
            color: #000;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .product-results .view-details-btn:hover {
            background: #ffd700;
        }
    </style>
</head>

<body class="bg-light">
  

    <div class="container mt-5">
        <h1 class="text-center text-primary mb-5" style="margin-top: 7rem;">SELECT A JOURNEY</h1>
        <?php if (!empty($availableSessions)): ?>
        <div class="row product-results">
            <?php foreach($availableSessions as $session): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if (!empty($session['images'][0]['thumbnailUrl'])): ?>
                    <img src="<?php echo htmlspecialchars($session['images'][0]['thumbnailUrl']); ?>"
                        class="card-img-top" alt="<?php echo htmlspecialchars($session['name']); ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($session['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($session['shortDescription']); ?></p>
                        <p class="card-text"><strong>Product Code:</strong> <?php echo htmlspecialchars($session['productCode']); ?></p>
                        <p class="card-text bg-success text-white p-2 rounded">
                            <strong>Advertised Price:</strong>
                            $<?php echo isset($session['advertisedPrice']) ? htmlspecialchars($session['advertisedPrice']) : 'N/A'; ?>
                            <?php echo isset($session['currency']) ? htmlspecialchars($session['currency']) : ''; ?>
                        </p>
                        <p class="card-text"><strong>Price Options:</strong></p>
                        <ul class="list-unstyled">
                            <?php foreach ($session['priceOptions'] as $priceOption): ?>
                            <li><?php echo htmlspecialchars($priceOption['label']); ?>: $<?php echo htmlspecialchars($priceOption['price']); ?></li>
                            <?php break; ?>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-auto">
                            <button type="button" class="btn btn-primary view-details-btn" data-product-code="<?php echo htmlspecialchars($session['productCode']); ?>">View Details</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <h2 class="text-center text-danger">Packages are not available</h2>
        <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".view-details-btn").forEach(button => {
            button.addEventListener("click", function() {
                const productCode = this.getAttribute("data-product-code");
                window.location.href = `productDetails.php?productCode=${productCode}`;
            });
        });
    });
    </script>
</body>

</html>
