<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_share";


error_reporting(E_ALL);
ini_set('display_errors', 1);


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            die("Failed to create upload directory");
        }
    }

    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (file_exists($target_file)) {
        $response = ['error' => 'Sorry, file already exists.'];
        $uploadOk = 0;
    }

    if ($_FILES["file"]["size"] > 5000000) { // 5MB limit
        $response = ['error' => 'Sorry, your file is too large.'];
        $uploadOk = 0;
    }

    $allowedTypes = ['jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'];
    if (!in_array($fileType, $allowedTypes)) {
        $response = ['error' => 'Sorry, only certain file types are allowed.'];
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo json_encode($response);
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO files (file_name, file_path, uploaded_at) VALUES (?, ?, NOW())");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ss", $file_name, $target_file);

            if ($stmt->execute()) {
                $file_id = $stmt->insert_id;
                $fileURL = "http://localhost/WEB%202/download.php?id=" . $file_id;
                $response = ['fileURL' => $fileURL];
                echo json_encode($response);
            } else {
                $response = ['error' => 'Failed to save file info to database.'];
                echo json_encode($response);
            }
            $stmt->close();
        } else {
            $response = ['error' => 'Sorry, there was an error uploading your file.'];
            echo json_encode($response);
        }
    }
} else {
    $response = ['error' => 'Invalid request.'];
    echo json_encode($response);
}

$conn->close();
?>
