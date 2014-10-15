<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                      "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>GSSA Squash Ladder: Result Entry</title>
</head>

<body>
<?php
	// include common funtions
	require("common_functions.php");

	// set database access information
	define('DB_USER', 'xxxxxx');
	define('DB_PASSWORD', 'yyyyyyy');
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'zzzzzzzzzzz');
	echo '<H1>GSSA Squash Ladder: Result Entry</H1>';
	echo '<p>If you have any problems with this page email <a href="mailto:andrew.braithwaite@atosorigin.com">Andrew Braithwaite</a>';

	// connect to the database
	$dbc = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Could not connect to mysql: ' . mysql_error());
	mysql_select_db(DB_NAME) or die('Could not select database: ' . mysql_error());

	// if the form has been posted then process it
	if (isset($_POST['submit'])) {

		$message = NULL;

		// check player 1
		if (empty($_POST['player1'])) {
			$p1 = FALSE;
			$message .= '<p>player 1 not been set</p>';
		} else {
			$p1 = $_POST['player1'];
		}

		// check player 2
		if (empty($_POST['player2'])) {
			$p2 = FALSE;
			$message .= '<p>player 2 not been set</p>';
		} else {
			$p2 = $_POST['player2'];
		}

		// check result
		if (empty($_POST['result'])) {
			$matchresult = FALSE;
			$message .= '<p>match result not been set</p>';
		} else {
			$matchresult = $_POST['result'];
		}

		// check date
		if (empty($_POST['date'])) {
			$matchdate = FALSE;
			$message .= '<p>date not been set</p>';
		} else {
			if (!eregi("^[1-2][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]",trim($_POST['date']))) {
				$matchdate = FALSE;
				$message .= '<p>incorrect date format</p>';
			} else {
				$matchdate = $_POST['date'];
			}
		}

		if ( $matchdate && $p1 && $p2 && $matchresult ) {
			// insert the record
			$query = "insert into squash_results(match_date, player1, player2, result)";
			$query .= " values ('$matchdate',$p1,$p2,$matchresult)";
			$result = @mysql_query($query);

			if ($result) {
				echo '<p>Result entered successfully</p>';
			} else {
				echo '<font color="red">';
				echo '<p>Unable to enter result: ' . mysql_error() . '</p>';
				echo '<p>Query: ' . $query . '</p>';
				echo '</font>';
			}

			// get player details
			$player1details = retrievePlayerDetails($p1);
			$player2details = retrievePlayerDetails($p2);

			notifyPlayers($player1details, $player2details, $matchresult);
			if ( $matchresult == 1 )
			{
				if ( $player1details[4] > $player2details[4] ) {
					swapPlayers($player1details[4], $player2details[4]);
				}
			}
		} else {
			echo '<font color="red">';
			echo $message;
			echo '</font>';
		}

		echo '<p>Back to <a href="index.php">main page</a></p>';
		echo '<p>Enter a <a href="enter_result.php">result</a></p>';
	} else {
		echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
		echo '<table cellspacing="2" cellpadding="2">';
		echo '<tr><td>Date</td><td><input type="text" name="date" size="11"></td></tr>';
		echo '<tr><td>Player 1</td><td>' . playerNameDropDown("player1") . '</td></tr>';
		echo '<tr><td></td><td>';
		echo '<select name="result"><option value="1">beat</option><option value="2">drew with</option></select>';
		echo '<tr><td>Player 2</td><td>' . playerNameDropDown("player2") . '</td></tr>';
		echo '</table>';
		echo '<input type="reset" name="reset" value="Reset">';
		echo '<input type="submit" name="submit" value="Submit">';
		echo '</form>';
	}
	mysql_close();
?>
</body>

</html>

