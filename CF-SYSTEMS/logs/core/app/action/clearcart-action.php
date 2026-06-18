<?php if(isset($_GET["opt"]) && $_GET["opt"]=="all"){

/////////////////////////////////////////////////// ALL //////////////////////////////////////////////

if(isset($_GET["product_id"])){
	$cart=$_SESSION["cotization"];
	if(count($cart)==1){
	 unset($_SESSION["cotization"]);
	}else{
		$ncart = array();
		//$nx=0;
		foreach($cart as $c){
			if($c["product_id"]!=$_GET["product_id"]){
				$ncart[]= $c;
			}
			//$nx++;
		}
		$_SESSION["cotization"] = $ncart;
	}

}else{
 unset($_SESSION["cotization"]);
}

header('location:./?view=cotization&opt=new'); 

}elseif(isset($_GET["opt"]) && $_GET["opt"]=="traspase"){
//////////////////////////////////////////////////// TRASPASE /////////////////////////////

if(isset($_GET["product_id"])){
	$cart=$_SESSION["traspase"];
	if(count($cart)==1){
	 unset($_SESSION["traspase"]);
	}else{
		$ncart = null;
		$nx=0;
		foreach($cart as $c){
			if($c["product_id"]!=$_GET["product_id"]){
				$ncart[$nx]= $c;
			}
			$nx++;
		}
		$_SESSION["traspase"] = $ncart;
	}

}else{
 unset($_SESSION["traspase"]);
}

header('location:./?view=trasps');

}

?>