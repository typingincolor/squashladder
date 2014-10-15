<?php
	function playerNameDropDown ($id) {
		$query = 'select playerid, forename, surname from squash_players';
		$result = @mysql_query($query);

		if ($result) {
			$retstring = '<select name="' . $id . '">';
			while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
				$retstring = $retstring . '<option value="' . $row[0] . '">';
				$retstring = $retstring . $row[1] . ' ' . $row[2];
				$retstring = $retstring . '</option>';
			}
			$retstring = $retstring . '</select>';
		} else {
			$retstring = '<p>Unable to execute query: ' . mysql_error() . '</p>';
		}
		
		return $retstring;
	}

	function retrievePlayerDetails ($playerid) {
		$query = "select playerid, forename, surname, email, rank from squash_players where playerid = $playerid";
		$result = @mysql_query($query);

		if ($result) {
			$row = mysql_fetch_array($result, MYSQL_NUM);
			return $row;
		} else {
			echo '<p>Unable to find player: ' . mysql_error() . '</p>';
			return null;
		}
	}

	function swapPlayers ($rank1, $rank2) {
		$query1 = "update squash_players set rank = -1 where rank = $rank1";
		$result1 = @mysql_query($query1);
		if (! $result1) {
			echo '<p>Unable to execute query: ' . mysql_error() . '</p>';
		}

		$query2 = "update squash_players set rank = rank + 1 where rank >= $rank2 and rank < $rank1";
		$result2 = @mysql_query($query2);
		if (! $result2) {
			echo '<p>Unable to execute query: ' . mysql_error() . '</p>';
		}

		$query3 = "update squash_players set rank = $rank2 where rank = -1";
		$result3 = @mysql_query($query3);
		if (! $result3) {
			echo '<p>Unable to execute query: ' . mysql_error() . '</p>';
		}
	}

	function notifyPlayers ($player1, $player2, $result) {
		if ($result == 1) {
			$text = "beat";
		} else {
			$text = "drew with";
		}

		$body = "The result:\n\n$player1[1] $player1[2] $text $player2[1] $player2[2]\n\nhas been entered in the squash ladder system";
		$from = "From: Squash Ladder Admin <squashadmin@e-ginger.com>";

		if (isset ($player1[3])) {
			if (! mail("$player1[3]", 'GSSA Squash Ladder: Result', $body, $from)) {
				echo "<p>Unable to send email to $player1[1] $player1[2]</p>";
			} 
		}

		if (isset ($player2[3])) {
			if (! mail("$player2[3]", 'GSSA Squash Ladder: Result', $body, $from)) {
				echo "<p>Unable to send email to $player2[1] $player2[2]</p>";
			}
		}
	}
?>
