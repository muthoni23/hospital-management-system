<?php
session_start();

require_once 'db.php';

function findAvailableDoctor($conn, $appointment_date){
    $sql = "SELECT id, username
            FROM users
            WHERE role = 'doctor'
            ORDER BY username ASC";

    $result = mysqli_query($conn, $sql);

    if($result){
        while($doctor = mysqli_fetch_assoc($result)){
            $check = "SELECT COUNT(*) AS total
                      FROM appointments
                      WHERE assigned_doctor_id = ?
                      AND appointment_date = ?
                      AND status != 'Cancelled'";

            $stmt = mysqli_prepare($conn, $check);
            mysqli_stmt_bind_param($stmt, "is", $doctor['id'], $appointment_date);
            mysqli_stmt_execute($stmt);
            $doctor_result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($doctor_result);

            if($row['total'] == 0){
                return $doctor;
            }
        }
    }

    return null;
}

$services = [
    "General Consultation" => 1000,
    "Laboratory Test" => 1500,
    "Dental Checkup" => 2000,
    "Maternity Clinic" => 2500,
    "Vaccination" => 1200,
    "Emergency Care" => 3000,
    "Specialist Doctor Review" => 3500
];

$success = "";
$error = "";

if(isset($_POST['book'])){

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $fullname = trim($_POST['fullname']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $appointment_date = trim($_POST['appointment_date']);
    $area = "";
    $city = "";
    $state = "";
    $postal_code = "";
    $selected_services = ["General Consultation"];
    $payment_method = trim($_POST['payment_method']);
    $payment_reference = trim($_POST['payment_reference']);
    $confirm_payment = isset($_POST['confirm_payment']);
    $service_price = 0;
    $valid_services = [];

    foreach($selected_services as $service){
        if(isset($services[$service])){
            $valid_services[] = $service;
            $service_price += $services[$service];
        }
    }

    $service_name = implode(", ", $valid_services);

    if(count($valid_services) == 0 || $service_price <= 0 || $payment_method == "" || $payment_reference == "" || !$confirm_payment){

        $error = "Please pay the consultation fee first, then enter your payment reference before booking.";

    } else {

        $available_doctor = findAvailableDoctor($conn, $appointment_date);

        if(!$available_doctor){

            $error = "All doctors are occupied on the selected date. Please reschedule and choose another appointment date.";

        } else {

            $assigned_doctor_id = $available_doctor['id'];
            $assigned_doctor_name = $available_doctor['username'];

            $sql = "INSERT INTO appointments
                (user_id,fullname,mobile,email,appointment_date,area,city,state,postal_code,service_name,service_price,payment_method,payment_reference,payment_status,assigned_doctor_id,assigned_doctor_name,status)
                VALUES
                (?,?,?,?,?,?,?,?,?,?,?,?,?,'Paid',?,?,'Scheduled')";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isssssssssdssis", $user_id, $fullname, $mobile, $email, $appointment_date, $area, $city, $state, $postal_code, $service_name, $service_price, $payment_method, $payment_reference, $assigned_doctor_id, $assigned_doctor_name);

            if(mysqli_stmt_execute($stmt)){
                $success = "Appointment Booked Successfully. Assigned doctor: " . $assigned_doctor_name;
            } else {
                $error = "Error Booking Appointment";
            }

        }

    }

}

$history = [];
$doctors = [];
$doctors_count = 0;
$patient_appointments_count = 0;
$patient_cancelled_count = 0;
$patient_total_paid = 0;
$next_appointment = "None";

if(isset($_SESSION['user_id'])){
    $sql = "SELECT * FROM appointments
            WHERE user_id = ?
            OR email = ?
            ORDER BY appointment_date DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $_SESSION['user_id'], $_SESSION['email']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while($row = mysqli_fetch_assoc($result)){
        $history[] = $row;
    }
}

foreach($history as $appointment){
    $patient_appointments_count++;

    if($appointment['status'] == "Cancelled"){
        $patient_cancelled_count++;
    } else {
        $patient_total_paid += $appointment['service_price'];

        if($next_appointment == "None" && $appointment['appointment_date'] >= date("Y-m-d")){
            $next_appointment = $appointment['appointment_date'];
        }
    }
}

$result = mysqli_query($conn, "SELECT username, email, phone FROM users WHERE role = 'doctor' ORDER BY username ASC");

if($result){
    while($row = mysqli_fetch_assoc($result)){
        $doctors[] = $row;
    }
}

$doctors_count = count($doctors);
?>

<!DOCTYPE html>
<html>

    <head>

        <title>Patient Appointment</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

        <style>

            body{
            background:azure;
            font-family:Arial, sans-serif;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        .navbar{
        background:darkcyan;
    }

    .navbar-brand,
    .nav-link,
    .navbar-text{
    color:white !important;
}

.dashboard-hero{
margin-top:30px;
}

.dashboard-card{
background:darkcyan;
color:white;
border-radius:12px;
padding:22px;
min-height:125px;
box-shadow:0 8px 18px silver;
}

.dashboard-card h3{
font-size:36px;
margin:0;
font-weight:bold;
}

.dashboard-card p{
margin:6px 0 0;
}

.doctor-list{
margin-top:12px;
font-size:14px;
}

.doctor-list span{
display:inline-block;
background:lightseagreen;
border-radius:999px;
padding:5px 10px;
margin:4px 4px 0 0;
}

.card{
margin-top:35px;
border:none;
border-radius:15px;
overflow:hidden;
}

.card-header{
background:darkcyan;
color:white;
text-align:center;
padding:20px;
font-size:28px;
font-weight:bold;
}

.btn-book{
background:darkcyan;
color:white;
height:48px;
font-size:18px;
border:none;
border-radius:8px;
transition:0.3s;
}

.btn-book:hover{
background:teal;
color:white;
}

.form-control{
height:48px;
border-radius:8px;
}

.popup-success{
position:fixed;
top:20px;
right:20px;
background:seagreen;
color:white;
padding:15px 25px;
border-radius:10px;
box-shadow:0 5px 15px gray;
z-index:9999;
animation:slideIn 0.5s ease;
}

.site-footer{
margin-top:auto;
background:midnightblue;
color:white;
text-align:center;
padding:18px;
}

.history-card{
margin-bottom:35px;
}

.payment-section{
margin-top:35px;
}

.services-section{
margin-top:35px;
}

.service-display-card{
border:1px solid gainsboro;
border-radius:12px;
padding:18px;
height:100%;
background:ghostwhite;
}

.service-display-card strong{
display:block;
color:midnightblue;
margin-bottom:8px;
}

.service-display-card span{
color:darkcyan;
font-weight:bold;
}

.payment-card{
height:150px;
border:1px solid gainsboro;
border-radius:12px;
background:white;
display:flex;
flex-direction:column;
align-items:center;
justify-content:center;
gap:12px;
transition:0.3s;
}

.payment-card:hover{
transform:translateY(-4px);
box-shadow:0 10px 25px silver;
}

.payment-card img{
max-width:130px;
max-height:60px;
object-fit:contain;
}

.payment-card span{
font-weight:bold;
color:midnightblue;
}

.service-price{
font-weight:bold;
color:darkcyan;
}

.services-box{
border:1px solid gainsboro;
border-radius:10px;
padding:15px;
background:ghostwhite;
}

.service-option{
display:flex;
justify-content:space-between;
gap:12px;
padding:10px 0;
border-bottom:1px solid aliceblue;
}

.service-option:last-child{
border-bottom:none;
}

.service-option label{
margin:0;
cursor:pointer;
}

@keyframes slideIn{
from{
opacity:0;
transform:translateX(100px);
}

to{
opacity:1;
transform:translateX(0);
}
}

</style>

</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand fw-bold" href="patient.php">Tumaini Health Centre</a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="navbar-text">
                    <?php if(isset($_SESSION['username'])){ echo htmlspecialchars($_SESSION['username']); } ?>
                </span>
                <a class="btn btn-light btn-sm" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">

        <div class="dashboard-hero">
            <div class="row g-3">

                <div class="col-md-3">
                    <div class="dashboard-card">
                        <h3><?php echo $doctors_count; ?></h3>
                        <p>Doctors Available</p>
                        <div class="doctor-list">
                            <?php if(count($doctors) > 0) { ?>
                                <?php foreach($doctors as $doctor) { ?>
                                    <span><?php echo htmlspecialchars($doctor['username']); ?></span>
                                <?php } ?>
                            <?php } else { ?>
                                <span>No doctors listed</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card">
                        <h3><?php echo $patient_appointments_count; ?></h3>
                        <p>My Appointments</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card">
                        <h3><?php echo $patient_cancelled_count; ?></h3>
                        <p>My Cancelled Appointments</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card">
                        <h3><?php echo number_format($patient_total_paid); ?></h3>
                        <p>My Payments</p>
                        <small>Next: <?php echo htmlspecialchars($next_appointment); ?></small>
                    </div>
                </div>

            </div>
        </div>

        <div class="row justify-content-center">

            <div class="col-md-7">

                <div class="card shadow">

                    <div class="card-header">
                        Book Appointment
                    </div>

                    <div class="card-body p-4">

                        <form method="POST">

                            <div class="mb-3">
                                <input type="text"
                                class="form-control"
                                name="fullname"
                                placeholder="Enter Full Name"
                                value="<?php if(isset($_SESSION['username'])){ echo htmlspecialchars($_SESSION['username']); } ?>"
                                required>
                            </div>

                            <div class="mb-3">
                                <input type="text"
                                class="form-control"
                                name="mobile"
                                placeholder="Enter Mobile Number"
                                required>
                            </div>

                            <div class="mb-3">
                                <input type="email"
                                class="form-control"
                                name="email"
                                placeholder="Enter Email Address"
                                value="<?php if(isset($_SESSION['email'])){ echo htmlspecialchars($_SESSION['email']); } ?>"
                                required>
                            </div>

                            <div class="mb-3">
                                <input type="date"
                                class="form-control"
                                name="appointment_date"
                                required>
                            </div>

                            <div class="mb-3">
                                <select class="form-control" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="M-Pesa">M-Pesa</option>
                                    <option value="Card">Card</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="SHA">SHA</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <input type="text"
                                class="form-control"
                                name="payment_reference"
                                placeholder="Enter consultation payment reference"
                                required>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox"
                                class="form-check-input"
                                name="confirm_payment"
                                id="confirmPayment"
                                required>
                                <label class="form-check-label" for="confirmPayment">
                                    I have paid the consultation fee
                                </label>
                            </div>

                            <button class="btn btn-book w-100"
                                name="book">
                                Confirm Payment & Book Appointment
                            </button>

                        </form>

                        <?php if($error != "") { ?>
                            <div class="alert alert-danger mt-3">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow history-card">
                    <div class="card-header">
                        Appointment History
                    </div>
                    <div class="card-body p-4">

                        <?php if(count($history) > 0) { ?>

                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Service</th>
                                            <th>Payment</th>
                                            <th>Payment Status</th>
                                            <th>Reference</th>
                                            <th>Date</th>
                                            <th>Assigned Doctor</th>
                                            <th>Status</th>
                                            <th>Disease</th>
                                            <th>Doctor Services</th>
                                            <th>Treated By</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($history as $appointment) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($appointment['fullname']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['mobile']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['payment_method']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['payment_status']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['payment_reference']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                                <td><?php echo $appointment['assigned_doctor_name'] ? htmlspecialchars($appointment['assigned_doctor_name']) : "Not assigned yet"; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $appointment['status'] == 'Cancelled' ? 'bg-danger' : 'bg-success'; ?>">
                                                        <?php echo htmlspecialchars($appointment['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($appointment['disease']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['doctor_services']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['treated_by']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['area'] . " " . $appointment['city']); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php } else { ?>

                            <p class="text-muted mb-0">No appointments booked yet.</p>

                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center services-section">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header">
                        Services
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <?php foreach($services as $service => $price) { ?>
                                <div class="col-md-4">
                                    <div class="service-display-card">
                                        <strong><?php echo htmlspecialchars($service); ?></strong>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center payment-section">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header">
                        Payment
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="alert alert-info mb-0">
                                    <strong>Payment Status</strong><br>
                                    Consultation fee required before booking
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning mb-0">
                                    <strong>Payment Methods</strong><br>
                                    M-Pesa, Card, PayPal
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="payment-card">
                                    <img src="https://commons.wikimedia.org/wiki/Special:FilePath/M-PESA%20LOGO-01.svg" alt="M-Pesa">
                                    <span>M-Pesa</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="payment-card">
                                    <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Bootstrap%20credit-card.svg" alt="Card payment">
                                    <span>Card</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="payment-card">
                                    <img src="https://commons.wikimedia.org/wiki/Special:FilePath/PayPal.svg" alt="PayPal">
                                    <span>PayPal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php if(isset($_GET['created'])) { ?>
        <div class="popup-success" id="successPopup">
            Account Created Successfully. Please check your email to verify your account.
        </div>
    <?php } ?>

    <?php if($success != "") { ?>
        <div class="popup-success" id="successPopup">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php } ?>

    <footer class="site-footer">
        Tumaini Health Centre | Your Health, Our Priority
    </footer>

    <script>
        setTimeout(function(){
            let popup = document.getElementById("successPopup");

            if(popup){
                popup.style.display = "none";
            }
        },4000);
    </script>

</body>
</html>
