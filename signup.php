<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";     
$dbname = "dummy"; 
$conn = new mysqli($servername, $username, $password, $dbname);
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            $message = '';
            $class = '';
            if ($status == 'success') {
                $message = 'Account registered successfully! Please proceed to login. �';
                $class = 'success';
            } elseif ($status == 'exists') {
                $message = 'Error: Username already exists. Please choose a different one. 😟';
                $class = 'error';
            } elseif ($status == 'password_mismatch') {
                $message = 'Error: Passwords do not match. Please try again. 🔒';
                $class = 'error';
            } elseif ($status == 'empty_fields') {
                $message = 'Error: All fields are required. Please fill them out. 📝';
                $class = 'error';
            } else {
                $message = 'Error: Something went wrong during registration. Please try again. 😔';
                $class = 'error';
            }
            echo '<div class="message-container ' . $class . '">' . htmlspecialchars($message) . '</div>';
        }
if ($conn->connect_error) {
    header("Location: login.html?status=error");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = htmlspecialchars(trim($_POST['username']));
    $input_password = $_POST['password']; 
    if (empty($input_username) || empty($input_password)) {
        header("Location: login.html?status=empty_fields");
        exit();
    }
    $stmt = $conn->prepare("SELECT id, username, password, userType FROM users WHERE username = ?");
    if ($stmt === false) {
        header("Location: login.html?status=error");
        exit();
    }
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($input_password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['userType'] = $user['userType'];
            if ($user['userType'] === 'admins') {
                header("Location: admin_dashboard.html");
            } elseif ($user['userType'] === 'students') {
                header("Location: student_dashboard.html");
            } elseif ($user['userType'] === 'employees') {
                header("Location: employee_dashboard.html");
            } else {
                header("Location: login.html?status=login_failed");
            }
            exit(); 
        } else {
            header("Location: login.html?status=login_failed");
            exit();
        }
    } else {
        header("Location: login.html?status=login_failed");
        exit();
    }
    $stmt->close(); 
} else {
    header("Location: login.html?status=error");
    exit();
} 
$stmt->close();
?>
