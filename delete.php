<?php require_once 'config.php'; ?>
<?php
// Chỉ cho admin xóa file
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php?msg=Bạn cần đăng nhập admin!');
    exit;
}
if (!isset($_GET['file'])) {
    header('Location: admin.php?msg=Thiếu tên file.');
    exit;
}
$file = basename($_GET['file']);
$path = UPLOAD_DIR . $file;
if (!file_exists($path)) {
    header('Location: admin.php?msg=File không tồn tại.');
    exit;
}
// Chỉ xóa file trong thư mục uploads, không cho xóa ngoài
if (strpos(realpath($path), realpath(UPLOAD_DIR)) !== 0) {
    header('Location: admin.php?msg=Không hợp lệ.');
    exit;
}
if (@unlink($path)) {
    header('Location: admin.php?msg=Đã xóa file thành công!');
} else {
    header('Location: admin.php?msg=Không thể xóa file.');
}
exit;