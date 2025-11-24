<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

header('Content-Type: application/json');

$allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$allowedVideoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];

$formatUpload = function ($upload) use ($allowedImageExtensions, $allowedVideoExtensions) {
    $extension = strtolower(pathinfo($upload['filepath'], PATHINFO_EXTENSION));
    $type = in_array($extension, $allowedVideoExtensions, true) ? 'video' : (in_array($extension, $allowedImageExtensions, true) ? 'image' : 'file');

    return array_merge($upload, [
        'file_type' => $type,
        'url' => '../' . ltrim($upload['filepath'], '/'),
        'uploaded_at_human' => date('M d, Y', strtotime($upload['uploaded_at']))
    ]);
};

try {
    $stmt = $pdo->query("SELECT id, filename, filepath, uploaded_at FROM uploads ORDER BY uploaded_at DESC");
    $uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $media = array_map($formatUpload, $uploads);

    echo json_encode([
        'success' => true,
        'media' => $media,
        'images' => $media // backwards compatibility with existing JS
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
