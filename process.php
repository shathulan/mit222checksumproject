<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prasun";



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to calculate MD5 checksum
function calculate_md5($file_path)
{
    return md5_file($file_path);
}

// Function to handle file upload and duplication check
function handle_file_upload($file, $conn)
{
    // Directory to store uploaded files
    $upload_directory = 'uploads/';

    // Create upload directory if it doesn't exist
    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0777, true);
    }

    // Get file details
    $file_name = basename($file["name"]);
    $file_path = $upload_directory . $file_name;
    $file_tmp_path = $file["tmp_name"];

    // Check if file was uploaded without errors
    if ($file["error"] !== UPLOAD_ERR_OK) {
        return "Error uploading file.";
    }

    // Move file to the upload directory
    if (!move_uploaded_file($file_tmp_path, $file_path)) {
        return "Failed to move uploaded file.";
    }

    // Calculate checksum
    $file_checksum = calculate_md5($file_path);
    $existing_file_name = null;
    $existing_file_path = null;
    // Check for duplicates in the database
    $stmt = $conn->prepare("SELECT file_name, file_path FROM file_checksums WHERE checksum = ?");
    $stmt->bind_param("s", $file_checksum);
    $stmt->execute();
    $stmt->bind_result($existing_file_name, $existing_file_path);
    $stmt->fetch();
    $stmt->close();

    if ($existing_file_name) {
        // Duplicate found
        // unlink($file_path); // Remove the uploaded file if it's a duplicate
        return "Duplicate file found: $file_name (duplicate of $existing_file_name)";
    } else {
        // Insert new file info into the database
        $stmt = $conn->prepare("INSERT INTO file_checksums (file_name, file_path, checksum) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $file_name, $file_path, $file_checksum);
        $stmt->execute();
        $stmt->close();

        return "File uploaded successfully: $file_name";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $message = handle_file_upload($_FILES['file'], $conn);
    } else {
        $message = "No file uploaded.";
    }

    // Display JavaScript alert with the message and redirect back to the form
    echo "<script>alert('" . addslashes($message) . "'); window.location.href = 'index.php';</script>";
    exit();
}

$conn->close();