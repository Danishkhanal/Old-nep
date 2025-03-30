<?php 
require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

// Include Stripe's PHP library
require_once('vendor/autoload.php'); // Ensure Stripe SDK is installed correctly

date_default_timezone_set("Asia/Kathmandu");

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('index.php');
}

\Stripe\Stripe::setApiKey('sk_test_51Qvg87P8o5tuCFx3xjT0db2guee24USQ7MmFncZ2pits8g3jsyd9a79Au0RE1lCdVVq8xkPJ0EM3TL2zwr7eMbhN00If4lh8iE');

if (isset($_POST['pay_now'])) {
  // Get user and room details
  $CUST_ID = $_SESSION['uId'];
  $TXN_AMOUNT = $_SESSION['room']['payment'];

  // Generate ORDER_ID *before* creating the Stripe session
  $ORDER_ID = 'ORD_' . $_SESSION['uId'] . random_int(11111, 9999999);

  // Save order data to the database
  $paramList = filteration($_POST);
  $query1 = "INSERT INTO `booking_order` (`user_id`, `room_id`, `check_in`, `check_out`, `order_id`) VALUES (?,?,?,?,?)";
  insert($query1, [$CUST_ID, $_SESSION['room']['id'], $paramList['checkin'], $paramList['checkout'], $ORDER_ID], 'issss');
  
  $booking_id = mysqli_insert_id($con);

  $query2 = "INSERT INTO `booking_details` (`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
  insert($query2, [$booking_id, $_SESSION['room']['name'], $_SESSION['room']['price'], $TXN_AMOUNT, $paramList['name'], $paramList['phonenum'], $paramList['address']], 'issssss');

  // Create Stripe Checkout Session *after* ORDER_ID is defined
  $checkoutSession = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [
      [
        'price_data' => [
          'currency' => 'NPR',
          'product_data' => [
            'name' => $_SESSION['room']['name'],
          ],
          'unit_amount' => $TXN_AMOUNT * 100,
        ],
        'quantity' => 1,
      ],
    ],
    'mode' => 'payment',
    'client_reference_id' => $ORDER_ID, // Now correctly set
    'success_url' => 'http://localhost/NepalNivas/pay_response.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://localhost/NepalNivas/pay_status.php?status=failed',
  ]);

  // Redirect to Stripe checkout
  header("Location: " . $checkoutSession->url);
  exit();
}
?>