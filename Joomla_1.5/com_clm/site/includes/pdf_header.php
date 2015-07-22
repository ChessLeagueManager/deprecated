<?php

	// Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$email_fromname=$config->get('email_fromname');

 	//Logo
    $this->Image(JPATH_COMPONENT.DS.'images'.DS.'clm_logo.png',15,8,16);
    //Arial fett 15
    $this->SetFont('Arial','B',18);
    //nach rechts gehen
    $this->Cell(60);
    //Titel
    $this->Cell(80,10,''.utf8_decode($email_fromname).'',0,0,'C');
    //Linie
    $this->Line(15, 22, 195, 22);
    //Zeilenumbruch
    $this->Ln(15);
?>