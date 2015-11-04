<?php
	
	require_once("functions.php");
	
	//siia pääseb ligi sisseloginud kasutaja
	//kui kasutaja ei ole sisseloginud, suunan data.php lehele
	if(!isset($_SESSION["logged_in_user_id"])){
		header("Location: login.php");
	}
	
	$number_plate = $color ="";
	$number_plate_error = $color_error ="";
	//keegi vajutas nuppu numbrimärgi lisamiseks
	
	if(isset($_POST["add_plate"])){
		if ( empty($_POST["number_plate"]) ) {
				$number_plate_error = "See väli on kohustuslik";
			}else{
				//kõik korras, test_input eemaldab pahatahtlikud osad
				$number_plate = test_input($_POST["number_plate"]);
				}
	

		if ( empty($_POST["color"]) ) {
				$color_error = "See väli on kohustuslik";
			}else{
				//kõik korras, test_input eemaldab pahatahtlikud osad
				$color = test_input($_POST["color"]);
				}
		//mõlemad on kohustuslikud
		if($color_error == "" && $number_plate_error == ""){
			//salvestate ab'i fn kaudu addCarPlate
			//message funktsioonist
			
			$message = addCarPlate($number_plate, $color);
			if($message != ""){
				//õnnestus, teeme inputi väljad tühjaks
				$number_plate = "";
				$color = "";
				
				echo $message;
			}
		}
	}
	
	//kasutaja tahab välja logida
	if(isset($_GET["logout"])){
		//aadressireal on olemas muutuja logout
		//kustutame kõik session muutujad ja peatame sessiooni
		session_destroy();
		
		header("Location: login.php");
	}
	
	function test_input($data) {	
		$data = trim($data);	//võtab ära tühikud,enterid,tabid
		$data = stripslashes($data);  //võtab ära tagurpidi kaldkriipsud
		$data = htmlspecialchars($data);	//teeb htmli tekstiks, nt < läheb &lt
		return $data;
	}
?>
<p>Tere, <?=$_SESSION["logged_in_user_email"];?>
	<a href="?logout=1"> Logi välja <a>
</p>
	
<h2>Lisa autonumbrimärk</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
		<label for ="number_plate">Auto numbrimärk</label><br>
		<input id="number_plate" name="number_plate" type="text" value="<?php echo $number_plate; ?>" > <?php echo $number_plate_error; ?><br><br>
		<label for ="color">Värv</label><br>
		<input id="color" name="color" type="text" value="<?php echo $color; ?>"> <?php echo $color_error; ?> <br><br>
		<input type="submit" name="add_plate" value="Sisesta"><br>
		</form>	