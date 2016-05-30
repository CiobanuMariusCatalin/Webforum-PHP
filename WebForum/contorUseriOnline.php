<?php
	require_once("conexiuneBD.php");
	$sql="INSERT INTO useri_online(ip,data_inserarii) VALUES('". $_SERVER['REMOTE_ADDR']."',now())";
	$raspuns = $conn->query($sql);
	if(!$raspuns) echo "eroare baza de date". $conn->errno." ".$conn->error ;
	else{
		$sql="SELECT COUNT(DISTINCT ip) nr FROM useri_online WHERE (data_inserarii + INTERVAL 5 MINUTE)> now()";
		$raspuns = $conn->query($sql);
		if(!$raspuns) echo "eroare baza de date". $conn->errno." ".$conn->error ;
		else{
			while($row = $raspuns->fetch_assoc()) {
				$numarVizitatori=$row['nr'];
			}
			echo 'Numarul de vizitatori '.$numarVizitatori."</br>";
		}
		
	}
?>