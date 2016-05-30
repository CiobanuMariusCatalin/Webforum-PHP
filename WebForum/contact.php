<?php
	session_start();
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
			<?php if(isset($_SESSION["username"])&&!empty($_SESSION["username"])){
				echo '<li><a href="logOut.php">Log Out</a></li>';
			}
			else{
				echo '<li><a href="logIn.php">Log In</a></li>';
				echo '<li><a href="SignUp.php">Sign Up</a></li>';
			}
			?>
		</ul>
		
		<div class="bigContainer">
			<?php 
			include_once('contorUseriOnline.php');
			
			
				if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
					if($_REQUEST['action']=="sendMail"){
						//daca trebuie sa trimita mail
						if(isset($_REQUEST['mesajMail']) && !empty($_REQUEST['mesajMail'])&&isset($_REQUEST['motiv']) && !empty($_REQUEST['motiv'])){
							//daca este logat poate trimite mail
							if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
								
								//cod sa trimit mail
								include('PHPMailer-master\class.phpmailer.php');
								
								require('PHPMailer-master\class.smtp.php');
								$mail = new PHPMailer(); // create a new object
								$mail->IsSMTP(); // enable SMTP
								//$mail->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
								$mail->SMTPAuth = true; // authentication enabled
								$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
								$mail->Host = "plus.smtp.mail.yahoo.com";
								$mail->Port = 465; // or 587
								$mail->IsHTML(true);
								$mail->Username = "proiectphpforum@yahoo.ro";
								$mail->Password = "ab1234@#~!#@a";
								$mail->SetFrom("proiectphpforum@yahoo.ro");
								$mail->Subject = $_REQUEST['motiv'];
								$mail->Body = $_REQUEST['mesajMail'];
								$mail->AddAddress("proiectphpforum@yahoo.ro");
								if(!$mail->Send())
								{
									echo "Mailer Error: " . $mail->ErrorInfo;
								}
								else
								{
									echo "Mesajul a fost trimis";
								}
								
								
								
							}
							else echo 'trebuie sa fii logat sa ne poti trimite mail';
						}
					}
				}
						//daca queryeul action nu este setat inseamna ca trebuie arat formularul
						if(!isset($_REQUEST['action'])){
							echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
							<input type="hidden" name="action" value="sendMail"></input>
							Daca vreti sa ne contactati puteti sa ne trimiteti mail prin completarea formularului de mai jos<br/>
							Motiv</br>
							<input type="text" name="motiv" ></input></br>
							
							Mesajul</br>
							<textarea rows="6" cols="50" name="mesajMail" required></textarea></br>
							<button type="submit">Trimite</button>';
						}
					?>
				</div>
			</body>
		</html>		