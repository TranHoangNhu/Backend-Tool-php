<?php
// File: progress.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$progressFile = __DIR__ . '/uploads/progress.json';

while (true) {
    clearstatcache();

    if (file_exists($progressFile)) {
        $progress = file_get_contents($progressFile);
        echo "data: $progress\n\n";
    } else {
        echo "data: {}\n\n";
    }

    ob_flush();
    flush();
    sleep(1); // Đợi một giây trước khi gửi dữ liệu mới
}
?>