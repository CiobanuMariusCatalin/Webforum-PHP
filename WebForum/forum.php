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
				include_once('jokeOfTheDay.php');
				require_once("conexiuneBD.php");
				
				//Aici arat formul in care se pot introduce alte categorii
				
				
				
				//Aici arat formul in care se pot introduce alte subforumuri
				if(isset($_REQUEST['actiune']) && !empty($_REQUEST['actiune'])){
					//folosesc la input type="hidden" pentru ca nu pot scrie direct in action queriul asa
					// pot pune ce parametru vreau eu	
					if($_REQUEST['actiune']==="showAddSubforum"){
						echo'<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
						
						<input type="hidden" name="actiune" value="addSubforum">
						<table>
						<tr>
						<td>Nume Subformul</td>
						<td><input type="text" name="nume" maxlength="500" required></input></td>
						</tr>
						<tr>
						<td>Descriere</td>
						<td><input type="text" name="descriere" maxlength="500" required></input></td>
						</tr>						
						</table>
						<button type="submit">Adauga Subforum</button>
						</form>';
					}
					//Aici bag datele in baza de date sau zic de ce nu s-au putut baga
					if($_REQUEST['actiune']==="addSubforum"){
						if(isset($_REQUEST['nume']) && !empty($_REQUEST['nume'])){
							if(isset($_REQUEST['descriere']) && !empty($_REQUEST['descriere'])){
								$sql="INSERT INTO subforum(nume,descriere) VALUES('".$_REQUEST['nume']."','".$_REQUEST['descriere']."')";
								$rezultat=$conn->query($sql);
								if(rezultat==true){
									
									echo"Inserare cu succes<br/>";
									echo'<a href="forum.php">Inapoi</a>';
								}else
								echo"Inserarea nu a fost efectuata" .$conn->errno ."  " .$conn->error;
								
							}
							else
							echo "Descrierea subforumului nu este introdusa";
							
							
						}else
						echo "Numele subforumului nu este introdus";
						
						
					}
					//Aici arat formul in care se pot introduce categorii in subforumul respectiv daca actiunea e sa arat acel subforum
					//si  daca avem parametru ce ne zice idul subforumul al queriului care nu este gol
					if($_REQUEST['actiune']==="showAddCategorie" && isset($_REQUEST['subForum']) && !empty($_REQUEST['subForum'])){
						echo'<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
						<table>
						<input type="hidden" name="actiune" value="addCategorie">
						<input type="hidden" name="subForum" value="'.$_REQUEST['subForum'].'">
						<tr>
						<td>Nume Categorie</td>
						<td><input type="text" name="nume" maxlength="500"  required></input></td>
						</tr>
						<tr>
						<td>Descriere</td>
						<td><input type="text" name="descriere" maxlength="500"  required></input></td>
						</tr>
						</table>
						<button type="submit">Adauga Categorie</button>
						</form>';
					}
					//Aici introduc datele categoriei primite de la formular in subforumul respectiv daca actiunea e sa adaug datele
					//si  daca avem parametru ce ne zice idul subforumul al queriului care nu este gol
					if($_REQUEST['actiune']==="addCategorie" && isset($_REQUEST['subForum']) && !empty($_REQUEST['subForum'])){
						if(isset($_REQUEST['nume']) && !empty($_REQUEST['nume'])){
							if(isset($_REQUEST['descriere']) && !empty($_REQUEST['descriere'])){
								$sql="INSERT INTO categorie(sub_forum,nume,descriere) VALUES(".$_REQUEST['subForum'].",'".$_REQUEST['nume']."','".$_REQUEST['descriere']."')";
								$rezultat=$conn->query($sql);
								if(rezultat==true){
									
									echo"Inserare cu succes<br/>";
									echo'<a href="forum.php">Inapoi</a>';
								}else
								echo"Inserarea nu a fost efectuata" .$conn->errno ."  " .$conn->error;
								
							}
							else
							echo "Descrierea subforumului nu este introdusa";
							
							
						}else
						echo "Numele subforumului nu este introdus";
					}
					if($_REQUEST['actiune']==="deleteSubforum" && isset($_REQUEST['subForum']) &&!empty($_REQUEST['subForum'])){
						//verific inca odata daca e admin sa fiu sigur ca nu a ajuns aici fara autorizatie
						if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
							$sql="SELECT tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
							$rezultat=$conn->query($sql);
							if (!$rezultat) 
							echo "eroare baza de date". $conn->errno." ".$conn->error ;
							else{
								while($row3=$rezultat->fetch_assoc()){
									
									if($row3["tip_utilizator"]=='admin'){
										$sql="DELETE FROM subforum WHERE id=".$_REQUEST['subForum'];
										$rezultat=$conn->query($sql);
										if(!$rezultat){
											echo"Stergerea nu a fost efectuata" .$conn->errno ."  " .$conn->error;
										}
										else echo "<script>window.location ='forum.php';</script>";
										
									} else echo'Nu ai privilegii de admin';
								}
							}
						}
					}
					if($_REQUEST['actiune']==="deleteCategorie" && isset($_REQUEST['categorie']) &&!empty($_REQUEST['categorie'])){
						//verific inca odata daca e admin sa fiu sigur ca nu a ajuns aici fara autorizatie
						if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
							$sql="SELECT tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
							$rezultat=$conn->query($sql);
							if (!$rezultat) 
							echo "eroare baza de date". $conn->errno." ".$conn->error ;
							else{
								while($row3=$rezultat->fetch_assoc()){
									
									if($row3["tip_utilizator"]=='admin'){
										$sql="DELETE FROM categorie WHERE id=".$_REQUEST['categorie'];
										$rezultat=$conn->query($sql);
										if(!$rezultat){
											echo"Stergerea nu a fost efectuata" .$conn->errno ."  " .$conn->error;
										}
										else echo "<script>window.location ='forum.php';</script>";
										
									} else echo'Nu ai privilegii de admin';
								}
							}
						}
					}
				}
				// aici intru in mod default cand nu am nici un query si afisez toate subforumurile cu categoriile lor
				if(!isset($_REQUEST['actiune'])){
					//afisez butonul de adaugat subforumuri doar pentru admini
					if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
						$sql="SELECT tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
						$rezultat=$conn->query($sql);
						if (!$rezultat) 
						echo "eroare baza de date". $conn->errno." ".$conn->error ;
						else{
							while($row=$rezultat->fetch_assoc()){
								if($row["tip_utilizator"]=='admin'){
									
									//butonul de adaugat subForumuri
									//pathul $_SERVER['PHP_SELF'] imi zice ca e acelasi fisier
									echo'<form method="GET" action="'.$_SERVER['PHP_SELF'].'">
									<input type="hidden" name="actiune" value="showAddSubforum">
									<button type="submit">Adauga Subforum</button>
									</form>';
								}
							}
							
							
						}
					}
					
					$sql="SELECT id,nume FROM subforum";
					$subforumuri = $conn->query($sql);
					if(!$subforumuri) echo "eroare baza de date". $conn->errno." ".$conn->error ;
					while($row = $subforumuri->fetch_assoc()) {
						
						
						echo '<table class="tabelPrincipal">';
						
						$sql="SELECT id,nume,descriere FROM categorie WHERE sub_forum=".$row["id"].";";
						$categorii=$conn->query($sql);
						if (!$categorii) 
						echo "eroare baza de date". $conn->errno." ".$conn->error ;
						//daca am eroare cu queriul numai intru sa iau rezultatele ci doar afisez un mesaj de eroare
						else{
							echo "
							<caption>".$row["nume"]."</caption>";
							
							while($row2=$categorii->fetch_assoc()){
								echo "<tr><td><a href=\"categorie.php?categorie=".$row2["id"] ." \"  >".$row2["nume"]."</a></td>";
								echo "<td>".$row2["descriere"]."</td>";
								//afisez butonul de sters categorii doar pentru admini
								if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
									$sql="SELECT tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
									$rezultat=$conn->query($sql);
									if (!$rezultat) 
									echo "eroare baza de date". $conn->errno." ".$conn->error ;
									else{
										while($row4=$rezultat->fetch_assoc()){
											
											if($row4["tip_utilizator"]=='admin'){
												
												//butonul de sters categorii
												echo '<td>
												<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
												<input type="hidden" name="actiune" value="deleteCategorie">	
												<input type="hidden" name="categorie" value="'.$row2["id"].'">	
												<button type="submit" >Sterge Categorie</button>
												</form></td>';
												
											}
										}
									}
								}
								//sa fiu sigur ca termin randul chiar daca utilizatorul nu este admin
								echo '</tr>';
							}
							
						}
						echo "</table> ";
						
						//afisez butonul de adaugat categorii doar pentru admini
						if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
							$sql="SELECT tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
							$rezultat=$conn->query($sql);
							if (!$rezultat) 
							echo "eroare baza de date". $conn->errno." ".$conn->error ;
							else{
								while($row3=$rezultat->fetch_assoc()){
									
									if($row3["tip_utilizator"]=='admin'){
										
										//am 2 hidden input unul sa imi zica ca vreau sa adaug o categorie altul sa imi zica in ce subforum sa adaug acea
										//categorie.$row["id"] este idul subforumului
										
										echo '
										<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
										<input type="hidden" name="actiune" value="showAddCategorie">	
										<input type="hidden" name="subForum" value="'.$row["id"].'">	
										<button type="submit" >Adauga Categorie</button>
										</form>';
										echo '<br/>';
										echo '
										<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
										<input type="hidden" name="actiune" value="deleteSubforum">	
										<input type="hidden" name="subForum" value="'.$row["id"].'">	
										<button type="submit" >Sterge SubForum</button>
										</form>';
									}
								}
								
								
							}
						}
						
						
					}
					
				}
			?>
			
		</div>
	</body>
</html>																		