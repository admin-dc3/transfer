<?php require_once 'config.php'; ?>
<?php
// Xử lý tải về
if (!isset($_GET['file'])) {
    http_response_code(400);
    echo 'Thiếu tên file.';
    exit;
}
$file = basename($_GET['file']);
$path = UPLOAD_DIR . $file;
if (!file_exists($path)) {
    http_response_code(404);
    echo 'File không tồn tại.';
    exit;
}
// Header cho phép tải mọi định dạng
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;