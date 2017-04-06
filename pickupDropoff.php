<!DOCTYPE HTML>
<html>
    <head>
        <title>KTCS: Pickup/Dropoff</title>
    </head>
<body>

<center>
    <h1>
    K-Town Car Share<br>
    </h1>
    <h2>
    Pickup/Dropoff
    </h2>
</center>

<a href="profile.php">Back to Profile.</a><br/>


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
// check if the dropoff form has been submitted
if(isset($_POST['dropoffBtn'])){

	// insert into RentalHistory
    $query = "INSERT INTO RentalHistory VALUES (?, ?, ?, (SELECT odometer FROM Car WHERE vin = ?), ?, ?, ?, ?, null)";
 
    // prepare query for execution
    if($stmt = $con->prepare($query)){
        // bind the parameters. This is the best way to prevent SQL injection hacks.
        $stmt->bind_Param("ssssssss", $_SESSION['memberId'], $_SESSION['vin'], $_POST['date'], $_SESSION['vin'], $_POST['odometer'],  $_POST['status'], $_POST['rating'],  $_POST['comment']);
 
		$stmt->execute(); // Execute the query		
	} else {
			echo "Failed to insert into RentalHistory";
	}

    // remove from Reservations
    $query = "DELETE FROM Reservations WHERE reservationId = ".$_SESSION['reservationId'];

    // prepare query for execution
    if($stmt = $con->prepare($query)){
		$stmt->execute(); // Execute the query		
	} else {
			echo "Failed to delete from Reservations";
	}
 }

// check if pickup form has been submitted
if(isset($_POST['pickupBtn'])){
    // make reservation active
    $query = "UPDATE Reservations SET active = 1 WHERE reservationId = ".$_SESSION['reservationId'];
  
    // prepare query for execution
    if($stmt = $con->prepare($query)){
		$stmt->execute(); // Execute the query		
	} else {
			echo "Failed to update Reservations";
	}

    header("Location: profile.php");
    die();
}
?>



<?php
if ($con->query("SELECT * FROM Reservations WHERE active = 1 AND memberId = ".$_SESSION['memberId'])->num_rows > 0) {
    // if the user currently has an active rez (car checked out)
    $result = $con->query("SELECT reservationId, vin, year, make, model FROM (Reservations NATURAL JOIN Car) WHERE active = 1 AND memberId = ".$_SESSION['memberId']);
    $myrow = $result->fetch_assoc();
    echo "<h3>Drop off your Active Reservation: ";
    echo $myrow['year']." ".$myrow['make']." ".$myrow['model']."</h3>";
    $_SESSION['vin'] = $myrow['vin'];
    $_SESSION['reservationId'] = $myrow['reservationId'];
?>
<form name='dropoff' id='dropoff' action='pickupDropoff.php' method='post'>
    <table border='0'>
        <tr>
            <td>Date (YYYY-MM-DD)</td>
            <td><input type='date' name='date' id='date' /></td>
        </tr>
        <tr>
            <td>Odometer Reading (km)</td>
            <td><input type='text' name='odometer' id='odometer' /></td>
        </tr>
        <tr>
            <td>Car Status (Normal, Damaged, or Not Running)</td>
            <td><input type='text' name='status' id='status' /></td>
        </tr>
        <tr>
            <td><h4>Optional fields:</h4></td>
        </tr>
        <tr>
            <td>Rating</td>
            <td>
  <input type="radio" name="rating" value=1> ★<br>
  <input type="radio" name="rating" value=2> ★★<br>
  <input type="radio" name="rating" value=3> ★★★<br>
  <input type="radio" name="rating" value=4> ★★★★ 
            </td>
        </tr>
        <tr>
            <td>Comment</td>
            <td><input type='text' name='comment' id='comment' /></td>
        </tr>
        <tr>
            <td>
                <input type='submit' id='dropoffBtn' name='dropoffBtn' value='Drop Off' /> 
            </td>        
        </tr>
    </table>
</form>

<?php
}
else if ($con->query("SELECT * FROM Reservations WHERE memberId = ".$_SESSION['memberId'])->num_rows > 0) {
    // if the user has a rez, but none are active
    echo "<h3>Pick up your reserved car:</h3>";
    $result = $con->query("SELECT reservationId, date, year, make, model FROM (Reservations NATURAL JOIN Car) WHERE memberId = ".$_SESSION['memberId']." ORDER BY date");
    $myrow = $result->fetch_assoc();
    $_SESSION['reservationId'] = $myrow['reservationId'];
?>
<form name='pickup' id='pickup' action='pickupDropoff.php' method='post'>
    <table border='0'>
        <tr>
            <td><?php echo $myrow['year']." ".$myrow['make']." ".$myrow['model'] ?></td>
            <td><input type='submit' id='pickupBtn' name='pickupBtn' value='Pick Up' /> </td>
        </tr>
    </table>
</form>
<?php
    }
else {
    // no reservations have been made yet
    echo "<h3>You need to make a reservation before picking up a car!</h3>";
}
?>

</body>
</html>

