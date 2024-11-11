// progress.php
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Giả lập tiến độ nén (bạn có thể cập nhật logic để phù hợp với tiến độ thực tế)
$progress = [
    "completedPages" => rand(1, 10),
    "totalPages" => 10
];

echo "data: " . json_encode($progress) . "\n\n";
flush();
?>
