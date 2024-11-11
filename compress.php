<?php
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

$inputPath = $_FILES['file']['tmp_name'];
$outputDir = __DIR__ . '/uploads/';
$outputPath = $outputDir . 'compressed_' . basename($_FILES['file']['name']);

try {
    $imagick = new Imagick();
    $imagick->readImage($inputPath);
    $imagick->setImageCompressionQuality(75);
    $imagick->writeImage($outputPath);
    $imagick->clear();
    $imagick->destroy();

    // Trả về file đã nén
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
    readfile($outputPath);
    
    // Xoá file tạm sau khi download xong
    unlink($inputPath);
    unlink($outputPath);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error compressing PDF", "error" => $e->getMessage()]);
}
?>
