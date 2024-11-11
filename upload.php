<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests allowed']);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['message' => 'No file uploaded']);
    exit;
}

$uploadedFile = $_FILES['file'];
$targetDir = __DIR__ . '/uploads/';
$targetPath = $targetDir . basename($uploadedFile['name']);

// Kiểm tra nếu thư mục uploads chưa tồn tại thì tạo mới
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Di chuyển file vào thư mục uploads
if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
    echo json_encode(['message' => 'File uploaded successfully!', 'filePath' => $targetPath]);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to move uploaded file']);
}
?>
