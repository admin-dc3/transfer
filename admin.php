<?php require_once 'config.php'; ?>
<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
  header('Location: login.php?msg=Bạn cần đăng nhập admin!');
  exit;
}
$dir = UPLOAD_DIR;
$files = is_dir($dir) ? array_diff(scandir($dir), array('.', '..')) : [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản trị file</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container" style="max-width:900px;">
    <h1>Quản trị file đã upload</h1>
    <div style="margin-bottom:18px;">
      <a href="index.php" style="color:#fff;text-decoration:underline;">Trang upload</a>
      <a href="logout.php" style="color:#bbb;margin-left:18px;">Đăng xuất</a>
    </div>
    <?php if (isset($_GET['msg'])): ?>
      <div class="alert"> <?php echo htmlspecialchars($_GET['msg']); ?> </div>
    <?php endif; ?>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Tên file</th>
            <th>Kích thước</th>
            <th>Ngày upload</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($files)): ?>
            <tr><td colspan="4" style="text-align:center;color:#aaa;">Chưa có file nào.</td></tr>
          <?php else: ?>
            <?php foreach ($files as $file): ?>
              <tr>
                <td><?php echo htmlspecialchars($file); ?></td>
                <td><?php echo filesize($dir . $file) ? number_format(filesize($dir . $file)/1048576, 2) . ' MB' : '-'; ?></td>
                <td><?php echo date('d/m/Y H:i', filemtime($dir . $file)); ?></td>
                <td>
                  <a class="action-btn" href="download.php?file=<?php echo urlencode($file); ?>">Tải về</a>
                  <a class="action-btn" style="background:#a33;" href="delete.php?file=<?php echo urlencode($file); ?>" onclick="return confirm('Xóa file này?');">Xóa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>