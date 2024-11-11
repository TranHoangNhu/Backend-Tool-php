<?php
// File: upload.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Only POST requests allowed"]);
    exit();
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(["message" => "No file uploaded"]);
    exit();
}

$uploadDir = __DIR__ . '/uploads/';
$uploadFile = $uploadDir . basename($_FILES['file']['name']);

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
    http_response_code(200);
    echo json_encode(["message" => "File uploaded successfully", "filePath" => $uploadFile]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Error uploading file"]);
}
?>