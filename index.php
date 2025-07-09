<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload File</title>
  <link rel="icon" href="a.png" type="image/png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Upload File Nhanh</h1>
    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
      <div style="margin-bottom:18px;">
        <a href="admin.php" style="color:#fff;text-decoration:underline;">Quản trị file</a>
        <a href="logout.php" style="color:#bbb;margin-left:18px;">Đăng xuất</a>
      </div>
    <?php else: ?>
      <div style="margin-bottom:18px;">
        <a href="login.php" style="color:#bbb;">Đăng nhập admin</a>
      </div>
    <?php endif; ?>
    <?php if (isset($_GET['msg'])): ?>
      <div class="alert"> <?php echo htmlspecialchars($_GET['msg']); ?> </div>
    <?php endif; ?>
    <form id="uploadForm" action="upload.php" method="post" enctype="multipart/form-data" onsubmit="return false;">
      <label for="file">Chọn file để upload (tối đa 2GB mỗi file):</label>
      <input type="file" name="file[]" id="file" required multiple>
      <input type="submit" id="uploadBtn" value="Tải lên">
    </form>
    <div id="progressArea" style="margin-top:18px;"></div>
    <div style="margin-top:32px;color:#aaa;font-size:0.98em;">
      <b>Lưu ý:</b> Guest chỉ có thể upload file, không xem được danh sách file.<br>
      Hỗ trợ mọi định dạng, file nhỏ hơn 2GB.
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>