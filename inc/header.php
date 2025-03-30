<nav id="nav-bar" class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php"><?php echo $settings_r['site_title'] ?></a>
    <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link me-2" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="rooms.php">Rooms</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="facilities.php">Facilities</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="contact.php">Contact us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.php">About</a>
        </li>
        <li class="nav-item">
    <button type="button" class="nav-link" data-bs-toggle="modal" data-bs-target="#compareRoomsModal" style="border: none; background: transparent;">Compare Room</button>
</li>
      </ul>
      <div class="d-flex">
        <?php
          if(isset($_SESSION['login']) && $_SESSION['login'] == true) {
            $path = USERS_IMG_PATH;
            echo<<<data
              <div class="btn-group">
                <button type="button" class="btn btn-outline-dark shadow-none dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                  <img src="$path$_SESSION[uPic]" style="width: 25px; height: 25px;" class="me-1 rounded-circle">
                  $_SESSION[uName]
                </button>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                  <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                  <li><a class="dropdown-item" href="bookings.php">Bookings</a></li>
                  <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
              </div>
            data;
          } else {
            echo<<<data
              <button type="button" class="btn btn-outline-dark shadow-none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                Login
              </button>
              <button type="button" class="btn btn-outline-dark shadow-none" data-bs-toggle="modal" data-bs-target="#registerModal">
                Register
              </button>
            data;
          }
        ?>
      </div>
    </div>
  </div>
</nav>
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="login-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-person-circle fs-3 me-2"></i> User Login
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Email / Mobile</label>
            <input type="text" name="email_mob" required class="form-control shadow-none">
          </div>
          <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="pass" required class="form-control shadow-none">
          </div>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <button type="submit" class="btn btn-dark shadow-none">LOGIN</button>
            <button type="button" class="btn text-secondary text-decoration-none shadow-none p-0" data-bs-toggle="modal" data-bs-target="#forgotModal" data-bs-dismiss="modal">
              Forgot Password?
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="register-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-person-lines-fill fs-3 me-2"></i> User Registration
          </h5>
          <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input name="name" type="text" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Phone Number</label>
                <input name="phonenum" type="number" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Picture</label>
                <input name="profile" type="file" accept=".jpg, .jpeg, .png, .webp" class="form-control shadow-none" required>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control shadow-none" rows="1" required></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Pincode</label>
                <input name="pincode" type="number" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Date of birth</label>
                <input name="dob" type="date" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Password</label>
                <input name="pass" type="password" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Confirm Password</label>
                <input name="cpass" type="password" class="form-control shadow-none" required>
              </div>
            </div>
          </div>
          <div class="text-center my-1">
            <button type="submit" class="btn btn-dark shadow-none">REGISTER</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="forgotModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="forgot-form">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-person-circle fs-3 me-2"></i> Forgot Password
          </h5>
        </div>
        <div class="modal-body">
          <span class="badge rounded-pill bg-light text-dark mb-3 text-wrap lh-base">
            Note: A link will be sent to your email to reset your password!
          </span>
          <div class="mb-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" required class="form-control shadow-none">
          </div>
          <div class="mb-2 text-end">
            <button type="button" class="btn shadow-none p-0 me-2" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
              CANCEL
            </button>
            <button type="submit" class="btn btn-dark shadow-none">SEND LINK</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="compareRoomsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center">
          <i class="bi bi-arrow-left-right fs-3 me-2"></i> Compare Rooms
        </h5>
        <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid" id="compare-rooms-list">
          <div class="row" id="room-selection-area"></div>
          <div class="row mt-3" id="comparison-area" style="display:none;"></div>
        </div>
        <p id="compare-rooms-error" class="text-danger" style="display:none;">** Select at least one and maximum two rooms to compare! **</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="compare-rooms-btn" class="btn btn-dark shadow-none" onclick="compareSelectedRooms()" disabled>Compare Selected Rooms</button>
      </div>
    </div>
  </div>
</div>

<script>
let roomSelectionArray = [];

function fetchRoomsForComparison() {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "admin/ajax/room_list.php?get_rooms_for_comparison=true", true); 
    xhr.onload = function() {
        document.getElementById('room-selection-area').innerHTML = this.responseText;
        initializeRoomSelection();
    };
    xhr.send();
}

function initializeRoomSelection() {
    roomSelectionArray = [];
    let checkboxes = document.querySelectorAll('.compare-room-checkbox');
    let compareBtn = document.getElementById('compare-rooms-btn');
    let errorP = document.getElementById('compare-rooms-error');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                roomSelectionArray.push(parseInt(this.value)); // Add room ID to the array
            } else {
                roomSelectionArray = roomSelectionArray.filter((val) => val != parseInt(this.value)); // Remove room ID from the array
            }

            // Enable the compare button if at least one room is selected
            if (roomSelectionArray.length > 0) {
                compareBtn.disabled = false;
                errorP.style.display = 'none'; // Hide error message
            } else {
                compareBtn.disabled = true;
            }
        });
    });
}

function compareSelectedRooms() {
    let comparisonArea = document.getElementById('comparison-area');
    let roomSelectionArea = document.getElementById('room-selection-area');

    if (roomSelectionArray.length < 1) {
        return false; // No rooms selected
    }

    // Log the selected room IDs for debugging
    console.log("Selected Room IDs:", roomSelectionArray);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "compare_rooms.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (xhr.status === 200) {
            // Update the comparison area with the response from the server
            comparisonArea.innerHTML = xhr.responseText;

            // Hide the room selection area and show the comparison area
            roomSelectionArea.style.display = 'none';
            comparisonArea.style.display = 'block';
        } else {
            console.error("Failed to send request");
        }
    };

    xhr.send('room_ids=' + JSON.stringify(roomSelectionArray)); // Send the selected room IDs as JSON
}



let compareRoomsModal = document.getElementById('compareRoomsModal');
compareRoomsModal.addEventListener('shown.bs.modal', function () {
  fetchRoomsForComparison();
});

compareRoomsModal.addEventListener('hidden.bs.modal', function () {
    let comparisonArea = document.getElementById('comparison-area');
    let roomSelectionArea = document.getElementById('room-selection-area');

    roomSelectionArea.style.display = 'block';
    comparisonArea.style.display = 'none';
});
</script>
