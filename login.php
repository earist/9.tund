<?php
	//Loome ühenduse andmebaasiga
	require_once("functions.php");
	
	//kui kasutaja on sisseloginud, suunan data.php lehele
	if(isset($_SESSION["logged_in_user_id"])){
		header("Location: data.php");
	}

	
	//login.php
	
	$email1_error = "";
	$email2_error = "";
	$password1_error = "";
	$password2_error = "";
	$password3_error ="";
	$firstname_error ="";
	$lastname_error ="";
	//muutujad andmebaasi väärtuse jaoks
	$email1 ="";
	$email2 ="";
	$firstname ="";
	$lastname ="";
	$password1 ="";
	$password2 ="";
	
	//kontrollime, et keegi vajutas input nuppu
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		
		//echo "Keegi vajutas nuppu"; sedasi tehakse kontroll ainult siis, kui vajutatakse login nuppu
		//vajutas login nuppu
		if(isset($_POST['login'])){
			
			
			//kontrollin, et epost ei ole tühi
			if ( empty($_POST["email1"]) ) {
				$email1_error = "See väli on kohustuslik";
			}else{
				//kõik korras, test_input eemaldab pahatahtlikud osad
				$email1 = test_input($_POST["email1"]);
				}
				
			//kontrollin, et parool ei ole tühi
			if ( empty($_POST["password1"]) ) {
				$password1_error = "See väli on kohustuslik";	
			} else {
				$password1 = test_input($_POST["password1"]);
			}
		//Võib kasutaja sisse logida
			if($password1_error == "" && $email1_error == ""){
				echo "Võib sisse logida! Kasutajanimi on ".$email1." ja parool on ".$password1;
				
				$hash = hash("sha512", $password1);
				
				//kasutaja sisselogimise function,
				$login_response = $User->loginUser($email1, $hash);
				
				//kasutaja logis edukalt sisse
				if(isset($login_response->success)){
					
					//vaja teada, id-d emaili
					$_SESSION["logged_in_user_id"] = $login_response->user->id;
					$_SESSION["logged_in_user_email"] = $login_response->user->email;
					
					//saadan sõnumi teise faili kasutades SESSIOONI
					$_SESSION["login_success_message"] = $login_response->success->message;
					header("Location:data.php");
				}
			}
			
			
			//*****************************************
			//keegi vajutas registreeri nuppu
		}elseif(isset($_POST['registreeri'])) {
			
			//kontrollin, et nimi pole tühi
			if ( empty($_POST["firstname"]) ) {
				$firstname_error = "See väli on kohustuslik";
			}else{
				//kõik korras, test_input eemaldab pahatahtlikud osad
				$firstname = test_input($_POST["firstname"]);
				}
				
				
			// kontrollin et lastname pole tühi
			if ( empty($_POST["lastname"]) ) {
				$lastname_error = "See väli on kohustuslik";
			}else{
				//kõik korras, test_input eemaldab pahatahtlikud osad
				$lastname = test_input($_POST["lastname"]);
				}
			
			//kontrollin, et epost ei ole tühi
			if ( empty($_POST["email2"]) ) {
				$email2_error = "See väli on kohustuslik";
			}else{
				//kõik korras, test_input eemaldab pahatahtlikud osad
				$email2 = test_input($_POST["email2"]);
				}
				
			
			//kontrollin, et parool ei ole tühi
			if ( empty($_POST["password2"]) ) {
				$password2_error = "See väli on kohustuslik";	
			} else {
				
				
				if(strlen($_POST["password2"]) < 8) {
					$password2_error ="Peab olema vähemalt 8 sümbolit pikk!";
				}else{
					$password2 = test_input($_POST["password2"]);
				}
			}
			
			
			//kontrollin, et paroolid klapiksid
			if ($_POST["password2"] != $_POST["password3"]) {
				$password3_error = "Paroolid ei kattu. Proovi uuesti.";	

				}
				
				
			if(	$email2_error == "" && $password2_error == ""){
				
				//räsi paroolist, mille salvestame andmebaasi
				$hash = hash("sha512", $password2);
				
				echo "Võib kasutajat luua! Kasutajanimi on ".$email2." ja parool on ".$password2. "ja räsi on ".$hash;
				
				//kasutaja loomise function, failist function.php
				//saadame kaasa muutujad
				//fn User klassist
				$create_response = $User->createUser($firstname, $lastname, $email2, $hash);
			}
			
				
			}
		} 
		
		
	function test_input($data) {	
		$data = trim($data);	//võtab ära tühikud,enterid,tabid
		$data = stripslashes($data);  //võtab ära tagurpidi kaldkriipsud
		$data = htmlspecialchars($data);	//teeb htmli tekstiks, nt < läheb &lt
		return $data;
	}
	
	
	
?>
<html>

<head>
	<title>Login page</title>
	<metadata encoding="UTF8">
</head>
<body bgcolor="#BF9E7C"><br><br>
		<center><h2>Log in</h2></center>
		<?php if(isset($login_response->error)): ?>
		
		<p style="color:red;"> <?=$login_response->error->message;?> </p>
		
		<?php elseif(isset($login_response->success)): ?>
		
		<p style="color:green;"> <?=$login_response->success->message;?> </p>
		
		<?php endif; ?>
		<center><form action="login.php" method="post"></center>
			<center><input name="email1" type="email" value="<?php echo $email1 ?>" placeholder="E-post"> <?php echo $email1_error; ?></center><br><br>
			<center><input name="password1" type="password" placeholder="Parool"> <?php echo $password1_error; ?> </center><br><br>
			<center><input type="submit" name="login" value="Log in"></center><br>
			<center><a href="http://www.tlu.ee/~earist/">Unustasid parooli?</a></center>
		</form>
	
	<center><h2>Create user</h2></center>
	
	<?php if(isset($create_response->error)): ?>
	
	<p style="color:red;"> <?=$create_response->error->message;?> </p>
	
	<?php elseif(isset($create_response->success)): ?>
	
	<p style="color:green;"> <?=$create_response->success->message;?> </p>
	
	<?php endif; ?>
	
		<form action ="login.php" method="post">
			<center><input type="text" name="firstname" value ="<?php echo $firstname ?>" placeholder="Eesnimi"><?php echo $firstname_error;?></center><br>
			<center><input type="text" name="lastname" value ="<?php echo $lastname ?>" placeholder="Perekonnanimi"><?php echo $lastname_error;?></center><br>
			<center><input name="email2" type="email" placeholder="E-post" value ="<?php echo $email2 ?>"><?php echo $email2_error; ?></center><br>
			<center><input name="password2" type="password" placeholder="Parool"><br><?php echo $password2_error; ?></center><br>
			<center><input name="password3" type="password" placeholder="Korda parooli"><?php echo $password3_error; ?></center><br><br>
			<center>Sugu?<br><input type="radio" name="sugu" value="mees">Mees&nbsp;&nbsp;&nbsp;<input type="radio" name="sugu" value="naine">Naine</center><br><br>
			<center><input type="submit" name="registreeri" value="Registreeri"></center><br>
		</form>
</body>

</html>