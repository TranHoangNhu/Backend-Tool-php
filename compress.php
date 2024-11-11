// compress.php
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

// Đường dẫn cho file input và output
$inputPath = $_FILES['file']['tmp_name'];
$outputDir = __DIR__ . '/uploads/';
$outputPath = $outputDir . 'compressed_' . basename($_FILES['file']['name']);

// Kiểm tra thư mục lưu trữ và tạo nếu không tồn tại
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

try {
    $imagick = new Imagick();
    $imagick->readImage($inputPath);

    // Kiểm tra nếu file PDF nhiều trang, xử lý từng trang riêng biệt
    if ($imagick->getNumberImages() > 1) {
        $imagick = $imagick->coalesceImages();
        foreach ($imagick as $frame) {
            $frame->setImageFormat('jpeg');
            $frame->setImageCompression(Imagick::COMPRESSION_JPEG);
            $frame->setImageCompressionQuality(75);
            $frame->stripImage();
        }
        $imagick = $imagick->deconstructImages();
    } else {
        $imagick->setImageFormat('jpeg');
        $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality(75);
        $imagick->stripImage();
    }

    // Lưu lại file nén dưới dạng PDF
    $imagick->writeImages($outputPath, true);
    $imagick->clear();
    $imagick->destroy();

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
    readfile($outputPath);

    // Xóa các file tạm sau khi xử lý xong
    unlink($inputPath);
    unlink($outputPath);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error compressing PDF", "error" => $e->getMessage()]);
}
?>
