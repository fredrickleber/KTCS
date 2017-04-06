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
    if(isset($_POST['updateBtn']) && isset($_SESSION['memberId'])){
      // include database connection
      include_once 'config/connection.php'; 
    
    $query = "UPDATE user SET password=?,email=? WHERE id=?";
   
    $stmt = $con->prepare($query);  $stmt->bind_param('sss', $_POST['password'], $_POST['email'], $_SESSION['memberId']);
    
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
              <li><a href="reserve.php">Reserve</a>
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
  <section id="register">
    <div class="well" id="register">
      <hr>
      <div class="container text">
        <center><h3>Update Profile</h3></center>
        <hr>
        <form action="profile.php" name="update" class="form-horizontal">
          <div class="form-group">
            <label for="inputEmail" class="col-sm-2 control-label">Email Address</label>
            <div class="col-sm-8">
              <input type="email" class="form-control" name="email" id="email" placeholder="Email Address">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-primary" name="updateBtn" id="updateBtn">Update</button>
            </div>
          </div>
        <hr>

      </div><!-- end Container-->

    </div><!-- end well-->
  </section><!-- Register -->
</div>
</body>
</html>