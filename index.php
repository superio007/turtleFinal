<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
            text-align: center;
        }

        .container {
            padding: 0 5%;
        }

        .header h1 {
            font-size: 8vw;
            font-weight: 500;
            color: #ffeb3b; /* Changed the color to yellow for better visibility */
            margin-bottom: 20px;
        }

        .search-bar {
            background: #B7D3F2;
            width: 90%;
            max-width: 500px; /* Reduced max-width */
            margin: 0 auto;
            padding: 10px 30px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex-direction: column;
        }

        .search-bar form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .search-bar form input, .search-bar form select {
            border: 0;
            outline: none;
            background: transparent;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        .search-bar form button {
            padding: 8px 12px; /* Reduced padding */
            background: #177E89;
            border-radius: 42px;
            border: 0;
            outline: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px; /* Smaller font size */
        }

        .search-bar form button img {
            width: 20px;
        }

        .location-input {
            width: 100%;
        }

        .search-bar form label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .cal, .guest {
            width: 100%;
        }

        /* Center align the form items */
        .form-section {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
            max-width: 500px; /* Reduced max-width */
            margin: 0 auto; /* Center the booking container */
        }

        .form-section form {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
        }

        .form-section .form__group {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
            max-width: 350px; /* Reduced max-width */
            margin: 0 auto; /* Center the form group */
        }

        .form-section .input__group {
            width: 100%;
            margin-bottom: 10px;
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
            width: 100%;
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
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 350px; /* Reduced max-width */
            margin: 0 auto; /* Center the form group */
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
            border-color: #297373;
            background: #fff;
        }

        .section__header {
            margin-top: 40px;
            font-size: 6vw;
            text-align: center;
        }

        .popular__grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .popular__card, .city__card {
            width: 90%;
            max-width: 300px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .popular__card:hover, .city__card:hover {
            transform: translateY(-10px);
        }

        .popular__card img, .city__card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .popular__content, .city__content {
            padding: 20px;
            text-align: center;
        }

        .popular__card__header, .city__card__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .back__btn {
            display: none;
            margin: 20px 0;
            padding: 10px 20px;
            background: #ffeb3b;
            color: #000;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .back__btn:hover {
            background: #ffd700;
        }

        @media(min-width: 768px) {
            .form-section form {
                flex-direction: row;
                justify-content: space-between;
            }

            .form-section .form__group {
                flex-direction: row;
            }

            .search-bar {
                flex-direction: row;
            }

            .search-bar form {
                flex-direction: row;
                align-items: center;
            }

            .search-bar form input, .search-bar form select {
                margin-right: 10px;
            }

            .search-bar form button {
                margin-left: 10px;
                width: auto;
            }

            .section__header {
                font-size: 4vw;
            }

            .popular__grid {
                justify-content: space-between;
            }

            .popular__card, .city__card {
                width: calc(33.333% - 20px);
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

    function displayCityCards($productsByCity) {
        $output = '<div class="popular__grid">';
        $validCities = ['Sydney', 'Melbourne', 'Byron Bay', 'Brisbane', 'Adelaide', 'Perth'];
        foreach ($productsByCity as $city => $products) {
            if (in_array($city, $validCities)) {
                // Use the first product's image as the city's image card
                $productImage = isset($products[0]['images'][0]['mediumSizeUrl']) ? htmlspecialchars($products[0]['images'][0]['mediumSizeUrl']) : 'path/to/default-image.jpg';
                $output .= "
                <div class='city__card' data-city='$city'>
                    <img src='$productImage' alt='$city' />
                    <div class='city__content'>
                        <h4>$city</h4>
                    </div>
                </div>";
            }
        }
        $output .= '</div>';
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

    // Collect unique city names
    $cityNames = array_keys($productsByCity);

    $selectedCity = isset($_GET['city']) ? $_GET['city'] : '';

    if (!empty($selectedCity) && isset($productsByCity[$selectedCity])) {
        $filteredProducts = $productsByCity[$selectedCity];
    } else {
        $filteredProducts = [];
    }
    ?>

    <header class="section__container header__container">
        <div class="header__image__container">
            <div class="header__content">
                <h1>Enjoy Your Dream Vacation</h1>
                <p>Book Hotels, Flights and stay packages at lowest price.</p>
            </div>
            <div class="booking__container form-section">
                <form action="city-products-handler.php" method="GET">
                    <div class="form__group">
                        <div class="input__group">
                            <input type="text" id="city" name="city" class="form-control" placeholder="Enter City" required autocomplete="off">
                        </div>
                        <div class="input__group">
                            <input type="text" id="datepicker" name="date" class="form-control" placeholder="Select Date" required autocomplete="off">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-small"><i class="ri-search-line"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <section class="section__container popular__container" id="city-section">
        <h2 class="section__header">Discover Australia's Best Cities: Adventure Awaits!</h2>
        <?php 
        if (!empty($productsByCity)) {
            echo displayCityCards($productsByCity);
        } else {
            echo "<p>No popular hotels available at the moment.</p>";
        }
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            var availableCities = <?php echo json_encode($cityNames); ?>;
            $("#city").autocomplete({
                source: availableCities
            });

            $("#datepicker").datepicker({
                dateFormat: "yy-mm-dd",
                minDate: 0
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const cityCards = document.querySelectorAll('.city__card');
            cityCards.forEach(card => {
                card.addEventListener('click', () => {
                    const city = card.getAttribute('data-city');
                    window.location.href = 'city-products-handler.php?city=' + encodeURIComponent(city);
                });
            });
        });
    </script>
</body>
</html>
