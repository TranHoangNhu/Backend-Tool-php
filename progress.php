<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['progress'])) {
    $_SESSION['progress'] = ['completedPages' => 0, 'totalPages' => 0];
}

// Giả lập cập nhật tiến độ
$progress = $_SESSION['progress'];
$progress['completedPages'] += rand(1, 5);
$progress['totalPages'] = 100;

if ($progress['completedPages'] >= $progress['totalPages']) {
    $progress['completedPages'] = $progress['totalPages'];
}

// Cập nhật session
$_SESSION['progress'] = $progress;

echo json_encode($progress);
?>
