<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Trả về 200 OK cho yêu cầu OPTIONS
    http_response_code(200);
    exit();
}

// Tiến độ nén file sẽ được giả lập bằng giá trị ngẫu nhiên
$progress = [
    "completedPages" => rand(1, 10),
    "totalPages" => 10
];

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

echo "data: " . json_encode($progress) . "\n\n";
flush();
?>