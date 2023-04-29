<?php
//Start a new session
session_start();
$expiryTime = 60 * 60 * 60;

// Check if the user is already logged in
// if (isset($_SESSION['student_ID'])) {
//     header('Location: index.php');
//     exit();
// }

//Check the session start time is set or not
if (!isset($_SESSION['start'])) {
    //Set the session start time
    $_SESSION['start'] = time();
}

if (!isset($_SESSION['student_ID'])) {
    // If the user is not logged in, redirect to login page
    header('Location: login.php');
    exit;
} else {
    // $_SESSION["student_ID"] = "";
    // $_SESSION["last_name"] = "";
    // $_SESSION["first_name"] = "";
    // $_SESSION["DOB"] = "";
    // $_SESSION["student_email"] = "";
    // $_SESSION["program"] = "";
    // $_SESSION["city"] = "";
    // $_SESSION["street_number"] = "";
    // $_SESSION["street_name"] = "";
    // $_SESSION["province"] = "";
    // $_SESSION["postal_code"] = "";
    // $_SESSION["account_type"] = "";
    session_unset();
    header('Location: login.php');
    exit;
}

?>