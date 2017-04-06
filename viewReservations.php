<!DOCTYPE HTML>
<html>
    <head>
        <title>KTCS: View Reservations</title>
    </head>
<body>

<center>
    <h1>
        K-Town Car Share<br>
    </h1>
    <h2>
        View Reservations
    </h2>
</center>


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

<form name='viewRez' id='viewRez' action='viewReservations.php' method='post'>
    <table border='0'>
        <tr>
            <td>Select Date (YYYYMMDD): </td>
            <td><input type='date' name='date' id='date' /></td>
            <td>
                <input type='submit' id='viewRezBtn' name='viewRezBtn' value='View Reservations' /> 
            </td>        
        </tr>
    </table>
</form>

<?php
// check if pickup form has been submitted
if(isset($_POST['viewRezBtn'])){
    // fetch reservations
    $result = $con->query("SELECT * FROM Reservations WHERE date =".$_POST['date']);
    if (!$result == false) {
    echo "<table border=\"1px solid black\" width=\"80%\"><tr><th>Date</th><th>Reservation ID</th><th >Member ID</th><th>VIN</th><th>Access Code</th><th>Length</th><th>Active</th></tr>" ;
    while($row = $result->fetch_assoc()) {
    echo "<tr><td style=\"text-align:center;\">". $row[('date')] . "</td><td style=\"text-align:center;\">". $row[('reservationId')] . "</td><td style=\"text-align:center;\">".$row[('memberId')]."</td><td style=\"text-align:center;\">".$row[('vin')] . "</td><td style=\"text-align:center;\">". $row[('accessCode')] . "</td><td style=\"text-align:center;\">". $row[('length')] . "</td><td style=\"text-align:center;\">". $row[('active')];
    echo "</td></tr>";
    }
    echo "</table>";
    }
    else {
        echo "<br/>No reservations in the system for that date.";
    }
}
?>

</body>
</html>

