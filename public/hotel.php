<?php
require 'config.php';
$user = current_user($pdo);

$id = (int)($_GET['id'] ?? 0);

// Fetch hotel details
$hotelStmt = $pdo->prepare('SELECT * FROM hotels WHERE id = ?');
$hotelStmt->execute([$id]);
$hotel = $hotelStmt->fetch();

if (!$hotel) { 
    echo 'Hotel not found'; 
    exit; 
}

// Fetch rooms for the hotel
$roomStmt = $pdo->prepare('SELECT * FROM rooms WHERE hotel_id = ?');
$roomStmt->execute([$id]);
$rooms = $roomStmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=htmlspecialchars($hotel['name'])?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <a href="index.php">← Back to Hotels</a>
    <h2><?=htmlspecialchars($hotel['name'])?></h2>
    <p class="small"><?=htmlspecialchars($hotel['location'])?></p>
    <p><?=nl2br(htmlspecialchars($hotel['description']))?></p>

    <h3>Make a Booking</h3>

    <?php if(!$user): ?>
        <p class="small">Please <a href="login.php">login</a> to book a room.</p>
    <?php elseif(empty($rooms)): ?>
        <p>No rooms available for this hotel at the moment.</p>
    <?php else: ?>
        <form method="post" action="book.php">
            <input type="hidden" name="hotel_id" value="<?=$hotel['id']?>">

            <div class="form-row">
                <label>Room Type</label>
                <select name="room_id" required>
                    <?php foreach($rooms as $r): ?>
                        <option value="<?=$r['id']?>">
                            <?=htmlspecialchars($r['type'])?> — Rs <?=number_format($r['price'],2)?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Check-in Date</label>
                <input type="date" name="checkin" required>
            </div>

            <div class="form-row">
                <label>Check-out Date</label>
                <input type="date" name="checkout" required>
            </div>

            <button class="btn" type="submit">Confirm Booking</button>
        </form>
    <?php endif; ?>
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

