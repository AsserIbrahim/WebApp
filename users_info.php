<?php
session_start();

//Check if user is logged in
if (!isset($_SESSION['account_type'])) {
  // Redirect to login page
  header('Location: login.php');
  exit();
}

//Check if user is an Admin
if ($_SESSION['account_type'] !== 0) {
  // Display error message
  echo '<p>You do not have permission to access this page.</p>';
  header('Location: index.php');
  exit();
}

// Retrieve user data from database (e.g. using PDO or mysqli)

include "connection.php"; // server's information
try {
    // create an object that connect to the database
    $conn = new mysqli($server_name, $username, $password, $database_name);

    echo "Connected Successfully <br>";

    $user_data = $conn->prepare("SELECT * FROM users_info INNER JOIN users_program ON users_info.student_ID = users_program.student_ID");
    //$user_data->bind_param("s", $student_email);
    $user_data->execute();
    $user_data = $user_data->get_result();

    // $program_data = $conn->prepare("SELECT * FROM users_program");
    // //$user_data->bind_param("s", $student_email);
    // $program_data->execute();
    // $program_data = $program_data->get_result();
    // $user_data = $result->fetch_assoc();

} catch (mqsqli_sql_exception $e) {
    $error = $e->getMessage();
    echo $error;
    echo "Hello plz work";
}

// Display user data in a table (using HTML and PHP code as described above)
?>

<table>
    <tr>
        <th>Student ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Program</th>
    </tr>
    <?php while ($user = $user_data->fetch_assoc()): ?>
        <tr>
            <td>
                <?php echo $user['student_ID']; ?>
            </td>
            <td style="padding-left: 100px;">
                <?php echo $user['first_name']; ?>
            </td>
            <td style="padding-left: 100px;">
                <?php echo $user['last_name']; ?>
            </td>
            <td style="padding-left: 50px;">
                <?php echo $user['student_email']; ?>
            </td>
            <td style="padding-left: 280px;">
                <?php echo $user['program']; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>