<?php

/**
  * @ CLM Extern Component
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
*/

defined('_JEXEC') or die('Restricted access');

$ext_view	= JRequest::getVar('ext_view');
$saison		= JRequest::getInt('saison');
$liga		= JRequest::getInt('liga');
$runde		= JRequest::getInt('runde');
$dg		= JRequest::getInt('dg');
$tlnr		= JRequest::getInt('tlnr');
$zps		= JRequest::getVar('zps');
$mglnr		= JRequest::getInt('mglnr');
$url		= ereg_replace ( '\'', '', JRequest::getVar('url'));
$keyword	= JRequest::getVar('keyword');
$mcolor		= JRequest::getVar('mcolor');

// delete backslashs if exist
if (substr($url,0,1) == chr(92) ) { $url = substr($url,1,strlen($url)-1); }
if (substr($url,strlen($url)-1,1) == chr(92) ) { $url = substr($url,0,strlen($url)-1); }

include_once('idna_convert.class.php');
// Instantiate it (depending on the version you are using) with
$IDN = new idna_convert();
// The work string
$url1 = $url;
// Encode it to its punycode presentation
$url = $IDN->encode($url1);

$ext_url 	= "http://".$url;
$ext_url1 	= "http://".$url1;

if($ext_view=="" OR $saison=="" ) { ?>
<h1>Die Anzeigeparameter sind falsch gesetzt !</h1><h2>Kontaktieren Sie umgehend den Administrator.</h2>
<?php } else {

	$document	= &JFactory::getDocument();
	$cssDir		= JURI::base().'components'.DS.'com_clm_ext'.DS;
	$document->addStyleSheet( $cssDir.DS.'clm_content.css', 'text/css', null, array() );

/////////////////////
// Aufrufen mit z.B.:
// http://localhost/install/index.php?option=com_clm_ext&view=clm_ext&ext_view=rangliste&saison=1&liga=1
/////////////////////

// Views der Hauptauswahl des CLM Moduls

	$part		= explode("/", $url);
	$count 		= count($part);

	for($x=1; $x < $count; $x++) {
		if ($part[$x] !="" ){
			$url_org .= DS.$part[$x];	
		}
	}
	
if ($ext_view =="rangliste" OR $ext_view =="paarungsliste" OR $ext_view =="dwz_liga" OR $ext_view =="statistik"){
	$link 		= $ext_url.DS.'index.php?option=com_clm&view='.$ext_view.'&format=raw&html=0&saison='.$saison.'&liga='.$liga;
	}

if ($ext_view =="runde") {
	$link = $ext_url.DS.'index.php?option=com_clm&view='.$ext_view.'&format=raw&html=0&saison='.$saison.'&liga='.$liga.'&runde='.$runde.'&dg='.$dg;
	$path = "option=com_clm_ext&amp;view=clm_ext&amp;url='$url'&amp;ext_view=";
	}
	
// sekundäre Views durch Links	
if ($ext_view =="mannschaft") {
	$link = $ext_url.DS.'index.php?option=com_clm&view='.$ext_view.'&format=raw&html=0&saison='.$saison.'&liga='.$liga.'&tlnr='.$tlnr;
	$path = "option=com_clm_ext&amp;view=clm_ext&amp;url='$url'&amp;ext_view=";
	}
if ($ext_view =="verein") {
	$link = $ext_url.DS.'index.php?option=com_clm&view='.$ext_view.'&format=raw&html=0&saison='.$saison.'&zps='.$zps;
	$path = "option=com_clm_ext&amp;view=clm_ext&amp;url='$url'&amp;ext_view=";
	}
if ($ext_view =="dwz") {
	$link = $ext_url.DS.'index.php?option=com_clm&view='.$ext_view.'&format=raw&html=0&saison='.$saison.'&zps='.$zps;
	}
if ($ext_view =="spieler") {
	$link = $ext_url.DS.'index.php?option=com_clm&view='.$ext_view.'&format=raw&html=0&saison='.$saison.'&zps='.$zps.'&mglnr='.$mglnr;
	$path = "option=com_clm_ext&amp;view=clm_ext&amp;url='$url'&amp;ext_view=";
	}
if ($ext_view =="vereinsliste") {
	$link = $ext_url.DS.'index.php?option=com_clm&view='.$ext_view.'&format=raw&html=0&saison='.$saison.
	$path = "option=com_clm_ext&amp;view=clm_ext&amp;url='$url'&amp;ext_view=";
	}
if ($ext_view =="termine") {
	$link = $ext_url.DS.'index.php?option=com_clm&view='.$ext_view.'&format=raw&html=0&saison='.$saison.
	$path = "option=com_clm_ext&amp;view=clm_ext&amp;url='$url'&amp;ext_view=";
	}
	
	$data		= file_get_contents ($link);

	// Umsetzen &amp; --> &   zur Vereinfachung
	$url_org0 = '#&amp;#';
	$url_trans	= '&';
	$data		= preg_replace ( $url_org0, $url_trans, $data, -1, $anz );

	// URL anfügen !! WICHTIG !!!
	$url_org1 = 'href="'.$url_org.DS.'index.php';
	$url_trans	= 'href="'.JURI::base().'index.php';
	$data		= preg_replace ( '#'.$url_org1.'#', $url_trans, $data, -1, $anz );
	if ($anz == 0) {
	$url_org1 = $url_org.DS.'component/clm/';
	$url_trans	= JURI::base().'component/clm/';
	$data		= preg_replace ( '#'.$url_org1.'#', $url_trans, $data, -1, $anz );
    }
 
    // Auswahlfelder einbeziehen
	$url_org1 = 'value="'.$ext_url.DS.'index.php';
	$url_trans	= 'value="'.JURI::base().'index.php';
	$data		= preg_replace ( '#'.$url_org1.'#', $url_trans, $data, -1, $anz );
	
if ($ext_view =="rangliste" OR $ext_view =="dwz" OR $ext_view =="runde" OR $ext_view =="paarungsliste") {
	// PDF Adresse ersetzen - Suchmaschinenfreundliche URLs auf gerufener Seite: Nein
	$pdf_org	= JURI::base()."index.php\?option=com_clm&view=$ext_view&format=clm_pdf";
	if ($part[$count-1] !="" ){
		$pdf_trans = "http://".$url.DS."index.php?option=com_clm&view=$ext_view&format=clm_pdf";
	} else {
		$pdf_trans = "http://".$url."index.php?option=com_clm&view=$ext_view&format=clm_pdf";
	}
	$data		= ereg_replace ( $pdf_org, $pdf_trans, $data );
	// PDF Adresse ersetzen - Suchmaschinenfreundliche URLs auf gerufener Seite: Ja
	$pdf_org	= '#'.JURI::base()."index.php/component/clm/\?view=$ext_view&format=clm_pdf#";
	if ($part[$count-1] !="" ){
		$pdf_trans = "http://".$url.DS."index.php?option=com_clm&view=$ext_view&format=clm_pdf";
	} else {
		$pdf_trans = "http://".$url."index.php?option=com_clm&view=$ext_view&format=clm_pdf";
	}
	$data		= preg_replace ( $pdf_org, $pdf_trans, $data );
	}
 
	// Alle anderen ersetzen - Suchmaschinenfreundliche URLs auf gerufener Seite: Nein
	$url_org	= JURI::base().'index.php\?option=com_clm&view=';
	$url_trans	= JURI::base()."index.php?option=com_clm_ext&view=clm_ext&url='$url'&ext_view=";
	$data		= preg_replace ( '#'.$url_org.'#', $url_trans, $data, -1, $anz );
	// Alle anderen ersetzen - Suchmaschinenfreundliche URLs auf gerufener Seite: Nein
	$url_org	= JURI::base().'index.php?option=com_clm&view=';
	$url_trans	= JURI::base()."index.php?option=com_clm_ext&view=clm_ext&url='$url'&ext_view=";
	$data		= preg_replace ( '#'.$url_org.'#', $url_trans, $data, -1, $anz );
	// Alle anderen ersetzen - Suchmaschinenfreundliche URLs auf gerufener Seite: Ja und mod_rewrite Nein  (Landesseite)
	$url_org	= '#'.JURI::base().'index.php/component/clm/\?view=#';
	$url_trans	= JURI::base()."index.php?option=com_clm_ext&view=clm_ext&url='$url'&ext_view=";
	$data		= preg_replace ( $url_org, $url_trans, $data, -1, $anz );
	// Alle anderen ersetzen - Suchmaschinenfreundliche URLs auf gerufener Seite: Ja und mod_rewrite Ja     (Dessau)
	$url_org	= '#'.JURI::base().'component/clm/\?view=#';
	$url_trans	= JURI::base()."index.php?option=com_clm_ext&view=clm_ext&url='$url'&ext_view=";
	$data		= preg_replace ( $url_org, $url_trans, $data, -1, $anz );
 
	// Bilderpfad ändern
	$url_org	= $ext_url.DS.'components'.DS.'com_clm'.DS.'images';
	$url_trans	= JURI::base().'components'.DS.'com_clm_ext'.DS.'images';
	$data		= ereg_replace ( $url_org, $url_trans, $data );

	// Hervorheben von ausgewählten Verein
	if ($keyword != '') {
	    $iwhile = 0; 
	    $pstart = 0;
		$span11  = '<span style="background-color:';
		$span12  = ' ! important;">';
		$span2   = '</span>';
	    while ($iwhile < 20 AND $pstart < strlen($data)) {
			$iwhile++;
		    $treffer = strpos ( $data, $keyword , $pstart );
			if ($treffer === false) break;
			$imax = 30; $i1 = 1; $i2 = 1;
			while ($i1 < $imax AND substr($data,$treffer-$i1,1) != '>') { //echo "<br>Rückwärts: ".substr($data,$treffer-$i1,1); 
				$i1++; }
			if ($i1 < $imax) $sstart = $treffer-$i1+1; else $sstart = false;
			while ($i2 < $imax AND substr($data,$treffer+$i2,1) != '<') { //echo "<br>Vorwärts: ".substr($data,$treffer+$i2,1); 
				$i2++; }
			if ($i2 < $imax) $sende = $treffer+$i2; else $sende = false;
			if ($sstart === false OR $sende === false) {
				$pstart = $treffer + strlen($keyword);
			} else {
				$data = substr($data,0,$sstart).$span11.$mcolor.$span12.substr($data,$sstart,$sende-$sstart).$span2.substr($data,$sende,strlen($data)-$sende);
				$pstart = $sende + strlen($span11) + strlen($span12) + strlen($mcolor) + strlen($span2) ;
			}
		}
	}
// Daten anzeigen
echo $data;

?>
<br>

<hr>
Die hier dargestellten Ligen werden extern angezeigt und befinden sich im Orginal auf <a href="<?php echo $ext_url;?>"><?php echo $ext_url1;?></a>
<?php } ?>