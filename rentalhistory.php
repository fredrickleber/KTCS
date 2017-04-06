<!DOCTYPE HTML>
<html>
    <head>
        <title>Rental History</title>
    </head>

    <body>

        <?php session_start(); ?>

        <form name='back' id='back' action='rentalhistory.php' method='post'>
        	<input type='submit' id='back' name='back' value='Back' />
        </form>
        <br>

        <?php
        	if (isset($_POST['back'])) {
        		header("Location: fleet.php");
                die();
        	}
        ?>

        <?php
            include_once 'config/connection.php'; 
            
            $query = "SELECT vin, make, model, year, odometer, parkedAddress, dailyFee FROM car WHERE vin = ?";
            $stmt = $con->prepare($query);  
            $stmt->bind_param('s', $_SESSION['rentalhistoryvin']);
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

        <h2>Rental History</h2>

        <?php
            include_once 'config/connection.php'; 
            
            $query = "SELECT memberId, date, pickupOdometer, dropoffOdometer, status, rating, commentText, replyText FROM rentalhistory WHERE vin = ?";
            $stmt = $con->prepare($query);  
            $stmt->bind_param('s', $_SESSION['rentalhistoryvin']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                echo '<b>Member ID:</b> '; echo $row['memberId']; echo '<br>';
                echo '<b>Date:</b> '; echo $row['date']; echo '<br>';
                echo '<b>Pickup Odometer:</b> '; echo $row['pickupOdometer']; echo '<br>';
                echo '<b>Dropoff Odometer:</b> '; echo $row['dropoffOdometer']; echo '<br>';
                echo '<b>Status:</b> '; echo $row['status']; echo '<br>';
                echo '<b>Rating:</b> '; echo $row['rating']; echo '<br>';
                echo '<b>Comment:</b> '; echo $row['commentText']; echo '<br>';
                if ($row['replyText'] == null) {
                    echo '<b>Admin Reply:</b> '; echo "<form name='reply' id='reply' action='rentalhistory.php' method='post'><input type='hidden' name='date' value='"; echo $row['date']; echo "'><input type='text' name='replyText'/>&nbsp;<input type='submit' id='reply' name='reply' value='Reply' /></form>"; echo '<br>';
                } else {
                    echo '<b>Admin Reply:</b> '; echo $row['replyText']; echo '<br>';
                }
                echo '<br>';
            }

            if (isset($_POST['reply'])) {
                if ($_POST['replyText'] != "") {
                    $reply_query = "UPDATE rentalhistory SET replyText = ? WHERE vin = ? AND date = ?";
                    $reply_stmt = $con->prepare($reply_query);  
                    $reply_stmt->bind_param('sss', $_POST['replyText'], $_SESSION['rentalhistoryvin'], $_POST['date']);
                    $reply_stmt->execute();
                    header("Refresh:0");
                }
            }
        ?>

    </body>
</html>