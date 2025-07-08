<?php require_once 'config.php'; ?>
<?php
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
  header('Location: login.php?msg=Bạn cần đăng nhập admin!');
  exit;
}
$dir = UPLOAD_DIR;
$files = is_dir($dir) ? array_diff(scandir($dir), array('.', '..')) : [];
function is_image($file) {
  $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
  return in_array($ext, ['jpg','jpeg','png','gif','webp']);
}
function is_text($file) {
  $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
  return in_array($ext, ['txt','csv','md','log','json','xml','html','css','js','php']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản trị file</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .modal-bg {
      position: fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.7); z-index:9999; display:none; align-items:center; justify-content:center;
    }
    .modal-box {
      background:#232526; border-radius:12px; max-width:90vw; max-height:90vh; padding:24px 18px; box-shadow:0 4px 32px #000a; position:relative; overflow:auto;
      display:flex; flex-direction:column; align-items:center;
    }
    .modal-close {
      position:absolute; top:10px; right:18px; background:none; border:none; color:#fff; font-size:1.5rem; cursor:pointer;}
    .modal-img { max-width:70vw; max-height:70vh; border-radius:8px; box-shadow:0 2px 12px #0007; }
    .modal-text { color:#eee; background:#18191a; border-radius:8px; padding:16px; max-width:65vw; max-height:60vh; overflow:auto; font-size:1.05em; white-space:pre-wrap; }
    @media (max-width:600px) { .modal-img{max-width:95vw;max-height:50vh;} .modal-text{max-width:95vw;} }
  </style>
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
                  <?php if (is_image($file)): ?>
                    <button class="action-btn" style="background:#2a5;" onclick="showImageModal('<?php echo htmlspecialchars(urlencode($file)); ?>')">Xem trước</button>
                  <?php elseif (is_text($file)): ?>
                    <button class="action-btn" style="background:#28a;" onclick="showTextModal('<?php echo htmlspecialchars(urlencode($file)); ?>')">Xem trước</button>
                  <?php endif; ?>
                  <a class="action-btn" style="background:#a33;" href="delete.php?file=<?php echo urlencode($file); ?>" onclick="return confirm('Xóa file này?');">Xóa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="modal-bg" id="modalBg">
    <div class="modal-box" id="modalBox">
      <button class="modal-close" onclick="closeModal()">×</button>
      <div id="modalContent"></div>
    </div>
  </div>
  <script>
    function showImageModal(file) {
      var modalBg = document.getElementById('modalBg');
      var modalContent = document.getElementById('modalContent');
      modalContent.innerHTML = '<img class="modal-img" src="uploads/' + file + '" alt="Ảnh xem trước">';
      modalBg.style.display = 'flex';
    }
    function showTextModal(file) {
      var modalBg = document.getElementById('modalBg');
      var modalContent = document.getElementById('modalContent');
      modalContent.innerHTML = '<div style="color:#aaa;">Đang tải nội dung...</div>';
      modalBg.style.display = 'flex';
      fetch('preview.php?file=' + file)
        .then(r => r.text())
        .then(txt => {
          modalContent.innerHTML = '<div class="modal-text">' + escapeHtml(txt) + '</div>';
        })
        .catch(() => {
          modalContent.innerHTML = '<div style="color:#f66;">Không thể tải nội dung file.</div>';
        });
    }
    function closeModal() {
      document.getElementById('modalBg').style.display = 'none';
      document.getElementById('modalContent').innerHTML = '';
    }
    function escapeHtml(text) {
      var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
      return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape') closeModal();
    });
  </script>
</body>
</html>