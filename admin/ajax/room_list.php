<?php
    require('../inc/db_config.php');
    require('../inc/essentials.php');

    if(isset($_GET['get_rooms_for_comparison'])){
        $output = "";
        // SQL Query to fetch rooms with status = 1
        $res = mysqli_query($con, "SELECT * FROM `rooms` WHERE `status`=1 ORDER BY `name`");

        // Check if rooms are available
        if(mysqli_num_rows($res) > 0){
            $i = 1;
            while($row = mysqli_fetch_assoc($res)){
                $output .= "
                    <div class='col-md-3'>
                        <div class='form-check'>
                            <input type='checkbox' value='".$row['id']."' id='room-".$i."' class='form-check-input compare-room-checkbox'>
                            <label class='form-check-label' for='room-".$i."'>
                                ".$row['name']."
                            </label>
                        </div>
                    </div>
                ";
                $i++;
            }
        } else {
            $output = "<p>No rooms available for comparison.</p>";
        }

        // Output the list of rooms (or error message)
        echo $output;
    }
?>
