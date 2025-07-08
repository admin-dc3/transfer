<?php require_once 'config.php'; ?>
<?php
// Xử lý upload nhiều file (AJAX hoặc form thường)
function respond($msg, $http_code = 200) {
    http_response_code($http_code);
    echo $msg;
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file'])) {
        respond('Không nhận được file.', 400);
    }
    $files = $_FILES['file'];
    // Nếu chỉ upload 1 file, $_FILES['file']['name'] là string, nếu nhiều file là mảng
    $is_multi = is_array($files['name']);
    $count = $is_multi ? count($files['name']) : 1;
    $results = [];
    for ($i = 0; $i < $count; $i++) {
        $name = $is_multi ? $files['name'][$i] : $files['name'];
        $size = $is_multi ? $files['size'][$i] : $files['size'];
        $tmp  = $is_multi ? $files['tmp_name'][$i] : $files['tmp_name'];
        $err  = $is_multi ? $files['error'][$i] : $files['error'];
        if ($err !== UPLOAD_ERR_OK) {
            $results[] = "$name: Lỗi khi upload.";
            continue;
        }
        if ($size > 2 * 1024 * 1024 * 1024) {
            $results[] = "$name: File vượt quá 2GB.";
            continue;
        }
        $filename = basename($name);
        $target = UPLOAD_DIR . $filename;
        $j = 1;
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        $file_base = pathinfo($filename, PATHINFO_FILENAME);
        while (file_exists($target)) {
            $filename = $file_base . "_" . $j . ($file_ext ? "." . $file_ext : "");
            $target = UPLOAD_DIR . $filename;
            $j++;
        }
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0777, true);
        }
        if (move_uploaded_file($tmp, $target)) {
            $results[] = "$name: Upload thành công!";
        } else {
            $results[] = "$name: Không thể lưu file.";
        }
    }
    // Nếu là AJAX (progress), trả về text cho từng file
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_SERVER['HTTP_ORIGIN'])) {
        respond(implode("\n", $results));
    } else {
        // Nếu là submit form thường, redirect về index.php
        header('Location: index.php?msg=' . urlencode(implode(" | ", $results)));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}