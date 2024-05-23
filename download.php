<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_share"; // Database name

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $file_id = intval($_GET['id']);
    
    // Prepare statement
    $stmt = $conn->prepare("SELECT file_name, file_path FROM files WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $stmt->bind_result($file_name, $file_path);
        $stmt->fetch();
        $stmt->close();

        // Debugging: Log file details
        error_log("File ID: $file_id");
        error_log("File name: $file_name");
        error_log("File path: $file_path");

        if ($file_path && file_exists($file_path)) {
            error_log("File found: $file_path");

            // Set headers and serve the file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            flush();
            readfile($file_path);
            exit;
        } else {
            error_log("File not found or invalid path: $file_path");
            echo "File not found.";
        }
    } else {
        error_log("Failed to prepare statement: " . $conn->error);
        echo "Failed to retrieve file.";
    }
} else {
    echo "Invalid request.";
}

// Close connection
$conn->close();
?>
