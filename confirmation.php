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

  <!-- jumbotron-->
  	<div class="jumbotron">
  		<div class="container text-center">
        <h2>Congratulations, you have successfully rented a car!</h2>
        <p>Your access code is: <?php echo $_SESSION['access_code']; ?></p>
        <hr>
        <img src="highway.jpg" class="img-responsive" alt="image">
  		</div><!-- End container -->
  	</div><!-- End jumbotron-->
</body>
</html>