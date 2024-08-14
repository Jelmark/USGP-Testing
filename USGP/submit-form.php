<?php
// Debug output to check incoming data
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usgp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$stud_name = isset($_POST['stud_name']) ? $_POST['stud_name'] : '';
$course = isset($_POST['stud_course']) ? $_POST['stud_course'] : '';
$tree_species = isset($_POST['tree_species']) ? $_POST['tree_species'] : '';
$date_planted = isset($_POST['date']) ? $_POST['date'] : '';
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : 0;
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : 0;
$submitted_date = date('Y-m-d H:i:s');

// Debug output to check data before SQL execution
echo "Name: " . htmlspecialchars($stud_name) . "<br>";
echo "Course: " . htmlspecialchars($course) . "<br>";
echo "Tree Species: " . htmlspecialchars($tree_species) . "<br>";
echo "Date Planted: " . htmlspecialchars($date_planted) . "<br>";
echo "Latitude: " . htmlspecialchars($latitude) . "<br>";
echo "Longitude: " . htmlspecialchars($longitude) . "<br>";

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO student_records (stud_name, stud_course, tree_species, date_planted, latitude, longitude, date_submitted) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sssssss", $stud_name, $course, $tree_species, $date_planted, $latitude, $longitude, $submitted_date);

// Execute the statement
if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
