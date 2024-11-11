<?php
// File: compress.php
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

// Nhận giá trị scale và chất lượng hình ảnh từ yêu cầu POST
$scale = isset($_POST['scale']) ? intval($_POST['scale']) : 1000; // Giá trị mặc định là 1000
$imageQuality = isset($_POST['imageQuality']) ? intval($_POST['imageQuality']) : 75; // Giá trị mặc định là 75

try {
    // Đọc file PDF và gửi lại cho frontend
    $fileContent = file_get_contents($inputPath);
    if ($fileContent !== false) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($_FILES['file']['name']) . '"');
        echo $fileContent;
    } else {
        throw new Exception("Error reading PDF file.");
    }

    // Xóa file tạm sau khi gửi dữ liệu đến frontend
    unlink($inputPath);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error preparing PDF for compression", "error" => $e->getMessage()]);
}
?>