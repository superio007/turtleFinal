<?php
// Base URL for the merchant API
$apiUrl = "https://test.paydollar.com/b2cDemo/eng/merchant/api/orderApi.jsp";

// Function to capture authorized payment
function captureAuthorizedPayment($merchantId, $loginId, $password, $payRef, $amount) {
    global $apiUrl;
    $data = array(
        'merchantId' => $merchantId,
        'loginId' => $loginId,
        'password' => $password,
        'actionType' => 'Capture',
        'payRef' => $payRef,
        'amount' => $amount
    );

    return sendPostRequest($apiUrl, $data);
}

// Function to void accepted payment
function voidAcceptedPayment($merchantId, $loginId, $password, $payRef) {
    global $apiUrl;
    $data = array(
        'merchantId' => $merchantId,
        'loginId' => $loginId,
        'password' => $password,
        'actionType' => 'Void',
        'payRef' => $payRef
    );

    return sendPostRequest($apiUrl, $data);
}

// Function to request refund for accepted payment
function requestRefundAcceptedPayment($merchantId, $loginId, $password, $payRef, $amount = null) {
    global $apiUrl;
    $data = array(
        'merchantId' => $merchantId,
        'loginId' => $loginId,
        'password' => $password,
        'actionType' => 'RequestRefund',
        'payRef' => $payRef
    );

    if (!is_null($amount)) {
        $data['amount'] = $amount;
    }

    return sendPostRequest($apiUrl, $data);
}

// Function to query payment status
function queryPaymentStatus($merchantId, $loginId, $password, $payRef = null, $orderRef = null) {
    global $apiUrl;
    $data = array(
        'merchantId' => $merchantId,
        'loginId' => $loginId,
        'password' => $password,
        'actionType' => 'Query'
    );

    if (!is_null($payRef)) {
        $data['payRef'] = $payRef;
    }

    if (!is_null($orderRef)) {
        $data['orderRef'] = $orderRef;
    }

    return sendPostRequest($apiUrl, $data);
}

// Helper function to send POST request and parse response
function sendPostRequest($url, $data) {
    echo "<pre>Request Data:\n";
    print_r($data);
    echo "</pre>";

    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        CURLOPT_VERBOSE => true // Enable verbose output for debugging
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $curl_info = curl_getinfo($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    echo "<pre>cURL Info:\n";
    print_r($curl_info);
    echo "</pre>";

    if ($curl_error) {
        echo "<pre>cURL Error:\n$curl_error\n</pre>";
    }

    return parseResponse($response);
}

// Helper function to parse response
function parseResponse($response) {
    echo "<pre>Response Data:\n$response\n</pre>";

    parse_str($response, $result);
    return $result;
}

// Example usage with dynamic inputs
$merchantId = '16000806';
$loginId = 'apiturtle';
$password = 'turtle0807';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $payRef = $_POST['payRef'];
    $amount = isset($_POST['amount']) ? $_POST['amount'] : null;
    $orderRef = isset($_POST['orderRef']) ? $_POST['orderRef'] : null;

    switch ($action) {
        case 'capture':
            $response = captureAuthorizedPayment($merchantId, $loginId, $password, $payRef, $amount);
            break;
        case 'void':
            $response = voidAcceptedPayment($merchantId, $loginId, $password, $payRef);
            break;
        case 'refund':
            $response = requestRefundAcceptedPayment($merchantId, $loginId, $password, $payRef, $amount);
            break;
        case 'query':
            $response = queryPaymentStatus($merchantId, $loginId, $password, $payRef, $orderRef);
            break;
        default:
            $response = array('error' => 'Invalid action specified.');
    }

    echo "<pre>Final Response:\n";
    print_r($response);
    echo "</pre>";
} else {
    // Display form for testing purposes
    echo '<form method="POST">
            Action: <select name="action">
                <option value="capture">Capture</option>
                <option value="void">Void</option>
                <option value="refund">Refund</option>
                <option value="query">Query</option>
            </select><br>
            PayRef: <input type="text" name="payRef"><br>
            Amount: <input type="text" name="amount"><br>
            OrderRef: <input type="text" name="orderRef"><br>
            <input type="submit" value="Submit">
          </form>';
}
?>
