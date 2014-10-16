<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                      "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>GSSA Squash Ladder: Make Challenge</title>
</head>

<body>
<?php
	// include common funtions
	require("common_functions.php");

	include("db.php");

	echo '<H1>GSSA Squash Ladder: Make Challenge</H1>';
	echo '<p>If you have any problems with this page email <a href="mailto:andrew.braithwaite@atosorigin.com">Andrew Braithwaite</a>';

	// connect to the database
	$dbc = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Could not connect to mysql: ' . mysql_error());
	mysql_select_db(DB_NAME) or die('Could not select database: ' . mysql_error());

	// if the form has been posted then process it
	if (isset($_POST['submit'])) {
		$challenger = retrievePlayerDetails($_POST['challenger']);
		$body = "Message from $challenger[1] $challenger[2]:\n";
		$body .= "\n{$_POST['challenge']}";
		$body .= "\n\nReply to mailto:$challenger[3]";

		$copy = "You challenged {$_POST['email']} with the message:\n";
		$copy .= "\n{$_POST['challenge']}";

		$ok = TRUE;

		$from = "From: $challenger[1] $challenger[2] <$challenger[3]>";
		$fromcopy = "From: Squash Ladder Admin <squashadmin@e-ginger.com>";

		if (! mail($_POST['email'], 'GSSA Squash Ladder: Challenge', $body, $from)) {
			echo "<p>Unable to send an email to {$_POST['email']}</p>";
			$ok = FALSE;
		}

		if (! mail($challenger[3], 'GSSA Squash Ladder: Challenge (copy)', $copy, $fromcopy)) {
			echo "<p>Unable to send copy of challenge email to {$_POST['email']}</p>";
			$ok = FALSE;
		}

		if ($ok)
		{
			echo "<p>Challenge email sent...</p>";
		}

		echo '<p>Back to <a href="index.php">main page</a></p>';
	} else {
		echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
		echo '<table cellspacing="2" cellpadding="2">';
		echo '<tr><td>Challenger:</td><td>' . playerNameDropDown("challenger") . '</td></tr>';
		echo '<tr><td valign="top">Message:</td><td valign="top"><textarea name="challenge" rows="6" cols="50"></textarea></td></tr>';
		echo '</table>';
		echo '<input type="hidden" name="email" value="' . $email . '" />';
		echo '<input type="reset" name="reset" value="Reset" />';
		echo '<input type="submit" name="submit" value="Submit" />';
		echo '</form>';
		echo '<p>Back to <a href="index.php">main page</a></p>';
	}
	mysql_close();
?>
</body>

</html>

