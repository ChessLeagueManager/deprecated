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

$turnierid		= JRequest::getInt('turnier','1');
$config			= &JComponentHelper::getParams( 'com_clm' );

$turParams = new JParameter($this->turnier->params);

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
$zelle = 6;
// Zellenbreiten je Spalte
$breite = 0;
$br00 = 10;
$br01 = 50;
$br02 = 15;
$br03 = 10;
$br04 = 50;
$br05 = 15;
$br06 = 20;
// Fontgröße Standard = 10
$font = 10;

// Datum der Erstellung
$date =& JFactory::getDate();
$now = $date->toMySQL();

$pdf=new PDF();
$pdf->AliasNbPages();

// Anzahl der Mannschaften durchlaufen
$p=0;
$p1=false;
foreach ($this->matches as $key => $value) {
	$p++; // rowCount
//Anzahl Paarungen pro Seite
	if ($p > 36) $p = 1;
	if ($p == 1) {
$pdf->AddPage();
$pdf->SetFont('Times','',7);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell(175,3,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('%d. %B %Y ,  %H %M'))),0,1,'R');
	
$pdf->SetFont('Times','',14);
	$pdf->Cell(10,15,' ',0,0);
	$heading = utf8_decode($this->turnier->name).": ".utf8_decode(JText::_('TOURNAMENT_ROUND'))." ".$this->round->nr;
	if ($this->round->datum != "0000-00-00" AND $turParams->get('displayRoundDate', 1) == 1) {
		$heading .=  ", ".utf8_decode(JHTML::_('date',  $this->round->datum, JText::_('%d. %B %Y'))); 
	}
	if ($this->turnier->dg > 1 AND $this->round->dg == 1) { 
		$heading .=  " (".utf8_decode(JText::_('TOURNAMENT_STAGE_1')).")";
	}
	if ($this->turnier->dg > 1 AND $this->round->dg == 2) { 
		$heading .=  " (".utf8_decode(JText::_('TOURNAMENT_STAGE_2')).")";
	}
	$pdf->Cell(150,15,$heading,0,1,'L');
		
$pdf->SetFont('Times','',$font);
$pdf->SetTextColor(255);
$pdf->SetFillColor(0);
	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br01,$zelle,utf8_decode(JText::_('TOURNAMENT_WHITE')),1,0,'C',1);
	$pdf->Cell($br02,$zelle,utf8_decode(JText::_('TOURNAMENT_TWZ')),1,0,'C',1); 
	$pdf->Cell($br03,$zelle,"-",1,0,'C',1);
	$pdf->Cell($br04,$zelle,utf8_decode(JText::_('TOURNAMENT_BLACK')),1,0,'L',1); 
	$pdf->Cell($br05,$zelle,utf8_decode(JText::_('TOURNAMENT_TWZ')),1,0,'L',1); 
	$pdf->Cell($br06,$zelle,JText::_('RESULT'),1,0,'C',1);
	$pdf->Cell(1,$zelle," ",0,1,'C');
}
// Anzahl der Teilnehmer durchlaufen
$pdf->SetFont('Times','',$font);
$pdf->SetTextColor(0);
	if ($p1 == false) {
		$p1 = true; 
		$pdf->SetFillColor(255); }
	else {
		$p1 = false; 
		$pdf->SetFillColor(240); }	
if ( ($value->spieler != 0 AND $value->gegner != 0) OR $value->ergebnis != NULL) {

	$pdf->Cell($br00,$zelle," ",0,0,'C');
	$pdf->Cell($br01,$zelle,utf8_decode($value->wname),1,0,'L',1);
	$pdf->Cell($br02,$zelle,$value->wtwz,1,0,'C',1); 
	$pdf->Cell($br03,$zelle,"-",1,0,'C',1);
	$pdf->Cell($br04,$zelle,utf8_decode($value->sname),1,0,'L',1); 
	$pdf->Cell($br05,$zelle,$value->stwz,1,0,'C',1); 
	if ($value->ergebnis != NULL) {
		if ($value->ergebnis == 2) { $ergebnis = chr(189).":".chr(189); 
			if ($this->turnier->typ == 3 AND ($value->tiebrS > 0 OR $value->tiebrG > 0)) {
				$ergebnis .= '  ('.$value->tiebrS.':'.$value->tiebrG.')'; 
			}
		}
		else { $ergebnis = CLMText::getResultString($value->ergebnis); }
	} else $ergebnis = " ";
	$pdf->Cell($br06,$zelle,$ergebnis,1,0,'C',1); 
	$pdf->Cell(1,$zelle," ",0,1,'C');
}	
}
// Ausgabe
$pdf->Output(utf8_decode(JText::_('TOURNAMENT_ROUND'))." ".$this->round->nr.' '.utf8_decode($this->turnier->name).'.pdf','D');


?>