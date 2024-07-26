<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
    <title>City Products | WDM&Co</title>
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
            color: #ffeb3b;
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

        .section__header {
            margin-top: 40px;
        }

        .popular__grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .popular__card {
            width: calc(33.333% - 20px);
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .popular__card:hover {
            transform: translateY(-10px);
        }

        .popular__card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .popular__content {
            padding: 20px;
        }

        .popular__card__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .view-details-btn {
            padding: 10px 15px;
            background: #ffeb3b;
            color: #000;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .view-details-btn:hover {
            background: #ffd700;
        }
    </style>
</head>
<body>
    <?php
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

    function displayPopularHotels($products) {
        $output = "<div class='popular__grid'>";
        foreach ($products as $product) {
            $productName = htmlspecialchars($product['name']);
            $productPrice = htmlspecialchars($product['priceOptions'][0]['price']);
            $productImage = isset($product['images'][0]['mediumSizeUrl']) ? htmlspecialchars($product['images'][0]['mediumSizeUrl']) : 'path/to/default-image.jpg';
            $productCode = htmlspecialchars($product['productCode']);

            $output .= "
            <div class='popular__card'>
                <img src='$productImage' alt='$productName' />
                <div class='popular__content'>
                    <div class='popular__card__header'>
                        <h4>$productName</h4>
                        <h4>\$$productPrice</h4>
                    </div>
                    <a href='productdetails.php?productCode=$productCode' class='view-details-btn'>View Details</a>
                </div>
            </div>";
        }
        $output .= "</div>";
        return $output;
    }

    $apiKey = "81c3566e60ef42e6afa1c2719e7843fd";
    $productDetails = getRezdyProducts($apiKey);

    // Organize products by city
    $productsByCity = [];
    if (!empty($productDetails['products'])) {
        foreach ($productDetails['products'] as $product) {
            $city = isset($product['locationAddress']['city']) ? $product['locationAddress']['city'] : 'Unknown City';
            $productsByCity[$city][] = $product;
        }
    }

    $selectedCity = isset($_GET['city']) ? $_GET['city'] : '';

    if (empty($selectedCity) || !isset($productsByCity[$selectedCity])) {
        die('No products found for the selected city.');
    }
    ?>

    <header class="section__container header__container">
        <div class="header__image__container">
            <div class="header__content">
                <h1>Products in <?php echo htmlspecialchars($selectedCity); ?></h1>
            </div>
        </div>
    </header>

    <section class="section__container popular__container">
        <?php 
        echo displayPopularHotels($productsByCity[$selectedCity]);
        ?>
    </section>

    <footer class="footer">
        <div class="section__container footer__container">
            <div class="footer__col">
                <h3>TurtleDownUnder</h3>
                <p>
                    WDM&Co is a premier hotel booking website that offers a seamless and
                    convenient way to find and book accommodations worldwide.
                </p>
                <p>
                    With a user-friendly interface and a vast selection of hotels,
                    WDM&Co aims to provide a stress-free experience for travelers
                    seeking the perfect stay.
                </p>
            </div>
            <div class="footer__col">
                <h4>Company</h4>
                <p>About Us</p>
                <p>Our Team</p>
                <p>Blog</p>
                <p>Book</p>
                <p>Contact Us</p>
            </div>
            <div class="footer__col">
                <h4>Legal</h4>
                <p>FAQs</p>
                <p>Terms & Conditions</p>
                <p>Privacy Policy</p>
            </div>
            <div class="footer__col">
                <h4>Resources</h4>
                <p>Social Media</p>
                <p>Help Center</p>
                <p>Partnerships</p>
            </div>
        </div>
        <div class="footer__bar">
            Copyright © 2023 Web Design Mastery. All rights reserved.
        </div>
    </footer>
</body>
</html>
