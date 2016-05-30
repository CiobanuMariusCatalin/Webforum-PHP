<?php 
	session_start();
	//daca este deja logat redirectionez catre pagina de start
	if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
		echo "<script>window.location = 'home.php';</script>";
	}
	require_once("conexiuneBD.php");
	if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
		if($_REQUEST['action']==='signUp'){
			if(isset($_REQUEST['nume']) && !empty($_REQUEST['nume']) &&isset($_REQUEST['parola']) && !empty($_REQUEST['parola']) &&isset($_REQUEST['confirmareParola']) && !empty($_REQUEST['confirmareParola'])){
				//verific daca exista deja utilizatorul cu numele acesta in baza de date
				$sql="SELECT '1' FROM utilizatori WHERE nume='".$_REQUEST['nume']."'";
				$raspuns = $conn->query($sql);
				if(!$raspuns) echo "eroare baza de date". $conn->errno." ".$conn->error ;
				//daca imi intoarce 0 rezultate inseamna nu exista persoane cu acest nume
				if(mysqli_num_rows($raspuns) ==0){
					//verific daca parolele se potrivesc
					if($_REQUEST['parola']!==$_REQUEST['confirmareParola']){
						echo'<script>alert("Parolele nu se potrivesc");</script>';
					}
					else{
						//daca toate condiitile de pana acum se indeplinesc inserez datele in BD
						$sql="INSERT INTO utilizatori(nume,parola,tip_utilizator) VALUES('".$_REQUEST['nume']."','".$_REQUEST['parola']."','normal')";
						$rezultat=$conn->query($sql);
						if($rezultat){
							//daca rezultatul este acceptat redirectionez spre log in
							echo "<script>
							window.location = 'logIn.php';
							</script>";
						}
						else
						echo"Inserarea nu a fost efectuata" .$conn->errno ."  " .$conn->error;
					}
				}
				else {
					echo'<script>alert("Numele exista deja in baza de date");</script>';
				}
			}else echo 'nume , parola, confirmareParola din query nu sunt setate corect';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		
		<link href="Css/Stiluri.css" type="text/css" rel="stylesheet"/>
		<script src="Script/script.js" type="text/javascript"></script>
	</head>
	<body>
		
		<ul id="meniu">
			<li><a href="forum.php">Home</a>
			</li>
			<li><a href="contact.php">Contact</a>
			</li>
			
		</ul>
		<div class="bigContainer">
			<?php
				include_once('contorUseriOnline.php');
			?>
			<h1>Sign Up</h1>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type="hidden" name="action" value="signUp">
				<table>
				<tr><td>Nume</td><td><input type="text" name="nume" required ></input></td></tr>
				<tr><td>Parola</td><td> <input type="password" name="parola"required ></input></td></tr>
				<tr><td>Confirmare Parola </td><td><input type="password" name="confirmareParola" required></input></td></tr>
				<tr><td><button type="submit">Sign up</button></td></tr>
				</table>
			</form>
		</div>
	</body>
</html>						