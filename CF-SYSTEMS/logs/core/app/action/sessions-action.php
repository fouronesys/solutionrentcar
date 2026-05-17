<?php 
///////////////////////////////////////////////////////////////////// TOCART /////////////////////////////////////////

if(isset($_GET["opt"]) && $_GET["opt"]=="cars"):

if(!isset($_SESSION["carx"])){
	$product = array("model"=>$_POST["mcars"]);
	$_SESSION["cars"] = array($product);

	$cart = $_SESSION["carx"];
///////////////////////////////////////////////////////////////////

//echo $num_succ;
if($num_succ==count($cart)){
	$process = true;
}
if($process==false){
	unset($_SESSION["carx"]);
$_SESSION["errors"] = $errors;
header('location:./?view=cars&opt=new');
}




}else {

$found = false;
$cart = $_SESSION["carx"];
$index=0;

$can = true;

if($can==false){
$_SESSION["errors"] = $errors;
header('location:./?view=cars&opt=new');
}
if($can==true){
foreach($cart as $c){
	if($c["model"]==$_POST["mcars"]){
		echo "found";
		$found=true;
		break;
	}
	$index++;
//	print_r($c);
//	print "<br>";
}

if($found==true){
	$q1 = $cart[$index]["model"];
	$cart[$index]["model"]=$q1;
	$_SESSION["carx"] = $cart;
}

if($found==false){
    $nc = count($cart);
		$product = array("model"=>$_POST["mcars"]);
	$cart[$nc] = $product;
//	print_r($cart);
	$_SESSION["carx"] = $cart;
}

}
}
header('location:./?view=cars&opt=new');
///////////////////////////////////////////////////////////////////// TOCART /////////////////////////////////////////

endif;?>