<?php
	$website=file_get_contents('http://jokes.cc.com/');
	$temp1=explode('<div class="fulltext">',$website);
	$temp2=$temp1[1];
	$temp2='<div class="fulltext">'.$temp2;
	$sfarsit=strpos($temp2, "</div>");
	$temp3=substr($temp2,0,$sfarsit);
	$temp3=$temp3.'</div>';
	echo '<div class="gluma_zilei">';
	echo '<h2>Gluma zilei</h2>';
	echo $temp3;
	echo '</div>';
	?>