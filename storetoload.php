<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);
    if (isset($data['storeToRetrieve'])) {
        $_SESSION['storeToRetrieve'] = json_decode($data['storeToRetrieve'], true);
        echo "Session data stored successfully.";
    } else {
        echo "No data provided.";
    }
    exit;
}
?>
