<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                      "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>GSSA Squash Ladder: Player Update</title>
</head>

<body>
<?php
	// include common funtions
	require("common_functions.php");

	include("db.php");

	echo '<H1>GSSA Squash Ladder: Player Update</H1>';
	echo '<p>If you have any problems with this page email <a href="mailto:andrew.braithwaite@atosorigin.com">Andrew Braithwaite</a>';

	// connect to the database
	$dbc = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Could not connect to mysql: ' . mysql_error());
	mysql_select_db(DB_NAME) or die('Could not select database: ' . mysql_error());

	// if the form has been posted then process it
	if (isset($_POST['submit'])) {

		$message = NULL;

		// check player 1
		if (empty($_POST['player'])) {
			$p = FALSE;
			$message .= '<p>player not been set</p>';
		} else {
			$p = $_POST['player'];
		}

		// check email address
		if (empty($_POST['email'])) {
			$e = FALSE;
			$message .= '<p>email not been set</p>';
		} else {
			if (!eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", stripslashes(trim($_POST['email'])))) {
				$e = FALSE;
				$message .= '<p>invalid email address</p>';
			} else {
				$e = $_POST['email'];
			}
		}

		if ( $p && $e ) {
			// insert the record
			$query = "update squash_players set email = '$e' where playerid = $p";
			$result = @mysql_query($query);

			if ($result) {
				echo '<p>Player updated successfully</p>';
			} else {
				echo '<font color="red">';
				echo '<p>Unable to update player: ' . mysql_error() . '</p>';
				echo '<p>Query: ' . $query . '</p>';
				echo '</font>';
			}
		} else {
			echo '<font color="red">';
			echo $message;
			echo '</font>';
		}

		echo '<p>Back to <a href="index.php">main page</a></p>';
		echo '<p><a href="update_player.php">Update</a> another player</p>';
	} else {
		echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
		echo '<table cellspacing="2" cellpadding="2">';
		echo '<tr><td>Player</td><td>' . playerNameDropDown("player") . '</td></tr>';
		echo '<tr><td>Email address</td><td>';
		echo '<input type="text" name="email" size="40"></input>';
		echo '</td></tr>';
		echo '</table>';
		echo '<input type="reset" name="reset" value="Reset">';
		echo '<input type="submit" name="submit" value="Submit">';
		echo '</form>';
	}
	mysql_close();
?>
</body>

</html>

