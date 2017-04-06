<!DOCTYPE HTML>
<html>
    <head>
        <title>Invoice Sent</title>
    </head>
<body>

<form name='back' id='back' action='fleet.php' method='post'>
    <input type='submit' id='back' name='back' value='Admin Panel' />
</form>

<?php
    if (isset($_POST['back'])) {
        header("Location: admin.php");
        die();
    }
?>

<h2>
    Invoice Sent
</h2>

<h4>An invoice for $<?php echo $_GET['fee']; ?> has successfully been emailed to <?php echo $_GET['email']; ?>.</h4>

</body>
</html>