<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>K-Town Car Share</title>
	<meta name="description" content="KTCS App">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<style>
/* Add CSS here */
</style>

<body>
  <?php
  //Create a user session or resume an existing one
  session_start();
  ?>

  <?php
    if(isset($_SESSION['memberId'])){
      if($_SESSION['memberId'] == 1) {
        header("Location: admin.php");
        die();
      } else {
      include_once 'config/connection.php'; 
      $query = "SELECT name FROM Member WHERE memberId=?";
      $stmt = $con->prepare($query);
      $stmt->bind_Param("s", $_SESSION['memberId']);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_assoc();
      }
    } else {
      //User is not logged in. Redirect the browser to the login index.php page and kill this page.
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

        header("Location: reserve.php");
        die();
    }
  ?>

  <!-- NavBar -->
    <nav class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation" id="my-navbar">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbarColapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand">KTCS</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="nav navbar-nav">
              <li><a href="reserve.php">Reserve</a>
              <li class="active"><a href="#">Pickup/Dropoff</a>
              <li><a href="history.php">History</a> 
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <p class="navbar-text">Signed in as <b><?php echo $data['name']; ?></b></p>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- <span class="glyphicon glyphicon-cog"></span> -->
              <b> Settings </b>
              <span class="caret"></span>
              </a>
              <ul id="settings" class="dropdown-menu">
                <li><a href="profile.php">Profile</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="index.php?logout=1">Log out</a></li>
              </ul>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

  <?php
    if ($con->query("SELECT * FROM Reservations WHERE active = 1 AND memberId = ".$_SESSION['memberId'])->num_rows > 0) {
        // if the user currently has an active rez (car checked out)
        $result = $con->query("SELECT reservationId, vin, year, make, model FROM (Reservations NATURAL JOIN Car) WHERE active = 1 AND memberId = ".$_SESSION['memberId']);
        $myrow = $result->fetch_assoc();
    ?>
    <div class="container-fluid">
    <hr>
    <hr>
    <h4><center>Drop off your:
    <?php
      echo $myrow['year']." ".$myrow['make']." ".$myrow['model']."</h3>";
      $_SESSION['vin'] = $myrow['vin'];
      $_SESSION['reservationId'] = $myrow['reservationId'];
    ?>
    </center></h4>
    <form class = "form-horizontal" name='dropoff' id='dropoff' action='pickupDropoff.php' method='post'>
        <table class='table table-striped'>
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
                <td>Rating</td>
                <td>
            <div class="radio">
              <label>
                <input type="radio" name="rating" value=1> ★
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="rating" value=2> ★★
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="rating" value=3> ★★★
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="rating" value=4> ★★★★
              </label>
            </div>
                </td>
            </tr>
            <tr>
                <td>Comment</td>
                <td><input type='text' name='comment' id='comment' /></td>
            </tr>
            <tr>
                <td>
                    <input type='submit' class = "btn btn-primary" id='dropoffBtn' name='dropoffBtn' value='Drop Off' /> 
                </td>        
            </tr>
        </table>
    </form>
    </div><!-- end well-->

    <?php
    }
    else if ($con->query("SELECT * FROM Reservations WHERE memberId = ".$_SESSION['memberId'])->num_rows > 0) {
        // if the user has a rez, but none are active
        echo "<h3>Pick up your reserved car:</h3>";
        $result = $con->query("SELECT reservationId, date, year, make, model FROM (Reservations NATURAL JOIN Car) WHERE memberId = ".$_SESSION['memberId']." ORDER BY date");
        $myrow = $result->fetch_assoc();
        $_SESSION['reservationId'] = $myrow['reservationId'];
    ?>
    <form class="form-horizontal" name='pickup' id='pickup' action='pickupDropoff.php' method='post'>
        <h3>Cars availible for pickup:</h3>
        <table class = "table table-striped" border='0'>
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
        echo '<div class="jumbotron">';
        echo '<div class="container text-center">';
        echo '<h2>You have to reserve a car before you can pick it up!</h2>';
        echo '</div></div>';
        }
    ?>
</body>
</html>