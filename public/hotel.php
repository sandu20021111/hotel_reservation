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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --error-color: #f72585;
        --success-color: #4ad66d;
        --text-color: #2b2d42;
        --light-bg: #f9f9fb;
        --card-bg: #fff;
        --navbar-height: 60px;
    }
    * {margin: 0; padding: 0; box-sizing: border-box;}
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        color: var(--text-color);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        padding-top: var(--navbar-height);
    }
    a {text-decoration: none; color: var(--primary-color);}
    a:hover {color: var(--secondary-color);}

    /* Navbar */
    nav.navbar {
        position: fixed;
        top: 0; left: 0; right: 0;
        height: var(--navbar-height);
        background: var(--primary-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        z-index: 1000;
    }
    nav.navbar .logo {
        font-weight: 700;
        font-size: 1.4rem;
        color: white;
    }
    nav.navbar .nav-links a {
        color: white;
        margin-left: 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        transition: 0.3s;
    }
    nav.navbar .nav-links a:hover {color: var(--accent-color);}
    nav.navbar .nav-user {color: white; margin-right: 1rem;}

    /* Container */
    .container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 2rem;
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        animation: fadeSlideUp 0.6s ease forwards;
    }
    .container img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .container h2 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }
    .container p.small {
        font-size: 0.9rem;
        opacity: 0.8;
        margin-bottom: 1rem;
    }
    .container p {margin-bottom: 1rem; line-height: 1.6;}
    .container a {font-weight: 600;}

    /* Form */
    form {
        margin-top: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }
    .form-row {
        display: flex;
        flex-direction: column;
    }
    .form-row label {
        font-weight: 600;
        margin-bottom: 0.4rem;
        color: var(--text-color);
    }
    .form-row input, 
    .form-row select {
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: border 0.3s;
    }
    .form-row input:focus, 
    .form-row select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(67,97,238,0.2);
    }
    .btn {
        padding: 0.9rem;
        background: var(--primary-color);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }
    .btn:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }

    /* Footer */
    footer.footer {
        margin-top: auto;
        background: var(--primary-color);
        color: white;
        padding: 2.5rem 1rem 1rem;
    }
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 2rem;
    }
    .footer-section {flex: 1 1 250px;}
    .footer-section h3 {
        margin-bottom: 1rem;
        font-size: 1.2rem;
        border-bottom: 2px solid var(--accent-color);
        padding-bottom: 0.5rem;
    }
    .footer-section ul {list-style: none;}
    .footer-section ul li {margin-bottom: 0.6rem;}
    .footer-section ul li a {color: white;}
    .footer-section ul li a:hover {color: var(--accent-color);}
    .footer-bottom {text-align: center; margin-top: 1.5rem; font-size: 0.85rem; opacity: 0.8;}

    /* Animations */
    @keyframes fadeSlideUp {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">LankaShen 🏤</div>
    <div class="nav-links">
        <?php if ($user): ?>
            <span class="nav-user">👋 Hello, <?=htmlspecialchars($user['name'])?></span>
            <a href="index.php">Home</a>
            <a href="bookings.php">Bookings</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="index.php">Home</a>
            <a href="#hotels">Hotels</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

    <!-- Hotel Image -->
    <?php $img = !empty($hotel['image']) ? $hotel['image'] : 'default.jpg'; ?>
    <img src="uploads/<?=htmlspecialchars($img)?>" 
         alt="<?=htmlspecialchars($hotel['name'])?>">

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

<footer class="footer">
  <div class="footer-container">
    <div class="footer-section">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="bookings.php">Bookings</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Contact Us</h3>
      <p>Email: <a href="mailto:support@hotelreservation.com">support@hotelreservation.com</a></p>
      <p>Phone: <a href="tel:+94123456789">+94 123 456 789</a></p>
      <p>Address: 123 Main Street, Colombo, Sri Lanka</p>
    </div>
  </div>
  <div class="footer-bottom">
    &copy; <?=date('Y')?> Hotel Reservation System. All rights reserved.
  </div>
</footer>

<script>
document.querySelector('form')?.addEventListener('submit', function(e) {
  let form = e.target;
  if (form.checkin && form.checkout) {
    let checkin = new Date(form.checkin.value);
    let checkout = new Date(form.checkout.value);
    if (checkin >= checkout) {
      alert('Check-out date must be after check-in date.');
      e.preventDefault();
      return false;
    }
  }
});
</script>
</body>
</html>
