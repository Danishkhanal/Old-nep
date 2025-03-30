<?php
require('admin/inc/db_config.php');

// Check if room_ids is set in the POST request
if (isset($_POST['room_ids'])) {
    // Debugging: Log the raw POST data to check if the data is received
    error_log("Received room_ids: " . $_POST['room_ids']);  // Logs to the PHP error log

    // Decode the JSON-encoded room IDs
    $room_ids = json_decode($_POST['room_ids']);
    
    // Check if the decoded value is an array and not null
    if (is_array($room_ids)) {
        // Start building the comparison HTML with Bootstrap classes
        $comparison_data = '<div class="row">';

        // Prepare the queries for fetching room features and facilities
        $fea_query = $con->prepare("SELECT f.name FROM `features` f 
            INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
            WHERE rfea.room_id = ?");
        $fac_query = $con->prepare("SELECT f.name FROM `facilities` f 
            INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
            WHERE rfac.room_id = ?");
        
        // Fetch details for each room ID
        foreach ($room_ids as $room_id) {
            // Fetch room data
            $room_res = $con->query("SELECT * FROM `rooms` WHERE `id` = $room_id");
            if ($room_res && $room_res->num_rows > 0) {
                $room_data = $room_res->fetch_assoc();

                // Fetch features for the room
                $fea_query->bind_param('i', $room_id);
                $fea_query->execute();
                $fea_result = $fea_query->get_result();
                $features_data = "<strong>Features:</strong><ul class='list-unstyled'>";
                while ($fea_row = $fea_result->fetch_assoc()) {
                    $features_data .= "<li>- " . htmlspecialchars($fea_row['name'], ENT_QUOTES, 'UTF-8') . "</li>";
                }
                $features_data .= "</ul>";

                // Fetch facilities for the room
                $fac_query->bind_param('i', $room_id);
                $fac_query->execute();
                $fac_result = $fac_query->get_result();
                $facilities_data = "<strong>Facilities:</strong><ul class='list-unstyled'>";
                while ($fac_row = $fac_result->fetch_assoc()) {
                    $facilities_data .= "<li>- " . htmlspecialchars($fac_row['name'], ENT_QUOTES, 'UTF-8') . "</li>";
                }
                $facilities_data .= "</ul>";

                // Get guest capacity
                $guests_data = "<strong>Guests:</strong><ul class='list-unstyled'><li>Adults: " . $room_data['adult'] . "</li><li>Children: " . $room_data['children'] . "</li></ul>";

                // Build the comparison result for each room with Bootstrap classes
                $comparison_data .= '<div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($room_data['name'], ENT_QUOTES, 'UTF-8') . '</h5>
                            <p class="card-text"><strong>Price:</strong> NPR ' . $room_data['price'] . '</p>
                            ' . $features_data . '
                            ' . $facilities_data . '
                            ' . $guests_data . '
                        </div>
                    </div>
                </div>';
            } else {
                $comparison_data .= '<div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Room Not Found</h5>
                        </div>
                    </div>
                </div>';
            }
        }

        // Close the row div
        $comparison_data .= '</div>';

        // Return the comparison data
        echo $comparison_data;
    } else {
        echo "<div class='alert alert-danger'>Error: Invalid room data!</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Error: No room IDs received!</div>";
}
?>