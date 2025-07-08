<?php require_once 'config.php'; ?>
<?php
// Xử lý đăng nhập
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = isset($_POST['user']) ? trim($_POST['user']) : '';
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $msg = 'Sai tài khoản hoặc mật khẩu!';
    }
} elseif (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập admin</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #18191a; }
    .login-box {
      background: #232526;
      border-radius: 18px;
      box-shadow: 0 6px 32px rgba(0,0,0,0.45);
      padding: 38px 32px 28px 32px;
      max-width: 370px;
      width: 100%;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      animation: fadeIn 0.7s cubic-bezier(.4,0,.2,1);
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(30px);} to { opacity: 1; transform: none; } }
    .login-icon {
      font-size: 2.8rem;
      margin-bottom: 12px;
      color: #fff;
      background: linear-gradient(135deg,#444 60%,#111 100%);
      border-radius: 50%;
      width: 60px; height: 60px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 2px 12px #0005;
    }
    .login-title {
      color: #fff;
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 18px;
      letter-spacing: 1px;
    }
    .login-form {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .login-form label {
      color: #bbb;
      font-size: 1rem;
      margin-bottom: 2px;
    }
    .login-form input[type="text"], .login-form input[type="password"] {
      background: #2c2f31;
      color: #f1f1f1;
      border: 1.5px solid #444;
      border-radius: 8px;
      padding: 11px 12px;
      font-size: 1rem;
      outline: none;
      transition: border 0.2s, box-shadow 0.2s;
    }
    .login-form input[type="text"]:focus, .login-form input[type="password"]:focus {
      border: 1.5px solid #888;
      box-shadow: 0 0 0 2px #4443;
    }
    .show-pass {
      position: relative;
      display: flex;
      align-items: center;
    }
    .show-pass input {
      flex: 1;
    }
    .toggle-pass {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      color: #aaa;
      font-size: 1.1em;
      cursor: pointer;
      padding: 0 2px;
      transition: color 0.2s;
    }
    .toggle-pass:hover { color: #fff; }
    .login-form input[type="submit"] {
      background: linear-gradient(90deg, #444 0%, #222 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 12px 0;
      font-size: 1.08rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 8px;
      transition: background 0.2s, transform 0.2s;
    }
    .login-form input[type="submit"]:hover {
      background: linear-gradient(90deg, #666 0%, #333 100%);
      transform: translateY(-2px) scale(1.03);
    }
    .alert {
      margin-bottom: 18px;
      width: 100%;
      text-align: center;
    }
    .login-back {
      margin-top: 24px;
      color: #bbb;
      text-align: center;
      display: block;
      text-decoration: none;
      font-size: 1rem;
      transition: color 0.2s;
    }
    .login-back:hover { color: #fff; }
    @media (max-width: 600px) {
      .login-box { padding: 18px 4vw 16px 4vw; }
      .login-title { font-size: 1.1rem; }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="login-icon">🔒</div>
    <div class="login-title">Đăng nhập quản trị</div>
    <?php if ($msg): ?>
      <div class="alert"> <?php echo htmlspecialchars($msg); ?> </div>
    <?php endif; ?>
    <form class="login-form" method="post" action="" autocomplete="on">
      <label for="user">Tài khoản</label>
      <input type="text" name="user" id="user" required autocomplete="username" placeholder="Nhập tài khoản admin">
      <label for="pass">Mật khẩu</label>
      <div class="show-pass">
        <input type="password" name="pass" id="pass" required autocomplete="current-password" placeholder="Nhập mật khẩu">
        <button type="button" class="toggle-pass" tabindex="-1" onclick="togglePassword()" aria-label="Hiện/ẩn mật khẩu">👁️</button>
      </div>
      <input type="submit" value="Đăng nhập">
    </form>
    <a href="index.php" class="login-back">← Quay lại trang upload</a>
  </div>
  <script>
    function togglePassword() {
      var pass = document.getElementById('pass');
      pass.type = pass.type === 'password' ? 'text' : 'password';
    }
    // Tự động focus vào ô tài khoản khi vào trang
    window.onload = function() {
      var user = document.getElementById('user');
      if(user) user.focus();
    }
  </script>
</body>
</html>