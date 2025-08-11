<?php
require 'config.php';
$user = current_user($pdo);

// Handle search query
$q = '';
if (!empty($_GET['q'])) {
    $q = trim($_GET['q']);
    $stmt = $pdo->prepare('SELECT * FROM hotels WHERE name LIKE ? OR location LIKE ?');
    $stmt->execute(["%$q%", "%$q%"]);
    $hotels = $stmt->fetchAll();
} else {
    $hotels = $pdo->query('SELECT * FROM hotels')->fetchAll();
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hotel Reservation System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Hotels</h1>
        <nav>
            <?php if($user): ?>
                Hello, <?=htmlspecialchars($user['name'])?> |
                <a href="bookings.php">My Bookings</a> |
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="register.php">Register</a> |
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Search form -->
    <form method="get" style="margin-bottom: 20px;">
        <input type="text" name="q" placeholder="Search hotels or location"
               value="<?=htmlspecialchars($q)?>">
        <button class="btn" type="submit">Search</button>
    </form>

    <?php if (empty($hotels)): ?>
        <p>No hotels found.</p>
    <?php else: ?>
        <?php foreach($hotels as $h): ?>
            <div class="hotel">
                <h3><?=htmlspecialchars($h['name'])?></h3>
                <p class="small"><?=htmlspecialchars($h['location'])?></p>
                <p><?=nl2br(htmlspecialchars($h['description']))?></p>
                <a class="btn" href="hotel.php?id=<?=(int)$h['id']?>">View & Book</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
