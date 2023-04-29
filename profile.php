<?php
//Start a new session
session_start();
//Set the session duration for 60 seconds
$expiryTime = 60 * 60 * 60;

//Check the session start time is set or not
if (!isset($_SESSION['start'])) {
   //Set the session start time
   $_SESSION['start'] = time();
}

// Check if the user is not logged in
if (!isset($_SESSION['student_ID'])) {
   // Redirect to the login page
   header("Location: login.php");
   exit();
}

echo '<h2>' . $_SESSION['first_name'] . '</h2>';
include "connection.php"; // server's information
try {
   // create an object that connect to the database
   $conn = new mysqli($server_name, $username, $password, $database_name);

   echo "Connected Successfully <br>";
   if (isset($_POST['submit'])) {

      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];
      $DOB = $_POST['DOB'];
      $student_email = $_POST['student_email'];
      $program = $_POST['program'];
      $city = $_POST['city'];
      $street_number = $_POST['street_number'];
      $street_name = $_POST['street_name'];
      $province = $_POST['province'];
      $postal_code = $_POST['postal_code'];
      $avatar = $_POST['avatar'];
      $zero = 0;

      // Add a row (record) to table users_info
      $stmt1 = $conn->prepare("INSERT INTO users_info VALUES (NULL, ?, ?, ?, ?)");
      $stmt1->bind_param("ssss", $student_email, $first_name, $last_name, $DOB);

      //  $sql_info1 = "INSERT INTO users_info VALUES ('', '$student_email', '$first_name', '$last_name', '$DOB')";

      if ($stmt1->execute()) {
         $parent_id = mysqli_insert_id($conn);
         echo "New record created successfully";
      } else {
         echo "We in deep trouble";
      }
      $stmt1->close();

      $one = 1;

      $parent_id = mysqli_insert_id($conn);
      $_SESSION["student_ID"] = $parent_id;
      $_SESSION["last_name"] = $last_name;
      $_SESSION["first_name"] = $first_name;
      $_SESSION["DOB"] = $DOB;
      $_SESSION["student_email"] = $student_email;
      $_SESSION["program"] = $program;
      $_SESSION["city"] = $city;
      $_SESSION["street_number"] = $street_number;
      $_SESSION["street_name"] = $street_name;
      $_SESSION["province"] = $province;
      $_SESSION["postal_code"] = $postal_code;
      $_SESSION["account_type"] = $one;

      $stmt2 = $conn->prepare("INSERT INTO users_program VALUES (?, ?)");
      $stmt2->bind_param("is", $parent_id, $program);

      if ($stmt2->execute()) {
         echo "New record created successfully";
      } else {
         echo "We in deep trouble";
      }
      $stmt2->close();

      $stmt3 = $conn->prepare("INSERT INTO users_avatar VALUES (?, ?)");
      $stmt3->bind_param("is", $parent_id, $avatar);

      if ($stmt3->execute()) {
         echo "New record created successfully";
      } else {
         echo "We in deep trouble";
      }
      $stmt3->close();

      $stmt4 = $conn->prepare("INSERT INTO users_address VALUES (?, ?, ?, ?, ?, ?)");
      $stmt4->bind_param("isssss", $parent_id, $street_number, $street_name, $city, $province, $postal_code);
      if ($stmt4->execute()) {
         echo "New record created successfully";
      } else {
         echo "We in deep trouble";
      }
      $stmt4->close();

      //  $sql_info2 = "INSERT INTO users_program VALUES ('$parent_id', '$program')";
      //  $sql_info3 = "INSERT INTO users_avatar VALUES ('$parent_id', '$avatar')";
      //  $sql_info4 = "INSERT INTO users_address VALUES ('$parent_id', '$street_number', '$street_name', '$city', 'province', '$postal_code')";

      header('Location: profile.php');
      exit;
   }

   $conn->close();
} catch (mqsqli_sql_exception $e) {
   $error = $e->getMessage();
   echo $error;
   echo "Hello plz work";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Update SYSCBOOK profile</title>
   <link rel="stylesheet" href="assets/css/reset.css" />
   <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
   <header>
      <h1>SYSCBOOK</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>
   <nav>
      <?php
      if (!isset($_SESSION["student_ID"])) { // user is not logged in
         ?>
         <table class="loggedOut">
            <tr class="highlightedText">
               <td><a href="index.php">Home</a></td>
            </tr>
            <tr>
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
            <tr class="highlightedText">
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
      <section name="form">
         <h2>Update Profile information</h2>
         <form method="POST" action="" id="form">
            <fieldset class="personal">
               <legend>
                  <p>Personal information</p>
               </legend>
               <p>
                  <label>First name:</label>
                  <input type="text" name="first_name" id="first_name" value="<?php
                  if (isset($_SESSION['first_name']))
                     echo $_SESSION['first_name'];
                  else
                     echo "";
                  ?>">
               </p>

               <p>
                  <label>Last Name:</label>
                  <input type="text" name="last_name" id="last_name" value="<?php
                  if (isset($_SESSION['last_name']))
                     echo $_SESSION['last_name'];
                  else
                     echo "";
                  ?>">
               </p>
               <p>
                  <label>DOB:</label>
                  <input placeholder="yyyy-mm-dd" type="date" name="DOB" id="DOB" value="<?php
                  if (isset($_SESSION['DOB']))
                     echo $_SESSION['DOB'];
                  else
                     echo "";
                  ?>">
               </p>

               <legend>Address</legend>
               <p>
                  <label>Street Number:</label>
                  <input type="number" name="street_number" id="street_number" value="<?php
                  if (isset($_SESSION['street_number']))
                     echo $_SESSION['street_number'];
                  else
                     echo "";
                  ?>">
               </p>
               <p>
                  <label>Street Name:</label>
                  <input type="text" name="street_name" id="street_name" value="<?php
                  if (isset($_SESSION['street_name']))
                     echo $_SESSION['street_name'];
                  else
                     echo "";
                  ?>">
               </p>
               <pre></pre>
               <p>
                  <label>City:</label>
                  <input type="text" name="city" id="city" value="<?php
                  if (isset($_SESSION['city']))
                     echo $_SESSION['city'];
                  else
                     echo "";
                  ?>">
               </p>
               <p>
                  <label>Province:</label>
                  <input type="text" name="province" id="province" value="<?php
                  if (isset($_SESSION['province']))
                     echo $_SESSION['province'];
                  else
                     echo "";
                  ?>">
               </p>
               <p>
                  <label>Postal Code:</label>
                  <input type="text" name="postal_code" id="postal_code" value="<?php
                  if (isset($_SESSION['postal_code']))
                     echo $_SESSION['postal_code'];
                  else
                     echo "";
                  ?>">>
               </p>

               <legend>Profile Information</legend>
               <p>
                  <label>Email Address:</label>
                  <input type="email" name="student_email" id="student_email" value="<?php
                  if (isset($_SESSION['student_email']))
                     echo $_SESSION['student_email'];
                  else
                     echo "";
                  ?>">
               </p>
               <p>
                  <label>Program:</label>
                  <input placeholder="Choose Program" type="text" name="program" id="program" value="<?php
                  if (isset($_SESSION['program']))
                     echo $_SESSION['program'];
                  else
                     echo "";
                  ?>">
               </p>
               <pre></pre>


               <label class="avatar" name="avatar" for="avatar">Choose Your Avatar</label>
               <pre></pre>
               <optgroup name="avatar" id="avatar">
                  <option class="avatar" name="avatar">
                     <input type="radio" name="avatar" value="images\img_avatar1.png">
                     <img src="images\1.png" alt="images\2.png" width="50" height="50">
                  </option>
                  <option class="avatar" name="avatar">
                     <input type="radio" name="avatar" value="images\img_avatar2.png">
                     <img src="images\2.png" alt="images\2.png" width="50" height="50">
                  </option>
                  <option class="avatar" name="avatar">
                     <input type="radio" name="avatar" value="images\img_avatar3.png">
                     <img src="images\3.png" alt="images\2.png" width="50" height="50">
                  </option>
                  <option class="avatar" name="avatar">
                     <input type="radio" name="avatar" value="images\img_avatar4.png">
                     <img src="images\4.png" alt="images\2.png" width="50" height="50">
                  </option>
                  <option class="avatar" name="avatar">
                     <input type="radio" name="avatar" value="images\img_avatar5.png">
                     <img src="images\5.png" alt="images\2.png" width="50" height="50">
                  </option>
               </optgroup>
               <pre></pre>
               <p>
                  <input type="submit" name="submit" value="Submit">
                  <input type="reset">
               </p>
            </fieldset>
         </form>
      </section>

   </main>


</body>

</html>