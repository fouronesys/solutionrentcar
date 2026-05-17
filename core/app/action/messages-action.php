<?php

if(isset($_GET["opt"]) && $_GET["opt"]!=""){
	$opt = $_GET["opt"];
	if($opt=="addmsg1"){
		$m = new MessageData();
	$length=15;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

		$m->user_from = $_SESSION["user_id"];
		$m->user_to = $_POST["user_to"];
		$m->code = $randomString;
		$m->message = $_POST["message"];
		$m->client = 0;
		$m->add();
		echo 'true';
	}
	elseif($opt=="addmsg3"){
		$m = new MessageData();
	$length=15;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

		$m->user_from = $_SESSION["client_id"];
		$m->user_to = $_POST["user_to"];
		$m->code = $randomString;
		$m->message = $_POST["message"];
		$m->client = 1;
		$m->add();
		echo 'true';
	}
	else if($opt=="addmsg2"){
		$m = new MessageData();

		$m->user_from = $_SESSION["user_id"];
		$m->user_to = $_POST["user_to"];
		$m->code = $_POST["code"];
		$m->message = $_POST["message"];
		$m->client = 0;
		$m->add();
		header('location:./?view=messages&opt=open&code='.$m->code);
	}
	
	else if($opt=="addmsg4"){
		$m = new MessageData();

		$m->user_from = $_SESSION["client_id"];
		$m->user_to = $_POST["user_to"];
		$m->code = $_POST["code"];
		$m->message = $_POST["message"];
		$m->client = 1;
		$m->add();
		header('location:./?view=messages&opt=open&code='.$m->code);
	}


}




?>