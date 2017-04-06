<!DOCTYPE HTML>
<html>
    <head>
        <title>KTCS: Profile</title>
    </head>
<body>

<center>
    <h1>
    K-Town Car Share<br>
    </h1>
    <h2>
    Profile
    </h2>
</center>

 <?php
  //Create a user session or resume an existing one
 session_start();
 ?>
 
 <?php
 if(isset($_POST['updateBtn']) && isset($_SESSION['memberId'])){
    // include database connection
    include_once 'config/connection.php'; 
	
	$query = "UPDATE user SET password=?,email=? WHERE id=?";
 
	$stmt = $con->prepare($query);	$stmt->bind_param('sss', $_POST['password'], $_POST['email'], $_SESSION['memberId']);
	
    // Execute the query
    if($stmt->execute()) {
        echo "Account information was updated. <br/>";
    } else {
        echo 'Unable to update account information. Please try again. <br/>';
    }
    $stmt->close();
 }
 
 ?>
 
<?php
if(isset($_SESSION['memberId'])){
    // include database connection
    include_once 'config/connection.php'; 
	
	// SELECT query
    $query = "SELECT name FROM Member WHERE memberId=?";
 
    // prepare query for execution
    $stmt = $con->prepare($query);
		
    // bind the parameters. This is the best way to prevent SQL injection hacks.
    $stmt->bind_Param("s", $_SESSION['memberId']);

    // Execute the query
    $stmt->execute();
 
    // results 
	$result = $stmt->get_result();
		
	// Row data
	$myrow = $result->fetch_assoc();
} else {
	//User is not logged in. Redirect the browser to the login index.php page and kill this page.
	header("Location: index.php");
	die();
}
?>

 Welcome <?php echo $myrow['name']; ?>. <a href="index.php?logout=1">Log Out</a><br/><br/>

<h3> Available Actions: </h3>
<a href="locations.php">View KTSC Locations.</a><br/>
<a href="rentals.php">Search available rentals.</a><br/>
<a href="pickupDropoff.php">Pick-up or drop-off a car.</a><br/>
<a href="rentalHistory.php">View rental history.</a><br/>

<br/>
<h3> Update Account Information: </h3>
<!-- dynamic content will be here -->
<form name='editProfile' id='editProfile' action='profile.php' method='post'>
    <table border='0'>
        <tr>
            <td>Password</td>
             <td><input type='password' name='password' id='password' /></td>
        </tr>
		<tr>
            <td>Email</td>
            <td><input type='text' name='email' id='email' /></td>
        </tr>
        <tr>
            <td>Phone Number</td>
            <td><input type='text' name='phonenumber' id='phonenumber' /></td>
        </tr>
        <tr>
            <td>Address</td>
            <td><input type='text' name='address' id='address' /></td>
        </tr>
        <tr>
            <td>License Number</td>
            <td><input type='text' name='license' id='license' /></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type='submit' name='updateBtn' id='updateBtn' value='Update' /> 
            </td>
        </tr>
    </table>
</form>
</body>
</html>
