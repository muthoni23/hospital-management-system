<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Tumaini Health Centre</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>

            body{
            margin:0;
            font-family:Arial;
            background:black url('https://images.unsplash.com/photo-1584982751601-97dcc096659c') center/cover no-repeat;
            background-blend-mode:multiply;
            color:white;
        }

        .hero{
        height:100vh;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        text-align:center;
    }

    .hero h1{
    font-size:60px;
    font-weight:bold;
    animation: fadeInDown 1.5s ease;
}

.hero p{
font-size:20px;
margin-top:10px;
max-width:700px;
animation: fadeIn 2s ease;
}

.btn-group-custom{
margin-top:30px;
}

.btn-custom{
padding:12px 25px;
font-size:18px;
margin:10px;
border:none;
border-radius:10px;
cursor:pointer;
transition:0.3s ease;
}

.btn-custom:hover{
transform: scale(1.1);
}

.login-btn{
background:dodgerblue;
color:white;
}

.create-btn{
background:seagreen;
color:white;
}

.services-btn{
background:gold;
color:black;
}

.info-section{
background:white;
color:black;
padding:60px 20px;
text-align:center;
}

.card-box{
padding:25px;
border-radius:15px;
box-shadow:0 0 15px silver;
margin:10px;
transition:0.3s ease;
background:white;
}

.card-box:hover{
transform: scale(1.08);
box-shadow:0 10px 25px gray;
}

.card-box i{
font-size:30px;
margin-bottom:10px;
color:dodgerblue;
}

.price{
color:darkcyan;
font-weight:bold;
font-size:18px;
margin-top:10px;
}

.footer{
background:black;
color:white;
text-align:center;
padding:20px;
}

@keyframes fadeIn {
from {opacity:0;}
to {opacity:1;}
}

@keyframes fadeInDown {
from {
opacity:0;
transform: translateY(-30px);
}
to {
opacity:1;
transform: translateY(0);
}
}

</style>

</head>

<body>

    <div class="hero">

        <h1>WELCOME TO TUMAINI HEALTH CENTRE</h1>

        <p>
            Your trusted healthcare partner providing quality medical services,
            professional doctors, and 24/7 emergency support for all patients.
        </p>

        <div class="btn-group-custom">

            <a href="login.php">
                <button class="btn-custom login-btn">
                    <i class="fa fa-sign-in-alt" title="Login"></i> Login
                </button>
            </a>

            <a href="createaccount.php">
                <button class="btn-custom create-btn">
                    <i class="fa fa-user-plus" title="Create account"></i> Create Account
                </button>
            </a>

            <a href="#services">
                <button class="btn-custom services-btn">
                    <i class="fa fa-hospital" title="Services"></i> Services
                </button>
            </a>

        </div>

        <div class="mt-3 text-center">

            <p style="margin-bottom:5px;">Don't have an account?</p>

            <p>
                <a href="createaccount.php" style="color:seagreen; font-weight:bold; text-decoration:none;">
                    Create one here
                </a>
            </p>

        </div>

    </div>

    <div class="info-section">

        <h2>About Tumaini Health Centre</h2>

        <p>
            We are a modern healthcare facility located in Nairobi, Kenya,
            committed to providing affordable and quality medical services.
        </p>

        <div class="row mt-4">

            <div class="col-md-4">
                <div class="card-box">
                    <i class="fa fa-map-marker-alt" title="Location"></i>
                    <h4>Location</h4>
                    <p>Nairobi, Kenya</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box">
                    <i class="fa fa-phone" title="Phone"></i>
                    <h4>Customer Support</h4>
                    <p>+254 700 123 456</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-box">
                    <i class="fa fa-clock" title="Opening hours"></i>
                    <h4>24/7 Services</h4>
                    <p>Emergency & Full-Time Care</p>
                </div>
            </div>

        </div>

    </div>

    <div class="info-section" id="services">

        <h2>Our Services</h2>

        <p>
            View our consultation and treatment fees before booking an appointment.
        </p>

        <div class="row mt-4">

            <div class="col-md-3">
                <div class="card-box">
                    <i class="fa fa-stethoscope" title="Consultation"></i>
                    <h5>General Consultation</h5>
                    <div class="price">KES 1,000</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box">
                    <i class="fa fa-vial" title="Laboratory test"></i>
                    <h5>Laboratory Test</h5>
                    <div class="price">KES 1,500</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box">
                    <i class="fa fa-tooth" title="Dental checkup"></i>
                    <h5>Dental Checkup</h5>
                    <div class="price">KES 2,000</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box">
                    <i class="fa fa-baby" title="Maternity clinic"></i>
                    <h5>Maternity Clinic</h5>
                    <div class="price">KES 2,500</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box">
                    <i class="fa fa-syringe" title="Vaccination"></i>
                    <h5>Vaccination</h5>
                    <div class="price">KES 1,200</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box">
                    <i class="fa fa-truck-medical" title="Emergency care"></i>
                    <h5>Emergency Care</h5>
                    <div class="price">KES 3,000</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box">
                    <i class="fa fa-user-doctor" title="Doctor"></i>
                    <h5>Specialist Doctor Review</h5>
                    <div class="price">KES 3,500</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card-box">
                    <i class="fa fa-credit-card" title="Payment"></i>
                    <h5>Payment Methods</h5>
                    <div class="price">M-Pesa, Card, PayPal,SHA</div>
                </div>
            </div>

        </div>

    </div>

    <div class="footer">

        <p>Copyright 2026 Tumaini Health Centre | Caring for Life</p>
        <p>Motto: "Your Health, Our Priority"</p>
    </div>

</body>
</html>
