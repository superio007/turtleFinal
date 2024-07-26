<?php
session_start();
if (!isset($_SESSION['selectedExtras'])) {
    $_SESSION['selectedExtras'] = [];
}
$sessionData = $_SESSION['selectedExtras'];

function getRezdyProductDetails($apiKey, $productCode) {
    $url = "https://api.rezdy-staging.com/v1/products/$productCode?apiKey=" . urlencode($apiKey);
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

function createRezdyBooking($apiKey, $bookingData) {
    $url = "https://api.rezdy-staging.com/v1/bookings?apiKey=" . urlencode($apiKey);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bookingData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        return "Error: Curl request failed: " . curl_error($ch);
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if ($data === null) {
        return "Error: Failed to decode JSON response";
    }
    return $data;
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


date_default_timezone_set('UTC');

$apiKey = "81c3566e60ef42e6afa1c2719e7843fd";
$productCode = $_GET['productCode'] ?? '';
if (empty($productCode)) {
    die("Error: Product code must be provided.");
}

$productDetails = getRezdyProductDetails($apiKey, $productCode);

$bookingMessage = '';
$bookingResponse = null;
$bookingDataArray = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = htmlspecialchars($_POST['firstName'] ?? '');
    $lastName = htmlspecialchars($_POST['lastName'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $country = htmlspecialchars($_POST['country'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $pickupLocation = htmlspecialchars($_POST['pickupLocation'] ?? '');
    $specialRequirements = htmlspecialchars($_POST['specialRequirements'] ?? '');
    $resellerReference = htmlspecialchars($_POST['resellerReference'] ?? '');
    $resellerComments = htmlspecialchars($_POST['resellerComments'] ?? '');
    $participants = [];

    if (isset($_POST['participants']) && is_array($_POST['participants'])) {
        foreach ($_POST['participants'] as $i => $participant) {
            $participants[] = [
                "fields" => [
                    ["label" => "First Name", "value" => htmlspecialchars($participant['firstName'])],
                    ["label" => "Last Name", "value" => htmlspecialchars($participant['lastName'])],
                ]
            ];
        }
    }

    foreach ($sessionData as $session) {
        $extrasArray = [];
        foreach ($session['Extras'] as $Extras) {
            $extrasArray[] = [
                "name" => $Extras['name'],
                "quantity" => (int) $Extras['quantity']
            ];
        }

        $quantities = [];
        if (!empty($session['PriceOptions'])) {
            foreach ($session['PriceOptions'] as $priceOption) {
                if ((int) $priceOption['count'] > 0) {
                    $quantities[] = ["optionLabel" => $priceOption['label'], "value" => (int) $priceOption['count']];
                }
            }
        }

        $Amount = (float) $session['Amount']; // Cast Amount to float
        $startTimeLocal = $session['sessionStartTime']; // Retrieve session start time from session

        $bookingData = [
            "resellerReference" => $resellerReference,
            "resellerComments" => $resellerComments,
            "customer" => [
                "firstName" => $firstName,
                "lastName" => $lastName,
                "email" => $email,
                "phone" => $phone
            ],
            "items" => [
                [
                    "productCode" => $productCode,
                    "startTimeLocal" => $startTimeLocal,
                    "quantities" => $quantities,
                    "extras" => $extrasArray,
                    "participants" => $participants,
                    "pickupLocation" => ["locationName" => $pickupLocation] // Dynamic pickup location
                ]
            ],
            "fields" => [
                ["label" => "Special Requirements", "value" => $specialRequirements] // Dynamic special requirements
            ],
            "payments" => [
                [
                    "amount" => $Amount, // Use Amount from session
                    "type" => "CASH",
                    "label" => "Paid in cash to API specification demo"
                ]
            ]
        ];

        $bookingDataArray[] = $bookingData;
    }

    foreach ($bookingDataArray as $bookingData) {
        $bookingResponse = createRezdyBooking($apiKey, $bookingData);
        if (isset($bookingResponse['requestStatus']['success']) && $bookingResponse['requestStatus']['success'] == true) {
            $bookingMessage = "Booking successful!";
            unset($_SESSION['selectedExtras']);
            echo "<script>sessionStorage.removeItem('selectedExtras');</script>";
        } else {
            $bookingMessage = "Booking failed: " . $bookingResponse['requestStatus']['error']['errorMessage'];
            if (isset($bookingResponse['requestStatus']['error']['validationErrors'])) {
                $bookingMessage .= " Validation Errors: " . json_encode($bookingResponse['requestStatus']['error']['validationErrors']);
            }
        }
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addParticipantButton = document.getElementById('add-participant');
            const participantsContainer = document.getElementById('participants-container');
            let participantCount = 0;

            addParticipantButton.addEventListener('click', function() {
                participantCount++;
                const participantHTML = `
                    <div class="participant-form">
                        <h4>Participant ${participantCount}</h4>
                        <div class="form-group">
                            <label for="participantFirstName_${participantCount}">First Name</label>
                            <input type="text" id="participantFirstName_${participantCount}" name="participants[${participantCount}][firstName]">
                        </div>
                        <div class="form-group">
                            <label for="participantLastName_${participantCount}">Last Name</label>
                            <input type="text" id="participantLastName_${participantCount}" name="participants[${participantCount}][lastName]">
                        </div>
                        <div class="form-group">
                            <label for="participantCertLevel_${participantCount}">Certification Level</label>
                            <input type="text" id="participantCertLevel_${participantCount}" name="participants[${participantCount}][certLevel]">
                        </div>
                        <div class="form-group">
                            <label for="participantCertNumber_${participantCount}">Certification Number</label>
                            <input type="text" id="participantCertNumber_${participantCount}" name="participants[${participantCount}][certNumber]">
                        </div>
                        <div class="form-group">
                            <label for="participantCertAgency_${participantCount}">Certification Agency</label>
                            <input type="text" id="participantCertAgency_${participantCount}" name="participants[${participantCount}][certAgency]">
                        </div>
                    </div>
                `;
                    participantsContainer.insertAdjacentHTML('beforeend', participantHTML);
                });
                var Amount = "<?php echo $Amount;?>";
                const bookingMessage = "<?php echo $bookingMessage; ?>";
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
                        text: 'Please move ahead with payment!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `payment.php?amount=${Amount}`;
                        }
                    });
                }

                const sessionData = <?php echo json_encode($sessionData); ?>;
                if (sessionData.length > 0) {
                    sessionData.forEach((session) => {
                        session.PriceOptions.forEach((priceOption) => {
                            for (let i = 0; i < priceOption.count; i++) {
                                addParticipantButton.click();
                            }
                        });
                    });
                }
            });
    </script>
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
            <div class="form-group">
                <label for="pickupLocation">Pickup Location</label>
                <input type="text" id="pickupLocation" name="pickupLocation" placeholder="Enter pickup location">
            </div>
            <div class="form-group">
                <label for="specialRequirements">Special Requirements</label>
                <input type="text" id="specialRequirements" name="specialRequirements" placeholder="Enter special requirements">
            </div>
            <div class="form-group">
                <label for="resellerReference">Reseller Reference</label>
                <input type="text" id="resellerReference" name="resellerReference" placeholder="Enter reseller reference">
            </div>
            <div class="form-group">
                <label for="resellerComments">Reseller Comments</label>
                <input type="text" id="resellerComments" name="resellerComments" placeholder="Enter reseller comments">
            </div>
            <div id="participants-container"></div>
            <button type="button" id="add-participant">Add Participant</button>
            <div class="d-flex align-items-baseline form-group">
                <input type="checkbox" id="email-updates" name="email-updates" checked>
                <label for="email-updates">Send me discounts and other offers by email</label>
            </div>
            <p>We’ll only contact you with essential updates or changes to your booking.
            <?php
                var_dump($bookingDataArray);
            ?>
            </p>
            <input type="hidden" name="merchantId">
            <input type="hidden" name="amount">
            <input type="hidden" name="orderRef">
            <input type="hidden" name="currCode">
            <input type="hidden" name="successUrl">
            <input type="hidden" name="failUrl">
            <input type="hidden" name="cancelUrl">
            <input type="hidden" name="payType">
            <input type="hidden" name="lang">
            <input type="hidden" name="mpsMode">
            <input type="hidden" name="payMethod">
            <input type="hidden" name="secureHash">
            <input type="hidden" name="remark">
            <input type="hidden" name="redirect">
            <input type="hidden" name="oriCountry">
            <input type="hidden" name="destCountry">
            <button type="submit">Go to payment</button>
            <p>You'll receive email reminders for this and future GetYourGuide products. You can opt out at any time. See our
            <?php
                var_dump($bookingResponse);
            ?>
            <a href="#">Privacy Policy</a>.</p>
        </form>
    </div>
</body>
</html>
