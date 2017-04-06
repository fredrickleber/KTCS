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
  <?php session_start(); ?>

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
              <li class="active"><a href="#">Reserve</a>
              <li><a href="pickupDropoff.php">Pickup/Dropoff</a>
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

<!-- Update -->
  <section id="locations">
    <div class="well" id="locations">
      <hr>
      <div class="container text">
        <h3>Locations</h3>
        <hr>
        <table class="table table-striped">
          <?php
              include_once 'config/connection.php'; 

            $query = "SELECT parkedAddress FROM parkinglocations";
            $stmt = $con->prepare($query);  
            $stmt->execute();
              $result = $stmt->get_result();

              while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                  echo '<tr><td>';
                  echo $row['parkedAddress'];
                  echo '</t></tr>';
              }
          ?>
        </table>
      </div>
  <!-- Reserve -->
      <hr>
      <div class="container text">
        <h3>Reserve</h3>
        <?php
          include_once 'config/connection.php'; 

          $query = "SELECT make, model, year, parkedAddress, dailyFee, vin FROM car";
          $stmt = $con->prepare($query);  
          $stmt->execute();
          $result = $stmt->get_result();

          $reservations_query = "SELECT date, vin, length FROM reservations";
          $reservations_stmt = $con->prepare($reservations_query);

          date_default_timezone_set('America/Toronto');
          if (!isset($_SESSION['query_date'])) {
              $_SESSION['query_date'] = date('Y-m-d');
          }

          if (isset($_POST['update'])) {
              if ($_POST['date'] == '' || $_POST['date'] < date('Y-m-d')) {
                  $_SESSION['query_date'] = date('Y-m-d');
              } else {
                  $_SESSION['query_date'] = $_POST['date'];
              }
          }

          if (isset($_POST['reserve'])) { 
              if ($_POST['days'] < 1) {
                  echo "<script>alert('You must reserve for at least one day.');</script>";
              } else {
                  $vin = $_POST['vin'];
                  $reserve_end_date = date('Y-m-d', strtotime($_SESSION['query_date']. ' + '. $_POST['days']. ' days'));

                  $reservations_stmt->execute();
                  $reservations_result = $reservations_stmt->get_result();
                  $already_reserved = false;
                  while ($reservations_row = $reservations_result->fetch_array(MYSQLI_ASSOC)) {
                      if ($reservations_row['vin'] == $vin) {
                          $date_begin = $reservations_row['date'];
                          $date_end = date('Y-m-d', strtotime($date_begin. ' + '. $reservations_row['length']. ' days'));
                          if (($reserve_end_date >= $date_begin) && ($reserve_end_date <= $date_end)) {
                              $already_reserved = true;
                          }
                      }
                  }

                  if ($already_reserved == false) {
                      $_SESSION['access_code'] = generateRandomString();

                      $reserve_query = "INSERT INTO Reservations VALUES (null, ?, ?, ?, ?, ?, 0)";
                      $reserve_stmt = $con->prepare($reserve_query);  
                      $reserve_stmt->bind_param('sissi', $_SESSION['query_date'], $_SESSION['memberId'], $vin, $_SESSION['access_code'], $_POST['days']);
                      $reserve_stmt->execute();

                      header("Location: confirmation.php");
                      die();
                  } else {
                      echo "<script>alert('This car has been reserved during the length you have requested.');</script>";
                  }
              }
          }

          function generateRandomString($length = 6) {
              $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
              $charactersLength = strlen($characters);
              $randomString = '';
              for ($i = 0; $i < $length; $i++) {
                  $randomString .= $characters[rand(0, $charactersLength - 1)];
              }
              return $randomString;
          }
      ?>
        <form name='updatedate' id='updatedate' action='reserve.php' method='post'>
      <table border='0'>
          <tr>
              <td>Date:</td>
              <td><input type='date' name='date' id='date' value='<?php echo $_SESSION['query_date']; ?>'/></td>
              <td><input type='submit' id='update' name='update' value='Update' /></td>
          </tr>
      </table>
  </form>
  <br>
    <?php
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $already_reserved = false;
          $reservations_stmt->execute();
          $reservations_result = $reservations_stmt->get_result();
          while ($reservations_row = $reservations_result->fetch_array(MYSQLI_ASSOC)) {
              if ($reservations_row['vin'] == $row['vin']) {
                  $date_begin = $reservations_row['date'];
                  $date_end = date('Y-m-d', strtotime($date_begin. ' + '. $reservations_row['length']. ' days'));
                  if (($_SESSION['query_date'] >= $date_begin) && ($_SESSION['query_date'] <= $date_end)) {
                      $already_reserved = true;
                  }
              }
          }
          if ($already_reserved == false) {
              echo '<b>Make:</b> '; echo $row['make']; echo '<br>';
              echo '<b>Model:</b> '; echo $row['model']; echo '<br>';
              echo '<b>Year:</b> '; echo $row['year']; echo '<br>';
              echo '<b>Parked Address:</b> '; echo $row['parkedAddress']; echo '<br>';
              echo '<b>Daily Fee:</b> '; echo $row['dailyFee']; echo '<br>';
              echo "<form name='reserve' id='reserve' action='reserve.php' method='post'><input type='submit' id='reserve' name='reserve' value='Reserve' /><input type='hidden' name='vin' value='"; echo $row['vin']; echo "'>&nbsp;<input type='number' name='days' value='0' />&nbsp;days</form>";
              echo '<br>';
          }
      }
  ?>
      </div>
    </div>
  </section>

</body>
</html>