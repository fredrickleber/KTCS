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

  #login-dp{
      min-width: 250px;
      padding: 14px 14px 0;
      overflow:hidden;
      background-color:rgba(255,255,255,.8);
  }
  #login-dp .help-block{
      font-size:12px    
  }
  #login-dp .bottom{
      background-color:rgba(255,255,255,.8);
      border-top:1px solid #ddd;
      clear:both;
      padding:14px;
  }
  #login-dp .social-buttons{
      margin:12px 0    
  }
  #login-dp .social-buttons a{
      width: 49%;
  }
  #login-dp .form-group {
      margin-bottom: 10px;
  }
  @media(max-width:768px){
      #login-dp{
          background-color: inherit;
          color: #fff;
      }
      #login-dp .bottom{
          background-color: inherit;
          border-top:0 none;
      }
  }
</style>

<body data-spy="scroll" data-target="#my-navbar">
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
      if($_SESSION['memberId'] == 1)
        header("Location: admin.php");
      else
        header("Location: reserve.php");
    die();
    }
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
                    if($myrow['memberId'] == 1)
                      header("Location: admin.php");
                    else
                      header("Location: reserve.php");
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
      $stmt->bind_Param("ss", $_POST['loginEmail'], $_POST['loginPassword']);
    
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
        if($myrow['memberId'] == 1)
          header("Location: admin.php");
        else
          header("Location: reserve.php");
        die();
      } else {
        //If the username/password doesn't match a user in our database
        // Display an error message and the login form
        echo "Invalid username or password.";
      }
    } else {
      echo "Failed to prepare the SQL";
    }
  }
  ?>

  <!-- NavBar -->
    <nav class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation" id="my-navbar">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">KTCS</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
              <li><a href="#features">Features</a>
              <li><a href="#feedback">Feedback</a>
              <li><a href="#register">Register</a> 
              <li><a href="#contact">Contact Us</a> 
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><p class="navbar-text">Already have an account?</p></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b>Login</b> <span class="caret"></span></a>
          <ul id="login-dp" class="dropdown-menu">
            <li>
               <div class="row">
                  <div class="col-md-12">
                     <form class="form" name="login" id="login" action="index.php" method="post">
                        <div class="form-group">
                           <label class="sr-only" for="loginEmail">Email address</label>
                           <input type="text" class="form-control" id="loginEmail" name="loginEmail" placeholder="Email address" required>
                        </div>
                        <div class="form-group">
                           <label class="sr-only" for="loginPassword">Password</label>
                           <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Password" required>
                                                 <div class="help-block text-right"><a href="#contact">Forgot the password?</a></div>
                        </div>
                        <div class="form-group">
                           <button type="submit" class="btn btn-primary btn-block" id="loginBtn" name="loginBtn">Sign in</button>
                        </div>
                        <div class="checkbox">
                           <label>
                           <input type="checkbox"> Keep me logged-in
                           </label>
                        </div>
                     </form>
                  </div>
                  <div class="bottom text-center">
                    New here? <a href="#register"><b>Register Now</b></a>
                  </div>
               </div>
            </li>
          </ul>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

  <!-- jumbotron-->
  	<div class="jumbotron">
  		<div class="container text-center">
        <h1> K-Town Car Share</h1>
        <p> Get the ride you want, when you want</p>

        <div class="btn-group">
          <a href="#register" class="btn btn-lg btn-primary"> Register </a>
          <a hred="" class="btn btn-lg btn-success"> Download App </a>
          <a hred="#features" class="btn btn-lg btn-primary"> Learn More </a>
        </div>
  		</div><!-- End container -->
  	</div><!-- End jumbotron-->

  <!-- Features -->
    <div class="container"  id="features">
      <section>
        <div class="page-header">
          <div class="row">
            <div class="col-sm-8">
              <h3>Book cars effortlessly</h3>
              <p>With the K-Town Car Share app, finding a ride has never been easier. Just log into your account from either your computer or your smart phone, and within a few clicks you can reserve, pickup or return a vehicle. Transportation has never been so fun!</p>
            </div>
            <div class="col-sm-4">
              <img src="keys.jpg" class="img-responsive" alt="image">
            </div>
          </div><!-- End row -->
          <div class="row">
            <div class="col-sm-4">
              <img src="car.jpg" class="img-responsive" alt="image">
            </div>
            <div class="col-sm-8">
              <h3>Drive today's best cars</h3>
              <p> We offer the best new vehicles around, from Ford Fusions to the new Toyota Camery, we have it all! By joining K-Town Car Share you will get the unique opportunity of driving a wide variety of the latest car. Need we say more? </p>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-8">
              <h3>Help the environment</h3>
              <p>Work with us to make the world a better place by reducing green house emissions. We're happy to annouce that our fleet includes the world's best low-emission and hybrid cars. </p>
            </div>
            <div class="col-sm-4">
              <img src="driver.png" class="img-responsive" alt="image">
            </div>
          </div>
        </div>
      </section>
    </div><!-- End Container -->

  <!-- Feedback-->
  	<div class="container" id="feedback">
  		<section>
  			<div class="page-header">
  				<h2>Feedback</h2>
  			</div>

  			<div class="row">
  				<div class="col-md-4">
  					<blockquote>
  						<p> Driving will never be the same again. Thanks KTCS for making me feel young again! </p>
  						<footer>Freddy Flinstone</footer>
  					</blockquote>
  				</div>
  				<div class="col-md-4">
  					<blockquote>
  						<p>After trying K-Town Car Share I sold my car right away! Why pay so much for a car when you can have hundreds at your finger tips? I highly recommend KTCS to anyone with a driver's license. </p>
  						<footer>Rylan Chui</footer>
  					</blockquote>
  				</div>
  				<div class="col-md-4">
  					<blockquote>
  						<p>I was never really the car sharing type of guy, but then I came across K-Town Car Share. Ride on KTCS! </p>
  						<footer>Pick Rennie</footer>
  					</blockquote>
  				</div>
  			</div><!-- End row -->
  		</section>
  	</div><!--End Container-->

<!-- Register -->
  <section id="register-sc">
    <div class="well" id="register">
      <hr>
      <div class="container text">
        <center><h3>Register for free!</h3></center>
        <hr>
        <form name="register" id="register" action= "index.php" method="post" class="form-horizontal">
          <div class="form-group">
            <label for="inputEmail" class="col-sm-2 control-label">Email Address</label>
            <div class="col-sm-8">
              <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
          </div>
          <div class="form-group">
            <label for="inputName" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="name" name="name" placeholder="Name">
            </div>
          </div>
          <div class="form-group">
            <label for="inputAddress" class="col-sm-2 control-label">Address</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="address" name="address" placeholder="Address">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPhoneNum" class="col-sm-2 control-label">Phone Number</label>
            <div class="col-sm-8">
              <input type="tel" class="form-control" id="phonenumber" name="phonenumber" placeholder="Phone Number">
            </div>
          </div>
          <div class="form-group">
            <label for="inputLicense" class="col-sm-2 control-label">License</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="license" name="license" placeholder="License">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-primary" name = "registerBtn">Register</button>
            </div>
          </div>
        <hr>

      </div><!-- end Container-->

    </div><!-- end well-->
  </section><!-- Register -->

<!-- Contact -->
  <div class="container">
    <section>
      <div class="page-header" id="contact">
          <h2>Contact Us</h2>
        </div><!-- End Page Header -->

        <div class="row">
          <div class="col-lg-4">
            <address>
              <strong>K-Town Car Share</strong></br>
              557 Goodwin Hall </br>
              Queen's University</br>
              Kingston, Ontario, Canada</br>
              K7L 2N8
            </address>
          </div>
          
          <div class="col-lg-8">
            <form action="" class="form-horizontal">
              <div class="form-group">
                <label for="user-name" class="col-lg-2 control-label">Name</label>
                <div class="col-lg-10">
                  <input type="text" class="form-control" id="user-name" placeholder="Name">
                </div>
              </div><!-- End form group -->

              <div class="form-group">
                <label for="user-email" class="col-lg-2 control-label">Email</label>
                <div class="col-lg-10">
                  <input type="text" class="form-control" id="user-email" placeholder="Email address">
                </div>
              </div><!-- End form group -->

              <div class="form-group">
                <label for="user-message" class="col-lg-2 control-label">Message</label>
                <div class="col-lg-10">
                  <textarea name="user-message" id="user-message" class="form-control" 
                  cols="20" rows="10" placeholder="Enter your message"></textarea>
                </div>
              </div><!-- End form group -->

              <div class="form-group">
                <div class="col-lg-10 col-lg-offset-2">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div><!-- End the row -->

    </section>
  </div>

<!-- Footer -->
    <footer>
      <hr>
        <div class="container text-center">
        <hr>
        <ul class="list-inline">
          <li><a href="http://www.twitter.com/welovepatrickmartin">Twitter</a></li>
          <li><a href="http://www.facebook.com/patrickmartinismyhero">Facebook</a></li>
          <li><a href="http://www.youtube.com/patrick4prez">YouTube</a></li>
        </ul>
        <p>&copy; Copyright @ 2017</p>

      </div><!-- end Container-->
      

    </footer>
  </div>
</div>
</body>
</html>