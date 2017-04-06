<!DOCTYPE HTML>
<html>
    <head>
        <title>Manage Fleet</title>
    </head>

    <body>

        <?php session_start(); ?>

		<form name='back' id='back' action='fleet.php' method='post'>
        	<input type='submit' id='back' name='back' value='Admin Panel' />
        </form>

        <?php
        	if (isset($_POST['back'])) {
        		header("Location: admin.php");
                die();
        	}
        ?>

        <h2>Manage Fleet</h2>

        <form name='addcar' id='addcar' action='fleet.php' method='post'>
        	<input type='submit' id='addcar' name='addcar' value='Add New Car' />
        </form>

        <?php
        	if (isset($_POST['addcar'])) {
        		header("Location: addcar.php");
                die();
        	}
        ?>
        <br>

        <form name='filter' id='filter' action='fleet.php' method='post'>
        	<input type='submit' id='all' name='all' value='View' /> <u>all cars</u><br>
        	<input type='submit' id='maintenance' name='maintenance' value='View' /> <u>all cars that have travelled 5000 km or more since their last maintenance</u><br>
        	<input type='submit' id='damaged' name='damaged' value='View' /> <u>all cars that are damaged or need repair</u><br>
        	<br>
        </form>

        <?php
            include_once 'config/connection.php'; 
    		
    		$query = "SELECT vin, make, model, year, odometer, parkedAddress, dailyFee FROM car";
    		if (isset($_POST['all'])) {
	            $query = "SELECT vin, make, model, year, odometer, parkedAddress, dailyFee FROM car";
    		}
    		if (isset($_POST['maintenance'])) {
	            $query = "SELECT vin, make, model, year, car.odometer, parkedAddress, dailyFee FROM car JOIN maintenancehistory USING(vin) WHERE car.odometer - maintenancehistory.odometer > 5000";
	        }
    		if (isset($_POST['damaged'])) {
	            $query = "SELECT vin, make, model, year, odometer, parkedAddress, dailyFee FROM car JOIN rentalhistory USING(vin) WHERE status = 'damaged'";
    		}

	        $stmt = $con->prepare($query);  
	        $stmt->execute();
	        $result = $stmt->get_result();
	        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	           	echo '<b>VIN:</b> '; echo $row['vin']; echo '<br>';
	            echo '<b>Make:</b> '; echo $row['make']; echo '<br>';
	            echo '<b>Model:</b> '; echo $row['model']; echo '<br>';
	            echo '<b>Year:</b> '; echo $row['year']; echo '<br>';
	            echo '<b>Odometer Reading:</b> '; echo $row['odometer']; echo '<br>';
	            echo '<b>Parked Address:</b> '; echo $row['parkedAddress']; echo '<br>';
	            echo '<b>Daily Fee:</b> '; echo $row['dailyFee']; echo '<br>';
	            echo "<form name='rentalhistory' id='rentalhistory' action='fleet.php' method='post'><input type='submit' id='rentalhistory' name='rentalhistory' value='Rental History' /><input type='hidden' name='vin' value='"; echo $row['vin']; echo "'></form>";
	            echo '<br>';
	        }

	        if (isset($_POST['rentalhistory'])) {
	        	$_SESSION['rentalhistoryvin'] = $_POST['vin'];
	        	header("Location: rentalhistory.php");
                die();
	        }
        ?>

    </body>
</html>