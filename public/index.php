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
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 2rem;
        margin: 1.5rem 0 3rem;
        text-align: center;
        overflow: hidden;
        color: white;
        animation: fadeSlideUp 0.8s ease forwards;
        opacity: 0;
    }
    .hero::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(67, 97, 238, 0.5); /* overlay */
        border-radius: 15px;
        z-index: 1;
    }
    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
    }
    .hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 3px 10px rgba(0,0,0,0.7);
    }
    .hero p {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        text-shadow: 0 2px 8px rgba(0,0,0,0.6);
    }
    .hero-btn {
        background: var(--accent-color);
        color: var(--dark-color);
        padding: 1rem 2.5rem;
        font-weight: 600;
        font-size: 1.2rem;
        border-radius: 50px;
        text-decoration: none;
        box-shadow: 0 5px 20px rgba(76, 201, 240, 0.7);
        transition: background 0.3s ease, transform 0.3s ease;
    }
    .hero-btn:hover {
        background: var(--secondary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 7px 25px rgba(67, 97, 238, 0.9);
    }

    /* Main Container */

    .container {
        width: 100%;
        max-width: 1200px;
        padding: 2rem;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2rem;
        position: relative;
    }
    .container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(90deg);
        animation: rainbow 8s linear infinite;
    }
    @keyframes fadeSlideUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes rainbow {
        0% {background-position: 0% 50%;}
        50% {background-position: 100% 50%;}
        100% {background-position: 0% 50%;}
    }

    .header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .header h1 {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 0.3rem;
        font-size: 2.5rem;
    }

    nav.container-nav {
        margin-bottom: 2rem;
        text-align: center;
    }
    nav.container-nav a {
        color: var(--primary-color);
        text-decoration: none;
        margin: 0 1rem;
        font-weight: 600;
        transition: color 0.3s ease;
        font-size: 1.1rem;
    }
    nav.container-nav a:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    form {
        display: flex;
        gap: 0;
        margin-bottom: 3rem;
    }
    form input[type="text"] {
        flex-grow: 1;
        font-size: 1.2rem;
        padding: 1rem 1.25rem;
        border: none;
        border-radius: 12px 0 0 12px;
        outline: none;
        box-shadow: 0 0 0 2px #e9ecef inset;
        transition: box-shadow 0.3s ease;
    }
    form input[type="text"]:focus {
        box-shadow: 0 0 0 3px rgba(67,97,238,0.5);
    }
    form button.btn {
        border-radius: 0 12px 12px 0;
        font-size: 1.2rem;
        padding: 1.2rem 2rem;
        background: var(--primary-color);
        color: white;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s ease, transform 0.3s ease;
    }
    form button.btn:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67,97,238,0.3);
    }

    /* Grid container for hotels */
    .hotels-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto 3rem;
        animation: fadeSlideUp 0.8s ease forwards;
        opacity: 0;
        animation-delay: 0.2s;
    }

    /* Responsive: 2 columns for tablets, 1 column for phones */
    @media (max-width: 900px) {
        .hotels-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 600px) {
        .hotels-grid {
            grid-template-columns: 1fr;
        }
    }

    .hotel {
        background: var(--light-color);
        border-radius: 12px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.1);
        transition: box-shadow 0.3s ease;
        color: var(--text-color);
        opacity: 0;
        transform: translateY(20px);
        animation: fadeSlideUp 0.6s ease forwards;
    }
    .hotel:hover {
        box-shadow: 0 10px 30px rgba(67, 97, 238, 0.2);
    }
    .hotel h3 {
        color: var(--secondary-color);
        margin-bottom: 0.25rem;
        font-size: 1.5rem;
    }
    .hotel .small {
        font-size: 0.85rem;
        color: #7a7f9a;
        margin-bottom: 0.75rem;
    }
    .hotel p {
        font-size: 1rem;
        line-height: 1.5;
        margin-bottom: 1rem;
        white-space: pre-line;
    }
    .hotel a.btn {
        display: inline-block;
        width: auto;
        padding: 0.5rem 1.25rem;
        font-size: 1rem;
        text-transform: none;
        letter-spacing: normal;
        background: var(--primary-color);
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.3s ease;
    }
    .hotel a.btn:hover {
        background: var(--secondary-color);
    }
    p.no-hotels {
        text-align: center;
        color: var(--error-color);
        font-weight: 600;
        margin-top: 3rem;
        font-size: 1.2rem;
        opacity: 0;
        animation: fadeSlideUp 0.8s ease forwards;
        animation-delay: 0.2s;
    }

    /* Animation stagger for hotel cards */
    .hotels-grid .hotel:nth-child(3n+1) {
        animation-delay: 0.3s;
    }
    .hotels-grid .hotel:nth-child(3n+2) {
        animation-delay: 0.5s;
    }
    .hotels-grid .hotel:nth-child(3n+3) {
        animation-delay: 0.7s;
    }

    /* Banner About Section */
    .banner-about {
      display: flex;
      max-width: 1200px;
      margin: 2rem auto 4rem;
      background: var(--light-color);
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(67, 97, 238, 0.1);
      overflow: hidden;
      color: var(--text-color);
      opacity: 0;
      animation: fadeSlideUp 0.8s ease forwards;
      animation-delay: 0.8s;
    }

    .banner-image {
      flex: 1;
      background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80');
      background-size: cover;
      background-position: center;
      min-height: 300px;
    }

    .banner-text {
      flex: 1;
      padding: 2.5rem 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .banner-text h2 {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      color: var(--primary-color);
    }

    .banner-text p {
      font-size: 1.1rem;
      line-height: 1.6;
      margin-bottom: 1rem;
      color: var(--text-color);
    }

    /* Responsive for mobile */
    @media (max-width: 768px) {
      .banner-about {
        flex-direction: column;
      }
      .banner-image {
        min-height: 200px;
      }
      .banner-text {
        padding: 1.5rem 2rem;
      }
      .banner-text h2 {
        font-size: 2rem;
      }
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

<section class="hero">
    <div class="hero-content">
        <h1>Welcome to Hotel Reservation System</h1>
        <p>Discover and book your perfect stay in just a few clicks.</p>
        <a href="#hotels" class="btn hero-btn">Book Now</a>
    </div>
</section>

<div class="container" id="search">
    <!-- Search form -->
    <form method="get" action="">
        <input type="text" name="q" placeholder="Search hotels or location" value="<?=htmlspecialchars($q)?>" />
        <button class="btn" type="submit">Search</button>
    </form>

    <?php if (empty($hotels)): ?>
        <p class="no-hotels">No hotels found.</p>
    <?php else: ?>
        <div class="hotels-grid" id="hotels">
            <?php foreach($hotels as $h): ?>
                <div class="hotel">
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
    <p>
      We provide you with an easy-to-use platform to find and book the best hotels
      at great prices. Whether you’re traveling for business or leisure, our
      system helps you discover perfect stays and make hassle-free reservations.
    </p>
    <p>
      Enjoy exclusive deals, verified reviews, and a smooth booking experience.
      Start exploring now and make your next trip unforgettable!
    </p>
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
