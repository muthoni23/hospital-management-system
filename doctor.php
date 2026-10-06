<!DOCTYPE html>
<html>

    <head>

        <title>Dashboard</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

        <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>

            body{
            background:ghostwhite;
        }

        .sidebar{
        height:100vh;
        background:midnightblue;
        color:white;
        position:fixed;
        width:250px;
    }

    .sidebar h3{
    padding:20px;
    text-align:center;
    background:darkcyan;
}

.sidebar ul{
list-style:none;
padding:0;
}

.sidebar ul li{
padding:15px 20px;
}

.sidebar ul li:hover{
background:darkslategray;
cursor:pointer;
}

.main{
margin-left:250px;
padding:20px;
}

.card-box{
background:darkcyan;
color:white;
border-radius:15px;
padding:25px;
}

</style>

</head>

<body>

    <div class="sidebar">

        <h3>HEALTH ADMIN</h3>

        <ul>
            <li><i class="fa fa-home" title="Home"></i> Dashboard</li>
            <li><i class="fa fa-user" title="Patient"></i> Patients</li>
            <li><i class="fa fa-calendar" title="Appointments"></i> Appointments</li>
            <li><i class="fa fa-user-doctor" title="Doctor"></i> Doctors</li>
            <li><i class="fa fa-file" title="Reports"></i> Reports</li>
        </ul>

    </div>

    <div class="main">

        <h2 class="mb-4">Dashboard</h2>

        <div class="row">

            <div class="col-md-3">
                <div class="card-box shadow">
                    <h1>9</h1>
                    <p>Doctors</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box shadow">
                    <h1>11</h1>
                    <p>Patients</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box shadow">
                    <h1>5</h1>
                    <p>Nurses</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box shadow">
                    <h1>3</h1>
                    <p>Employees</p>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
