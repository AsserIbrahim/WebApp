<?php
//Start a new session
session_start();
$expiryTime = 60 * 60 * 60;

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
         $_SESSION["student_ID"] = '';
         $_SESSION["last_name"] = '';
         $_SESSION["first_name"] = '';
         $_SESSION["DOB"] = '';
         $_SESSION["student_email"] = '';
         $_SESSION["program"] = '';


         $first_name = $_POST['first_name'];
         $last_name = $_POST['last_name'];
         $DOB = $_POST['DOB'];
         $student_email = $_POST['student_email'];
         $program = $_POST['program'];
         $password = $_POST['password'];
         $confirm_password = $_POST['confirm_password'];

         $_POST['city'] = "";
         $_POST['street_number'] = "";
         $_POST['street_name'] = "";
         $_POST['province'] = "";
         $_POST['postal_code'] = "";
         $_POST['avatar'] = "";
         $_POST['city'] = "";
         $_POST['city'] = "";

         $zero = 0;
         $one = 1;
   
         // Check if the email already exists in the database
         $sql_check_email = $conn->prepare("SELECT * FROM users_info WHERE student_email = ?");
         $sql_check_email->bind_param("s", $student_email);
         $sql_check_email->execute();
         $result = $sql_check_email->get_result();

         // If email already exists, display an error message
         if ($result->num_rows > 0) {
            echo "<p>Email address already exists. Please enter a new email address.</p>";
         } else {
            // Check if the password and confirm password fields match
            if ($password != $confirm_password) {
               echo "<p>Passwords do not match. Please enter matching passwords.</p>";
            } else {
               echo "WE made it";
               // User's info SQL
               $sql_info1 = $conn->prepare("INSERT INTO users_info (student_email, first_name, last_name, DOB) VALUES (?, ?, ?, ?)");
               $sql_info1->bind_param("ssss", $student_email, $first_name, $last_name, $DOB);

               if ($sql_info1->execute() === TRUE) {
                  $parent_id = $sql_info1->insert_id;
                  echo "parent id is, $parent_id";
                  echo "sql_info 1 New record created successfully";
               } else {
                  echo "We in deep trouble";
                  throw new Exception('Could not insert in user_info database');
               }
               $sql_check_email->bind_param("s", $student_email);
               //$sql_check_email->execute();
               // Use password_hash() php function to create a hashed value of the password before adding it to the users_passwords table
               $hashed_password = password_hash($password, PASSWORD_BCRYPT);

               // Insert user's password into users_passwords table
               $sql_password = $conn->prepare("INSERT INTO users_passwords (student_ID, password) VALUES (?, ?)");
               $sql_password->bind_param("is", $parent_id, $hashed_password);
               if ($sql_password->execute() === TRUE) {
                  echo "New record created successfully";
               } else {
                  echo "We in deep trouble";
               }

               #$parent_id = mysqli_insert_id($conn);
               $_SESSION["student_ID"] = $parent_id;
               $_SESSION["last_name"] = $last_name;
               $_SESSION["first_name"] = $first_name;
               $_SESSION["DOB"] = $DOB;
               $_SESSION["student_email"] = $student_email;
               $_SESSION["program"] = $program;

               $sql_info2 = $conn->prepare("INSERT INTO users_program (student_ID, program) VALUES (?, ?)");
               $sql_info2->bind_param("is", $parent_id, $program);
               if ($sql_info2->execute() === TRUE) {
                  echo "New record created successfully";
               } else {
                  echo "We in deep trouble";
               }

               $sql_info3 = $conn->prepare("INSERT INTO users_avatar (student_ID, avatar) VALUES (?, ?)");
               $sql_info3->bind_param("ii", $parent_id, $zero);
               if ($sql_info3->execute() === TRUE) {
                  echo "New record created successfully";
               } else {
                  echo "We in deep trouble";
               }

               $sql_info4 = $conn->prepare("INSERT INTO users_address (student_ID, street_number, street_name, city, province, postal_code) VALUES (?, ?, ?, ?, ?, ?)");
               $sql_info4->bind_param("isssss", $parent_id, $_POST['street_number'], $_POST['street_name'], $_POST['city'], $_POST['province'], $_POST['postal_code']);
               if ($sql_info4->execute() === TRUE) {
                  echo "New record created successfully";
               } else {
                  echo "We in deep trouble";
               }

               $sql_info5 = $conn->prepare("INSERT INTO users_permissions (student_ID, account_type) VALUES (?, ?)");
               $sql_info5->bind_param("is", $parent_id, $one);
               if ($sql_info5->execute() === TRUE) {
                  echo "New record created successfully";
               } else {
                  echo "We in deep trouble";
               }

               // $sql_info2 = "INSERT INTO users_program VALUES ('$parent_id', '$program')";
               // $sql_info3 = "INSERT INTO users_avatar VALUES ('$parent_id', '$zero')";
               // $sql_info4 = "INSERT INTO users_address VALUES ('$parent_id', '$zero', '', '', '', '')";
   
               $_SESSION['first_name'] = $_POST['first_name'];
               $_SESSION['last_name'] = $_POST['last_name'];
               $_SESSION['DOB'] = $_POST['DOB'];
               $_SESSION['student_email'] = $_POST['student_email'];
               $_SESSION['program'] = $_POST['program'];
               $_SESSION['account_type'] = $one;
               header('Location: profile.php');
               exit;
            }
         }
      }
      // $stmt->close();
      $conn->close();
   } catch (mysqli_sql_exception $e) {
      $error = $e->getMessage();
      echo $error;
      echo "Hello plz work";
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
            <tr class="highlightedText">
               <td><a href="register.php">Register</a></td>
            </tr>
            <tr>
               <td><a href="login.php">Log in</a></td>
            </tr>
         </table>
         <?php
      } else { // user is logged in
         ?>
         <table class="loggedIn">
            <tr>
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
         <h2>Register a new profile</h2>
         <form method="POST" action="" id="form">
            <fieldset class="personal">
               <legend>
                  <p>Personal information</p>
               </legend>
               <p>
                  <label>First name:</label>
                  <input type="text" name="first_name" id="first_name">
               </p>

               <p>
                  <label>Last Name:</label>
                  <input type="text" name="last_name" id="last_name">
               </p>
               <p>
                  <label>DOB:</label>
                  <input placeholder="yyyy-mm-dd" type="date" name="DOB" id="DOB">
               </p>

               <legend>Profile Information</legend>
               <p>
                  <label>Email Address:</label>
                  <input type="email" name="student_email" id="student_email">
               </p>
               <pre></pre>
               <p>
                  <label>Program:</label>
                  <input placeholder="Choose Program" type="text" name="program" id="program">
               </p>
               <pre></pre>
               <p>
                  <label for="password">Password:</label>
                  <input type="password" name="password" id="password" required>
               </p>
               <pre></pre>
               <p>
                  <label for="confirm_password">Confirm password:</label>
                  <input type="password" name="confirm_password" id="confirm_password" oninput="checkPasswords()"
                     required>
                  <span id="confirmError" class="error" style="visibility: collapse;">Passwords do not match!</span>
               </p>
               <p>
                  Already have an account?
                  <a href="login.php">Login in here</a>
               </p>
               <p>
                  <input type="submit" onclick="redirect" name="submit" value="Submit" id="submit">
                  <input type="reset">
               </p>
            </fieldset>
         </form>
      </section>
   </main>
   <script src="assets/css/fetch.js"></script>
</body>

</html>