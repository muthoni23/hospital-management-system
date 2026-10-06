<?php require_once 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($dashboard_title); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body{
            margin:0;
            background:aliceblue;
            font-family:Arial, sans-serif;
            color:darkslategray;
        }
        .sidebar{
        width:250px;
        min-height:100vh;
        background:darkslategray;
        position:fixed;
        left:0;
        top:0;
        color:lightgray;
    }
    .brand{
    height:66px;
    background:darkslategray;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    font-weight:bold;
    letter-spacing:1px;
    color:white;
}
.brand span{
color:lightseagreen;
}
.profile{
background:darkcyan;
padding:22px;
color:white;
display:flex;
align-items:center;
gap:14px;
}
.profile-icon{
width:54px;
height:54px;
border-radius:50%;
background:aliceblue;
color:darkslategray;
display:flex;
align-items:center;
justify-content:center;
font-size:28px;
}
.profile strong{
display:block;
text-transform:uppercase;
}
.sidebar-menu{
list-style:none;
padding:0;
margin:0;
}
.sidebar-menu a{
color:lightgray;
text-decoration:none;
display:flex;
align-items:center;
gap:14px;
padding:15px 18px;
border-left:4px solid transparent;
}
.sidebar-menu a:hover,
.sidebar-menu a.active{
background:darkslategray;
border-left-color:darkcyan;
color:white;
}
.main{
margin-left:250px;
min-height:100vh;
display:flex;
flex-direction:column;
}
.topbar{
height:66px;
background:steelblue;
color:white;
display:flex;
align-items:center;
justify-content:space-between;
padding:0 28px;
}
.topbar-icons{
display:flex;
align-items:center;
gap:22px;
}
.topbar-icons i{
color:darkcyan;
font-size:22px;
}
.role-badge{
background:darkcyan;
color:white;
border-radius:999px;
padding:5px 12px;
font-size:13px;
text-transform:capitalize;
}
.content-header{
background:white;
border-bottom:1px solid lightgray;
padding:18px 30px;
display:flex;
align-items:center;
justify-content:space-between;
}
.content-title{
display:flex;
align-items:center;
gap:15px;
}
.content-title i{
width:54px;
height:54px;
border-radius:50%;
background:darkcyan;
color:white;
display:flex;
align-items:center;
justify-content:center;
font-size:26px;
}
.content-title h2{
margin:0;
color:darkcyan;
}
.content-title p{
margin:0;
color:slategray;
}
.content{
padding:24px 30px;
}
.stat-card{
background:darkcyan;
color:white;
border-radius:4px;
padding:22px;
min-height:122px;
display:flex;
justify-content:space-between;
align-items:flex-start;
box-shadow:0 2px 6px gray;
}
.stat-card i{
font-size:31px;
opacity:0.8;
}
.stat-card h4{
font-size:18px;
margin:8px 0 0;
font-weight:bold;
}
.stat-card strong{
font-size:40px;
line-height:1;
}
.section-card{
border:none;
border-radius:4px;
}
.section-card .card-header{
background:white;
font-weight:bold;
}
.date-input{
min-width:150px;
}
.doctor-treatment-form{
align-items:flex-start;
flex-wrap:wrap;
}
.doctor-treatment-form label{
font-size:13px;
font-weight:bold;
margin-bottom:4px;
}
.service-listbox{
min-width:250px;
height:150px;
}
footer{
background:darkslategray;
color:white;
text-align:center;
padding:18px;
margin-top:auto;
}
@media(max-width:900px){
.sidebar{
position:static;
width:100%;
min-height:auto;
}
.main{
margin-left:0;
}
.topbar,
.content-header{
height:auto;
flex-direction:column;
align-items:flex-start;
gap:12px;
padding:18px;
}
}
</style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand">
            HEALTH <span>ADMIN</span>
        </div>

        <div class="profile">
            <div class="profile-icon">
                <i class="fa fa-user-doctor" title="Doctor"></i>
            </div>
            <div>
                <span>Welcome</span>
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                <small><?php echo htmlspecialchars($role); ?></small>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="#dashboard" class="active"><i class="fa fa-gauge" title="Dashboard"></i> Dashboard</a></li>
            <li><a href="#appointments"><i class="fa fa-calendar-check" title="Appointments"></i> Appointment</a></li>
            <li><a href="#payments"><i class="fa fa-credit-card" title="Payment"></i> Payment</a></li>
            <?php if($role == "doctor" || $role == "admin") { ?>
                <li><a href="#reports"><i class="fa fa-file-medical" title="Reports"></i> Report</a></li>
            <?php } ?>
            <li><a href="patient.php"><i class="fa fa-user" title="Patient"></i> Patient Page</a></li>
            <li><a href="logout.php"><i class="fa fa-right-from-bracket" title="Logout"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main">
        <div class="topbar">
            <div><i class="fa fa-bars" title="Menu"></i></div>
            <div class="topbar-icons">
                <i class="fa fa-cart-shopping" title="Payments"></i>
                <i class="fa fa-envelope" title="Messages"></i>
                <i class="fa fa-bell" title="Notifications"></i>
                <span class="role-badge"><?php echo htmlspecialchars($role); ?></span>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>

        <div class="content-header" id="dashboard">
            <div class="content-title">
                <i class="fa fa-gauge-high" title="Dashboard"></i>
                <div>
                    <h2><?php echo htmlspecialchars($dashboard_title); ?></h2>
                    <p>Dashboard features</p>
                </div>
            </div>
            <div>
                <i class="fa fa-home" title="Home"></i> Home / <strong>Dashboard</strong>
            </div>
        </div>

        <div class="content">

            <?php if($success != "") { ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php } ?>

            <?php if($error != "") { ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div>
                            <i class="fa fa-user-doctor" title="Doctor"></i>
                            <h4>Active Doctors</h4>
                        </div>
                        <strong><?php echo $doctors_count; ?></strong>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div>
                            <i class="fa fa-users" title="Patients"></i>
                            <h4>Active Patients</h4>
                        </div>
                        <strong><?php echo $patients_count; ?></strong>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div>
                            <i class="fa fa-user-tie" title="Employees"></i>
                            <h4>Employees</h4>
                        </div>
                        <strong><?php echo $employees_count; ?></strong>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div>
                            <i class="fa fa-calendar-check" title="Appointments"></i>
                            <h4>Appointments</h4>
                        </div>
                        <strong><?php echo $appointments_count; ?></strong>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div>
                            <i class="fa fa-calendar-day" title="Scheduled appointments"></i>
                            <h4>Scheduled</h4>
                        </div>
                        <strong><?php echo $scheduled_count; ?></strong>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div>
                            <i class="fa fa-ban" title="Cancelled appointments"></i>
                            <h4>Cancelled</h4>
                        </div>
                        <strong><?php echo $cancelled_count; ?></strong>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div>
                            <i class="fa fa-user-group" title="Users"></i>
                            <h4>All Users</h4>
                        </div>
                        <strong><?php echo $users_count; ?></strong>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div>
                            <i class="fa fa-money-bill-wave" title="Revenue"></i>
                            <h4>Revenue</h4>
                        </div>
                        <strong><?php echo number_format($paid_total); ?></strong>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 section-card" id="appointments">
                <div class="card-header">
                    <?php echo $role == "doctor" ? "My Assigned Appointment Schedule" : "Appointment"; ?>
                </div>
                <div class="card-body">
                    <?php if(count($appointments) > 0) { ?>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Service Paid For</th>
                                        <th>Price</th>
                                        <th>Payment</th>
                                        <th>Date</th>
                                        <th>Assigned Doctor</th>
                                        <th>Status</th>
                                        <th>Disease</th>
                                        <th>Doctor Services</th>
                                        <th>Treated By</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($appointments as $appointment) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($appointment['fullname']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['mobile']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                            <td>KES <?php echo number_format($appointment['service_price']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['payment_method']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['assigned_doctor_name']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $appointment['status'] == 'Cancelled' ? 'bg-danger' : 'bg-success'; ?>">
                                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($appointment['disease']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['doctor_services']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['treated_by']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['area'] . " " . $appointment['city']); ?></td>
                                            <td>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <form method="POST" class="d-flex gap-2">
                                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                        <input type="date" name="appointment_date" class="form-control form-control-sm date-input" required>
                                                        <button class="btn btn-primary btn-sm" name="reschedule">Reschedule</button>
                                                    </form>
                                                    <form method="POST">
                                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                        <button class="btn btn-danger btn-sm" name="cancel">Cancel</button>
                                                    </form>
                                                    <?php if($role == "admin" || $role == "employee") { ?>
                                                        <form method="POST" class="d-flex gap-2">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <select name="assigned_doctor_id" class="form-control form-control-sm" required>
                                                                <option value="">Doctor</option>
                                                                <?php foreach($doctor_options as $doctor_option) { ?>
                                                                    <option value="<?php echo $doctor_option['id']; ?>" <?php if($appointment['assigned_doctor_id'] == $doctor_option['id']){ echo "selected"; } ?>>
                                                                    <?php echo htmlspecialchars($doctor_option['username']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                        <button class="btn btn-warning btn-sm" name="assign_doctor">Assign</button>
                                                    </form>
                                                <?php } ?>
                                                <?php if($role == "doctor") { ?>
                                                    <?php $saved_services = array_map('trim', explode(',', $appointment['doctor_services'] ?? '')); ?>
                                                    <form method="POST" class="d-flex gap-2 doctor-treatment-form">
                                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                        <div>
                                                            <label>Disease</label>
                                                            <input type="text" name="disease" class="form-control form-control-sm" placeholder="Disease / diagnosis" value="<?php echo htmlspecialchars($appointment['disease'] ?? ''); ?>" required>
                                                        </div>
                                                        <div>
                                                            <label>Services given</label>
                                                            <select name="doctor_services[]" class="form-control form-control-sm service-listbox" multiple size="7" required>
                                                                <?php foreach($service_options as $service_option) { ?>
                                                                    <option value="<?php echo htmlspecialchars($service_option); ?>" <?php if(in_array($service_option, $saved_services)){ echo "selected"; } ?>>
                                                                        <?php echo htmlspecialchars($service_option); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <button class="btn btn-success btn-sm" name="mark_treated">Save Treatment</button>
                                                    </form>
                                                <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <p class="text-muted mb-0">No appointments yet.</p>
            <?php } ?>
        </div>
    </div>

    <div class="card shadow mb-4 section-card" id="payments">
        <div class="card-header">
            Payment
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="alert alert-success mb-0">
                        <strong>Total Paid Services</strong><br>
                        KES <?php echo number_format($paid_total); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info mb-0">
                        <strong>Scheduled Appointments</strong><br>
                        <?php echo $scheduled_count; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-danger mb-0">
                        <strong>Cancelled Appointments</strong><br>
                        <?php echo $cancelled_count; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($role == "doctor" || $role == "admin") { ?>
        <div class="card shadow mb-4 section-card" id="reports">
            <div class="card-header">
                Patient Reports
            </div>
            <div class="card-body">
                <h5 class="mb-3">Most Common Diseases</h5>

                <?php if(count($disease_reports) > 0) { ?>
                    <div class="table-responsive mb-4">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Disease / Diagnosis</th>
                                    <th>Number of Patients</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($disease_reports as $disease_report) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($disease_report['disease']); ?></td>
                                        <td><?php echo htmlspecialchars($disease_report['total_patients']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-muted">No treated disease reports yet.</p>
                <?php } ?>

                <h5 class="mb-3">Patient Summary</h5>

                <?php if(count($patient_reports) > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Email Verified</th>
                                    <th>Total Appointments</th>
                                    <th>Last Appointment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($patient_reports as $report) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($report['username']); ?></td>
                                        <td><?php echo htmlspecialchars($report['email']); ?></td>
                                        <td><?php echo htmlspecialchars($report['phone']); ?></td>
                                        <td><?php echo $report['email_verified'] ? "Yes" : "No"; ?></td>
                                        <td><?php echo htmlspecialchars($report['appointment_count']); ?></td>
                                        <td><?php echo htmlspecialchars($report['last_appointment']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-muted mb-0">No patient reports yet.</p>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

</div>

<footer>
    Tumaini Health Centre | Staff Dashboard
</footer>
</main>

</body>
</html>
