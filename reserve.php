<!DOCTYPE HTML>
<html>
    <head>
        <title>Reserve a Car</title>
    </head>

    <body>

        <?php session_start(); ?>

        <h2>Reserve a Car</h2>

        <h3>Locations</h3>
        <?php
            include_once 'config/connection.php'; 
	
	        $query = "SELECT parkedAddress FROM parkinglocations";
	        $stmt = $con->prepare($query);	
	        $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                echo $row['parkedAddress'];
                echo '<br>';
            }
        ?>

        <h3>Reserve</h3>
        <?php
            include_once 'config/connection.php'; 
    
            $query = "SELECT make, model, year, parkedAddress, dailyFee, vin FROM car";
            $stmt = $con->prepare($query);  
            $stmt->execute();
            $result = $stmt->get_result();

            $reservations_query = "SELECT date, vin, length FROM reservations";
            $reservations_stmt = $con->prepare($reservations_query);

            date_default_timezone_set('America/Toronto');
            if (!isset($_SESSION['query_date'])) {
                $_SESSION['query_date'] = date('Y-m-d');
            }

            if (isset($_POST['update'])) {
                if ($_POST['date'] == '' || $_POST['date'] < date('Y-m-d')) {
                    $_SESSION['query_date'] = date('Y-m-d');
                } else {
                    $_SESSION['query_date'] = $_POST['date'];
                }
            }

            if (isset($_POST['reserve'])) { 
                if ($_POST['days'] < 1) {
                    echo "<script>alert('You must reserve for at least one day.');</script>";
                } else {
                    $vin = $_POST['vin'];
                    $reserve_end_date = date('Y-m-d', strtotime($_SESSION['query_date']. ' + '. $_POST['days']. ' days'));

                    $reservations_stmt->execute();
                    $reservations_result = $reservations_stmt->get_result();
                    $already_reserved = false;
                    while ($reservations_row = $reservations_result->fetch_array(MYSQLI_ASSOC)) {
                        if ($reservations_row['vin'] == $vin) {
                            $date_begin = $reservations_row['date'];
                            $date_end = date('Y-m-d', strtotime($date_begin. ' + '. $reservations_row['length']. ' days'));
                            if (($reserve_end_date >= $date_begin) && ($reserve_end_date <= $date_end)) {
                                $already_reserved = true;
                            }
                        }
                    }

                    if ($already_reserved == false) {
                        $_SESSION['access_code'] = generateRandomString();

                        $reserve_query = "INSERT INTO Reservations VALUES (null, ?, ?, ?, ?, ?, 0)";
                        $reserve_stmt = $con->prepare($reserve_query);  
                        $reserve_stmt->bind_param('sissi', $_SESSION['query_date'], $_SESSION['memberId'], $vin, $_SESSION['access_code'], $_POST['days']);
                        $reserve_stmt->execute();

                        header("Location: confirmation.php");
                        die();
                    } else {
                        echo "<script>alert('This car has been reserved during the length you have requested.');</script>";
                    }
                }
            }

            function generateRandomString($length = 6) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
            }
        ?>

        <form name='updatedate' id='updatedate' action='reserve.php' method='post'>
            <table border='0'>
                <tr>
                    <td>Date:</td>
                    <td><input type='date' name='date' id='date' value='<?php echo $_SESSION['query_date']; ?>'/></td>
                    <td><input type='submit' id='update' name='update' value='Update' /></td>
                </tr>
            </table>
        </form>
        <br>

        <?php
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $already_reserved = false;
                $reservations_stmt->execute();
                $reservations_result = $reservations_stmt->get_result();
                while ($reservations_row = $reservations_result->fetch_array(MYSQLI_ASSOC)) {
                    if ($reservations_row['vin'] == $row['vin']) {
                        $date_begin = $reservations_row['date'];
                        $date_end = date('Y-m-d', strtotime($date_begin. ' + '. $reservations_row['length']. ' days'));
                        if (($_SESSION['query_date'] >= $date_begin) && ($_SESSION['query_date'] <= $date_end)) {
                            $already_reserved = true;
                        }
                    }
                }
                if ($already_reserved == false) {
                    echo '<b>Make:</b> '; echo $row['make']; echo '<br>';
                    echo '<b>Model:</b> '; echo $row['model']; echo '<br>';
                    echo '<b>Year:</b> '; echo $row['year']; echo '<br>';
                    echo '<b>Parked Address:</b> '; echo $row['parkedAddress']; echo '<br>';
                    echo '<b>Daily Fee:</b> '; echo $row['dailyFee']; echo '<br>';
                    echo "<form name='reserve' id='reserve' action='reserve.php' method='post'><input type='submit' id='reserve' name='reserve' value='Reserve' /><input type='hidden' name='vin' value='"; echo $row['vin']; echo "'>&nbsp;<input type='number' name='days' value='0' />&nbsp;days</form>";
                    echo '<br>';
                }
            }
        ?>

    </body>
</html>
