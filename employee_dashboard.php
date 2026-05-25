<?php
session_start();
// Check if the user is logged in AND is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['userType'] !== 'employee') {
    header("Location: index.html"); // Redirect to login page if not authorized
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Employee <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>This is the exclusive content for Employee.</p>
    <a href="logout.php">Logout</a>
</body>
</html>