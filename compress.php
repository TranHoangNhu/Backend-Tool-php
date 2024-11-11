<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Trả về 200 OK cho yêu cầu OPTIONS
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

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

try {
    // Tạo đối tượng Imagick để nén file PDF
    $imagick = new Imagick();
    $imagick->readImage($inputPath);
    $imagick->setImageFormat('pdf');
    $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
    $imagick->setImageCompressionQuality(75);
    $imagick->stripImage();
    $imagick->writeImages($outputPath, true);
    $imagick->clear();
    $imagick->destroy();

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
    readfile($outputPath);

    // Xóa file tạm sau khi hoàn thành
    unlink($inputPath);
    unlink($outputPath);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error compressing PDF", "error" => $e->getMessage()]);
}
?>
