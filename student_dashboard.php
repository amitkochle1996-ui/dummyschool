<?php
session_start();

// Check if the user is logged in AND is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['userType'] !== 'student') {
    header("Location: index.html"); // Redirect to login page if not authorized
    exit;
}

// Database connection details
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password
$dbname = "dummyschool"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_SESSION['user_id']; // Get the logged-in student's ID

// Fetch student basic details
$student_details = [];
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $student_details = $result->fetch_assoc();
}
$stmt->close();

// Fetch assignments
$assignments = [];
$stmt = $conn->prepare("SELECT * FROM assignments WHERE student_id = ? ORDER BY due_date ASC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}
$stmt->close();

// Fetch timetable
$timetable = [];
$stmt = $conn->prepare("SELECT * FROM timetables WHERE student_id = ? ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), start_time ASC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $timetable[] = $row;
}
$stmt->close();
$fee_status = [];
$stmt = $conn->prepare("SELECT * FROM fees WHERE student_id = ? ORDER BY due_date ASC LIMIT 1"); 
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $fee_status = $result->fetch_assoc();
}
$stmt->close();
$payment_history = [];
$stmt = $conn->prepare("SELECT * FROM fee_payments WHERE student_id = ? ORDER BY payment_date DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $payment_history[] = $row;
}
$stmt->close();
$conn->close();
?>
