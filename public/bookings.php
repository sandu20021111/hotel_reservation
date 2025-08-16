<?php
require 'config.php';
if (!is_logged_in()) { 
    header('Location: login.php'); 
    exit; 
}

$user = current_user($pdo);

// Fetch all bookings for this user with hotel and room info
$stmt = $pdo->prepare(
    'SELECT b.*, h.name AS hotel_name, h.image AS hotel_image, r.type AS room_type 
     FROM bookings b 
     JOIN hotels h ON b.hotel_id = h.id 
     JOIN rooms r ON b.room_id = r.id 
     WHERE b.user_id = ? 
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --danger: #f72585;
            --success: #4ad66d;
            --bg-light: #f5f7fa;
            --text-dark: #2b2d42;
        }
        * {margin:0; padding:0; box-sizing:border-box;}
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        nav.navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background: var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        nav.navbar .logo {font-weight: 700; font-size: 1.5rem; color: white;}
        nav.navbar .nav-links a,
        nav.navbar .nav-user a {color: white; text-decoration: none; margin-left: 1.2rem; font-weight: 500; transition: color .3s;}
        nav.navbar .nav-links a:hover,
        nav.navbar .nav-user a:hover {color: var(--accent);}
        nav.navbar .nav-user {font-weight: 600; color: white; margin-right: 1rem;}

        /* Page Container */
        .container {
            max-width: 1200px;
            margin: 100px auto 40px;
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.8rem;
        }

        /* Booking Card */
        .booking-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeInUp 0.6s forwards;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .booking-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 28px rgba(0,0,0,0.2);
        }

        .booking-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .booking-card:hover img {
            transform: scale(1.05);
        }

        .booking-card .content {
            padding: 1rem 1.2rem 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .booking-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
            transition: color 0.3s;
        }
        .booking-card p {font-size: 0.95rem; margin-bottom: 0.5rem;}
        .booking-card .dates {font-weight: 600;}
        .booking-card .status {
            font-weight: 600;
            margin: 0.5rem 0;
        }
        .status-booked {color: var(--success);}
        .status-cancelled {color: var(--danger);}
        .booking-card a.btn {
            margin-top: auto;
            text-align: center;
            display: inline-block;
            padding: 0.6rem;
            background: var(--danger);
            color: white;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.3s, transform 0.3s;
        }
        .booking-card a.btn:hover {
            background: #d10b60;
            transform: scale(1.05);
        }

        /* Fade-in animation */
        @keyframes fadeInUp {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        /* Staggered animation delay */
        .booking-card:nth-child(1) {animation-delay: 0.1s;}
        .booking-card:nth-child(2) {animation-delay: 0.2s;}
        .booking-card:nth-child(3) {animation-delay: 0.3s;}
        .booking-card:nth-child(4) {animation-delay: 0.4s;}
        .booking-card:nth-child(5) {animation-delay: 0.5s;}

        /* Footer */
        footer.footer {
            margin-top: auto;
            background: var(--primary);
            color: white;
            padding: 2rem 1rem 1rem;
        }
        .footer-container {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
        }
        .footer-section h3 {margin-bottom:.8rem; font-size:1.2rem; border-bottom:2px solid var(--accent); padding-bottom:.4rem;}
        .footer-section ul {list-style:none; padding:0;}
        .footer-section ul li {margin-bottom:.5rem;}
        .footer-section ul li a {color:white; text-decoration:none; transition: color .3s;}
        .footer-section ul li a:hover {color: var(--accent);}
        .footer-bottom {text-align:center; margin-top:1.5rem; font-size:0.85rem; opacity:0.8;}

        /* Responsive */
        @media(max-width: 768px){
            .footer-container {flex-direction: column; text-align:center;}
            nav.navbar {flex-direction: column; height:auto; padding: 1rem;}
            nav.navbar .nav-links {margin-top:.5rem;}
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
    <?php if(empty($bookings)): ?>
        <p style="grid-column:1/-1; text-align:center; font-size:1.2rem; color:var(--text-dark);">No bookings yet.</p>
    <?php else: ?>
        <?php foreach($bookings as $b): 
            $img = !empty($b['hotel_image']) ? $b['hotel_image'] : 'default.jpg';
        ?>
            <div class="booking-card">
                <img src="uploads/<?=htmlspecialchars($img)?>" alt="<?=htmlspecialchars($b['hotel_name'])?>">
                <div class="content">
                    <h3><?=htmlspecialchars($b['hotel_name'])?></h3>
                    <p>Room: <?=htmlspecialchars($b['room_type'])?></p>
                    <p class="dates">Dates: <?=htmlspecialchars($b['checkin_date'])?> → <?=htmlspecialchars($b['checkout_date'])?></p>
                    <p class="status status-<?=htmlspecialchars(strtolower($b['status']))?>"><?=htmlspecialchars($b['status'])?></p>
                    <?php if($b['status'] === 'booked' && $b['checkin_date'] > date('Y-m-d')): ?>
                        <a href="cancel.php?id=<?=$b['id']?>" class="btn" onclick="return confirm('Cancel this booking?')">Cancel Booking</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
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

</body>
</html>
