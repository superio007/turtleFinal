<?php
session_start();
date_default_timezone_set('UTC');
if(isset($_GET['productCode'])){
    $productCode = $_GET['productCode'];
}

function getRezdyProductDetails($apiKey, $productCode) {
    $url = "https://api.rezdy.com/v1/products/$productCode?apiKey=" . urlencode($apiKey);
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

function checkRezdyAvailability($apiKey, $productCode, $startTimeLocal, $endTimeLocal) {
    $url = "https://api.rezdy.com/v1/availability?apiKey=" . urlencode($apiKey) . "&productCode=" . urlencode($productCode) . "&startTimeLocal=" . urlencode($startTimeLocal) . "&endTimeLocal=" . urlencode($endTimeLocal);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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

function formatRezdyDate($date) {
    return $date->format('Y-m-d H:i:s'); // Format for Rezdy API
}

$apiKey = "b5a46c6c39624b908c2aef115af33942";
$productCode = $_GET['productCode'] ?? '';
if (empty($productCode)) {
    die("Error: Product code must be provided.");
}

$productDetails = getRezdyProductDetails($apiKey, $productCode);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkAvailability'])) {
    $datetime = $_POST['datetime'];
    $startTimeLocal = formatRezdyDate(new DateTime($datetime));
    $endTimeLocal = formatRezdyDate((new DateTime($datetime))->modify('+2 days'));
    $availability = checkRezdyAvailability($apiKey, $productCode, $startTimeLocal, $endTimeLocal);
    $_SESSION['availability'] = $availability; // Store availability in session
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rezdy API - Product Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    #payee {
        border: 1px solid #0000001f;
        padding: 8px 15px;
        width: fit-content;
    }
    .price-option {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .price-option label {
        flex-grow: 1;
    }
    .price-counter {
        display: flex;
        align-items: center;
    }
    .price-counter button {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 0;
        margin: 0 5px;
    }
    .price-counter input {
        width: 50px;
        text-align: center;
    }
    #amount {
        width: 120px; 
    }
</style>
<body class="bg-light">
    <?php require "header.php";?>
    <div class="container mt-5 p-4 bg-white rounded-lg shadow-lg">
        <form id="availability-form" action="" method="post">
            <h1 id="product_name" class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($productDetails['product']['name'] ?? ''); ?></h1>
            <p id="product_description" class="mb-4"><?php echo htmlspecialchars($productDetails['product']['shortDescription'] ?? ''); ?></p>
            <input type="text" value="<?php echo htmlspecialchars($productDetails['product']['images'][0]['itemUrl']); ?>" id="imgurl" hidden>
            <?php if (isset($productDetails['product']['images'][0]['itemUrl'])): ?>
                <img src="<?php echo htmlspecialchars($productDetails['product']['images'][0]['itemUrl']); ?>" alt="Product Image" class="w-full h-auto mb-4">
            <?php endif; ?>
            
            <div class="form-group mb-4">
                <h2 class="text-2xl font-bold mb-4 w-100">Select Date and Time</h2>
                <div class="form-group w-auto">
                    <?php
                    $currentDateTime = (new DateTime())->format('Y-m-d\TH:i');
                    ?>
                    <input type="datetime-local" id="datetimeInput" name="datetime" class="form-control" min="<?php echo $currentDateTime; ?>" value="<?php echo $currentDateTime; ?>" style="width: auto;">
                </div>
            </div>

            <button id="check-availability" type="submit" name="checkAvailability" class="btn btn-secondary mr-2">Check Availability</button>
        </form>

        <div id="availability-result" class="mt-4">
            <?php
            if (isset($_SESSION['availability'])) {
                $availability = $_SESSION['availability'];
                if (is_string($availability)) {
                    echo '<div class="alert alert-danger" role="alert">' . $availability . '</div>';
                } else {
                    if ($availability['requestStatus']['success'] && !empty($availability['sessions'])) {
                        $counter = 0; // Initialize the counter
                        foreach ($availability['sessions'] as $session) {
                            $counter++; // Increment the counter for each session
                            $sessionDivId = 'availability_' . $counter; // Create a unique ID
                            $seatsAvailable = $session['seatsAvailable'] ?? 0;
                            $seatsUsed = $session['seatsUsed'] ?? 0;
                            $seatsLeft = $seatsAvailable - $seatsUsed;
                            echo '<div id="' . $sessionDivId . '" class="alert alert-success" role="alert">';
                            echo 'Available session from ' . htmlspecialchars($session['startTimeLocal']) . ' to ' . htmlspecialchars($session['endTimeLocal']) . '<br>';
                            echo 'Seats available: ' . htmlspecialchars($seatsAvailable) . '<br>';
                            echo 'Seats used: ' . htmlspecialchars($seatsUsed) . '<br>';
                            echo 'Seats left: ' . htmlspecialchars($seatsLeft) . '<br>';
                            echo '<button id="select-session" data-session_start_time="' . htmlspecialchars($session['startTimeLocal']) . '" class="btn btn-primary mr-2">Select Session</button>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">No available sessions found.</div>';
                    }
                }
            }
            ?>
        </div>

        <form id="details-form" style="display: none;">
            <div class="form-group mb-4">
                <h2 class="text-2xl font-bold mb-4">Select Passengers</h2>
                <?php foreach ($productDetails['product']['priceOptions'] as $index => $priceOption): ?>
                    <div class="price-option">
                        <?php 
                            $price = htmlspecialchars($priceOption['price'] ?? '0');
                            $maxQuantity = (int)($priceOption['maxQuantity'] ?? 0);
                            $minQuantity = (int)($priceOption['minQuantity'] ?? 0);
                            $priceGroupType = htmlspecialchars($priceOption['priceGroupType'] ?? '');
                        ?>
                        <label class="font-weight-bold"><?php echo $priceOption['label'] . ' - $' . $price; ?></label>
                        <input type="text" id="priceoptionlabel-<?php echo $index;?>" value="<?php echo $priceOption['label'];?>" data-index="<?php echo $index; ?>" hidden>
                        <input type="text" id="price_<?php echo $index; ?>" value="<?php echo $maxQuantity; ?>" hidden>
                        <input type="text" id="minprice_<?php echo $index; ?>" value="<?php echo $minQuantity; ?>" hidden>
                        <input type="text" id="priceGroupType_<?php echo $index;?>" data-index="<?php echo $index; ?>" value="<?php echo $priceGroupType; ?>" hidden>
                        <div class="price-counter">
                            <button type="button" class="btn btn-outline-secondary price-minus" data-index="<?php echo $index; ?>">-</button>
                            <input type="text" class="form-control text-center price-count" id="price-count-<?php echo $index; ?>" data-index="<?php echo $index; ?>" value="0" max="<?php echo $maxQuantity; ?>">
                            <button type="button" class="btn btn-outline-secondary price-plus" data-price="<?php echo $price; ?>" data-index="<?php echo $index; ?>">+</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="form-group mb-4">
                <label for="amount" class="font-weight-bold">Total Amount</label>
                <input type="text" id="amount" class="form-control mb-4" disabled>
            </div>
            
            <div class="form-group mb-4">
                <label for="paymentType" class="font-weight-bold w-100">Payment Option</label>
                <div class="w-auto">
                    <select required name="paymentType" id="paymentType" class="form-control" style="width: auto;">
                        <option value="" selected hidden>Select</option>
                        <option value="CASH">CASH</option>
                        <option value="CREDIT CARD">CREDIT CARD</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-4">
                <h2 class="text-2xl font-bold mb-4 w-100">Choose Extras</h2>
                <?php if (isset($productDetails['product']['extras']) && is_array($productDetails['product']['extras'])): ?>
                    <?php foreach ($productDetails['product']['extras'] as $extra): ?>
                        <div class="d-flex gap-3 align-items-center mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="extra[]" id="extra-<?php echo htmlspecialchars($extra['name']); ?>" value="<?php echo htmlspecialchars($extra['name']); ?>" data-price="<?php echo htmlspecialchars($extra['price']); ?>" class="custom-control-input extra-checkbox">
                                <label class="custom-control-label" for="extra-<?php echo htmlspecialchars($extra['name']); ?>"><?php echo htmlspecialchars($extra['name']); ?></label>
                            </div>
                            <div class="form-group mb-0">
                                <label for="extra-qty-<?php echo htmlspecialchars($extra['name']); ?>">Qty:</label>
                                <input type="number" name="Extras_quantity[]" id="extra-qty-<?php echo htmlspecialchars($extra['name']); ?>" min="0" value="0" class="extra-quantity form-control" style="width: 60px; display: inline-block;">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No extras available for this product.</p>
                <?php endif; ?>
            </div>

            <div class="form-group mb-4">
                <h2 class="text-2xl font-bold mb-4 w-100">Find Us!</h2>
                <div id="map" class="w-100 h-64"></div>
            </div>

            <button id="add-to-cart" type="button" class="btn btn-primary mr-2">Add to cart</button>
            <a id="continue" class="btn btn-primary" href="bookings.php?productCode=<?php echo $productCode;?>">Continue</a>
        </form>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
    <script>
    $(document).ready(function() {
        function isFutureDate(dateString) {
            const selectedDate = new Date(dateString);
            const currentDate = new Date();
            return selectedDate > currentDate;
        }

        function isWeekend(dateString) {
            const date = new Date(dateString);
            const day = date.getUTCDay();
            return day === 6 || day === 0;
        }

        function updatePricesForWeekend() {
            const datetime = $('#datetimeInput').val();
            if (isWeekend(datetime)) {
                console.log("Weekend detected. Updating prices...");
                const session = <?php echo json_encode($_SESSION['availability']['sessions'] ?? []); ?>;
                const priceOptions = session.length > 0 ? session[0].priceOptions : [];
                if (priceOptions.length === 0) {
                    console.log("No price options available in session data.");
                    return;
                }
                $('.price-plus').each(function(index) {
                    const weekendPrice = priceOptions[index]?.price ?? 0;
                    $(this).data('price', weekendPrice);
                    $(this).closest('.price-option').find('label').text(`${priceOptions[index]?.label} - $${weekendPrice}`);
                });
            } else {
                console.log("Weekday detected. Using default prices...");
                const defaultPrices = <?php echo json_encode($productDetails['product']['priceOptions']); ?>;
                $('.price-plus').each(function(index) {
                    const defaultPrice = defaultPrices[index]?.price ?? 0;
                    $(this).data('price', defaultPrice);
                    $(this).closest('.price-option').find('label').text(`${defaultPrices[index]?.label} - $${defaultPrice}`);
                });
            }
            updateAmount();
        }

        function updateAmount() {
            let totalAmount = 0;
            let totalGroupPrice = 0;
            let includeTotalGroupPrice = false;

            $('.price-count').each(function() {
                const index = $(this).data('index');
                const price = parseFloat($(`.price-plus[data-index="${index}"]`).data('price'));
                const count = parseInt($(this).val());
                let priceGroupType = $(`#priceGroupType_${index}`).val() || '';

                if ($(this).data('updated') === true) { // Check if this counter has been updated
                    if (priceGroupType === "TOTAL") {
                        totalGroupPrice = price; // Assume only one 'TOTAL' price should be considered
                        if (count > 0) {
                            includeTotalGroupPrice = true; // Only include totalGroupPrice if any count is greater than 0
                        }
                    } else {
                        totalAmount += price * count;
                        if (count > 0) {
                            includeTotalGroupPrice = true; // Only include totalGroupPrice if any count is greater than 0
                        }
                    }
                }
            });

            if (includeTotalGroupPrice) {
                totalAmount += totalGroupPrice;
            }

            // Include extras in the amount calculation
            $('.extra-checkbox:checked').each(function() {
                const extraPrice = parseFloat($(this).data('price'));
                const quantity = parseInt($(this).closest('.d-flex').find('.extra-quantity').val()) || 0;
                totalAmount += extraPrice * quantity;
            });

            $('#amount').val(totalAmount.toFixed(2));
        }

        // Initialize counters for price options
        $('.price-count').each(function() {
            const index = $(this).data('index');
            const pricePlus = $(`.price-plus[data-index="${index}"]`);
            const price = parseFloat(pricePlus.data('price')) || 0;
            pricePlus.data('price', price);

            $(`.price-plus[data-index="${index}"]`).off('click').on('click', function() {
                let count = parseInt($(`#price-count-${index}`).val());
                let max = parseInt($(`#price_${index}`).val());
                let min = parseInt($(`#minprice_${index}`).val());
                let priceGroupType = $(`#priceGroupType_${index}`).val() || '';

                if (priceGroupType) {
                    if (count < max) {
                        if (count >= min) {
                            count++;
                        } else {
                            count = min;
                        }
                    }
                } else {
                    count++;
                }
                $(`#price-count-${index}`).val(count);
                $(`#price-count-${index}`).data('updated', true); // Mark this counter as updated
                updateAmount();
                storeExtras();
            });

            $(`.price-minus[data-index="${index}"]`).off('click').on('click', function() {
                let count = parseInt($(`#price-count-${index}`).val());
                let min = parseInt($(`#minprice_${index}`).val());
                let priceGroupType = $(`#priceGroupType_${index}`).val() || '';

                if (priceGroupType) {
                    if (count > min) {
                        count--;
                    } else if (count > 0 && count <= min) {
                        count = 0; // Set to zero if limits are achieved
                    } else {
                        count = 0;
                    }
                } else {
                    if (count > 0) {
                        count--;
                    }
                }
                $(`#price-count-${index}`).val(count);
                $(`#price-count-${index}`).data('updated', true); // Mark this counter as updated
                updateAmount();
                storeExtras();
            });
        });

        // Handle change events for other inputs
        $('#paymentType').off('change').on('change', function() {
            storeExtras();
        });

        $('#datetimeInput').off('change').on('change', function() {
            updatePricesForWeekend();
            storeExtras();
        });

        $('.extra-checkbox, .extra-quantity').off('change').on('change', function() {
            updateAmount();
            storeExtras();
        });

        // Load stored values on page load
        loadStoredExtras();

        $('#check-availability').click(function(event) {
            const datetime = $('#datetimeInput').val();
            if (!isFutureDate(datetime)) {
                alert('Please select a future date for the booking.');
                event.preventDefault();
            } else {
                $('#availability-form').submit();
            }
        });

        $(document).on('click', '#select-session', function() {
            const sessionStartTime = $(this).data('session_start_time');
            $('#details-form').show();
            $('#availability-form').hide();
            $('html, body').animate({ scrollTop: $("#details-form").offset().top }, 1000);
            updatePricesForWeekend(); // Call the function here as well

            // Store the selected session start time in session storage
            sessionStorage.setItem('selectedSessionStartTime', sessionStartTime);
        });

        $('#add-to-cart, #continue').click(function(event) {
            event.preventDefault();
            const sessionStartTime = sessionStorage.getItem('selectedSessionStartTime');
            const productCode = '<?php echo $productCode; ?>';
            const productname = $('#product_name').text();
            const productdescription = $('#product_description').text();
            const Amount = $('#amount').val();
            const imgUrl = $('#imgurl').val();
            const paymentType = $('#paymentType').val();
            const datetime = $('#datetimeInput').val();

            let priceOptions = [];
            let priceOptionsMap = new Map();

            $('.price-option input[data-index]').each(function() {
                const index = $(this).data('index');
                const labelprice = $(`#priceoptionlabel-${index}`).val();
                const countprice = $(`#price-count-${index}`).val();

                if (parseInt(countprice) > 0) { // Only add if count is greater than zero
                    const key = `${labelprice}_${countprice}`;
                    if (!priceOptionsMap.has(key)) {
                        priceOptionsMap.set(key, { label: labelprice, count: countprice });
                    }
                }
            });

            priceOptions = Array.from(priceOptionsMap.values());

            let extras = [];
            $('.extra-checkbox:checked').each(function() {
                const extraName = $(this).val();
                const quantity = $(this).closest('.d-flex').find('.extra-quantity').val();
                extras.push({ name: extraName, quantity: quantity });
            });

            let selectedExtra = {
                Amount: parseFloat(Amount),
                ProductCode: productCode,
                ProductName: productname,
                productdescription: productdescription,
                imgUrl: imgUrl,
                paymentType: paymentType,
                Extras: extras,
                PriceOptions: priceOptions,
                sessionStartTime: sessionStartTime,
                datetime: datetime
            };

            let selectedExtrasArray = JSON.parse(sessionStorage.getItem('selectedExtras')) || [];
            selectedExtrasArray.push(selectedExtra);
            sessionStorage.setItem('selectedExtras', JSON.stringify(selectedExtrasArray));

            if ($(this).attr('id') === 'continue') {
                window.location.href = `bookings.php?productCode=${productCode}&sessionStartTime=${sessionStartTime}`;
            } else {
                storeSessionAndRedirect(productCode, sessionStartTime);
                updateCartCounter();
                updateCartItems();
            }
        });

        function storeSessionAndRedirect(productCode, sessionStartTime) {
            const selectedExtrasArray = sessionStorage.getItem('selectedExtras');
            if (selectedExtrasArray) {
                fetch('storesession.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ selectedExtras: selectedExtrasArray, sessionStartTime: sessionStartTime })
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    alert("Trip has been added into your cart.");
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }

        function storeExtras() {
            let extras = [];
            let priceOptions = [];
            let datetimeInput = $('#datetimeInput').val();

            $('.price-count').each(function() {
                const index = $(this).data('index');
                const count = $(this).val();
                const label = $(`#priceoptionlabel-${index}`).val();
                priceOptions.push({ label: label, count: count });
            });

            $('.extra-checkbox').each(function() {
                const extraName = $(this).val();
                const isChecked = $(this).is(':checked');
                const quantity = $(this).closest('.d-flex').find('.extra-quantity').val();
                extras.push({ name: extraName, isChecked: isChecked, quantity: quantity });
            });

            let storeToRetrieve = {
                Amount: $('#amount').val(),
                paymentType: $('#paymentType').val(),
                Extras: extras,
                PriceOptions: priceOptions,
                Date: datetimeInput,
            };

            sessionStorage.setItem('storeToRetrieve', JSON.stringify(storeToRetrieve));
        }

        function loadStoredExtras() {
            let storedValue = JSON.parse(sessionStorage.getItem('storeToRetrieve')) || {};

            // Set default values if the stored value is not present
            $('#paymentType').val(storedValue['paymentType'] ?? '');
            $('#amount').val(storedValue['Amount'] ?? ''); 
            $('#datetimeInput').val(storedValue['Date'] ?? '');

            if (storedValue['PriceOptions']) {
                storedValue['PriceOptions'].forEach((option, index) => {
                    const label = option.label;
                    const count = option.count;
                    $(`#priceoptionlabel-${index}`).val(label);
                    $(`#price-count-${index}`).val(count);
                    $(`#price-count-${index}`).data('updated', true); // Mark this counter as updated if loaded from storage
                });
            }

            if (storedValue['Extras']) {
                storedValue['Extras'].forEach(extra => {
                    const checkbox = $(`.extra-checkbox[value="${extra.name}"]`);
                    const quantityInput = checkbox.closest('.d-flex').find('.extra-quantity');

                    checkbox.prop('checked', extra.isChecked);
                    quantityInput.val(extra.quantity);
                });
            }

            updateAmount();
        }
    });
</script>

</body>
</html>
