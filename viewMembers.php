<!DOCTYPE HTML>
<html>
    <head>
        <title>KTCS: View Members</title>
    </head>
<body>

<center>
    <h1>
        K-Town Car Share<br>
    </h1>
    <h2>
        View Members
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

<a href="profile.php">Back to Profile.</a><br/>

<?php
    // fetch members
    $result = $con->query("SELECT * FROM Member");
if (!$result == false) {
    echo "<table border=\"1px solid black\" width=\"80%\"><tr><th>Member ID</th><th>Email</th><th>Name</th><th>Address</th><th>Phone Number</th><th>License Number</th><th>Annual Fee</th><th></th></tr>" ;
    while($row = $result->fetch_assoc()) {
        echo "<tr><td style=\"text-align:center;\">". $row[('memberId')] . "</td><td style=\"text-align:center;\">". $row[('email')] . "</td><td style=\"text-align:center;\">".$row[('name')]."</td><td style=\"text-align:center;\">".$row[('address')] . "</td><td style=\"text-align:center;\">". $row[('phoneNumber')] . "</td><td style=\"text-align:center;\">". $row[('licenseNumber')] . "</td><td style=\"text-align:center;\">". $row[('annualFee')] . "</td><td style=\"text-align:center;\">";
        ?>
        <a href="invoice.php?email=<?php echo $row[('email')];?>&fee=<?php echo $row[('annualFee')];?>">Generate Invoice</a>
        <?php
        echo "</td></tr>";
        }
  echo "</table>";
}
else {
    echo "<br/>No members exist in the system.";
     }
?>

</body>
</html>

