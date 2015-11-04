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
		// teen objekti, seal on error, ->id ja ->message või success ja sellel on message
		$response = new StdClass();
		//kas selline email on juba olemas
		$stmt = $this->connection->prepare("SELECT id FROM user_sample WHERE email=?");
		$stmt->bind_param("s", $email2);
		$stmt->bind_result($id);
		$stmt->execute();
		
		//kas sain rea andmeid
		if($stmt->fetch()){
			
			//annan errori- selline email olemas
			$error = new StdClass();
			$error->id = 0;
			$error->message = "Sellise e-postiga kasutaja on juba olemas!";
			
			$response->error = $error;
			//kõik mis on pärast returni enam ei käivitata
			return $response;
		}
		
		//sulgen eelmise päringu
		$stmt->close();
		
		
		$stmt = $this->connection->prepare("INSERT INTO users2 (email, password, firstname, lastname) VALUES (?, ?, ?, ?)");
		echo $mysqli->error;
		$stmt->bind_param("ssss", $email2, $hash, $firstname, $lastname);
		
		//sai edukalt salvestatud
		if($stmt->execute()){
			
			$success = new StdClass();
			$success->message = "Kasutaja edukalt loodud!";
			
			$response->success = $success;
			
			
		}else{
			//midagi läks katki
			$error = new StdClass();
			$error->id = 1;
			$error->message = "Midagi läks katki!";
			
			$response->error = $error;
			
			
		}
		
		$stmt->close();
		return $response;
	}
	function loginUser($email1, $hash){
		$response = new StdClass();
		//kas selline email on juba olemas
		$stmt = $this->connection->prepare("SELECT id FROM user_sample WHERE email=?");
		$stmt->bind_param("s", $email1);
		$stmt->bind_result($id);
		$stmt->execute();
		
		//ei olnud sellist emaili
		//! ei ole sellist
		if(!$stmt->fetch()){
			

			$error = new StdClass();
			$error->id = 0;
			$error->message = "Sellist kasutajat ei ole olemas";
			
			$response->error = $error;

			return $response;
		}
		//********************//
		//****OLULINE*********//
		//********************//
		$stmt->close();
		
		$stmt = $this->connection->prepare("SELECT id, email FROM users2 WHERE email=? AND password=?");
		//echo $this->connection->error;
		$stmt->bind_param("ss", $email1, $hash);
		$stmt->bind_result($id_from_db, $email_from_db);
		$stmt->execute();
		
		if($stmt->fetch()){
			echo "Email ja parool õiged, kasutaja id=" .$id_from_db;
			//kõik õige
			$success = new StdClass();
			$success->message = "Kasutaja edukalt sisse logitud!";
			
			$response->success = $success;
			
			$user = new StdClass();
			$user->id = $id_from_db;
			$user->email = $email_from_db;
			
			$response->user = $user;
			
		}else{
			echo "Wrong credentials";
			
			//parool vale
			$error = new StdClass();
			$error->id = 0;
			$error->message = "Parool on vale!";
			
			$response->error = $error;
		}
		$stmt->close();

		return $response;
	} 
	
} ?>