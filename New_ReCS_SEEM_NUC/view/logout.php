<?php
	require_once("../core/client.php");
	require_once("../core/ticket.php");
	
	InitializeClient();
	
	$db=ConnectSQL();
	$ticketId=GetClientValue("ticket");
	
	DisposeTicket($db,$ticketId);
	SetClientValue("ticket","");
	SetClientValue("userid","");
	SetClientValue("username","");
	DisposeSQL($db);
	header("Location: ../index.php");
?>
