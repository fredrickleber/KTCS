<!DOCTYPE HTML>
<html>
    <head>
        <title>Invoice Sent</title>
    </head>
<body>

<center>
    <h1>
        K-Town Car Share<br>
    </h1>
    <h2>
        Invoice Sent
    </h2>
</center>

        <h3>An invoice for $<?php echo $_GET['fee']; ?> has successfully been emailed to <?php echo $_GET['email']; ?>.</h3>

<a href="profile.php">Back to Profile.</a><br/>

    </body>
</html>

