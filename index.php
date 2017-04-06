<!DOCTYPE HTML>
<html>
    <head>
        <title>K-Town Car Share</title>
    </head>
<body>

<center>
    <h1>
    K-Town Car Share<br>
    </h1>
</center>

 <?php
  //Create a user session or resume an existing one
 session_start();
 ?>
 
 <?php
 //check if the user clicked the logout link and set the logout GET parameter
if(isset($_GET['logout'])){
	//Destroy the user's session.
	$_SESSION['memberId']=null;
	session_destroy();
}
 ?>
 
 
 <?php
 //check if the user is already logged in and has an active session
if(isset($_SESSION['memberId'])){
	//Redirect the browser to the profile editing page and kill this page.
	header("Location: profile.php");
	die();
}
 ?>
 
<?php
//check if the login form has been submitted
if(isset($_POST['loginBtn'])){
 
    // include database connection
    include_once 'config/connection.php'; 
	
	// SELECT query
    $query = "SELECT memberId FROM Member WHERE email=? AND password=?";
 
    // prepare query for execution
    if($stmt = $con->prepare($query)){
		
        // bind the parameters. This is the best way to prevent SQL injection hacks.
        $stmt->bind_Param("ss", $_POST['email'], $_POST['password']);
  
        // Execute the query
		$stmt->execute();
 
		/* resultset */
		$result = $stmt->get_result();

		// Get the number of rows returned
		$num = $result->num_rows;
		
		if($num>0){
			//If the username/password matches a user in our database
			//Read the user details
			$myrow = $result->fetch_assoc();
			//Create a session variable that holds the user's id
			$_SESSION['memberId'] = $myrow['memberId'];
			//Redirect the browser to the profile editing page and kill this page.
			header("Location: profile.php");
			die();
		} else {
			//If the username/password doesn't match a user in our database
			// Display an error message and the login form
			echo "Invalid username or password.";
		}
	} else {
			echo "failed to prepare the SQL";
	}
 }
?>

<?php
//check if the user is trying to make a new account
if(isset($_POST['registerBtn'])) {
			// redirect to the registration page
			header("Location: register.php");
			die();
}
?>

<h3> Log In: </h3>

<!-- dynamic content will be here -->
 <form name='login' id='login' action='index.php' method='post'>
    <table border='0'>
        <tr>
            <td>Email</td>
            <td><input type='text' name='email' id='email' /></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><input type='password' name='password' id='password' /></td>
        </tr>
        <tr>
            <td>
                <input type='submit' id='loginBtn' name='loginBtn' value='Log In' />
            </td>
            <td>
                <input type='submit' id='registerBtn' name='registerBtn' value='Register a New Account' /> 
            </td>        
        </tr>
    </table>
</form>


</body>
</html>
