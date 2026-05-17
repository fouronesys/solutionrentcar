<?php


if(Session::getUID()!=""){
	$user = UserData::getById(Session::getUID());
	$password = sha1(md5($_POST["password"]));
	if($password==$user->password){
		$user->password = sha1(md5($_POST["newpassword"]));
		$user->update();
		setcookie("password_updated","true");
		header('location:./?action=logout'); 
	}else{
		header('location:./?view=security&msg=invalidpasswd'); 
	}

}else {
header('location:index.php'); 
}


?>