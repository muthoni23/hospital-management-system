<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "hospital_db"
);

if(!$conn){
    die("Database connection failed");
}

mysqli_set_charset($conn, "utf8mb4");

function tableExists($conn, $table){
    $sql = "SELECT COUNT(*) AS total
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $table);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row['total'] > 0;
}

function columnExists($conn, $table, $column){
    $sql = "SELECT COUNT(*) AS total
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $table, $column);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row['total'] > 0;
}

function addColumnIfMissing($conn, $table, $column, $definition){
    if(!tableExists($conn, $table)){
        return;
    }

    if(!columnExists($conn, $table, $column)){
        mysqli_query($conn, "ALTER TABLE $table ADD $column $definition");
    }
}

function prepareDatabase($conn){
    addColumnIfMissing($conn, "appointments", "id", "INT AUTO_INCREMENT PRIMARY KEY FIRST");
    addColumnIfMissing($conn, "appointments", "user_id", "INT NULL");
    addColumnIfMissing($conn, "appointments", "status", "VARCHAR(30) NOT NULL DEFAULT 'Scheduled'");
    addColumnIfMissing($conn, "appointments", "service_name", "VARCHAR(100) NULL");
    addColumnIfMissing($conn, "appointments", "service_price", "DECIMAL(10,2) NOT NULL DEFAULT 0");
    addColumnIfMissing($conn, "appointments", "payment_method", "VARCHAR(30) NULL");
    addColumnIfMissing($conn, "appointments", "payment_reference", "VARCHAR(100) NULL");
    addColumnIfMissing($conn, "appointments", "payment_status", "VARCHAR(30) NOT NULL DEFAULT 'Pending'");
    addColumnIfMissing($conn, "appointments", "disease", "VARCHAR(120) NULL");
    addColumnIfMissing($conn, "appointments", "treated_by", "VARCHAR(100) NULL");
    addColumnIfMissing($conn, "appointments", "doctor_services", "TEXT NULL");
    addColumnIfMissing($conn, "appointments", "assigned_doctor_id", "INT NULL");
    addColumnIfMissing($conn, "appointments", "assigned_doctor_name", "VARCHAR(100) NULL");

    addColumnIfMissing($conn, "users", "phone", "VARCHAR(30) NULL");
    addColumnIfMissing($conn, "users", "email_verified", "TINYINT(1) NOT NULL DEFAULT 0");
    addColumnIfMissing($conn, "users", "verification_token", "VARCHAR(64) NULL");
}

prepareDatabase($conn);

if(basename($_SERVER['SCRIPT_NAME']) == "admin_dashboard.php"){
    if(session_status() == PHP_SESSION_NONE){
        session_start();
    }

    if(!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "doctor" && $_SESSION['role'] != "employee")){
        header("Location: login.php");
        exit();
    }

    function doctorIsFree($conn, $doctor_id, $appointment_date, $current_appointment_id = 0){
        $sql = "SELECT COUNT(*) AS total
                FROM appointments
                WHERE assigned_doctor_id = ?
                AND appointment_date = ?
                AND status != 'Cancelled'
                AND id != ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isi", $doctor_id, $appointment_date, $current_appointment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['total'] == 0;
    }

    $role = $_SESSION['role'];
    $dashboard_title = "Admin Dashboard";

    if($role == "doctor"){
        $dashboard_title = "Doctor Dashboard";
    }
    elseif($role == "employee"){
        $dashboard_title = "Employee Dashboard";
    }

    $success = "";
    $error = "";
    $doctor_options = [];
    $service_options = [
        "General Consultation",
        "Laboratory Test",
        "Dental Checkup",
        "Maternity Clinic",
        "Vaccination",
        "Emergency Care",
        "Specialist Doctor Review"
    ];

    $result = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'doctor' ORDER BY username ASC");
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            $doctor_options[] = $row;
        }
    }

    if(isset($_POST['reschedule'])){
        $appointment_id = intval($_POST['appointment_id']);
        $appointment_date = trim($_POST['appointment_date']);
        $assigned_doctor_id = 0;

        $check = "SELECT assigned_doctor_id
                  FROM appointments
                  WHERE id = ?
                  LIMIT 1";

        $stmt = mysqli_prepare($conn, $check);
        mysqli_stmt_bind_param($stmt, "i", $appointment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if($result && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $assigned_doctor_id = intval($row['assigned_doctor_id']);
        }

        if($assigned_doctor_id > 0 && !doctorIsFree($conn, $assigned_doctor_id, $appointment_date, $appointment_id)){
            $error = "Assigned doctor is occupied on that date. Please ask the patient to reschedule to another day.";
        } else {
            $sql = "UPDATE appointments
                    SET appointment_date = ?,
                        status = 'Rescheduled'
                    WHERE id = ?";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $appointment_date, $appointment_id);

            if(mysqli_stmt_execute($stmt)){
                $success = "Appointment rescheduled successfully.";
            } else {
                $error = "Unable to reschedule appointment.";
            }
        }
    }

    if(isset($_POST['assign_doctor']) && ($role == "admin" || $role == "employee")){
        $appointment_id = intval($_POST['appointment_id']);
        $assigned_doctor_id = intval($_POST['assigned_doctor_id']);
        $assigned_doctor_name = "";
        $appointment_date = "";

        foreach($doctor_options as $doctor_option){
            if($doctor_option['id'] == $assigned_doctor_id){
                $assigned_doctor_name = $doctor_option['username'];
            }
        }

        $check = "SELECT appointment_date
                  FROM appointments
                  WHERE id = ?
                  LIMIT 1";

        $stmt = mysqli_prepare($conn, $check);
        mysqli_stmt_bind_param($stmt, "i", $appointment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if($result && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $appointment_date = $row['appointment_date'];
        }

        if($assigned_doctor_id <= 0 || $assigned_doctor_name == ""){
            $error = "Please select a doctor to assign.";
        } elseif(!doctorIsFree($conn, $assigned_doctor_id, $appointment_date, $appointment_id)){
            $error = "That doctor is occupied on the selected appointment date. Please choose another doctor or ask the patient to reschedule.";
        } else {
            $sql = "UPDATE appointments
                    SET assigned_doctor_id = ?,
                        assigned_doctor_name = ?
                    WHERE id = ?";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isi", $assigned_doctor_id, $assigned_doctor_name, $appointment_id);

            if(mysqli_stmt_execute($stmt)){
                $success = "Doctor assigned successfully.";
            } else {
                $error = "Unable to assign doctor.";
            }
        }
    }

    if(isset($_POST['cancel'])){
        $appointment_id = intval($_POST['appointment_id']);

        $sql = "UPDATE appointments
                SET status = 'Cancelled'
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $appointment_id);

        if(mysqli_stmt_execute($stmt)){
            $success = "Appointment cancelled successfully.";
        } else {
            $error = "Unable to cancel appointment.";
        }
    }

    if(isset($_POST['mark_treated']) && $role == "doctor"){
        $appointment_id = intval($_POST['appointment_id']);
        $disease = trim($_POST['disease']);
        $selected_doctor_services = isset($_POST['doctor_services']) ? $_POST['doctor_services'] : [];
        $valid_doctor_services = [];
        $treated_by = $_SESSION['username'];

        foreach($selected_doctor_services as $doctor_service){
            if(in_array($doctor_service, $service_options)){
                $valid_doctor_services[] = $doctor_service;
            }
        }

        $doctor_services = implode(", ", $valid_doctor_services);

        if($disease == "" || $doctor_services == ""){
            $error = "Please enter the disease and services before marking the patient as treated.";
        } else {
            $sql = "UPDATE appointments
                    SET status = 'Treated',
                        disease = ?,
                        doctor_services = ?,
                        treated_by = ?
                    WHERE id = ?";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $disease, $doctor_services, $treated_by, $appointment_id);

            if(mysqli_stmt_execute($stmt)){
                $success = "Patient marked as treated successfully.";
            } else {
                $error = "Unable to update patient treatment status.";
            }
        }
    }

    $users_count = 0;
    $appointments_count = 0;
    $doctors_count = 0;
    $patients_count = 0;
    $employees_count = 0;
    $scheduled_count = 0;
    $cancelled_count = 0;
    $paid_total = 0;
    $appointments = [];
    $patient_reports = [];
    $disease_reports = [];

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
    if($result){
        $row = mysqli_fetch_assoc($result);
        $users_count = $row['total'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'doctor'");
    if($result){
        $row = mysqli_fetch_assoc($result);
        $doctors_count = $row['total'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'patient'");
    if($result){
        $row = mysqli_fetch_assoc($result);
        $patients_count = $row['total'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'employee'");
    if($result){
        $row = mysqli_fetch_assoc($result);
        $employees_count = $row['total'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments");
    if($result){
        $row = mysqli_fetch_assoc($result);
        $appointments_count = $row['total'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments WHERE status != 'Cancelled'");
    if($result){
        $row = mysqli_fetch_assoc($result);
        $scheduled_count = $row['total'];
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments WHERE status = 'Cancelled'");
    if($result){
        $row = mysqli_fetch_assoc($result);
        $cancelled_count = $row['total'];
    }

    $result = mysqli_query($conn, "SELECT SUM(service_price) AS total FROM appointments WHERE status != 'Cancelled'");
    if($result){
        $row = mysqli_fetch_assoc($result);
        $paid_total = $row['total'] ? $row['total'] : 0;
    }

    if($role == "doctor"){
        $sql = "SELECT * FROM appointments
                WHERE assigned_doctor_id = ?
                ORDER BY appointment_date ASC, id ASC";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while($row = mysqli_fetch_assoc($result)){
            $appointments[] = $row;
        }
    } else {
        $result = mysqli_query($conn, "SELECT * FROM appointments ORDER BY appointment_date ASC, id ASC");
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $appointments[] = $row;
            }
        }
    }

    if($role == "doctor" || $role == "admin"){
        $sql = "SELECT u.username,
                       u.email,
                       u.phone,
                       u.email_verified,
                       COUNT(a.id) AS appointment_count,
                       MAX(a.appointment_date) AS last_appointment
                FROM users u
                LEFT JOIN appointments a
                ON a.user_id = u.id OR a.email = u.email
                WHERE u.role = 'patient'
                GROUP BY u.id, u.username, u.email, u.phone, u.email_verified
                ORDER BY u.username ASC";

        $result = mysqli_query($conn, $sql);

        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $patient_reports[] = $row;
            }
        }

        $sql = "SELECT disease,
                       COUNT(*) AS total_patients
                FROM appointments
                WHERE disease IS NOT NULL
                AND disease != ''
                AND status = 'Treated'
                GROUP BY disease
                ORDER BY total_patients DESC, disease ASC";

        $result = mysqli_query($conn, $sql);

        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $disease_reports[] = $row;
            }
        }
    }
}

?>
