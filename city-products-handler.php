<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <title>City Products | WDM&Co</title>
    <style>
        /* Include your CSS styles here */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f4;
        }

        .header {
            min-height: 50vh;
            background-image: linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url(images/banner_tdu.png);
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
            padding: 0 2rem;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #ffeb3b;
            margin-bottom: 10px;
        }

        .filter__container {
            padding: 2rem;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            margin: 2rem auto;
            width: 90%;
            max-width: 1200px;
        }

        .filter__container form {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            align-items: center;
        }

        .filter__container label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .filter__container select,
        .filter__container input[type="date"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .filter__container select:focus,
        .filter__container input[type="date"]:focus {
            border-color: #297373;
            background: #fff;
        }

        .filter__container button {
            grid-column: span 2;
            padding: 0.75rem 2rem;
            background: #297373;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            justify-self: center;
        }

        .filter__container button:hover {
            background: #145f68;
        }

        .popular__container {
            padding: 2rem;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            margin: 2rem auto;
            width: 90%;
            max-width: 1200px;
        }

        .section__header {
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
            color: #333;
        }

        .popular__grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .popular__card {
            width: calc(33.333% - 20px);
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            background: #fff;
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
            padding: 1rem;
            text-align: center;
        }

        .popular__card__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .popular__card__header h4 {
            font-size: 1.25rem;
            color: #333;
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

        .footer {
            background: #333;
            color: #fff;
            padding: 2rem 0;
            text-align: center;
        }

        .footer__container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer__col {
            flex: 1 1 200px;
            min-width: 200px;
        }

        .footer__col h4,
        .footer__col h3 {
            margin-bottom: 1rem;
        }

        .footer__col p {
            margin-bottom: 0.5rem;
        }

        .footer__bar {
            margin-top: 2rem;
            border-top: 1px solid #555;
            padding-top: 1rem;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .popular__card {
                width: 100%;
            }

            .filter__container form {
                grid-template-columns: 1fr;
            }

            .filter__container button {
                grid-column: 1;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.5rem;
            }

            .filter__container {
                padding: 1rem;
            }

            .filter__container select,
            .filter__container input[type="date"],
            .filter__container button {
                padding: 0.5rem;
            }

            .view-details-btn {
                padding: 8px 12px;
            }

            .section__header {
                font-size: 1.5rem;
            }
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

    // Organize products by city and filter by product types
    $productsByCity = [];
    $productTypes = [];
    $languages = [];
    $tags = [];
    $rates = [];

    if (!empty($productDetails['products'])) {
        foreach ($productDetails['products'] as $product) {
            $city = isset($product['locationAddress']['city']) ? $product['locationAddress']['city'] : 'Unknown City';
            $productsByCity[$city][] = $product;

            // Collect unique product types
            if (!in_array($product['productType'], $productTypes)) {
                $productTypes[] = $product['productType'];
            }

            // Collect unique languages
            if (isset($product['languages'])) {
                foreach ($product['languages'] as $language) {
                    if (!in_array($language, $languages)) {
                        $languages[] = $language;
                    }
                }
            }

            // Collect unique tags
            foreach ($product['tags'] as $tag) {
                if (!in_array($tag, $tags)) {
                    $tags[] = $tag;
                }
            }

            // Collect unique rates
            foreach ($product['priceOptions'] as $priceOption) {
                if (!in_array($priceOption['price'], $rates)) {
                    $rates[] = $priceOption['price'];
                }
            }
        }
    }

    sort($productTypes);
    sort($languages);
    sort($tags);
    sort($rates);

    $selectedCity = isset($_GET['city']) ? $_GET['city'] : '';
    $selectedProductType = isset($_GET['productType']) ? $_GET['productType'] : '';
    $selectedLanguage = isset($_GET['language']) ? $_GET['language'] : '';
    $selectedTags = isset($_GET['tags']) ? $_GET['tags'] : '';
    $selectedDate = isset($_GET['date']) ? $_GET['date'] : '';
    $selectedRate = isset($_GET['rate']) ? $_GET['rate'] : '';

    if (empty($selectedCity) || !isset($productsByCity[$selectedCity])) {
        die('No products found for the selected city.');
    }

    // Filter products based on selected filters
    $filteredProducts = array_filter($productsByCity[$selectedCity], function($product) use ($selectedProductType, $selectedLanguage, $selectedTags, $selectedDate, $selectedRate) {
        $match = true;
        if ($selectedProductType !== '' && $product['productType'] !== $selectedProductType) {
            $match = false;
        }
        if ($selectedLanguage !== '' && (!isset($product['languages']) || !in_array($selectedLanguage, $product['languages']))) {
            $match = false;
        }
        if ($selectedTags !== '' && !in_array($selectedTags, $product['tags'])) {
            $match = false;
        }
        if ($selectedDate !== '' && !empty($product['availability'])) {
            $availableDates = array_column($product['availability'], 'date');
            if (!in_array($selectedDate, $availableDates)) {
                $match = false;
            }
        }
        if ($selectedRate !== '' && $selectedRate != $product['priceOptions'][0]['price']) {
            $match = false;
        }
        return $match;
    });

    ?>

    <header class="section__container header__container">
        <div class="header__image__container">
            <div class="header__content">
                <h1>Products in <?php echo htmlspecialchars($selectedCity); ?></h1>
            </div>
        </div>
    </header>

    <section class="section__container filter__container">
        <form method="GET" action="">
            <input type="hidden" name="city" value="<?php echo htmlspecialchars($selectedCity); ?>" />
            <div class="filter__group">
                <label for="productType">Product Type:</label>
                <select name="productType" id="productType">
                    <option value="">All Types</option>
                    <?php
                    foreach ($productTypes as $type) {
                        echo "<option value=\"$type\" " . ($selectedProductType === $type ? 'selected' : '') . ">$type</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter__group">
                <label for="language">Language:</label>
                <select name="language" id="language">
                    <option value="">All Languages</option>
                    <?php
                    foreach ($languages as $language) {
                        echo "<option value=\"$language\" " . ($selectedLanguage === $language ? 'selected' : '') . ">$language</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter__group">
                <label for="tags">Tags:</label>
                <select name="tags" id="tags">
                    <option value="">All Tags</option>
                    <?php
                    foreach ($tags as $tag) {
                        echo "<option value=\"$tag\" " . ($selectedTags === $tag ? 'selected' : '') . ">$tag</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter__group">
                <label for="date">Date:</label>
                <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($selectedDate); ?>" />
            </div>
            <div class="filter__group">
                <label for="rate">Rate:</label>
                <select name="rate" id="rate">
                    <option value="">All Rates</option>
                    <?php
                    foreach ($rates as $rate) {
                        echo "<option value=\"$rate\" " . ($selectedRate == $rate ? 'selected' : '') . ">$rate</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter__group">
                <button type="submit">Apply Filters</button>
            </div>
        </form>
    </section>

    <section class="section__container popular__container">
        <h2 class="section__header">Popular Products</h2>
        <?php 
        echo displayPopularHotels($filteredProducts);
        ?>
    </section>

    <footer class="footer">
        <div class="section__container footer__container">
            <div class="footer__col">
                <h3>WDM&Co</h3>
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
