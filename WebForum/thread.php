<?php
	session_start();
	require_once("conexiuneBD.php");
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
				
				//daca exista un id de al threadului ma duc mai departe altfel nu am ce face
				if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
					if(isset($_REQUEST['actiune']) && !empty($_REQUEST['actiune'])){
						//aici intru cand vreau sa arat forma ce ma lasa sa adaug replyuri
						if($_REQUEST['actiune']==="showAddReply"){
							echo'<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
							<table>
							<input type="hidden" name="actiune" value="addReply">
							<input type="hidden" name="id" value="'.$_REQUEST['id'].'">
							<tr>
							<td>reply</td>
							<td><textarea rows="6" cols="50" name="reply" required></textarea></td>
							</tr>
							</table>
							<button type="submit">Adauga Coment</button>
							</form>';
						}
						
						
						
						//creez fisierul excel si il trimit userului.Doar admini au acces la acesta
						if($_REQUEST['actiune']==="getExcel"){
							if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
								$sql="SELECT nume,tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
								$rezultat=$conn->query($sql);
								if (!$rezultat) 
								
								echo "eroare baza de date". $conn->errno." ".$conn->error ;
								else{
									while($row3=$rezultat->fetch_assoc()){
										//doar daca e admin il las sa ia excelul
										if($row3["tip_utilizator"]=='admin'){
											
											
											/** PHPExcel */
											include 'PHPExcelLibrarie/PHPExcel.php';
											
											/** PHPExcel_Writer_Excel2007 */
											include 'PHPExcelLibrarie/PHPExcel/Writer/Excel2007.php';
											
											// Create new PHPExcel object
											
											$objPHPExcel = new PHPExcel();
											
											
											$objPHPExcel->getProperties()->setCreator($row3['nume']);
											$objPHPExcel->getProperties()->setLastModifiedBy($row3['nume']);
											$objPHPExcel->getProperties()->setTitle("Comenturi");
											$objPHPExcel->getProperties()->setSubject("Comenturile threadului");
											$objPHPExcel->getProperties()->setDescription("Comenturile threadului cerute ca excel");
											
											
											$objPHPExcel->setActiveSheetIndex(0);
											$pozitiaInExcel=1;
											//iau prima data primul coment ce se afla in tabelul thread dupa iau replyurile
											$sql="SELECT autor,mesaj FROM thread WHERE id=".$_REQUEST['id'];
											$coment_initial = $conn->query($sql);
											if(!$coment_initial) echo "eroare baza de date". $conn->errno." ".$conn->error ;
											else{
												while($row = $coment_initial->fetch_assoc()) {
													//pun autorul si mesajul pe acelasi rand dupa incrementez randul
													$objPHPExcel->getActiveSheet()->SetCellValue('A'.$pozitiaInExcel.'', $row["autor"]);
													$objPHPExcel->getActiveSheet()->SetCellValue('B'.$pozitiaInExcel++.'', $row["mesaj"]);
												}
												//iau replyurile
												$sql="SELECT autor,mesaj FROM replyuri WHERE thread_id=".$_REQUEST['id']." ORDER BY data_creeri ASC";
												$replyuri = $conn->query($sql);
												
												if(!$replyuri) echo "eroare baza de date". $conn->errno." ".$conn->error ;
												//daca am eroare cu queriul numai intru sa iau rezultatele ci doar afisez un mesaj de eroare
												else{
													
													//scriu replyurile in excel
													while($row2 = $replyuri->fetch_assoc()) {
														$objPHPExcel->getActiveSheet()->SetCellValue('A'.$pozitiaInExcel.'', $row2["autor"]);
														$objPHPExcel->getActiveSheet()->SetCellValue('B'.$pozitiaInExcel++.'', $row2["mesaj"]);
													}
												}
												
												
											}
											
											
											// Save Excel 2007 file
											
											$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
											$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
											
											//Creez buton sa ma duca inapoi cu un pas
											echo'<a href="'.$_SERVER['HTTP_REFERER'].'">Inapoi</a>';
											//trimis fisierul spre downloadare
											echo "<script>window.location = 'thread.xlsx';</script>";
											
											
											
											
										}else echo"utilizatorul nu este admin";
									}
								}
							} else echo"utilizatorul nu este logat";
							
							
							
							
							
						}
						//aici intru cand formularul unde scriu replyul ma trimite sa adaug in baza de date informatiile primite del a user 
						if($_REQUEST['actiune']==="addReply"){
							//daca toate datele sunt in regula trec mai depare
							if( isset($_REQUEST['reply'])&& !empty($_REQUEST['reply'])){
								
								$sql="INSERT INTO replyuri(thread_id,autor,mesaj,data_creeri) VALUES(".$_REQUEST['id'].",'".$_SESSION["username"]."','".$_REQUEST['reply']."',now())";
								$rezultat=$conn->query($sql);
								if($rezultat){
									
									echo"Inserare cu succes<br/>";
									echo'<a href="thread.php?id='.$_REQUEST['id'].'&page=1">Inapoi</a>';
								}else
								echo"Inserarea nu a fost efectuata" .$conn->errno ."  " .$conn->error;
							}else echo"Nu sunt toate datele din query in regula";
						}
					}
					
					
					// aici intru in mod default cand nu am nici un query si afisez toate comenturile threadului
					if(!isset($_REQUEST['actiune'])){
						if(isset($_REQUEST['page'])&& !empty($_REQUEST['page'])){
							
							//selectez numele categoriei in care se afla threadul pentru o a afisa
							$sql2='SELECT id,nume FROM categorie WHERE id=(SELECT categorie_id FROM thread WHERE id='.$_REQUEST['id'].')';
							$rezultate=$conn->query($sql2);
							
							if(!$rezultate) echo "eroare baza de date". $conn->errno." ".$conn->error ;
							
							$nume_categorie;
							$id_categorie;
							
							while($rowul = $rezultate->fetch_assoc() ) {
								$nume_categorie=$rowul ['nume'];
								$id_categorie=$rowul ['id'];
							}
							
							
							$sql="SELECT nume,autor,mesaj FROM thread WHERE id=".$_REQUEST['id'];
							$coment_initial = $conn->query($sql);
							if(!$coment_initial) echo "eroare baza de date". $conn->errno." ".$conn->error ;
							else{
								
								while($row = $coment_initial->fetch_assoc()) {
									echo ' <a href="categorie.php?categorie='.$id_categorie.'">'.$nume_categorie.'</a> >>'.$row['nume'];
									echo'<div class="threadComentContainer">';
									echo'<div class="threadNumeAutorContainer">';
									echo'<div class="threadNumeAutor">';
									echo $row['autor'];
									echo "</div>";
									echo "</div>";
									echo'<div class="threadMesajContainer">';
									echo'<div class="threadMesaj">';
									echo $row['mesaj'];
									echo "</div>";
									echo'</div>';
									echo "</div>";
								}
								
								$nrComenturi=0;
								$sql="SELECT COUNT(*) nr FROM replyuri WHERE thread_id=".$_REQUEST['id'];
								$nrComenturiArray=$conn->query($sql);
								if(!$nrComenturiArray) {echo "eroare baza de date". $conn->errno." ".$conn->error ;
									die();
								}
								else{
									while($rowReply = $nrComenturiArray->fetch_assoc()) {
										$nrComenturi=$rowReply['nr'];
										
									}
								$nrComenturi++;
								}
								
								
								
								
								
								
								$sql="SELECT autor,mesaj FROM replyuri WHERE thread_id=".$_REQUEST['id']." ORDER BY data_creeri ASC";
								$replyuri = $conn->query($sql);
								
								if(!$replyuri) echo "eroare baza de date". $conn->errno." ".$conn->error ;
								//daca am eroare cu queriul numai intru sa iau rezultatele ci doar afisez un mesaj de eroare
								else{
									$contor=2;
									
									while($row2 = $replyuri->fetch_assoc() ) {
									//sa fie intre limitele paginii de exemplu pentru pagina 1
									//sa fie intre 1 si 10 
									
										if(($_REQUEST['page']-1)*10<$contor && $_REQUEST['page']*10>=$contor){
										echo'<div class="threadComentContainer">';
										
										echo'<div class="threadNumeAutorContainer">';
										echo'<div class="threadNumeAutor">';
										echo $row2['autor'];
										echo "</div>";
										echo "</div>";
										
										echo'<div class="threadMesajContainer">';
										echo'<div class="threadMesaj">';
										echo $row2['mesaj'];
										echo "</div>";
										echo'</div>';
										echo'</div>';
										}
										
										$contor++;
									}
									//afisez butoanele catre celelalte pagini
									for($i=0;$i<($nrComenturi/10);$i++){
									echo '<a class="nrPagina" href="thread.php?id='.$_REQUEST['id'].'&page='.($i+1).'">'.($i+1).'</a>';
									}
								}
								
							}
							
							//afisez butonul de adaugat comenturi doar pentru utilizatori inregistrati
							if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
								echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
								<input type="hidden" name="actiune" value="showAddReply">	
								<input type="hidden" name="id" value="'.$_REQUEST['id'].'">	
								<button type="hidden" >Adauga Comment</button></form>';
								
								
								
							}
							//daca userul e admin il las sa ia excel
							if(isset($_SESSION["username"]) && !empty($_SESSION["username"])){
								$sql="SELECT tip_utilizator FROM utilizatori WHERE nume='".$_SESSION["username"]."';";
								$rezultat=$conn->query($sql);
								if (!$rezultat) 
								echo "eroare baza de date". $conn->errno." ".$conn->error ;
								else{
									while($row3=$rezultat->fetch_assoc()){
										
										if($row3["tip_utilizator"]=='admin'){
											
											echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">
											<input type="hidden" name="actiune" value="getExcel">	
											<input type="hidden" name="id" value="'.$_REQUEST['id'].'">	
											<button type="hidden" >Get Excel</button></form>';
										}
									}
									
									
								}
							}
						}
					}
				}
			?>
			<div>
			</body>
		</html>																																											