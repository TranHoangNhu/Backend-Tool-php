<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Trả về 200 OK cho yêu cầu OPTIONS
    http_response_code(200);
    exit();
}

// Giả lập tiến độ nén file
$progress = [
    "completedPages" => rand(0, 10),
    "totalPages" => 10
];

header('Content-Type: application/json');
echo json_encode($progress);
?>