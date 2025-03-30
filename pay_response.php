<?php
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require_once('vendor/autoload.php');

session_start();

\Stripe\Stripe::setApiKey('sk_test_51Qvg87P8o5tuCFx3xjT0db2guee24USQ7MmFncZ2pits8g3jsyd9a79Au0RE1lCdVVq8xkPJ0EM3TL2zwr7eMbhN00If4lh8iE');

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
}

$sessionId = $_GET['session_id'];

try {
  $session = \Stripe\Checkout\Session::retrieve($sessionId);

  if ($session->payment_status == 'paid') {
    $orderId = $session->client_reference_id;
    echo "Order ID from Stripe: " . $orderId . "<br>";

    $query = "SELECT `booking_id`, `user_id` FROM `booking_order` WHERE `order_id`=?";
    $booking_res = select($query, [$orderId], 's');
    
    if (!$booking_res) {
      echo "Database error: " . mysqli_error($con);
      exit;
    }

    if (mysqli_num_rows($booking_res) > 0) {
      $booking_fetch = mysqli_fetch_assoc($booking_res);
      
      $updateQuery = "UPDATE `booking_order` SET `booking_status`='booked', `trans_id`=?, `trans_amt`=?, `trans_status`='TXN_SUCCESS' WHERE `booking_id`=?";
      insert($updateQuery, [$session->payment_intent, $session->amount_total / 100, $booking_fetch['booking_id']], 'ssi');

      // Display success message with HTML and Bootstrap 5 styling
      echo '
      <!DOCTYPE html>
      <html lang="en">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Successful</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
          .container {
            max-width: 600px;
            margin-top: 50px;
            text-align: center;
          }
          .success-message {
            color: #28a745;
            font-size: 1.5rem;
            margin-bottom: 20px;
          }
          .home-button {
            margin-top: 30px;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <div class="success-message">
            <h2>Payment Successful!</h2>
            <p>Your booking has been confirmed.</p>
            <p>Order ID: ' . $orderId . '</p>
            <p>Transaction ID: ' . $session->payment_intent . '</p>
          </div>
          <a href="index.php" class="btn btn-primary home-button">Return to Home</a>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
      </body>
      </html>';
    } else {
      echo "Booking not found. Order ID: " . $orderId . " does not exist in the database.";
    }
  } else {
    echo "Payment failed or is pending. Please try again.";
  }
} catch (\Stripe\Exception\ApiErrorException $e) {
  echo "An error occurred while processing your payment: " . $e->getMessage();
}
?>
