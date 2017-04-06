<!DOCTYPE html>
<html lang="en">

<head>
    <title>K-Town Car Share</title>
</head>

<body>

<form name='back' id='back' action='fleet.php' method='post'>
    <input type='submit' id='back' name='back' value='Admin Panel' />
</form>

<?php
    if (isset($_POST['back'])) {
        header("Location: admin.php");
        die();
    }
?>

<h2>
    View Reservations
</h2>

<?php
 //Create a user session or resume an existing one
 session_start();
 if(isset($_SESSION['memberId'])){
    include_once 'config/connection.php'; // include database connection
 } else {
	// user is not logged in, redirect the browser to the login index.php page and kill this page.
	header("Location: index.php");
	die();
 }
?>

<?php
if(isset($_SESSION['memberId'])){
  include_once 'config/connection.php'; 
  $query = "SELECT name FROM Member WHERE memberId=?";
  $stmt = $con->prepare($query);
  $stmt->bind_Param("s", $_SESSION['memberId']);
  $stmt->execute();
  $result = $stmt->get_result();
  $data = $result->fetch_assoc();
} else {
  //User is not logged in. Redirect the browser to the login index.php page and kill this page.
  header("Location: index.php");
  die();
}

if(isset($_POST['viewRezBtn'])){
  $query_date = $_POST['date'];
}
?>

<form name='viewRez' id='viewRez' action='viewReservations.php' method='post'>
    <table border='0'>
        <tr>
            <td>Select Date: </td>
            <td><input type='date' name='date' id='date' value='<?php echo $query_date; ?>'/></td>
            <td>
                <input type='submit' id='viewRezBtn' name='viewRezBtn' value='View Reservations' /> 
            </td>        
        </tr>
    </table>
</form>
<br>

<?php
// check if pickup form has been submitted
if(isset($_POST['viewRezBtn'])){
  if ($query_date != '') {
    // fetch reservations
    $reservation_stmt = $con->prepare("SELECT * FROM reservations WHERE date =?");
    $reservation_stmt->bind_Param("s", $query_date);
    $reservation_stmt->execute();
    $reservation_result = $reservation_stmt->get_result();
    if ($reservation_result->fetch_assoc()) {
    $reservation_stmt->execute();
    $reservation_result = $reservation_stmt->get_result();
    echo "<table border=\"1px solid black\" width=\"80%\"><tr><th>Date</th><th>Reservation ID</th><th >Member ID</th><th>VIN</th><th>Access Code</th><th>Length</th><th>Active</th></tr>" ;
    while($row = $reservation_result->fetch_assoc()) {
    echo "<tr><td style=\"text-align:center;\">". $row[('date')] . "</td><td style=\"text-align:center;\">". $row[('reservationId')] . "</td><td style=\"text-align:center;\">".$row[('memberId')]."</td><td style=\"text-align:center;\">".$row[('vin')] . "</td><td style=\"text-align:center;\">". $row[('accessCode')] . "</td><td style=\"text-align:center;\">". $row[('length')] . "</td><td style=\"text-align:center;\">". $row[('active')];
    echo "</td></tr>";
    }
    echo "</table>";
    } else {
        echo "There are no reservations in the system for this date.";
    }
  } else {
    echo "<script>alert('You must select a date.');</script>";
  }
}
?>

</body>
</html>