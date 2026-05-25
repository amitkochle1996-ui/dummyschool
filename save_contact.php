<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root"; 
    $password = "";    
    $database = "dummy";
    $conn = new mysqli( $servername, $username, $password, $database );
                if ($conn->connect_error) {
        header("Location: contact.html? status=error&message=" . urlencode("Connection failed: " . $conn->connect_error));
        exit();
    }
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $contact_date = trim($_POST['contact_date']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($phone_number) || empty($message)) {
        header("Location: contact.html? status=error&message=" . urlencode("Please fill in all required fields."));
        $conn->close();
        exit();
    }

    $sql = "INSERT INTO contacts (name, email, phone_number, contact_date, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        header("Location: contact.html? status=error&message=" . urlencode("Failed to prepare statement: " . $conn->error));
        $conn->close();
        exit();
    }
    $stmt->bind_param("ssiss", $name, $email, $phone_number, $contact_date, $message);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: contact.html? status=success");
        exit();
    } else {
        header("Location: contact.html? status=error&message=" . urlencode("Error: " . $stmt->error));
        $stmt->close();
        $conn->close();
        exit();
    }
}
?>