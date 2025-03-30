<?php 
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

// Make sure PHPMailer is included (add to essentials.php or here)
require 'C:\xampp\htdocs\NepalNivas\src\Exception.php';
require 'C:\xampp\htdocs\NepalNivas\src\PHPMailer.php';
require 'C:\xampp\htdocs\NepalNivas\src\SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_mail($to, $subject, $content) {
    $mail = new PHPMailer(true);
    
    try {
        // Mailtrap SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'f00d4a354fc2ff'; // Replace with your Mailtrap username
        $mail->Password = '7331e4e691ff6b'; // Replace with your Mailtrap password
        
        // Sender and recipient settings
        $mail->setFrom('np03cs4s230134@heraldcollege.edu.np', 'NepalNivas');
        $mail->addAddress($to);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error if needed
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

if(isset($_POST['get_bookings']))
{
    $frm_data = filteration($_POST);

    // Joining user_cred table to get email address
    $query = "SELECT bo.*, bd.*, uc.email FROM `booking_order` bo
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              INNER JOIN `user_cred` uc ON bo.user_id = uc.id
              WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?) 
              AND (bo.booking_status=? AND bo.arrival=?) ORDER BY bo.booking_id ASC";

    $res = select($query, ["%$frm_data[search]%", "%$frm_data[search]%", "%$frm_data[search]%", "booked", 0], 'sssss');
    
    $i = 1;
    $table_data = "";

    if(mysqli_num_rows($res) == 0){
        echo "<b>No Data Found!</b>";
        exit;
    }

    while($data = mysqli_fetch_assoc($res))
    {
        $date = date("d-m-Y", strtotime($data['datentime']));
        $checkin = date("d-m-Y", strtotime($data['check_in']));
        $checkout = date("d-m-Y", strtotime($data['check_out']));

        $table_data .= "
        <tr>
            <td>$i</td>
            <td>
            <span class='badge bg-primary'>
                Order ID: $data[order_id]
            </span>
            <br>
            <b>Name:</b> $data[user_name]
            <br>
            <b>Phone No:</b> $data[phonenum]
            </td>
            <td>
            <b>Room:</b> $data[room_name]
            <br>
            <b>Price:</b> NPR$data[price]
            </td>
            <td>
            <b>Check-in:</b> $checkin
            <br>
            <b>Check-out:</b> $checkout
            <br>
            <b>Paid:</b> NPR$data[trans_amt]
            <br>
            <b>Date:</b> $date
            </td>
            <td>
            <button type='button' onclick='assign_room($data[booking_id])' class='btn text-white btn-sm fw-bold custom-bg shadow-none' data-bs-toggle='modal' data-bs-target='#assign-room'>
                <i class='bi bi-check2-square'></i> Assign Room
            </button>
            <br>
            <button type='button' onclick='cancel_booking($data[booking_id])' class='mt-2 btn btn-outline-danger btn-sm fw-bold shadow-none'>
                <i class='bi bi-trash'></i> Cancel Booking
            </button>
            </td>
        </tr>
        ";

        $i++;
    }

    echo $table_data;
}

if(isset($_POST['assign_room']))
{
    $frm_data = filteration($_POST);

    // Get complete booking details including email address
    $query = "SELECT bo.*, bd.*, uc.email FROM `booking_order` bo
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              INNER JOIN `user_cred` uc ON bo.user_id = uc.id
              WHERE bo.booking_id = ?";
    
    $res = select($query, [$frm_data['booking_id']], 'i');
    $booking_data = mysqli_fetch_assoc($res);

    // Update booking status and assign room
    $query = "UPDATE `booking_order` bo INNER JOIN `booking_details` bd
              ON bo.booking_id = bd.booking_id
              SET bo.arrival = ?, bo.rate_review = ?, bd.room_no = ? 
              WHERE bo.booking_id = ?";

    $values = [1, 0, $frm_data['room_no'], $frm_data['booking_id']];
    $res = update($query, $values, 'iisi');

    if($res == 2) {
        // Prepare and send confirmation email
        $to = $booking_data['email'];
        $subject = "Booking Confirmed - Order #".$booking_data['order_id'];
        
        $content = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                    .details { margin: 20px 0; padding: 15px; background-color: #f8f9fa; }
                    .footer { margin-top: 20px; font-size: 0.9em; color: #6c757d; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Your Booking Has Been Confirmed!</h2>
                    </div>
                    
                    <p>Dear ".htmlspecialchars($booking_data['user_name']).",</p>
                    <p>We're pleased to inform you that your booking has been confirmed and your room has been assigned.</p>
                    
                    <div class='details'>
                        <h3>Booking Details</h3>
                        <p><strong>Order ID:</strong> ".htmlspecialchars($booking_data['order_id'])."</p>
                        <p><strong>Room Type:</strong> ".htmlspecialchars($booking_data['room_name'])."</p>
                        <p><strong>Room Number:</strong> ".htmlspecialchars($frm_data['room_no'])."</p>
                        <p><strong>Check-in Date:</strong> ".date("d-m-Y", strtotime($booking_data['check_in']))."</p>
                        <p><strong>Check-out Date:</strong> ".date("d-m-Y", strtotime($booking_data['check_out']))."</p>
                        <p><strong>Total Amount:</strong> NPR".htmlspecialchars($booking_data['trans_amt'])."</p>
                    </div>
                    
                    <p>We look forward to welcoming you to our hotel. If you have any special requests or need assistance, please don't hesitate to contact us.</p>
                    
                    <div class='footer'>
                        <p>Best regards,<br>The Hotel Team</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        // Send email and return response
        if(send_mail($to, $subject, $content)) {
            echo 1;
        } else {
            echo "Email failed but booking updated";
        }
    } else {
        echo 0;
    }
}

if(isset($_POST['cancel_booking']))
{
    $frm_data = filteration($_POST);
    
    // Get complete booking details including email address
    $query = "SELECT bo.*, bd.*, uc.email FROM `booking_order` bo
              INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
              INNER JOIN `user_cred` uc ON bo.user_id = uc.id
              WHERE bo.booking_id = ?";
    
    $res = select($query, [$frm_data['booking_id']], 'i');
    $booking_data = mysqli_fetch_assoc($res);
    
    // Update booking status to cancelled
    $query = "UPDATE `booking_order` SET `booking_status`=?, `refund`=? WHERE `booking_id`=?";
    $values = ['cancelled', 0, $frm_data['booking_id']];
    $res = update($query, $values, 'sii');

    if($res) {
        // Prepare and send cancellation email
        $to = $booking_data['email'];
        $subject = "Booking Cancelled - Order #".$booking_data['order_id'];
        
        $content = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                    .details { margin: 20px 0; padding: 15px; background-color: #f8f9fa; }
                    .footer { margin-top: 20px; font-size: 0.9em; color: #6c757d; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Your Booking Has Been Cancelled</h2>
                    </div>
                    
                    <p>Dear ".htmlspecialchars($booking_data['user_name']).",</p>
                    <p>We're sorry to inform you that your booking has been cancelled as per your request.</p>
                    
                    <div class='details'>
                        <h3>Cancelled Booking Details</h3>
                        <p><strong>Order ID:</strong> ".htmlspecialchars($booking_data['order_id'])."</p>
                        <p><strong>Room Type:</strong> ".htmlspecialchars($booking_data['room_name'])."</p>
                        <p><strong>Check-in Date:</strong> ".date("d-m-Y", strtotime($booking_data['check_in']))."</p>
                        <p><strong>Check-out Date:</strong> ".date("d-m-Y", strtotime($booking_data['check_out']))."</p>
                        <p><strong>Amount Paid:</strong> NPR".htmlspecialchars($booking_data['trans_amt'])."</p>
                    </div>
                    
                    <p>If this cancellation was a mistake or you have any questions, please contact our support team immediately.</p>
                    
                    <div class='footer'>
                        <p>Best regards,<br>The Hotel Team</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        // Send email and return response
        if(send_mail($to, $subject, $content)) {
            echo 1;
        } else {
            echo "Email failed but booking cancelled";
        }
    } else {
        echo 0;
    }
}
?>
