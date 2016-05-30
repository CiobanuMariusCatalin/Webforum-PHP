<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="ro">
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
				require_once("conexiuneBD.php");
				//daca exista un id de categorie ma duc mai departe altfel nu am ce face
				if(isset($_REQUEST['categorie']) && !empty($_REQUEST['categorie'])){
					
					//daca exista in query actiune si nu este gol il folosesc pentru a afisa formul de creat threaduri si
					//pentru a adauga in baza de date
					if(isset($_REQUEST['actiune']) && !empty($_REQUEST['actiune'])){
						if($_REQUEST['actiune']==="showAddThread"){
							echo'<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
							<input type="hidden" name="actiune" value="addThread">
							<input type="hidden" name="categorie" value="'.$_REQUEST['categorie'].'">
							<table>
							<tr>
							<td>Nume Thread</td>
							<td><input type="text" name="nume" maxlength="500"  required></input></td>
							</tr>
							<tr>
							<td>Descriere</td>
							<td><input type="text" name="descriere" maxlength="500"  required></input></td>
							</tr>	
							<tr>
							<td>Mesaj</td>
							<td><textarea rows="6" cols="50" name="mesaj" maxlength="5000"  required></textarea></td>
							</tr>
							</table>
							<button type="submit">Adauga Thread</button>
							</form>';
						}
						if($_REQUEST['actiune']==="addThread"){
							//daca toate campurile sunt completate
							if(isset($_REQUEST['nume'])&& !empty($_REQUEST['nume'])&&isset($_REQUEST['descriere'])&& !empty($_REQUEST['descriere'])&&isset($_REQUEST['mesaj'])&& !empty($_REQUEST['mesaj'])){
								$sql="INSERT INTO thread(categorie_id,nume,descriere,autor,mesaj,data_creeri) VALUES(".$_REQUEST['categorie'].",'".$_REQUEST['nume']."','".$_REQUEST['descriere']."','".$_SESSION["username"]."','".$_REQUEST['mesaj']."',now())";
								$rezultat=$conn->query($sql);
								if(rezultat==true){
									
									echo"Inserare cu succes<br/>";
									echo'<a href="categorie.php?categorie='.$_REQUEST['categorie'].'">Inapoi</a>';
								}else
								echo"Inserarea nu a fost efectuata" .$conn->errno ."  " .$conn->error;
							}else echo"Nu sunt toate datele din query in regula";
						}
						if($_REQUEST['actiune']==="deleteThread" && isset($_REQUEST['thread']) &&!empty($_REQUEST['thread'])){
							
							//verific inca odata daca e admin sau daca e autorul sa fiu sigur ca nu a ajuns aici fara autorizatie
							if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
								
								$sql="SELECT autor FROM thread WHERE categorie_id=".$_REQUEST['categorie'];
								$threaduri = $conn->query($sql);
								$autor;
								if(!$threaduri) {echo "eroare baza de date". $conn->errno." ".$conn->error ;
									die();
								}
								else{
									while($row = $threaduri->fetch_assoc()) {
									$autor=$row['autor'];
									}
								}
								
								$sql="SELECT tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
								
								$rezultat=$conn->query($sql);
								if (!$rezultat) 
								echo "eroare baza de date". $conn->errno." ".$conn->error ;
								else{
									while($row3=$rezultat->fetch_assoc()){
										
										if($row3["tip_utilizator"]=='admin' ||$autor===$_SESSION["username"]){
											$sql="DELETE FROM thread WHERE id=".$_REQUEST['thread'];
											$rezultat=$conn->query($sql);
											if(!$rezultat){
												echo"Stergerea nu a fost efectuata" .$conn->errno ."  " .$conn->error;
											}
											else echo  "<script>window.location ='categorie.php?categorie=".$_REQUEST['categorie']."';</script>";
											} else echo'Nu ai privilegii de admin sau nu esti autorul';
										}
									}
								}
								
								
								
								
						}
					}
					// aici intru in mod default cand nu am nici un query si afisez toate threadurile
					if(!isset($_REQUEST['actiune'])){
						echo '<table class="tabelPrincipal">';
						
						//afisez butonul de adaugat threaduri doar pentru utilizatori inregistrati
						if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
							echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
							<input type="hidden" name="actiune" value="showAddThread">	
							<input type="hidden" name="categorie" value="'.$_REQUEST['categorie'].'">	
							<button type="submit" >Adauga Thread</button></form>';
							
						}
						
						
						$sql="SELECT id,nume,autor,descriere FROM thread WHERE categorie_id=".$_REQUEST['categorie'];
						$threaduri = $conn->query($sql);
						if(!$threaduri) echo "eroare baza de date". $conn->errno." ".$conn->error ;
						else{
							while($row = $threaduri->fetch_assoc()) {
								//afisez categoria
								echo "<tr><td><a href=\"thread.php?id=".$row["id"] ."&page=1 \"  >".$row["nume"]."</a></td>";
								echo "<td>".$row["descriere"]."</td>";
								$autor=$row["autor"];
								
								//afisez butonul de sters threaduri doar pentru admini si pentru cei care au creat threadul
								if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
									$sql="SELECT tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
									$rezultat=$conn->query($sql);
									if (!$rezultat) 
									echo "eroare baza de date". $conn->errno." ".$conn->error ;
									else{
										while($row4=$rezultat->fetch_assoc()){
											
											if($row4["tip_utilizator"]=='admin'||$autor===$_SESSION["username"]){
												
												//butonul de sters categorii
												echo '<td>
												<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
												<input type="hidden" name="actiune" value="deleteThread">	
												<input type="hidden" name="categorie" value="'.$_REQUEST['categorie'].'">	
												<input type="hidden" name="thread" value="'.$row["id"].'">	
												<button type="submit" >Sterge Thread</button>
												</form></td>';
												
											}
										}
									}
								}
								//afisez aici sa fiu sigur ca se incheie randul chiar daca nu sunt admin si nu imi arata butonul
								echo '</tr>';
							}
						}
						echo "</table>";
					}
				}
			?>
			
		</div>
	</body>
</html>								