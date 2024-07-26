<?php
session_start();
if (!isset($_SESSION['selectedExtras'])) {
    $_SESSION['selectedExtras'] = [];
}
$sessionData = $_SESSION['selectedExtras'];

require 'apiFunctions.php';

date_default_timezone_set('UTC');

$apiKey = "81c3566e60ef42e6afa1c2719e7843fd";
$productCode = $_GET['productCode'] ?? '';
if (empty($productCode)) {
    die("Error: Product code must be provided.");
}

$productDetails = getRezdyProductDetails($apiKey, $productCode);
$startTimeLocal = (new DateTime())->format('Y-m-d H:i:s');
$endTimeLocal = (new DateTime())->modify('+1 month')->format('Y-m-d H:i:s');
$availability = getRezdyAvailability($apiKey, $productCode, $startTimeLocal, $endTimeLocal);

$bookingMessage = '';
$bookingResponse = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $email = htmlspecialchars($_POST['email']);
    $country = htmlspecialchars($_POST['country']);
    $phone = htmlspecialchars($_POST['phone']);
    $selectedExtras = $_POST['extra'] ?? [];

    if (isset($availability['sessions']) && count($availability['sessions']) > 0) {
        $startTimeLocal = $availability['sessions'][0]['startTimeLocal'];

        $bookingDataArray = [];
        foreach ($sessionData as $session) {
            $extrasArray = [];
            foreach ($session['Extras'] as $Extras) {
                $extrasArray[] = [
                    "name" => $Extras['name'],
                    "quantity" => (int) $Extras['quantity']
                ];
            }
            $bookingData = [
                "customer" => [
                    "firstName" => $firstName,
                    "lastName" => $lastName,
                    "email" => $email,
                    "phone" => $phone,
                    "country" => $country
                ],
                "items" => [
                    [
                        "productCode" => $session['ProductCode'],
                        "startTimeLocal" => $startTimeLocal,
                        "quantities" => [
                            [
                                "optionLabel" => "Adult",
                                "value" => (int) $session['Adults'] // Ensure 'Adults' is set
                            ]
                        ]
                    ]
                ],
                "extras" => $extrasArray,
                "payments" => [
                    [
                        "amount" => (float) $session['Amount'],
                        "type" => $session['paymentType'],
                        "recipient" => "AGENT",
                        "label" => "Paid in cash to API specification demo company"
                    ]
                ]
            ];

            // Conditionally add Child quantity if $session['Children'] is set and non-zero
            if (isset($session['Children']) && $session['Children'] > 0) {
                $bookingData['items'][0]['quantities'][] = [
                    "optionLabel" => "Child",
                    "value" => (int) $session['Children']
                ];
            }

            $bookingDataArray[] = $bookingData;
        }

        foreach ($bookingDataArray as $booking) {
            $bookingResponse = createRezdyBooking($apiKey, $booking);
            if (isset($bookingResponse['requestStatus']['success']) && $bookingResponse['requestStatus']['success'] == true) {
                $bookingMessage = "Booking successful!";
                unset($_SESSION['selectedExtras']);
                echo "<script>sessionStorage.removeItem('selectedExtras');</script>";
            } else {
                // Check for specific error codes and provide more meaningful messages
                $errorCode = $bookingResponse['requestStatus']['error']['errorCode'] ?? '';
                $errorMessage = $bookingResponse['requestStatus']['error']['errorMessage'] ?? '';
                
                if ($errorCode === '3') {
                    $bookingMessage = "Booking failed: Selling price too low or invalid quantity.";
                } elseif (strpos($errorMessage, 'Vendor agreement') !== false) {
                    $bookingMessage = "Booking failed: Vendor has not accepted the vendor agreement.";
                } elseif (strpos($errorMessage, 'Credit card token') !== false) {
                    $bookingMessage = "Booking failed: Credit card token is required for this rate.";
                } else {
                    $bookingMessage = "Booking failed: " . $errorMessage;
                }
            }
        }
    } else {
        $bookingMessage = "No available sessions found for the selected product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .container {
            background-color: white;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 34%;
            margin-top: 95px !important;
        }
        .header {
            margin-bottom: 20px;
        }
        .contact-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        p {
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
        p a {
            color: #007bff;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php require "header.php"; ?>
    <div class="container">
        <form class="contact-form" action="bookings.php?productCode=<?php echo htmlspecialchars($productCode); ?>" method="POST">
            <h2>Enter your contact details</h2>
            <div class="row">
                <div class="form-group col-6 d-grid">
                    <label for="fname">* First name</label>
                    <input type="text" id="fname" name="firstName" placeholder="Enter first name" required>
                </div>
                <div class="form-group col-6 d-grid">
                    <label for="lname">* Last name</label>
                    <input type="text" id="lname" name="lastName" placeholder="Enter last name" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">* Email</label>
                <input type="email" id="email" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label for="country">* Country</label>
                <select id="country" name="country" required>
                    <option value="" hidden selected>Select Country</option>
                    <option value="india">India (+91)</option>
                    <!-- Add other country options here -->
                </select>
            </div>
            <div class="form-group">
                <label for="phone">* Mobile phone number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter mobile number" required>
            </div>
            <div class="d-flex align-items-baseline form-group">
                <input type="checkbox" id="email-updates" name="email-updates" checked>
                <label for="email-updates">Send me discounts and other offers by email</label>
            </div>
            <p>We’ll only contact you with essential updates or changes to your booking.</p>

            <p><?php var_dump($bookingResponse); ?></p>

            <button type="submit">Go to payment</button>
            <p>You'll receive email reminders for this and future GetYourGuide products. You can opt out at any time. See our <a href="#">Privacy Policy</a>.</p>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var bookingMessage = "<?php echo $bookingMessage; ?>";
            if (bookingMessage.startsWith("Booking failed:")) {
                Swal.fire({
                    title: 'Error!',
                    text: bookingMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else if (bookingMessage === "Booking successful!") {
                Swal.fire({
                    title: 'Success!',
                    text: 'Your booking was successful!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
</body>
</html>
