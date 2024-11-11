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
$inputPath = __DIR__ . '/uploads/' . basename($uploadedFile['name']);

// Di chuyển file vào thư mục uploads
if (!move_uploaded_file($uploadedFile['tmp_name'], $inputPath)) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to move uploaded file']);
    exit;
}

$tempPdfPath = __DIR__ . '/uploads/temp_converted.pdf';

try {
    $imagick = new Imagick();
    $imagick->readImage($inputPath);
    
    // Set chất lượng hình ảnh
    $imagick->setImageFormat('jpeg');
    $imagick->setImageCompressionQuality(75);

    // Tạo PDF mới từ hình ảnh đã nén
    $imagick->writeImages($tempPdfPath, true);

    // Download file PDF nén
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="compressed_' . basename($uploadedFile['name']) . '"');
    readfile($tempPdfPath);

    // Xóa các file tạm sau khi xử lý xong
    unlink($inputPath);
    unlink($tempPdfPath);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error compressing PDF', 'error' => $e->getMessage()]);
}
?>
