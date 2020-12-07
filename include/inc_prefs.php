<?php

	if(!defined('AthenaHTTP')) header('Location: ..');
	
	$STATUS_MESSAGE = '';
	$STATUS_NOTE = false;
	
	if($_POST['pref_oldpass'] && $_POST['pref_newpass'] && $_POST['pref_newpassconf'])
	{
		$hashedpass = hash('sha512', trim($_POST['pref_oldpass']) . '|' . trim($_POST['pref_oldpass']));
	
		if($hashedpass == $USERINFO['password'] && trim($_POST['pref_newpass']) == trim($_POST['pref_newpassconf']))
		{
			$hashednewpass = hash('sha512', trim($_POST['pref_newpass']) . '|' . trim($_POST['pref_newpass']));
			$_SESSION['password'] = $hashednewpass;
			
			mysqli_query($conn, 'UPDATE users SET password = \'' . $hashednewpass . '\' WHERE username = \'' . mysql_real_escape_string($USERINFO['username']) . '\';');			
			
			$STATUS_MESSAGE = 'Password changed.';
		}
		else
		{
			$STATUS_MESSAGE = 'Failed to change password.';
		}
		
		$STATUS_NOTE = true;
	}
		
	if($_POST['pref_knock'] && is_numeric($_POST['pref_knock']) && $_POST['pref_dead'] && is_numeric($_POST['pref_dead']) && $USERINFO['admin'])
	{
		mysqli_query($conn, 'UPDATE config SET data = \'' . mysqli_real_escape_string($conn, trim($_POST['pref_knock'])) . '\' WHERE value = \'knock\';');
		mysqli_query($conn, 'UPDATE config SET data = \'' . mysqli_real_escape_string($conn, trim($_POST['pref_dead']) * 86400) . '\' WHERE value = \'dead\';');
		
		$config_online = mysqli_fetch_array(mysqli_query($conn, 'SELECT data FROM config WHERE value = \'knock\';'));
		$config_dead = mysqli_fetch_array(mysqli_query($conn, 'SELECT data FROM config WHERE value = \'dead\';'));	
		
		$STATUS_MESSAGE = 'Bot settings changed.';
		$STATUS_NOTE = true;
	}
	
	if($_POST['pref_loginpagekey'] && $USERINFO['admin'])
	{
		mysqli_query($conn, 'UPDATE config SET `key` = \'' . mysqli_real_escape_string($conn, trim($_POST['pref_loginpagekey'])) . '\' WHERE value = \'loginpagekey\';');
		
		$STATUS_MESSAGE = 'New login page key set.';
		$STATUS_NOTE = true;
	}
	
	if($_GET['c'] == 'enableloginpagekey' && $USERINFO['admin'])
	{
		mysqli_query($conn, 'UPDATE config SET data=1 WHERE value = \'loginpagekey\';');
		
		$STATUS_MESSAGE = 'Login page key enabled.';
		$STATUS_NOTE = true;
	}
	
	if($_GET['c'] == 'disableloginpagekey' && $USERINFO['admin'])
	{
		mysqli_query($conn, 'UPDATE config SET data=0 WHERE value = \'loginpagekey\';');
		
		$STATUS_MESSAGE = 'Login page key disabled.';
		$STATUS_NOTE = true;
	}
	
	if($_GET['c'] == 'kill' && $USERINFO['admin'])
	{
		mysqli_query($conn, 'UPDATE botlist SET botskilled=0, files=0, regkey=0 WHERE 1;');
		
		$STATUS_MESSAGE = 'Botkiller statistics cleared.';
		$STATUS_NOTE = true;
	}
	
	if($_GET['c'] == 'bots' && $USERINFO['admin'])
	{
		mysqli_query('TRUNCATE botlist');
		
		$STATUS_MESSAGE = 'Botlist cleared.';
		$STATUS_NOTE = true;
	}
	
	if($_GET['c'] == 'offdead' && $USERINFO['admin'])
	{
		mysqli_query($conn, 'DELETE FROM botlist WHERE lastseen < \'' . mysql_real_escape_string($TIME - ONLINE - 30) . '\';');
		$STATUS_MESSAGE = 'Offline & Dead Bots cleared.';
		$STATUS_NOTE = true;
	}
?>