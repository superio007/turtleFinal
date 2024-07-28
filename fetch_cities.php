<?php
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

$term = $_GET['term'];
$countryFilter = $_GET['countryFilter'];

$query = "SELECT DISTINCT city FROM products WHERE city LIKE ?";

if ($countryFilter === 'Australia') {
    $query .= " AND country = 'Australia'";
} elseif ($countryFilter === 'New Zealand') {
    $query .= " AND country = 'New Zealand'";
} elseif ($countryFilter === 'Other') {
    $query .= " AND country NOT IN ('Australia', 'New Zealand')";
}

$stmt = $conn->prepare($query);
$searchTerm = '%' . $term . '%';
$stmt->bind_param('s', $searchTerm);
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
