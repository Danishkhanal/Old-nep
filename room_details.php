<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?> <!-- Include external CSS/JS links -->
    <title><?php echo $settings_r['site_title'] ?> - ROOM DETAILS</title>
</head>
<body class="bg-light">
    <?php require('inc/header.php'); ?> <!-- Include header -->

    <?php
        // Check if room ID is provided in the URL, if not, redirect to rooms page
        if(!isset($_GET['id'])){
            redirect('rooms.php');
        }

        // Sanitize input to prevent SQL injection
        $data = filteration($_GET);

        // Query to fetch room details based on the provided room ID
        $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?",[$data['id'],1,0],'iii');

        // If no room is found, redirect to rooms page
        if(mysqli_num_rows($room_res)==0){
            redirect('rooms.php');
        }

        // Fetch room data from the query result
        $room_data = mysqli_fetch_assoc($room_res);

        // Get the selected currency from URL, default to 'NPR' if not provided
        $selected_currency = isset($_GET['currency']) ? $_GET['currency'] : 'NPR';

        // Fetch exchange rates and convert price
        $exchange_rates = getExchangeRates();
        $base_currency = 'NPR'; // Assuming NPR is the base currency
        $converted_price = convertCurrency($room_data['price'], $base_currency, $selected_currency, $exchange_rates);
    ?>

    <div class="container">
        <div class="row">
            <!-- Room details header -->
            <div class="col-12 my-5 mb-4 px-4">
                <h2 class="fw-bold"><?php echo $room_data['name'] ?></h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
                </div>
            </div>

            <!-- Room Image Carousel -->
            <div class="col-lg-7 col-md-12 px-4">
                <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                            $room_img = ROOMS_IMG_PATH."thumbnail.jpg"; // Default image
                            $img_q = mysqli_query($con,"SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]'");

                            // If room has images, display them
                            if(mysqli_num_rows($img_q)>0) {
                                $active_class = 'active';
                                while($img_res = mysqli_fetch_assoc($img_q)) {
                                    echo "<div class='carousel-item $active_class'>
                                            <img src='".ROOMS_IMG_PATH.$img_res['image']."' class='d-block w-100 rounded'>
                                          </div>";
                                    $active_class = '';
                                }
                            } else {
                                // If no images, display the default image
                                echo "<div class='carousel-item active'>
                                        <img src='$room_img' class='d-block w-100'>
                                      </div>";
                            }
                        ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            <!-- Room Details & Booking -->
            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <!-- Display converted price -->
                        <?php echo "<h4>$selected_currency $converted_price per night</h4>"; ?>

                        <!-- Room Rating -->
                        <?php
                            $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review` WHERE `room_id`='$room_data[id]' ORDER BY `sr_no` DESC LIMIT 20";
                            $rating_res = mysqli_query($con,$rating_q);
                            $rating_fetch = mysqli_fetch_assoc($rating_res);
                            $rating_data = "";

                            // Display stars based on average rating
                            if($rating_fetch['avg_rating']!=NULL) {
                                for($i=0; $i < $rating_fetch['avg_rating']; $i++) {
                                    $rating_data .= "<i class='bi bi-star-fill text-warning'></i> ";
                                }
                            }
                            echo "<div class='mb-3'>$rating_data</div>";
                        ?>

                        <!-- Room Features -->
                        <?php
                            $fea_q = mysqli_query($con,"SELECT f.name FROM `features` f
                                                      INNER JOIN `room_features` rfea ON f.id = rfea.features_id
                                                      WHERE rfea.room_id = '$room_data[id]'");
                            $features_data = "";
                            while($fea_row = mysqli_fetch_assoc($fea_q)) {
                                $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                                    $fea_row[name]
                                                  </span>";
                            }
                            echo "<div class='mb-3'><h6 class='mb-1'>Features</h6>$features_data</div>";
                        ?>

                        <!-- Room Facilities -->
                        <?php
                            $fac_q = mysqli_query($con,"SELECT f.name FROM `facilities` f
                                                       INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id
                                                       WHERE rfac.room_id = '$room_data[id]'");
                            $facilities_data = "";
                            while($fac_row = mysqli_fetch_assoc($fac_q)) {
                                $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                                      $fac_row[name]
                                                    </span>";
                            }
                            echo "<div class='mb-3'><h6 class='mb-1'>Facilities</h6>$facilities_data</div>";
                        ?>

                        <!-- Room Occupancy (Adults and Children) -->
                        <?php
                            echo "<div class='mb-3'>
                                    <h6 class='mb-1'>Guests</h6>
                                    <span class='badge rounded-pill bg-light text-dark text-wrap'>$room_data[adult] Adults</span>
                                    <span class='badge rounded-pill bg-light text-dark text-wrap'>$room_data[children] Children</span>
                                  </div>";
                        ?>

                        <!-- Room Area -->
                        <?php
                            echo "<div class='mb-3'>
                                    <h6 class='mb-1'>Area</h6>
                                    <span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>$room_data[area] sq. ft.</span>
                                  </div>";
                        ?>

                        <!-- Booking Button -->
                        <?php
                            if(!$settings_r['shutdown']) {
                                $login = isset($_SESSION['login']) && $_SESSION['login'] == true ? 1 : 0;
                                echo "<button onclick='checkLoginToBook($login,$room_data[id])' class='btn w-100 text-white custom-bg shadow-none mb-1'>Book Now</button>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Room Description and Reviews -->
            <div class="col-12 mt-4 px-4">
                <div class="mb-5">
                    <h5>Description</h5>
                    <p><?php echo $room_data['description'] ?></p>
                </div>
                <div>
                    <h5 class="mb-3">Reviews & Ratings</h5>
                    <?php
                        $review_q = "SELECT rr.*, uc.name AS uname, uc.profile, r.name AS rname FROM `rating_review` rr
                                     INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                                     INNER JOIN `rooms` r ON rr.room_id = r.id
                                     WHERE rr.room_id = '$room_data[id]'
                                     ORDER BY `sr_no` DESC LIMIT 15";
                        $review_res = mysqli_query($con,$review_q);
                        $img_path = USERS_IMG_PATH;

                        if(mysqli_num_rows($review_res)==0) {
                            echo 'No reviews yet!';
                        } else {
                            while($row = mysqli_fetch_assoc($review_res)) {
                                $stars = "<i class='bi bi-star-fill text-warning'></i> ";
                                for($i=1; $i<$row['rating']; $i++) {
                                    $stars .= " <i class='bi bi-star-fill text-warning'></i>";
                                }
                                echo "<div class='mb-4'>
                                        <div class='d-flex align-items-center mb-2'>
                                            <img src='$img_path$row[profile]' class='rounded-circle' loading='lazy' width='30px'>
                                            <h6 class='m-0 ms-2'>$row[uname]</h6>
                                        </div>
                                        <p class='mb-1'>$row[review]</p>
                                        <div>$stars</div>
                                      </div>";
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/footer.php'); ?> <!-- Include footer -->

    <?php
        // Exchange rate fetching function
        function getExchangeRates() {
            $api_key = '76f54876e1914127b401c01cb75c2af9'; // Replace with your actual API key
            $url = "https://openexchangerates.org/api/latest.json?app_id=$api_key";
            $response = file_get_contents($url);
            return json_decode($response, true)['rates'];
        }

        // Currency conversion function
        function convertCurrency($amount, $from_currency, $to_currency, $exchange_rates) {
            if ($from_currency == $to_currency) {
                return number_format($amount, 2);
            }
            if (isset($exchange_rates[$from_currency]) && isset($exchange_rates[$to_currency])) {
                $conversion_rate = $exchange_rates[$to_currency] / $exchange_rates[$from_currency];
                return number_format($amount * $conversion_rate, 2);
            }
            return number_format($amount, 2); // Return original amount if conversion fails
        }
    ?>
</body>
</html>
