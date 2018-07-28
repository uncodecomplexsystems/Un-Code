<!DOCTYPE html>
<html lang="de">
<head>
<meta http-equiv="content-type" content="text/html">
<title>UN-CODE - {$title}</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css">
<link rel="stylesheet" href="css/main.css" type="text/css">
<script language="javascript" type="text/javascript" src="include/jquery.js"></script>
<script language="javascript" type="text/javascript" src="include/jquery.hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="include/jquery-ui.js"></script>
<script language="javascript" type="text/javascript" src="include/common.js"></script>
</head>

<body>

<div class="topnav">
	<nav>
	<ul>
		{if $title != 'Studies Overview'}
		<li><a href="?">Studies Overview</a></li>
		{if (isset($projects))}
		{$count = 0}
		{foreach $projects as $row}
		{if $count < 6}
		<li><a id="smaller" href="?p=project&project_id={$row.project_id}">{$row.project_name}</a></li>
		{/if}
		{$count = $count + 1}
		{/foreach}
		{/if}
		{/if}
		<li class="right"><a href="?p=logout">Logout {$name}</a></li>
		<li class="right"><a href="?p=settings">Settings</a></li>
	</ul>
	</nav>
</div>