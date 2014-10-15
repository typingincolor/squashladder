<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                      "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>GSSA Squash Ladder</title>
</head>

<body>
<?php
	// set database access information
	define('DB_USER', 'xxxxxx');
	define('DB_PASSWORD', 'yyyyyyy');
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'zzzzzzzzzzz');

	echo '<H1>The latest GSSA Squash Ladder Standings</H1>';
	echo '<p>Send results to <a href="mailto:a@test.com">Your Name</a>';

	// connect to the database
	$dbc = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Could not connect to mysql: ' . mysql_error());
	mysql_select_db(DB_NAME) or die('Could not select database: ' . mysql_error());

	$query = "SELECT rank, forename, surname, email from squash_players order by rank asc";
	$result = @mysql_query($query);

	if ($result) {
		echo '<table align="center" cellspacing="2" cellpadding="2" border="1">';
		echo '<tr><th align="center">Rank</th><th align="left">Name</th><th>Action</th></tr>';

		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			echo '<tr>';
			echo '<td align="center">', $row[0], '</td><td align="left">', $row[1], ' ', $row[2], '</td>';
			echo '<td><a href="challenge.php?email=' . $row[3] . '">challenge</a></tr>';
			echo '</td>';
		}
		echo '</table>';

	} else {
		$message = '<p>Unable to execute query</p><p>' . mysql_error() . '</p>';
	}

	$query1 = "select date_format(result.match_date, '%d-%m-%Y'), p1.forename, p1.surname, p2.forename, p2.surname, descr.description ";
	$query1 .= "from squash_players p1, squash_players p2, squash_results result, squash_results_desc descr ";
	$query1 .= "where result.match_date >= subdate(now(), interval 7 day) ";
	$query1 .= "and p1.playerid = result.player1 and p2.playerid = result.player2 ";
	$query1 .= "and descr.descid = result.result ";
	$query1 .= "order by result.resultid desc";

	$result1 = @mysql_query($query1);
	if ($result1) {
		echo '<H1>Latest Results</H1>';
		echo '<table align="center" cellspacing="2" cellpadding="2">';

		while ($row = mysql_fetch_array($result1, MYSQL_NUM)) {
			echo '<tr><td>' . $row[0] . '</td>';
			echo "<td>$row[1] $row[2] $row[5] $row[3] $row[4]</td></tr>";
		}
		echo '</table>';
	} else {
		$message = '<p>Unable to execute query</p><p>' . mysql_error() . '</p>';
	}

	mysql_close();

	if (isset($message)) {
		echo '<font color="red">', $message, '</font>';
	}
?>
</body>

</html>

