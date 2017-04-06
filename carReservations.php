<!DOCTYPE HTML>
<html>
    <head>
        <title>Reservations</title>
    </head>

    <body>

        <?php session_start(); ?>

        <form name='back' id='back' action='carReservations.php' method='post'>
        	<input type='submit' id='back' name='back' value='Admin Panel' />
        </form>
        <br>

        <?php
        	if (isset($_POST['back'])) {
        		header("Location: admin.php");
                die();
        	}
        ?>

        <?php
            include_once 'config/connection.php'; 
            
            $query = "SELECT vin, make, model, year, odometer, parkedAddress, dailyFee FROM car WHERE vin = ?";
            $stmt = $con->prepare($query);  
            $stmt->bind_param('s', $_SESSION['view_car_reservations_vin']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_array(MYSQLI_ASSOC);
            echo '<b>VIN:</b> '; echo $row['vin']; echo '<br>';
            echo '<b>Make:</b> '; echo $row['make']; echo '<br>';
            echo '<b>Model:</b> '; echo $row['model']; echo '<br>';
            echo '<b>Year:</b> '; echo $row['year']; echo '<br>';
            echo '<b>Odometer Reading:</b> '; echo $row['odometer']; echo '<br>';
            echo '<b>Parked Address:</b> '; echo $row['parkedAddress']; echo '<br>';
            echo '<b>Daily Fee:</b> '; echo $row['dailyFee']; echo '<br>';
        ?>

        <h2>Reservations</h2>

        <?php
            include_once 'config/connection.php'; 
            
            $query = "SELECT reservationId, date, memberId, accessCode, length, active FROM reservations WHERE vin = ?";
            $stmt = $con->prepare($query);  
            $stmt->bind_param('s', $_SESSION['view_car_reservations_vin']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                echo '<b>Reservation ID:</b> '; echo $row['reservationId']; echo '<br>';
                echo '<b>Member ID:</b> '; echo $row['memberId']; echo '<br>';
                echo '<b>Date:</b> '; echo $row['date']; echo '<br>';
                echo '<b>Length:</b> '; echo $row['length']; echo '<br>';
                echo '<b>Access Code:</b> '; echo $row['accessCode']; echo '<br>';
                if ($row['active'] == 0) {
                    echo '<b>Active:</b> False<br>';
                } else {
                    echo '<b>Active:</b> True<br>';
                }
                echo '<br>';
            }
        ?>

    </body>
</html>