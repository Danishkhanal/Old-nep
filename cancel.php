<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

session_start();

if (!isset($_GET['order']) || empty($_GET['order'])) {
    redirect('index.php');
}

$order_id = $_GET['order'];

// Fetch booking details from the database
$query = "SELECT bo.*, bd.* FROM `booking_order` bo 
          JOIN `booking_details` bd ON bo.booking_id = bd.booking_id 
          WHERE bo.order_id = ?";
$res = select($query, [$order_id], 's');

if (mysqli_num_rows($res) == 0) {
    redirect('index.php');
}

$data = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Canceled</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h3 class="card-title">Payment Canceled</h3>
            </div>
            <div class="card-body">
                <p>Your payment was canceled. Please try again to complete your booking.</p>
                <h5>Booking Details:</h5>
                <ul>
                    <li><strong>Order ID:</strong> <?= $data['order_id'] ?></li>
                    <li><strong>Room Name:</strong> <?= $data['room_name'] ?></li>
                    <li><strong>Check-In:</strong> <?= $data['check_in'] ?></li>
                    <li><strong>Check-Out:</strong> <?= $data['check_out'] ?></li>
                    <li><strong>Total Amount:</strong> NPR<?= $data['total_pay'] ?></li>
                </ul>
                <a href="index.php" class="btn btn-primary">Return to Home</a>
                <a href="pay_now.php" class="btn btn-warning">Retry Payment</a>
            </div>
        </div>
    </div>
</body>
</html>