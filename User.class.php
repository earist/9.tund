<?php
class User {
	
	//private- klassi sees
	private $connection;
	
	//klassi loomisel (new User)
	function __construct($mysqli){
		
		//this tähendab selle klassi muutujat, mis on ülal defineeritud (rida5)
		$this->connection = $mysqli;
	
	}
	function createUser($firstname, $lastname, $email2, $hash){
		$stmt = $this->connection->prepare("INSERT INTO users2 (email, password, firstname, lastname) VALUES (?, ?, ?, ?)");
		echo $mysqli->error;
		$stmt->bind_param("ssss", $email2, $hash, $firstname, $lastname);
		$stmt->execute();
		$stmt->close();

	}
	function loginUser($email1, $hash){
		$stmt = $this->connection->prepare("SELECT id, email FROM users2 WHERE email=? AND password=?");
		//echo $this->connection->error;
		$stmt->bind_param("ss", $email1, $hash);
		$stmt->bind_result($id_from_db, $email_from_db);
		$stmt->execute();
		if($stmt->fetch()){
			echo "Email ja parool õiged, kasutaja id=" .$id_from_db;
			
			//tekitan sessiooni muutujad
			$_SESSION["logged_in_user_id"] = $id_from_db;
			$_SESSION["logged_in_user_email"] = $email_from_db;
			
			//suunan data.php lehele
			header("Location: data.php");
			
		}else{
			echo "Wrong credentials";
		}
		$stmt->close();

		
	} 
	
} ?>