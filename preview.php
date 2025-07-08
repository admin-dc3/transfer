<?php require_once 'config.php'; ?>
<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403); echo 'Không có quyền.'; exit;
}
if (!isset($_GET['file'])) {
    http_response_code(400); echo 'Thiếu tên file.'; exit;
}
$file = basename($_GET['file']);
$path = UPLOAD_DIR . $file;
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$allow = ['txt','csv','md','log','json','xml','html','css','js','php'];
if (!in_array($ext, $allow)) {
    http_response_code(415); echo 'Không hỗ trợ xem trước.'; exit;
}
if (!file_exists($path) || filesize($path) > 1024*1024) {
    http_response_code(404); echo 'File không tồn tại hoặc quá lớn.'; exit;
}
$content = file_get_contents($path);
echo $content; 