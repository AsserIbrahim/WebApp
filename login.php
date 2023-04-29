<?php
//Start a new session
session_start();
$expiryTime = 60 * 60 * 60;

// Check if the user is already logged in
if (isset($_SESSION['student_ID'])) {
    header('Location: index.php');
    exit();
}

//Check the session start time is set or not
if (!isset($_SESSION['start'])) {
   //Set the session start time
   $_SESSION['start'] = time();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Register on SYSCBOOK</title>
   <link rel="stylesheet" href="assets/css/reset.css" />
   <link rel="stylesheet" href="assets/css/style.css" />
   <script>
      function validateForm() {
         var password = document.getElementById("password").value;
         var confirm_password = document.getElementById("confirm_password").value;
         if (password != confirm_password) {
            alert("Passwords do not match!");
            return false;
         }
      }
      function checkPasswords(e) {
         let pass = document.getElementById("password").value
         let confirmPass = document.getElementById("confirm_password").value
         let confirmError = document.getElementById("confirmError")
         // console.log("Pass", pass)
         // console.log("Confirm Pass", confirmPass)
         // console.log("ConfirmError HTML: ", confirmError)
         if (pass != confirmPass) {
            confirmError.style = "visibility: visible;"
         }
         else {
            confirmError.style = "visibility: collapse;"
         }
      }
   </script>
</head>

<body>

   <?php

   include "connection.php"; // server's information
   try {
      // create an object that connect to the database
      $conn = new mysqli($server_name, $username, $password, $database_name);

      echo "Connected Successfully <br>";

      if (isset($_POST['submit'])) {

         $_SESSION["student_ID"] = "";
         $_SESSION["last_name"] = "";
         $_SESSION["first_name"] = "";
         $_SESSION["DOB"] = "";
         $_SESSION["student_email"] = "";
         $_SESSION["program"] = "";
         $_SESSION["city"] = "";
         $_SESSION["street_number"] = "";
         $_SESSION["street_name"] = "";
         $_SESSION["province"] = "";
         $_SESSION["postal_code"] = "";
         $_SESSION["account_type"] = "";

         $student_email = $_POST['student_email'];
         $password = $_POST['password'];

         // Check if the email already exists in the database
         $sql_check_email = $conn->prepare("SELECT * FROM users_info WHERE student_email = ?");
         $sql_check_email->bind_param("s", $student_email);
         $sql_check_email->execute();
         $result = $sql_check_email->get_result();
         if ($result->num_rows == 1) {
            // The email exists in the database, now we check the password
            $row = $result->fetch_assoc();
            $ID = $row['student_ID'];
            echo "ID FOUND: " . $ID;

            $sql_check_pass = $conn->prepare("SELECT * FROM users_passwords WHERE student_ID = ?");
            $sql_check_pass->bind_param("s", $ID);
            $sql_check_pass->execute();
            $result_pass = $sql_check_pass->get_result();

            echo "before first sql check";

            if ($result_pass->num_rows === 1) {
               echo 'we made it';
               $row1 = $result_pass->fetch_assoc();
               $hash = $row1['password'];
               echo $hash;
               if (password_verify($password, $hash)) {
                  // The password is correct, set the session variable and redirect to index.php
                  $sql_check_permission = $conn->prepare("SELECT * FROM users_permissions WHERE student_ID = ?");
                  $sql_check_permission->bind_param("s", $ID);
                  $sql_check_permission->execute();
                  $result_permission = $sql_check_permission->get_result();

                  $sql_check_program = $conn->prepare("SELECT * FROM users_program WHERE student_ID = ?");
                  $sql_check_program->bind_param("s", $ID);
                  $sql_check_program->execute();
                  $result_program = $sql_check_program->get_result();

                  $sql_check_address = $conn->prepare("SELECT * FROM users_address WHERE student_ID = ?");
                  $sql_check_address->bind_param("s", $ID);
                  $sql_check_address->execute();
                  $result_address = $sql_check_address->get_result();

                  if ($result_permission->num_rows === 1) {
                     $rowPer = $result_permission->fetch_assoc();
                     $rowPro = $result_program->fetch_assoc();
                     $rowAdd = $result_address->fetch_assoc();
                     $rowPro = $result_program->fetch_assoc();
                     $_SESSION['first_name'] = $row['first_name'];
                     $_SESSION['last_name'] = $row['last_name'];
                     $_SESSION['DOB'] = $row['DOB'];
                     $_SESSION['student_email'] = $row['student_email'];
                     $_SESSION['program'] = $rowPro['program'];
                     $_SESSION['account_type'] = $rowPer['account_type'];
                     header("Location: index.php");
                     exit();
                  } else { //Everything works but user doesnt have an associated permission value in database
                     $rowPro = $result_program->fetch_assoc();
                     $rowAdd = $result_address->fetch_assoc();
                     $rowPro = $result_program->fetch_assoc();
                     $one = 1;
                     //Add user permission default to non-admin
                     $sql_info5 = $conn->prepare("INSERT INTO users_permissions (student_ID, account_type) VALUES (?, ?)");
                     $sql_info5->bind_param("is", $ID, $one);
                     if ($sql_info5->execute() === TRUE) {
                        echo "New record created successfully";
                     } else {
                        echo "We in deep trouble";
                     }
                     $_SESSION['first_name'] = $row['first_name'];
                     $_SESSION['last_name'] = $row['last_name'];
                     $_SESSION['DOB'] = $row['DOB'];
                     $_SESSION['student_email'] = $row['student_email'];
                     $_SESSION['program'] = $rowPro['program'];
                     $_SESSION['account_type'] = $one;
                     header("Location: index.php");
                  }

               } else {
                  echo "hi";
                  $error = "Incorrect password";
               }
            } else {
               echo "hi 1";
               $error = "Incorrect student ID";
               exit();
            }

            // if (password_verify($password, $hash)) {
            //    echo '2';
            //    // The password is correct, set the session variable and redirect to index.php
            //    $_SESSION['student_email'] = $student_email;
            //    $_SESSION['account_type'] = $row['account_type'];
            //    //header("Location: index.php");
            //    exit();
            // } else {
            //    $error = "Incorrect password";
            // }
         } else {
            echo 'error';
            $error = "Email not found";
         }
         $sql_check_email->close();
      }

      $conn->close();
   } catch (mysqli_sql_exception $e) {
      $error = $e->getMessage();
      echo $error;
   }

   ?>

   <header>
      <h1>SYSCBOOK</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>
   <nav>
      <?php
      if (!isset($_SESSION["student_ID"])) { // user is not logged in
         ?>
         <table class="loggedOut">
            <tr>
               <td><a href="index.php">Home</a></td>
            </tr>
            <tr>
               <td><a href="register.php">Register</a></td>
            </tr>
            <tr class="highlightedText">
               <td><a href="login.php">Log in</a></td>
            </tr>
         </table>
         <?php
      } else { // user is logged in
         ?>
         <table class="loggedIn">
            <tr class="highlightedText">
               <td><a href="index.php">Home</a></td>
            </tr>
            <tr>
               <td><a href="profile.php">Profile</a></td>
            </tr>
            <tr>
               <td><a href="logout.php">Log out</a></td>
            </tr>
         </table>
         <?php
      }
      ?>
   </nav>
   <main>
      <section>
         <h2>Login Page</h2>
         <form method="POST" action="" id="form">
            <fieldset class="personal">

               <legend>Login Username and Passowrd</legend>
               <p>
                  <label>Email Address:</label>
                  <input type="email" name="student_email" id="student_email">
               </p>
               <pre></pre>
               <pre></pre>
               <p>
                  <label for="password">Password:</label>
                  <input type="password" name="password" id="password" required>
               </p>
               <pre></pre>
               <p>
                  <input type="submit" onclick="redirect" name="submit" value="Login" id="submit">
                  <input type="reset">
               </p>
               <p>
                  Don't have an account?
                  <a href="register.php">Sign up here</a>
               </p>
            </fieldset>
         </form>
      </section>
   </main>
   <script src="assets/css/fetch.js"></script>
</body>
</html>