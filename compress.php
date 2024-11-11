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

$maxDimension = isset($_POST['scale']) ? intval($_POST['scale']) : 1000; // scale là kích thước tối đa của chiều rộng hoặc chiều cao
$imageQuality = isset($_POST['imageQuality']) ? intval($_POST['imageQuality']) : 75;

try {
    $imagick = new Imagick();
    $imagick->readImage($inputPath);

    // Coalesce tất cả các trang (nếu là PDF nhiều trang)
    $imagick = $imagick->coalesceImages();

    foreach ($imagick as $frame) {
        // Lấy kích thước hiện tại
        $width = $frame->getImageWidth();
        $height = $frame->getImageHeight();

        // Tính toán kích thước mới giữ nguyên tỷ lệ
        if ($width >= $height) {
            $newWidth = $maxDimension;
            $newHeight = ($height / $width) * $maxDimension;
        } else {
            $newHeight = $maxDimension;
            $newWidth = ($width / $height) * $maxDimension;
        }

        // Áp dụng thay đổi kích thước giữ nguyên tỷ lệ
        $frame->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);
        $frame->setImageCompression(Imagick::COMPRESSION_JPEG);
        $frame->setImageCompressionQuality($imageQuality);
        $frame->stripImage();
    }

    // Thiết lập định dạng và ghi vào file output
    $imagick->setImageFormat('pdf');
    $imagick->writeImages($outputPath, true);
    $imagick->clear();
    $imagick->destroy();

    if (file_exists($outputPath)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($outputPath) . '"');
        readfile($outputPath);

        // Xóa file tạm sau khi đã trả về cho client
        unlink($inputPath);
        unlink($outputPath);
    } else {
        throw new Exception("Output file could not be created.");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error compressing PDF", "error" => $e->getMessage()]);
}
?>
