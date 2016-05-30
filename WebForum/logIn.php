<?php
	session_start();
	if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
		echo "<script>window.location = 'forum.php';</script>";
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
				require_once("conexiuneBD.php");
				if(isset($_REQUEST['nume']) && !empty($_REQUEST['nume']) &&isset($_REQUEST['parola']) && !empty($_REQUEST['parola'])){
					$sql="SELECT '1' FROM utilizatori WHERE nume='".$_REQUEST['nume']."' AND parola='".$_REQUEST['parola']."'";
					$raspuns = $conn->query($sql);
					if(!$raspuns) echo "eroare baza de date". $conn->errno." ".$conn->error ;
					//daca imi intoarce  rezultate diferite de 1 inseamna nu exista persoane cu acest nume
					if(mysqli_num_rows($raspuns) ==1){
						$_SESSION["username"]=$_REQUEST['nume'];
						echo "<script>window.location = 'forum.php';</script>";
						}else{
						echo '<p>Contul sau parola sunt gresite</p>';
					}
					
				}
			?>
			<h1>Login</h1>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<table>
				<tr><td>Nume</td><td><input type="text" name="nume" required ></input></td></tr>
				<tr><td>Parola</td><td> <input type="password" name="parola" required></input></td></tr>
				<tr><td><button type="submit">Log In</button>
					</table>
			</form>
			<a href="signUp.php">Sign Up</a>
		</div>
	</body>
</html>