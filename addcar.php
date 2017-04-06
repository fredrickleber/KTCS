<!DOCTYPE HTML>
<html>
    <head>
        <title>Add Car</title>
    </head>

    <body>

        <?php session_start(); ?>

        <form name='back' id='back' action='addcar.php' method='post'>
        	<input type='submit' id='back' name='back' value='Back' />
        </form>

        <?php
        	if (isset($_POST['back'])) {
        		header("Location: fleet.php");
                die();
        	}
        ?>

        <h2>Add a New Car</h2>

        <form name='add' id='add' action='addcar.php' method='post'>
            <b>VIN:<b> <input type='text' name='vin'/><br>
            <b>Make:<b> <input type='text' name='make'/><br>
            <b>Model:<b> <input type='text' name='model'/><br>
            <b>Year:<b> <input type='number' name='year'/><br>
            <b>Odometer Reading:<b> <input type='number' name='odometer'/><br>
            <b>Parked Address:<b> <input type='text' name='parkedAddress'/><br>
            <b>Daily Fee:<b> <input type='number' name='dailyFee'/><br>
            <input type='submit' name='add' value='Add'/>
        </form>

        <?php
            include_once 'config/connection.php'; 
            
            if (isset($_POST['add'])) {
                $vin = $_POST['vin'];
                $make = $_POST['make'];
                $model = $_POST['model'];
                $year = $_POST['year'];
                $odometer = $_POST['odometer'];
                $parkedAddress = $_POST['parkedAddress'];
                $dailyFee = $_POST['dailyFee'];

                if ($vin == '' || $make == '' || $model == '' || $year == '' || $odometer == '' || $parkedAddress == '' || $dailyFee == '') {
                    echo "<script>alert('You must fill in all fields.');</script>";
                } else {
                    $query = "INSERT INTO car VALUES (?, ?, ?, ?, ?, ?, ?);";
                    $stmt = $con->prepare($query);  
                    $stmt->bind_param('sssiisi', $vin, $make, $model, $year, $odometer, $parkedAddress, $dailyFee);
                    $stmt->execute();
                    header("Location: fleet.php");
                    die();
                }
            }
        ?>

    </body>
</html>