<!DOCTYPE HTML>
<html>
    <head>
        <title>KTCS: Register a New Account</title>
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
//check if the user is trying to make a new account
if(isset($_POST['registerBtn'])) {
    // make sure all fields are filled out
    if (($_POST['email'] != "") && ($_POST['password'] != "") && ($_POST['name'] != "")) {
    // include database connection
    include_once 'config/connection.php';
	
   // $createAccountQuery = "INSERT INTO user(username, password, email) VALUES (?, ?, ?)";
    $createAccountQuery = "INSERT INTO Member VALUES (null, ?, ?, ?, ?, ?, ?, 10000)";

    // prepare query for execution
    if($stmt = $con->prepare($createAccountQuery)) {
		
        // bind the parameters to prevent SQL injection
        $stmt->bind_Param("ssssss", $_POST['email'], $_POST['password'], $_POST['name'], $_POST['address'], $_POST['phonenumber'], $_POST['license']);
         
        // Execute the query
        if ($stmt->execute()) { // if the new user was successfully added to the db

            // find the id of the newly created user
            $idQuery = "SELECT memberId FROM Member WHERE email = ? AND password = ?";
	        if ($stmt = $con->prepare($idQuery)) {         
	            $stmt->bind_Param("ss", $_POST['email'], $_POST['password']);
	            if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    $myrow = $result->fetch_assoc();
	                //Create a session variable that holds the user's id
	                $_SESSION['memberId'] = $myrow['memberId'];
	                //Redirect the browser to the profile editing page and kill this page.
                    $stmt->close();
	                header("Location: profile.php");
	                die();

                }
                else {
                    echo "Critical error!";
                }
		    }
            else {
                echo "Prepare failed: (" . $con->errno . ") " . $con->error;
	        }
        }
        else {
            // if the username matchs a user in our database
            echo "Username is already taken.";
        }
    } 
    else {
        echo "Prepare failed: (" . $con->errno . ") " . $con->error;
	}
  }
 }
?>

<h3> Register a new account: </h3>

<!-- dynamic content will be here -->
 <form name='register' id='register' action='register.php' method='post'>
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
            <td>Name</td>
            <td><input type='text' name='name' id='name' /></td>
        </tr>
        <tr>
            <td>Address</td>
            <td><input type='text' name='address' id='address' /></td>
        </tr>
        <tr>
            <td>Phone Number</td>
            <td><input type='text' name='phonenumber' id='phonenumber' /></td>
        </tr>
        <tr>
            <td>License Number</td>
            <td><input type='text' name='license' id='license' /></td>
        </tr>
        <tr>
            <td />
            <td>
                <input type='submit' id='registerBtn' name='registerBtn' value='Register' /> 
            </td>        
        </tr>
    </table>
</form>


</body>
</html>
