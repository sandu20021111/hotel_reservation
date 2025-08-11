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
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
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
</body>
</html>
