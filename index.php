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

// Check if the user is logged in
if (!isset($_SESSION['student_ID'])) {
   // If the user is not logged in, redirect to login page
   header('Location: login.php');
   exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>SYSCBOOK - Main</title>
   <link rel="stylesheet" href="assets/css/reset.css" />
   <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
   <header>
      <h1>SYSCBOOK</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>
   <nav>
   <?php if (!isset($_SESSION["student_ID"])) { // user is not logged in ?>
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
   <?php } else { // user is logged in ?>
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
         <?php if ($_SESSION["account_type"] === 0) { // admin account type ?>
            <tr>
               <td><a href="users_info.php">Users info (Admin only)</a></td>
            </tr>
         <?php } ?>
      </table>
   <?php } ?>
</nav>



   <main>
      <section name="newPost">
         <form method="POST" action="">
            <fieldset>
               <legend>
                  <p>New Post</p>
               </legend>
               <textarea placeholder="What's on your mind? (max 500 char)" type="text" name="new_post" id="" cols="60"
                  rows="5">text here</textarea>
               <div class="buttons">
                  <button>
                     <input type="submit" name="submit">
                  </button>
                  <button>
                     <input type="reset">
                  </button>
               </div>
            </fieldset>
         </form>

      </section>
      <?php
      echo '<h2>' . $_SESSION['student_ID'] . '</h2>';
      include "connection.php"; // server's information
      $new_ID = $_SESSION["student_ID"];
      try {
         // create an object that connect to the database
         $conn = new mysqli($server_name, $username, $password, $database_name);

         echo "Connected Successfully <br>";

         if (isset($_POST['submit'])) {
            $time = time();
            $new_post = $_POST['new_post'];
            $post_date = date("Y-m-d H:i:s", $time);
            $zero = 0;

            echo "" . $_SESSION['student_ID'];

            // Use prepared statement to add a row (record) to table users_posts
            $stmt = $conn->prepare("INSERT INTO users_posts (student_ID, new_post, post_date) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $_SESSION["student_ID"], $new_post, $post_date);

            // Add a row (record) to table users_info
            $sql_posts = "INSERT INTO users_posts VALUES ('', '{$_SESSION["student_ID"]}', '$new_post', '$post_date')";

            //$conn->query($sql_info);
            if ($stmt->execute()) {
               echo "New record created successfully";
            } else {
               echo "Error: " . $sql_posts . "<br>" . $conn->error;
            }

            $stmt->close();

            header('Location: index.php');
            exit;
         }

         // display the last 5 posts by the user
         $stmt = $conn->prepare("SELECT * FROM users_posts ORDER BY post_date DESC LIMIT 10");
         //$stmt->bind_param("i", $_SESSION["student_ID"]);
         $stmt->execute();
         $result_posts = $stmt->get_result();
         // $result_posts = mysqli_query($conn, $sql_posts);
         echo '<h2> here</h2>';
         while ($row = $result_posts->fetch_assoc()) {
            echo "<div class=" . "posts" . ">";
            echo "<details>";
            echo "<summary> POST </summary>";
            echo "<p>" . $row["new_post"] . "</p>";
            echo "</details>";
            echo "</div>";
         }

         $stmt->close();
         $conn->close();
      } catch (mqsqli_sql_exception $e) {
         $error = $e->getMessage();
         echo $error;
         echo "Hello plz work";
      }
      ?>
   </main>
</body>

</html>