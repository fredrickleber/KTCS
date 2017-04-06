<!DOCTYPE HTML>
<html>
    <head>
        <title>Reservation Confirmation</title>
    </head>

    <body>

        <?php session_start(); ?>

        <h2>You have reserved a car.</h2>

        Your access code is: <?php echo $_SESSION['access_code']; ?>

    </body>
</html>