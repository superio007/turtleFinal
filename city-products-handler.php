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
        /* Your CSS styles here */
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

        .filter__container select, #city,
        .filter__container input[type="date"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .filter__container select:focus, #city,
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

        .pagination {
            display: flex;
            justify-content: center;
            padding: 1rem 0;
        }

        .pagination a {
            margin: 0 5px;
            padding: 0.5rem 0.75rem;
            background: #297373;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .pagination a:hover {
            background: #145f68;
        }

        .pagination strong {
            margin: 0 5px;
            padding: 0.5rem 0.75rem;
            background: #ffeb3b;
            color: #000;
            border-radius: 5px;
        }

        @media screen and (max-width: 768px) {
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

        @media screen and (max-width: 480px) {
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            function fetchCitySuggestions(country) {
                $("#city").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "fetch_cities.php",
                            type: "GET",
                            dataType: "json",
                            data: {
                                term: request.term,
                                country: country
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    minLength: 1
                });
            }

            var countryFilter = $("#country").val();
            fetchCitySuggestions(countryFilter);

            $("#country").on("change", function() {
                countryFilter = $(this).val();
                fetchCitySuggestions(countryFilter);
            });
        });
    </script>
</head>
<body>
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

    function getRezdyProducts($conn) {
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $products = [];
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            return $products;
        } else {
            return [];
        }
    }

    function displayPopularHotels($products, $page, $perPage) {
        $totalProducts = count($products);
        $totalPages = ceil($totalProducts / $perPage);
        $start = ($page - 1) * $perPage;
        $products = array_slice($products, $start, $perPage);
    
        $output = "<div class='popular__grid'>";
        foreach ($products as $product) {
            $productName = htmlspecialchars($product['name']);
            $productPrice = htmlspecialchars($product['advertisedPrice']);
            $productImage = isset($product['itemUrl']) ? htmlspecialchars($product['itemUrl']) : 'path/to/default-image.jpg';
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
    
        // Preserve existing query parameters
        parse_str($_SERVER['QUERY_STRING'], $queryParams);
        unset($queryParams['page']);
    
        // Pagination controls
        $output .= "<div class='pagination'>";
        $startPage = max(1, $page - 5);
        $endPage = min($totalPages, $page + 4);
    
        if ($page > 1) {
            $queryParams['page'] = $page - 1;
            $queryString = http_build_query($queryParams);
            $output .= "<a href='?$queryString'>Back</a> ";
        }
    
        for ($i = $startPage; $i <= $endPage; $i++) {
            $queryParams['page'] = $i;
            $queryString = http_build_query($queryParams);
            if ($i == $page) {
                $output .= "<strong>$i</strong> ";
            } else {
                $output .= "<a href='?$queryString'>$i</a> ";
            }
        }
    
        if ($page < $totalPages) {
            $queryParams['page'] = $page + 1;
            $queryString = http_build_query($queryParams);
            $output .= "<a href='?$queryString'>Next</a>";
        }
        $output .= "</div>";
    
        return $output;
    }

    $products = getRezdyProducts($conn);

    // Pagination
    $perPage = 6;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Organize products by city and filter by product types
    $productsByCity = [];
    $productTypes = [];
    $cities = [];
    $tags = [];
    $rates = [];

    if (!empty($products)) {
        foreach ($products as $product) {
            $city = isset($product['city']) ? $product['city'] : 'Unknown City';
            $productsByCity[$city][] = $product;

            // Collect unique product types
            if (!in_array($product['productType'], $productTypes)) {
                $productTypes[] = $product['productType'];
            }

            // Collect unique cities
            if (!in_array($city, $cities)) {
                $cities[] = $city;
            }

            // Collect unique rates
            if (!in_array($product['advertisedPrice'], $rates)) {
                $rates[] = $product['advertisedPrice'];
            }
        }
    }

    sort($productTypes);
    sort($cities);
    sort($tags);
    sort($rates);

    $selectedCountry = isset($_GET['country']) ? $_GET['country'] : '';
    $selectedCity = isset($_GET['city']) ? $_GET['city'] : '';
    $selectedProductType = isset($_GET['productType']) ? $_GET['productType'] : '';
    $selectedTags = isset($_GET['tags']) ? $_GET['tags'] : '';
    $selectedDate = isset($_GET['date']) ? $_GET['date'] : '';
    $selectedRate = isset($_GET['rate']) ? $_GET['rate'] : '';

    if (empty($selectedCity) || !isset($productsByCity[$selectedCity])) {
        $filteredProducts = [];
    } else {
        // Filter products based on selected filters
        $filteredProducts = array_filter($productsByCity[$selectedCity], function($product) use ($selectedProductType, $selectedTags, $selectedDate, $selectedRate) {
            $match = true;
            if ($selectedProductType !== '' && $product['productType'] !== $selectedProductType) {
                $match = false;
            }
            return $match;
        });

        // Sort by rate if selected
        if ($selectedRate === 'low') {
            usort($filteredProducts, function($a, $b) {
                return $a['advertisedPrice'] <=> $b['advertisedPrice'];
            });
        } elseif ($selectedRate === 'high') {
            usort($filteredProducts, function($a, $b) {
                return $b['advertisedPrice'] <=> $a['advertisedPrice'];
            });
        }
    }
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
            <div class="filter__group">
                <label for="country">Country Filter:</label>
                <select name="country" id="country">
                    <option value="All" <?php echo ($selectedCountry === 'All') ? 'selected' : ''; ?>>All</option>
                    <option value="Australia" <?php echo ($selectedCountry === 'Australia') ? 'selected' : ''; ?>>Australia</option>
                    <option value="New Zealand" <?php echo ($selectedCountry === 'New Zealand') ? 'selected' : ''; ?>>New Zealand</option>
                    <option value="Other" <?php echo ($selectedCountry === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="filter__group">
                <label for="city">City:</label>
                <input type="text" name="city" id="city" placeholder="Enter city name" value="<?php echo htmlspecialchars($selectedCity); ?>">
            </div>
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
                <label for="rate">Rate:</label>
                <select name="rate" id="rate">
                    <option value="">All Rates</option>
                    <option value="low" <?php echo ($selectedRate === 'low') ? 'selected' : ''; ?>>Low to High</option>
                    <option value="high" <?php echo ($selectedRate === 'high') ? 'selected' : ''; ?>>High to Low</option>
                </select>
            </div>
            <div class="filter__group" style="height:3vh;align-content:end;">
                <button type="submit">Apply Filters</button>
            </div>
        </form>
    </section>

    <section class="section__container popular__container">
        <h2 class="section__header">Popular Products</h2>
        <?php 
        echo empty($filteredProducts) ? "<p class=\"text-center\">No products available for the selected city and filters.</p>" : displayPopularHotels($filteredProducts, $page, $perPage);
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
<?php $conn->close(); ?>
