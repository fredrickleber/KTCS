<!DOCTYPE HTML>
<html>
    <head>
        <title>Admin Panel</title>
    </head>

    <body>

        <?php session_start(); ?>

        <h2>Admin Panel</h2>

        <form name='buttons' id='buttons' action='admin.php' method='post'>
            <input type='submit' name='managefleet' value='Manage Fleet' />
            <input type='submit' name='viewmembers' value='View Members' />
            <input type='submit' name='viewreservations' value='View Reservations' />
        </form>

        <?php
            if (isset($_POST['managefleet'])) {
                header("Location: fleet.php");
                die();
            }
            if (isset($_POST['viewmembers'])) {
                header("Location: viewMembers.php");
                die();
            }
            if (isset($_POST['viewreservations'])) {
                header("Location: viewReservations.php");
                die();
            }
        ?>

        <h3><u>Most Rentals</u></h3>

        <?php
            include_once 'config/connection.php'; 

            $query = "SELECT vin, make, model, year, COUNT(vin) AS count FROM car JOIN rentalhistory USING(vin) GROUP BY vin ORDER BY count DESC";
            $stmt = $con->prepare($query);  
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            echo '<b>VIN:</b> '; echo $row['vin']; echo '<br>';
            echo '<b>Make:</b> '; echo $row['make']; echo '<br>';
            echo '<b>Model:</b> '; echo $row['model']; echo '<br>';
            echo '<b>Year:</b> '; echo $row['year']; echo '<br>';
            echo '<b>Rentals:</b> '; echo $row['count']; echo '<br>';
        ?>

        <h3><u>Least Rentals</u></h3>

        <?php
            $query = "SELECT vin, make, model, year, COUNT(vin) AS count FROM car JOIN rentalhistory USING(vin) GROUP BY vin ORDER BY count";
            $stmt = $con->prepare($query);  
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            echo '<b>VIN:</b> '; echo $row['vin']; echo '<br>';
            echo '<b>Make:</b> '; echo $row['make']; echo '<br>';
            echo '<b>Model:</b> '; echo $row['model']; echo '<br>';
            echo '<b>Year:</b> '; echo $row['year']; echo '<br>';
            echo '<b>Rentals:</b> '; echo $row['count']; echo '<br>';
        ?>

        <h3><u>Car Locations</u></h3>

        <?php
            $query = "SELECT parkedAddress FROM parkinglocations";
            $stmt = $con->prepare($query);  
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                echo "<b>"; echo $row['parkedAddress']; echo "</b><br><br>"; 

                $car_query = "SELECT vin, make, model, year FROM car NATURAL JOIN parkinglocations WHERE parkedAddress = '" .$row['parkedAddress'] ."'";
                $car_stmt = $con->prepare($car_query);  
                $car_stmt->execute();
                $car_result = $car_stmt->get_result();

                while ($car_row = $car_result->fetch_array(MYSQLI_ASSOC)) {
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>VIN:</b> '; echo $car_row['vin']; echo '<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Make:</b> '; echo $car_row['make']; echo '<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Model:</b> '; echo $car_row['model']; echo '<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Year:</b> '; echo $car_row['year']; echo '<br>';
                echo "<form name='reservations' action='admin.php' method='post'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='reservations' value='Reservations' /><input type='hidden' name='vin' value='"; echo $car_row['vin']; echo "'></form>";
                echo '<br>';
                }
            }

            if (isset($_POST['reservations'])) {
                $_SESSION['view_car_reservations_vin'] = $_POST['vin'];
                header("Location: carReservations.php");
                die();
            }
        ?>

    </body>
</html>