<?php
session_start(); 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "dummy");

    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        $_SESSION['error_message'] = "A server error occurred. Please try again later.";
        header('Location: login.html?userType=' . (isset($_POST['userType']) ? $_POST['userType'] : 'admin'));
        exit();
    }
    $identifier = trim($_POST['username']); 
    $password = $_POST['userpassword'];
    $userType = strtolower(trim($_POST['userType'])); 
    if (empty($identifier) || empty($password) || empty($userType)) {
        $_SESSION['error_message'] = "Please enter both username/email and password.";
        header('Location: login.html?userType=' . $userType);
        exit();
    }
    $validUserTypes = ['admin', 'student', 'employee'];
    if (!in_array($userType, $validUserTypes)) {
        $_SESSION['error_message'] = "Invalid user type selected. Please try again.";
        header('Location: login.html?userType=$userType'); 
        exit();
    }
    $stmt = $conn->prepare("SELECT id, userpassword FROM signup WHERE email = ? AND userType = ?");
    $stmt->bind_param("ss", $identifier, $userType);
    $stmt->execute();
    $stmt->store_result(); 
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch(); 
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_loggedin'] = true;
            $_SESSION['id'] = $userId; 
            $_SESSION['username'] = $identifier;
            $_SESSION['usertype'] = $userType;
            switch ($userType) {
                case 'admin':
                    header('Location: admin_dashboard.html');
                    break;
                case 'employee':
                    header('Location: employee_dashboard.html');
                    break;
                case 'student':
                    header('Location: student_dashboard.html');
                    break;
                default:
                    $_SESSION['error_message'] = "Authentication successful, but dashboard not found for this user type.";
                    header('Location: login.html?userType=' . $userType);
                    break;
            }
            exit();
        } else {
            $_SESSION['error_message'] = "Invalid password. Please try again.";
            header('Location: login.html?userType=' . $userType);
            exit();
        }
    } else {
            $_SESSION['error_message'] = "No account found with this username/email for the selected user type.";
        header('Location: login.html?userType=' . $userType);
        exit();
    }
  } else {
        header('Location: login.html');
    exit();
}
?>