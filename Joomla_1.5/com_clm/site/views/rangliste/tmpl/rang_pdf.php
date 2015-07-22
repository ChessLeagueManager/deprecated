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

//require('fpdf.php');

$lid = JRequest::getInt( 'liga', '1' ); 
$sid = JRequest::getInt( 'saison','1');
$view = JRequest::getVar( 'view');
// Variablen ohne foreach setzen
$liga=$this->liga;
$punkte=$this->punkte;
$spielfrei=$this->spielfrei;
$dwzschnitt=$this->dwzschnitt;
$saison     =$this->saison; 

$name_liga = $liga[0]->name;

require_once(JPATH_COMPONENT.DS.'includes'.DS.'fpdf.php');

class PDF extends FPDF
{
//Kopfzeile
function Header()
{
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'pdf_header.php');
}
//Fusszeile
function Footer()
{
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'pdf_footer.php');
}
}

// Array für DWZ Schnitt setzen
$dwz = array();
for ($y=1; $y< ($liga[0]->teil)+1; $y++){
	$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }

// Spielfreie Teilnehmer finden
$diff = $spielfrei[0]->count;

// Zellenhöhe -> Standard 6
$zelle = 6;
// Wert von Zellenbreite abziehen
// Bspl. für Standard (Null) für Liga mit 11 Runden und 1 Durchgang
$breite = 0;
$rbreite = 0;
$nbreite = 0;
if ((($liga[0]->teil - $diff) * $liga[0]->durchgang) > 11) { 
	$breite = 1;
	$rbreite = 1;
	$nbreite = 2; }
if ((($liga[0]->teil - $diff) * $liga[0]->durchgang) > 14) {
	$rbreite = 2;
	$nbreite = 10; }
// Überschrift Fontgröße Standard = 14
$head_font = 14;
// Fontgröße Standard = 9
$font = 9;
// Fontgröße Standard = 8
$date_font = 8;
// Leere Zelle zum zentrieren
$leer = (3 * (10-($liga[0]->teil-$diff)))-$rbreite;
if ( $liga[0]->b_wertung == 0) $leer = $leer + 4;
if ($leer < 3) $leer = 2;
 
// Datum der Erstellung
$date =& JFactory::getDate();
$now = $date->toMySQL();

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',$date_font);
	$pdf->Cell(10,3,' ',0,0);
	$pdf->Cell(175,2,utf8_decode(JText::_('WRITTEN')).' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $now, JText::_('%d. %B %Y ,  %H:%M'))),0,1,'R');

$pdf->SetFont('Times','B',$head_font+2);	
	$pdf->Cell(180,15,utf8_decode($liga[0]->name),0,1,'C');
	$pdf->Cell(180,10,utf8_decode($saison[0]->name),0,1,'C');
	$pdf->Ln(30);    	
$pdf->SetFont('Times','',$font+2);
	$pdf->Cell($leer,$zelle,' ',0,0,'L');
	$pdf->Cell(7-$rbreite,$zelle,JText::_('RANG'),1,0,'C');
	$pdf->Cell(7-$rbreite,$zelle,JText::_('TLN'),1,0,'C');
	$pdf->Cell(55-$nbreite-$breite,$zelle,JText::_('TEAM'),1,0,'L');
	
	if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) {    // vollrundig
// erster Durchgang
	for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) {
	$pdf->Cell(8-$breite,$zelle,$rnd+1,1,0,'C');
	}
// zweiter Durchgang
	if ($liga[0]->durchgang > 1) {
	for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) {
				$pdf->Cell(8-$breite,$zelle,$rnd+1,1,0,'C'); }
		}
	}
	if ($liga[0]->runden_modus == 3) { 				// Schweizer System
		for ($rnd=0; $rnd < $liga[0]->runden ; $rnd++) { 
			$pdf->Cell(13-$breite,$zelle,$rnd+1,1,0,'C'); }
	}
	
	$pdf->Cell(8-$rbreite,$zelle,JText::_('MP'),1,0,'C');
	if ( $liga[0]->liga_mt == 0) { 
		$pdf->Cell(10-$breite,$zelle,JText::_('BP'),1,0,'C'); }
	if ($liga[0]->b_wertung > 0) {
		$pdf->Cell(10-$breite,$zelle,JText::_('WP'),1,0,'C'); }
	if ( $liga[0]->tiebr1 > 0) { 
		$pdf->Cell(10-$breite,$zelle,JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1),1,0,'C'); }
	if ( $liga[0]->tiebr2 > 0) { 
		$pdf->Cell(10-$breite,$zelle,JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2),1,0,'C'); }
	if ( $liga[0]->tiebr3 > 0) { 
		$pdf->Cell(10-$breite,$zelle,JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3),1,0,'C'); }
	$pdf->Ln();

// Anzahl der Teilnehmer durchlaufen
for ($x=0; $x< ($liga[0]->teil)-$diff; $x++){
	$pdf->Cell($leer,$zelle,' ',0,0,'L');
	$pdf->Cell(7-$rbreite,$zelle,$x+1,1,0,'C');
	$pdf->Cell(7-$rbreite,$zelle,$punkte[$x]->tln_nr,1,0,'C');
	$pdf->Cell(45-$nbreite,$zelle,utf8_decode($punkte[$x]->name),1,0,'L');
	$pdf->Cell(10-$breite,$zelle,(int)$dwz[($punkte[$x]->tln_nr)],1,0,'C');

$runden = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,1,$liga[0]->runden_modus);

// Anzahl der Runden durchlaufen 1.Durchgang
if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) { 
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) {
			$pdf->Cell(8-$breite,$zelle,'X',1,0,'C'); }
		else {
			if ($punkte[$y]->tln_nr > $runden[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden[($punkte[$y]->tln_nr)-2]->brettpunkte,1,0,'C'); 
				}
			if ($punkte[$y]->tln_nr < $runden[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden[($punkte[$y]->tln_nr)-1]->brettpunkte,1,0,'C'); 
				}}}
}
if ($liga[0]->runden_modus == 3) { 
	for ($y=0; $y< $liga[0]->runden; $y++) { 
		if ($runden[$y]->name == "spielfrei") 
			$pdf->Cell(13-$breite,$zelle,"  +",1,0,'C'); 
		elseif (!isset($runden[$y])) 
			$pdf->Cell(13-$breite,$zelle,"",1,0,'C'); 
		else 
			$pdf->Cell(13-$breite,$zelle,$runden[$y]->brettpunkte." (".$runden[$y]->rankingpos.")",1,0,'C'); 
		}
	}
// Anzahl der Runden durchlaufen 2.Durchgang
	if ($liga[0]->durchgang > 1) {
			$runden_dg2 = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,2,$liga[0]->runden_modus);
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) {
			$pdf->Cell(8-$breite,$zelle,'X',1,0,'C'); }
		else {
			if ($punkte[$y]->tln_nr > $runden_dg2[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte,1,0,'C');
		}
			if ($punkte[$y]->tln_nr < $runden_dg2[0]->tln_nr) {
				$pdf->Cell(8-$breite,$zelle,$runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte,1,0,'C');
		}}}}
// Ende Runden
	$pdf->Cell(8-$rbreite,$zelle,$punkte[$x]->mp,1,0,'C');
	if ( $liga[0]->liga_mt == 0) {
		$pdf->Cell(10-$breite,$zelle,$punkte[$x]->bp,1,0,'C'); }
	if ($liga[0]->b_wertung > 0) {
		$pdf->Cell(10-$breite,$zelle,$punkte[$x]->wp,1,0,'C'); }
	if ( $liga[0]->tiebr1 > 0) {  
		$pdf->Cell(10-$breite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1),1,0,'C'); }
	if ( $liga[0]->tiebr2 > 0) {  
		$pdf->Cell(10-$breite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr2),1,0,'C'); }
	if ( $liga[0]->tiebr3 > 0) {  
		$pdf->Cell(10-$breite,$zelle,CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr3),1,0,'C'); }
	$pdf->Ln();
	}
$pdf->Ln();
$pdf->Ln();

if ($liga[0]->bemerkungen <> "") {
	$pdf->SetFont('Times','B',$font+2);
	$pdf->Cell(10,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,' '.utf8_decode(JText::_('NOTICE')).' :',0,1,'B');
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->MultiCell(150,$zelle,utf8_decode($liga[0]->bemerkungen),0,'L',0);
	$pdf->Ln();
	}

	$pdf->SetFont('Times','B',$font+2);
	$pdf->Cell(10,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,JText::_('CHIEF').' :',0,1,'L');
	$pdf->SetFont('Times','',$font);
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,utf8_decode($liga[0]->sl),0,1,'L');
	$pdf->Cell(15,$zelle,' ',0,0,'L');
	$pdf->Cell(150,$zelle,$liga[0]->email,0,1,'L');
$pdf->Ln();

// Ausgabe
$pdf->Output(JText::_('RANGLISTE').' '.utf8_decode($liga[0]->name).'.pdf','D');

?>