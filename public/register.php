<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validation
    if ($name === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            // Insert user
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <h2>Create Account</h2>
    <?php if (!empty($error)): ?>
        <p class="error"><?=htmlspecialchars($error)?></p>
    <?php endif; ?>
    <form method="post">
        <div class="form-row">
            <label>Name</label>
            <input type="text" name="name" value="<?=htmlspecialchars($name ?? '')?>" required>
        </div>
        <div class="form-row">
            <label>Email</label>
            <input type="email" name="email" value="<?=htmlspecialchars($email ?? '')?>" required>
        </div>
        <div class="form-row">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-row">
            <label>Confirm Password</label>
            <input type="password" name="confirm" required>
        </div>
        <button class="btn" type="submit">Register</button>
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
