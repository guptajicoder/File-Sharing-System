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
    $email = $_POST["email"];
    $pass = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, email, password FROM `users` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($pass, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.html");
            exit();
        } else {
            echo "<script type='text/javascript'>alert('Invalid email or password!'); window.location.href = 'login.html';</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Invalid email or not found!'); window.location.href = 'login.html';</script>";
    }
}

$stmt->close();
$conn->close();
?>
