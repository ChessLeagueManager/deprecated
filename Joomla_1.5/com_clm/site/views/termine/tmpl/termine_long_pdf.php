<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

$sid			= JRequest::getInt('saison','1');
$config			= &JComponentHelper::getParams( 'com_clm' );

$termine		= $this->termine;
$plan			= $this->plan;
 
require_once(JPATH_COMPONENT.DS.'includes'.DS.'fpdf.php');

class PDF extends FPDF
{
//Kopfzeile
function Header()
{
	require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_header.php');
}
//Fusszeile
function Footer()
{
	require(JPATH_COMPONENT.DS.'includes'.DS.'pdf_footer.php');
}
}

// Zellenhöhe -> Standard 6
$zelle = 5;
// Zellenbreiten je Spalte
$breite = 0;
$br00 = 10;
$br01 = 35;
$br02 = 55;
$br03 = 85;
$br99 = 175;
// Überschrift Fontgröße Standard = 14
$head_font = 14;
// Fontgröße Standard = 10
$font = 10;
// Fontgröße Standard = 9
$date_font = 9;
$page_length = 375;

$arrMonth = array(
    "January" => "Januar",
    "February" => "Februar",
    "March" => "März",
    "April" => "April",
    "May" => "Mai",
    "June" => "Juni",
    "July" => "Juli",
    "August" => "August",
    "September" => "September",
    "October" => "Oktober",
    "November" => "November",
    "December" => "Dezember"
	);
$arrWochentag = array( 
	"Monday" => "Montag", 
	"Tuesday" => "Dienstag", 
	"Wednesday" => "Mittwoch", 
	"Thursday" => "Donnerstag", 
	"Friday" => "Freitag", 
	"Saturday" => "Samstag", 
	"Sunday" => "Sonntag", 
	);
            
// Datum der Erstellung
$date =& JFactory::getDate();
$now = $date->toMySQL();

$pdf=new PDF();
$pdf->AliasNbPages();

// START : Terminschleife
	$tt = 0;	
	$pp = 0;
	$date = date("Y-m-d");
	for ($t = 0 ; $t <= count ($termine); $t++) {
      if ( $date <= $termine[$t]->datum ) {
        // Datumsberechnungen
		$datum[$t] = strtotime($termine[$t]->datum);
		$datum_arr[$t] = explode("-",$termine[$t]->datum);
		$monatsausgabe = mktime (0 , 0 , 0 , $datum_arr[$t][1]+1, 0 ,0);
            
		$termin_length = $zelle;
		if ( $datum_arr[$t][0] > $datum_arr[$t-1][0] ) { 
			$termin_length += $zelle; }
		if (( $datum_arr[$t][1] > $datum_arr[$t-1][1]) OR ( $datum_arr[$t][0] > $datum_arr[$t-1][0]) ) {
			$termin_length += $zelle; }
		if ($termine[$t]->source != 'liga') {
				$content_termin = utf8_decode($termine[$t]->name);        
				$content_detail = utf8_decode($termine[$t]->typ); }       
		else {
			$content_termin = utf8_decode($termine[$t]->typ).", ".utf8_decode($termine[$t]->name);        
			while (($termine[$t]->datum > $plan[$pp]->datum) AND isset($plan[$pp]->datum)) {
				$pp++; }
			$content_detail = "";
			$szelle = 0;
			while (($termine[$t]->datum == $plan[$pp]->datum) AND isset($plan[$pp]->datum) AND ($termine[$t]->typ_id == $plan[$pp]->lid)) {
				if ($content_detail !== "") {
					$termin_length += $zelle; 
					$content_detail .= "\n"; }
				$content_detail .= utf8_decode($plan[$pp]->hname)." - ".utf8_decode($plan[$pp]->gname); 
				$pp++; } 
		}
		$termin_length = $termin_length * 5; 
		//echo '<br>Termin: '.$termine[$t]->typ.' GetY: '.$pdf->GetY().'  Page: '.$page_length.' TerminL: '.$termin_length;
		if (($pdf->GetY() > ($page_length - $termin_length)) or ($tt == 0)) {
			$tt = 0;
			$pdf->AddPage();
			$pdf->SetFont('Times','',$date_font);
			$pdf->Cell(10,3,' ',0,0);
			$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('%d. %B %Y ,  %H %M'))),0,1,'R');
	
			$pdf->SetFont('Times','',$head_font);
			$pdf->Cell(10,10,' ',0,0);
			$pdf->Cell(150,10,utf8_decode(JText::_('TERMINE_HEAD')),0,1,'L');
		}
		$pdf->SetFont('Times','',$font);
		$pdf->SetTextColor(0);
		$pdf->SetFillColor(255); 
			// Monatsberechnungen
			if (( $datum_arr[$t][1] > $datum_arr[$t-1][1]) OR ( $datum_arr[$t][0] > $datum_arr[$t-1][0]) ) {
				// Jahresberechnungen
				if ( $datum_arr[$t][0] > $datum_arr[$t-1][0] ) { 
					$tt++;
					$pdf->SetTextColor(255);
					$pdf->SetFillColor(90); 
					$pdf->Cell($br00,$zelle," ",0,0,'C');    
					$pdf->Cell($br99,$zelle,$datum_arr[$t][0],1,1,'C',1); }
				$tt++;
				$pdf->SetTextColor(0);
				$pdf->SetFillColor(240); 
				$pdf->Cell($br00,$zelle," ",0,0,'C');    
				$pdf->Cell($br99,$zelle,utf8_decode($arrMonth[date('F',$monatsausgabe)]),1,1,'L',1);    
			} 
			$tt++;
			$pdf->SetTextColor(0);
			$pdf->SetFillColor(255); 
			$pdf->Cell($br00,$zelle," ",0,0,'C');    
			//if (($datum[$t] == $datum[$t-1]) AND ($tt > 1)) { $pdf->Cell($br01,$zelle," ",1,0,'C',1); }
			//else 
			$pdf->Cell($br01,$zelle,utf8_decode($arrWochentag[date("l",$datum[$t])]). ", " . $datum_arr[$t][2].".".$datum_arr[$t][1].".".$datum_arr[$t][0],1,0,'L',1); 
			$pdf->Cell($br02,$zelle,$content_termin,1,0,'L',1);        
			$pdf->Multicell($br03,$zelle,$content_detail,1,1,'L',1);        
			   
		}
		}
        // ENDE : Terminschleife 
        		
// Ausgabe
$pdf->Output(utf8_decode(JText::_('TERMINE_HEAD')).'.pdf','D');


?>