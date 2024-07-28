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

$searchTerm = $_GET['term'];

$sql = "SELECT DISTINCT city FROM products WHERE city LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%".$searchTerm."%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$cities = [];
while ($row = $result->fetch_assoc()) {
    $cities[] = $row['city'];
}

echo json_encode($cities);

$stmt->close();
$conn->close();
?>
