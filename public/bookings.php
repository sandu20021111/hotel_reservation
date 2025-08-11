<?php
require 'config.php';
if (!is_logged_in()) { 
    header('Location: login.php'); 
    exit; 
}

$user = current_user($pdo);

// Fetch all bookings for this user with hotel and room info
$stmt = $pdo->prepare(
    'SELECT b.*, h.name AS hotel_name, r.type AS room_type 
     FROM bookings b 
     JOIN hotels h ON b.hotel_id = h.id 
     JOIN rooms r ON b.room_id = r.id 
     WHERE b.user_id = ? 
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();
?>
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --error-color: #f72585;
        --success-color: #4ad66d;
        --text-color: #2b2d42;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --navbar-height: 60px;
    }
    * {
        margin: 0; padding: 0; box-sizing: border-box;
    }
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding-top: var(--navbar-height);
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        overflow-x: hidden;
    }
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
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        z-index: 1000;
    }
    nav.navbar .logo {
        font-weight: 700;
        font-size: 1.4rem;
        color: white;
        user-select: none;
    }
    nav.navbar .nav-links a {
        color: white;
        text-decoration: none;
        margin-left: 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        transition: color 0.3s ease;
    }
    nav.navbar .nav-links a:hover {
        color: var(--accent-color);
        text-decoration: none;
    }
    nav.navbar .nav-user {
        font-weight: 600;
        font-size: 0.95rem;
        color: white;
        user-select: none;
        margin-right: 3rem;
    }
    nav.navbar .nav-user a {
        color: white;
        margin-left: 1rem;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    nav.navbar .nav-user a:hover {
        color: var(--accent-color);
        text-decoration: underline;
    }

    /* Footer */
    footer.footer {
        margin-top: auto;
        width: 100%;
        background: var(--primary-color);
        color: white;
        padding: 2.5rem 1rem 1rem;
        font-size: 0.9rem;
        user-select: none;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.3);
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .footer-section {
        flex: 1 1 250px;
    }

    .footer-section h3 {
        margin-bottom: 1rem;
        font-size: 1.4rem;
        border-bottom: 2px solid var(--accent-color);
        padding-bottom: 0.5rem;
    }

    .footer-section ul {
        list-style: none;
        padding-left: 0;
    }

    .footer-section ul li {
        margin-bottom: 0.6rem;
    }

    .footer-section ul li a {
        color: white;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-section ul li a:hover {
        color: var(--accent-color);
        text-decoration: underline;
    }

    .footer-section p,
    .footer-section a {
        font-size: 1rem;
        color: white;
        text-decoration: none;
    }

    .footer-section a:hover {
        color: var(--accent-color);
        text-decoration: underline;
    }

    .footer-bottom {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* Responsive */
    @media (max-width: 720px) {
        .footer-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .footer-section {
            flex: unset;
        }
    }

    </style>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="css/styles.css">
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
    <h2>My Bookings</h2>

    <?php if (isset($_GET['show'])): 
        $bId = (int)$_GET['show'];
        $s = $pdo->prepare(
            'SELECT b.*, h.name AS hotel_name, r.type AS room_type 
             FROM bookings b 
             JOIN hotels h ON b.hotel_id = h.id 
             JOIN rooms r ON b.room_id = r.id 
             WHERE b.id = ?'
        );
        $s->execute([$bId]); 
        $one = $s->fetch();
        if ($one): ?>
            <div style="padding:10px; background:#e6ffe6; border:1px solid #b2d8b2; margin-bottom:15px;">
                <strong>Booking Confirmed!</strong><br>
                Booking #: <?=htmlspecialchars($one['id'])?><br>
                Hotel: <?=htmlspecialchars($one['hotel_name'])?><br>
                Room: <?=htmlspecialchars($one['room_type'])?><br>
                Dates: <?=htmlspecialchars($one['checkin_date'])?> to <?=htmlspecialchars($one['checkout_date'])?>
            </div>
        <?php endif; 
    endif; ?>

    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Hotel</th>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($bookings as $b): ?>
            <tr>
                <td><?=htmlspecialchars($b['id'])?></td>
                <td><?=htmlspecialchars($b['hotel_name'])?></td>
                <td><?=htmlspecialchars($b['room_type'])?></td>
                <td><?=htmlspecialchars($b['checkin_date'])?></td>
                <td><?=htmlspecialchars($b['checkout_date'])?></td>
                <td><?=htmlspecialchars($b['status'])?></td>
                <td>
                    <?php if($b['status'] === 'booked' && $b['checkin_date'] > date('Y-m-d')): ?>
                        <a href="cancel.php?id=<?=$b['id']?>" onclick="return confirm('Cancel this booking?')">Cancel</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
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

</body>
</html>
