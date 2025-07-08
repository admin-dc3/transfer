<?php require_once 'config.php'; ?>
<?php
// Chỉ cho admin xóa file
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php?msg=Bạn cần đăng nhập admin!');
    exit;
}
// Xóa nhiều file qua POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['files']) && is_array($_POST['files'])) {
    $deleted = 0;
    $errors = [];
    foreach ($_POST['files'] as $file) {
        $file = basename($file);
        $path = UPLOAD_DIR . $file;
        if (!file_exists($path)) {
            $errors[] = "$file không tồn tại.";
            continue;
        }
        if (strpos(realpath($path), realpath(UPLOAD_DIR)) !== 0) {
            $errors[] = "$file không hợp lệ.";
            continue;
        }
        if (@unlink($path)) {
            $deleted++;
        } else {
            $errors[] = "$file không thể xóa.";
        }
    }
    $msg = "Đã xóa $deleted file.";
    if ($errors) $msg .= ' Lỗi: ' . implode(' ', $errors);
    header('Location: admin.php?msg=' . urlencode($msg));
    exit;
}
// Xóa 1 file qua GET như cũ
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