<?php require_once 'config.php'; ?>
<?php
// Xử lý upload nhiều file (AJAX hoặc form thường)
function respond($msg, $http_code = 200, $json = false) {
    http_response_code($http_code);
    if ($json) {
        header('Content-Type: application/json');
        echo json_encode($msg);
    } else {
        echo $msg;
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file'])) {
        respond(['error' => 'Không nhận được file.'], 400, true);
    }
    $files = $_FILES['file'];
    // Nếu chỉ upload 1 file, $_FILES['file']['name'] là string, nếu nhiều file là mảng
    $is_multi = is_array($files['name']);
    $count = $is_multi ? count($files['name']) : 1;
    $results = [];
    $conflicts = [];
    $actions = isset($_POST['actions']) ? json_decode($_POST['actions'], true) : [];
    for ($i = 0; $i < $count; $i++) {
        $name = $is_multi ? $files['name'][$i] : $files['name'];
        $size = $is_multi ? $files['size'][$i] : $files['size'];
        $tmp  = $is_multi ? $files['tmp_name'][$i] : $files['tmp_name'];
        $err  = $is_multi ? $files['error'][$i] : $files['error'];
        if ($err !== UPLOAD_ERR_OK) {
            $results[] = "$name: Lỗi khi upload (mã lỗi $err).";
            continue;
        }
        if ($size > 2 * 1024 * 1024 * 1024) {
            $results[] = "$name: File vượt quá 2GB.";
            continue;
        }
        $filename = basename($name);
        $target = UPLOAD_DIR . $filename;
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        $file_base = pathinfo($filename, PATHINFO_FILENAME);
        $exists = file_exists($target);
        $action = isset($actions[$filename]) ? $actions[$filename] : null;
        if ($exists && !$action) {
            $conflicts[] = $filename;
            continue;
        }
        if ($exists && $action === 'skip') {
            $results[] = "$filename: Bỏ qua.";
            continue;
        }
        if ($exists && $action === 'rename') {
            $j = 1;
            $newname = $file_base . "_" . $j . ($file_ext ? "." . $file_ext : "");
            $newtarget = UPLOAD_DIR . $newname;
            while (file_exists($newtarget)) {
                $j++;
                $newname = $file_base . "_" . $j . ($file_ext ? "." . $file_ext : "");
                $newtarget = UPLOAD_DIR . $newname;
            }
            $filename = $newname;
            $target = $newtarget;
        }
        // Nếu ghi đè thì giữ nguyên $target
        if (!is_dir(UPLOAD_DIR)) {
            if (!mkdir(UPLOAD_DIR, 0777, true)) {
                $results[] = "$filename: Không tạo được thư mục uploads. Kiểm tra quyền ghi!";
                continue;
            }
        }
        if (!is_writable(UPLOAD_DIR)) {
            $results[] = "$filename: Thư mục uploads không cho phép ghi. Hãy cấp quyền ghi (chmod 777)!";
            continue;
        }
        if (!is_uploaded_file($tmp)) {
            $results[] = "$filename: File tạm không hợp lệ.";
            continue;
        }
        if (move_uploaded_file($tmp, $target)) {
            $results[] = "$filename: Upload thành công!";
        } else {
            $results[] = "$filename: Không thể lưu file. Kiểm tra quyền ghi hoặc lỗi hệ thống.";
        }
    }
    if (!empty($conflicts)) {
        respond(['conflicts' => $conflicts], 409, true);
    }
    // Luôn trả về JSON nếu là AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_SERVER['HTTP_ORIGIN'])) {
        respond($results, 200, true);
    } else {
        // Nếu là submit form thường, redirect về index.php
        header('Location: index.php?msg=' . urlencode(implode(" | ", $results)));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}