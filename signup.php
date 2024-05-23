<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_share";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $pass = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $pass);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: login.html");
        exit();
    } else {
        $error_msg = "Error registering user. Please try again.";
    }
}

$conn->close();
?>
