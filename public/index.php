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
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Hotel Reservation System - Home</title>
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
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding-top: var(--navbar-height);
    color: var(--text-color);
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
    opacity: 0;
    animation: fadeIn 1s forwards;
}
nav.navbar .logo { font-weight: 700; font-size: 1.4rem; color: white; }
nav.navbar .nav-links a { color: white; text-decoration: none; margin-left: 1.5rem; font-weight: 600; }
nav.navbar .nav-user { color: white; margin-right: 3rem; }
nav.navbar .nav-user a { color: white; margin-left: 1rem; text-decoration: none; }

/* Hero Section */
.hero {
    position: relative;
    width: 100%;
    max-width: 900px;
    min-height: 320px;
    background-image: url('https://images.unsplash.com/photo-1501117716987-c8f7ec1c2c7c?auto=format&fit=crop&w=1400&q=80');
    background-size: cover;
    background-position: center;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
    margin: 1.5rem 0 3rem;
    text-align: center;
    color: white;
    opacity: 0;
    animation: fadeIn 1.2s forwards;
}
.hero::after {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(67,97,238,0.5);
    border-radius: 15px;
}
.hero-content { position: relative; z-index: 2; max-width: 600px; }
.hero h1 { font-size: 3rem; font-weight: 700; margin-bottom: 1rem; }
.hero p { font-size: 1.3rem; margin-bottom: 2rem; }
.hero-btn {
    background: var(--accent-color);
    color: var(--dark-color);
    padding: 1rem 2.5rem;
    font-weight: 600;
    font-size: 1.2rem;
    border-radius: 50px;
    text-decoration: none;
    transition: transform 0.3s, background 0.3s;
}
.hero-btn:hover { background: var(--secondary-color); color: white; transform: scale(1.05); }

/* Container & Search */
.container { width: 100%; max-width: 1200px; padding: 2rem; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 2rem; }
form { display: flex; gap: 0; margin-bottom: 3rem; }
form input[type="text"] { flex-grow: 1; padding: 1rem 1.25rem; border-radius: 12px 0 0 12px; border: none; outline: none; }
form button.btn { border-radius: 0 12px 12px 0; padding: 1rem 2rem; background: var(--primary-color); color: white; border: none; cursor: pointer; transition: transform 0.3s; }
form button.btn:hover { background: var(--secondary-color); transform: scale(1.05); }

/* Hotels Grid */
.hotels-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; width: 100%; margin: 0 auto 3rem; }
@media (max-width: 900px) { .hotels-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .hotels-grid { grid-template-columns: 1fr; } }

.hotel {
    background: var(--light-color);
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 5px 15px rgba(67,97,238,0.1);
    transition: box-shadow 0.3s, transform 0.3s;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s forwards;
}
.hotel:hover { box-shadow: 0 10px 25px rgba(67,97,238,0.2); transform: scale(1.03); }
.hotel h3 { color: var(--secondary-color); margin: 0.5rem 0; }
.hotel .small { font-size: 0.85rem; color: #7a7f9a; margin-bottom: 0.5rem; }
.hotel p { font-size: 1rem; line-height: 1.5; margin-bottom: 1rem; white-space: pre-line; }
.hotel a.btn { display: inline-block; padding: 0.5rem 1.25rem; font-size: 1rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.3s; }
.hotel a.btn:hover { background: var(--secondary-color); transform: scale(1.05); }

/* Staggered animation for hotel cards */
.hotel:nth-child(1) { animation-delay: 0.1s; }
.hotel:nth-child(2) { animation-delay: 0.2s; }
.hotel:nth-child(3) { animation-delay: 0.3s; }
.hotel:nth-child(4) { animation-delay: 0.4s; }
.hotel:nth-child(5) { animation-delay: 0.5s; }

/* No hotels message */
p.no-hotels { text-align: center; color: var(--error-color); font-weight: 600; margin-top: 3rem; }

/* Banner Section */
.banner-about { display: flex; max-width: 1200px; margin: 2rem auto 4rem; background: var(--light-color); border-radius: 15px; overflow: hidden; color: var(--text-color); opacity: 0; animation: fadeIn 1.2s forwards; }
.banner-image { flex:1; background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80'); background-size: cover; background-position: center; min-height: 300px; }
.banner-text { flex:1; padding: 2.5rem 3rem; display: flex; flex-direction: column; justify-content: center; }
.banner-text h2 { font-size: 2.5rem; margin-bottom: 1rem; color: var(--primary-color); }
.banner-text p { font-size: 1.1rem; line-height: 1.6; margin-bottom: 1rem; }

/* Footer */
footer.footer { margin-top: auto; width: 100%; background: var(--primary-color); color: white; padding: 2.5rem 1rem; font-size: 0.9rem; }
.footer-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 2rem; }
.footer-section { flex: 1 1 250px; }
.footer-section h3 { margin-bottom: 1rem; font-size: 1.4rem; border-bottom: 2px solid var(--accent-color); padding-bottom: 0.5rem; }
.footer-section ul { list-style: none; padding-left: 0; }
.footer-section ul li { margin-bottom: 0.6rem; }
.footer-section ul li a { color: white; text-decoration: none; }
.footer-bottom { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; opacity: 0.8; }
@media (max-width: 720px) { .footer-container { flex-direction: column; align-items: center; text-align: center; } .footer-section { flex: unset; } }

/* Animations */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>

<nav class="navbar">
    <div class="logo">LankaShen 🏤</div>
    <div class="nav-links">
        <?php if ($user): ?>
            <span class="nav-user">👋 Hello, <?=htmlspecialchars($user['name'])?></span>
            <a href="index.php">Home</a>
            <a href="#hotels">Hotels</a>
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

<section class="hero">
    <div class="hero-content">
        <h1>Welcome to Hotel Reservation System</h1>
        <p>Discover and book your perfect stay in just a few clicks.</p>
        <a href="#hotels" class="btn hero-btn">Book Now</a>
    </div>
</section>

<!-- Hotels Section -->
<div class="container" id="hotels">
    <form method="get" action="">
        <input type="text" name="q" placeholder="Search hotels or location" value="<?=htmlspecialchars($q)?>" />
        <button class="btn" type="submit">Search</button>
    </form>

    <?php if (empty($hotels)): ?>
        <p class="no-hotels">No hotels found.</p>
    <?php else: ?>
        <div class="hotels-grid">
            <?php foreach($hotels as $h): ?>
                <?php $img = !empty($h['image']) ? $h['image'] : 'default.jpg'; ?>
                <div class="hotel">
                    <img src="uploads/<?=htmlspecialchars($img)?>" alt="<?=htmlspecialchars($h['name'])?>" style="width:100%; height:200px; object-fit:cover; border-radius:8px; margin-bottom:1rem;">
                    <h3><?=htmlspecialchars($h['name'])?></h3>
                    <p class="small"><?=htmlspecialchars($h['location'])?></p>
                    <p><?=nl2br(htmlspecialchars($h['description']))?></p>
                    <a class="btn" href="hotel.php?id=<?=(int)$h['id']?>">View &amp; Book</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<section class="banner-about">
  <div class="banner-image"></div>
  <div class="banner-text">
    <h2>About Our Hotel Reservation System</h2>
    <p>We provide you with an easy-to-use platform to find and book the best hotels at great prices.</p>
    <p>Enjoy exclusive deals, verified reviews, and a smooth booking experience. Start exploring now!</p>
  </div>
</section>

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
