<?php
	$user1 = new user("Ea");
	$user2 = new User("Sten");

?>

<?php
class User {
	//klassi loomisel(new User)
	function __construct($name){
		echo $name."<br>";
	}
	
	
} ?>