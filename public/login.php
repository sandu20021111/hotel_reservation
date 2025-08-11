<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Both fields are required.";
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
        <p class="error"><?=htmlspecialchars($error)?></p>
    <?php endif; ?>
    <form method="post">
        <div class="form-row">
            <label>Email</label>
            <input type="email" name="email" value="<?=htmlspecialchars($email ?? '')?>" required>
        </div>
        <div class="form-row">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button class="btn" type="submit">Login</button>
    </form>
</div>
</body>
</html>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
  let form = e.target;

  // Example: Check check-in/out dates in booking form
  if (form.checkin && form.checkout) {
    let checkin = new Date(form.checkin.value);
    let checkout = new Date(form.checkout.value);
    if (checkin >= checkout) {
      alert('Check-out date must be after check-in date.');
      e.preventDefault();
      return false;
    }
  }

  // Example: Password match check on registration form
  if (form.password && form.confirm) {
    if (form.password.value !== form.confirm.value) {
      alert('Passwords do not match.');
      e.preventDefault();
      return false;
    }
  }
});
</script>

