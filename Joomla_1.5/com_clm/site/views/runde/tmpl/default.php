<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip', '.CLMTooltip', $params);

$lid		= JRequest::getInt('liga','1'); 
$sid		= JRequest::getInt('saison','1');
$runde		= JRequest::getInt( 'runde', '1' );
$dg			= JRequest::getInt('dg','1');
$item		= JRequest::getInt('Itemid','1');
$liga		= $this->liga;
$einzel		= $this->einzel;
$pgn		= JRequest::getInt('pgn','0'); 

	// Userkennung holen
	$user	=& JFactory::getUser();
	$jid	= $user->get('id');
    // Check ob User Mitglied eines Vereins dieser Liga ist
	if ($jid != 0) {
		$clmuser = $this->clmuser;
		$club_jid = false;
		foreach ($einzel as $einz) {
			if ($einz->zps == $clmuser[0]->zps OR $einz->gzps == $clmuser[0]->zps) {
//			if ($einz->zps == 'G0222' OR $einz->gzps == 'G0222') {
				$club_jid = true; }
		}
	}
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $liga[0]->params);
	$liga[0]->params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$liga[0]->params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (!isset($liga[0]->params[pgntype])) $liga[0]->params['pgntype']= 0;
  if (($pgn == 1) OR ($pgn == 2)) { 
	$clmuser = $this->clmuser;
	$nl = "\n";
	$file_name = utf8_decode($liga[0]->name).'_'.utf8_decode($liga[$runde-1]->rname);
	if ($pgn == 1) $file_name .= '_'.utf8_decode($clmuser[0]->zps);
	$file_name .= '.pgn'; 
	$file_name = strtr($file_name,' ','_');
	$pdatei = fopen($file_name,"wt");
	foreach ($einzel as $einz) {
	  if (($einz->zps == $clmuser[0]->zps) OR ($einz->gzps == $clmuser[0]->zps) OR ($pgn == 2)) {
		switch ($liga[0]->params[pgntype]) {
		  case 1:
			fputs($pdatei, '[Event "'.utf8_decode($liga[0]->name).'"]'.$nl);
			break;
		  case 2:
			fputs($pdatei, '[Event "'.utf8_decode($liga[0]->params[pgnlname]).'"]'.$nl);
			break;
		  case 3:
			fputs($pdatei, '[Event "'.utf8_decode($einz->name).' - '.utf8_decode($einz->mgname).'"]'.$nl);
			break;
		  case 4:
			fputs($pdatei, '[Event "'.utf8_decode($einz->sname).' - '.utf8_decode($einz->smgname).'"]'.$nl);
			break;
		  case 5:
			fputs($pdatei, '[Event "'.utf8_decode($liga[0]->params[pgnlname]).': '.utf8_decode($einz->sname).' - '.utf8_decode($einz->smgname).'"]'.$nl);
			break;
		  default:
		 	fputs($pdatei, '[Event "'.'"]'.$nl);
		}
		fputs($pdatei, '[Site "?"]'.$nl);
		fputs($pdatei, '[Date "'.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('%Y.%m.%d')).'"]'.$nl);
		fputs($pdatei, '[Round "'.$runde.'.'.$einz->paar.'"]'.$nl);
		fputs($pdatei, '[Board "'.$einz->brett.'"]'.$nl);
		if ($einz->weiss == "0") {
			fputs($pdatei, '[White "'.utf8_decode($einz->gname).'"]'.$nl);
			fputs($pdatei, '[Black "'.utf8_decode($einz->hname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.utf8_decode($einz->mgname).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.utf8_decode($einz->name).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$einz->gelo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$einz->helo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$einz->gdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$einz->hdwz.'"]'.$nl);
		} else {
			fputs($pdatei, '[White "'.utf8_decode($einz->hname).'"]'.$nl);
			fputs($pdatei, '[Black "'.utf8_decode($einz->gname).'"]'.$nl);
			fputs($pdatei, '[WhiteTeam "'.utf8_decode($einz->name).'"]'.$nl);
			fputs($pdatei, '[BlackTeam "'.utf8_decode($einz->mgname).'"]'.$nl);
			fputs($pdatei, '[WhiteElo "'.$einz->helo.'"]'.$nl);
			fputs($pdatei, '[BlackElo "'.$einz->gelo.'"]'.$nl);
			fputs($pdatei, '[WhiteDWZ "'.$einz->hdwz.'"]'.$nl);
			fputs($pdatei, '[BlackDWZ "'.$einz->gdwz.'"]'.$nl);
		}
		if ($einz->erg_text == "0,5-0,5") fputs($pdatei, '[Result "1/2-1/2"]'.$nl);
		elseif ($einz->erg_text == "-/+") fputs($pdatei, '[Result "-:+"]'.$nl);
		elseif ($einz->erg_text == "+/-") fputs($pdatei, '[Result "+:-"]'.$nl);
		elseif ($einz->erg_text == "-/-") fputs($pdatei, '[Result "-:-"]'.$nl);
		else fputs($pdatei, '[Result "'.$einz->erg_text.'"]'.$nl);		
		fputs($pdatei, '[PlyCount "0"]'.$nl);
		fputs($pdatei, '[EventDate "'.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('%Y.%m.%d')).'"]'.$nl);
		fputs($pdatei, '[SourceDate "'.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('%Y.%m.%d')).'"]'.$nl);
		fputs($pdatei, ' '.$nl);

	  }
	}
	fclose($pdatei);
    header('Content-Disposition: attachment; filename='.$file_name);
		header('Content-type: text/html');
		header('Cache-Control:');
		header('Pragma:');
		readfile($file_name);
		flush();
		JFactory::getApplication()->close();
  }	

$runde_t = $runde + (($dg - 1) * $liga[0]->runden);  
// Test alte/neue Standardrundenname bei 2 Durchgängen
if ($liga[0]->durchgang > 1) {
	if ($liga[$runde_t-1]->rname == JText::_('ROUND').' '.$runde_t) {  //alt
		if ($dg == 1) { $liga[$runde_t-1]->rname = JText::_('ROUND').' '.$runde." (".JText::_('PAAR_HIN').")";}
		if ($dg == 2) { $liga[$runde_t-1]->rname = JText::_('ROUND').' '.$runde." (".JText::_('PAAR_RUECK').")";}
    }
}

// Browsertitelzeile setzen
$doc =& JFactory::getDocument();
$daten['title'] = $liga[0]->name.', '.JText::_('ROUND').' '.$runde; 
if(isset($liga[$runde-1]->datum)) { $daten['title'] = $daten['title'].' '.JText::_('ON_DAY').' '.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('%d. %B %Y'));}
$doc->setHeadData($daten);
	
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');

?>

<div id="clm">
<div id="runde">

<?php
$free_date_new = $this->free_date_new;	
$ok=$this->ok;
if ($free_date_new[0]->nr_aktion  == 201) $hint_freenew = JText::_('ROUND_FREE_COMMENT').' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $free_date_new[0]->datum, JText::_('%d. %B %Y'))); 
if ($free_date_new[0]->nr_aktion  == 202) $hint_freenew = JText::_('ROUND_FREE_COMMENT_DEL').' '.utf8_decode(JText::_('ON_DAY')).' '.utf8_decode(JHTML::_('date',  $free_date_new[0]->datum, JText::_('%d. %B %Y'))); 
if ((!isset($free_date_new[0]->nr_aktion)) AND (isset($ok[0]->sl_ok)) AND ($ok[0]->sl_ok > 0)) $hint_freenew = JText::_('CHIEF_OK');  
if ((!isset($free_date_new[0]->nr_aktion)) AND (isset($ok[0]->sl_ok)) AND ($ok[0]->sl_ok == 0)) $hint_freenew = JText::_('CHIEF_NOK');  
if ((!isset($free_date_new[0]->nr_aktion)) AND (!isset($ok[0]->sl_ok))) $hint_freenew = JText::_('CHIEF_NOK');  

$runden_modus = $liga[0]->runden_modus;
$runde_orig = $runde;
if ($dg >1) { $runde = $runde + $liga[0]->runden; }

if (isset($liga[$runde-1]->datum) AND $liga[$runde-1]->datum =='0000-00-00') {
?>

<div class="componentheading"><?php echo $liga[0]->name.', '.$liga[$runde-1]->rname;      // JText::_('ROUND').' '.$runde; 
?>
<?php } else { ?>
<div class="componentheading">
	<?php echo $liga[0]->name.', '.$liga[$runde-1]->rname;      // JText::_('ROUND').' '.$runde; 
	if(isset($liga[$runde-1]->datum)) { echo ' '.JText::_('ON_DAY').' '.JHTML::_('date',  $liga[$runde-1]->datum, JText::_('%d. %B %Y')); }
    ?>
    
    <?php } ?>
    
    <div id="pdf">
	<?php if (($liga[0]->params['pgntype'] > 0) AND ($jid != 0) AND ($club_jid == true)) { ?>
        <div class="pdf">
            <a href="index.php?option=com_clm&amp;view=runde&amp;liga=<?php echo $liga[0]->id ?>&amp;saison=<?php echo $liga[0]->sid; ?>&amp;runde=<?php echo $runde; ?>&amp;dg=<?php echo $dg; ?>&amp;pgn=1&amp;Itemid=<?php echo $item; ?>"><img src="<?php echo $imageDir; ?>pgn.gif" width="16" height="19" alt="PGN" border="" class="CLMTooltip" title="<?php echo JText::_('ROUND_PGN_CLUB') ?>"/></a>
        </div>
    <?php } ?>
	<?php if (($liga[0]->params['pgntype'] > 0) AND ($jid != 0)) { ?>
        <div class="pdf">
            <a href="index.php?option=com_clm&amp;view=runde&amp;liga=<?php echo $liga[0]->id ?>&amp;saison=<?php echo $liga[0]->sid; ?>&amp;runde=<?php echo $runde; ?>&amp;dg=<?php echo $dg; ?>&amp;pgn=2&amp;Itemid=<?php echo $item; ?>"><img src="<?php echo $imageDir; ?>pgn.gif" width="16" height="19" alt="PGN" border="" class="CLMTooltip" title="<?php echo JText::_('ROUND_PGN_ALL') ?>"/></a>
        </div>
    <?php } ?>
    <div class="pdf"><a href="index.php?option=com_clm&view=runde&format=clm_pdf&layout=runde&saison=<?php echo $liga[0]->sid ?>&liga=<?php echo $liga[0]->id ?>&runde=<?php echo $runde_orig ?>&dg=<?php echo $dg ?>"><img src="<?php echo $imageDir; ?>pdf_button.png" width="16" height="19" alt="PDF" class="CLMTooltip" title="<?php echo JText::_('ROUND_PDF') ?>"  /></a>
    </div>
	</div>
</div>
<div class="clr"></div>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php');

//if (
if ( !$liga OR $liga[0]->published == "0") { 
echo "<br>". CLMContent::clmWarning(JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD'))."<br>"; }
else if ($liga[0]->rnd == 0){ 
echo "<br>". CLMContent::clmWarning(JText::_('NO_ROUND_CREATED').'<br>'.JText::_('NO_ROUND_CREATED_HINT'))."<br>"; }
else if ($liga[$runde - 1]->pub == 0){ 
echo "<br>". CLMContent::clmWarning(JText::_('ROUND_UNPUBLISHED').'<br>'.JText::_('ROUND_UNPUBLISHED_HINT'))."<br>"; } 
else {   ?>

<?php // Kommentare zur Liga
if (isset($liga[$runde-1]->comment) AND $liga[$runde-1]->comment <> "") { ?>
<div id="desc">
    <p class="run_note_title"><?php echo JText::_('NOTICE') ?></p>
    <p><?php echo nl2br($liga[$runde-1]->comment); ?></p>
</div>
<?php } 

// Variablen ohne foreach setzen
$dwzschnitt	=$this->dwzschnitt;
$dwzgespielt=$this->dwzgespielt;
$paar		=$this->paar;
 
$summe		=$this->summe;
//$ok=$this->ok;

// Ergebnistext f�r flexibele Punktevergabe holen
$erg_text = CLMModelRunde::punkte_text($liga[0]->id);

// Array für DWZ Schnitt setzen
$dwz = array();
for ($y=1; $y< ($liga[0]->teil)+1; $y++){
	if(isset($dwzschnitt[($y-1)]->dwz)) {
	$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }
}
// Rundenschleife
?>
<br>

<table cellpadding="0" cellspacing="0" class="runde">
    <tr><td colspan="7">
    <div>
        <?php // Wenn SL_OK dann Haken anzeigen (nur wenn Staffelleiter eingegeben ist)
         if (isset($liga[0]->mf_name)) {
         if (isset($ok[0]->sl_ok) AND ($ok[0]->sl_ok > 0)) { ?>
            <div class="run_admit"><img  src="<?php echo $imageDir; ?>accept.png" class="CLMTooltip" title="<?php echo $hint_freenew; 	//echo JText::_('CHIEF_OK'); ?>" /></div>
            <?php } 
         else { ?>
            <div class="run_admit"><img  src="<?php echo $imageDir; ?>con_info.png" class="CLMTooltip" title="<?php echo $hint_freenew; 	//echo JText::_('CHIEF_OK'); ?>" /></div>
        <?php } } ?>
        <div class="run_titel">
            <a href="index.php?option=com_clm&amp;view=paarungsliste&amp;liga=<?php echo $liga[0]->id ?>&amp;saison=<?php echo $liga[0]->sid; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $liga[$runde-1]->rname; ?><img src="<?php echo $imageDir; ?>cancel_f2.png" title="<?php echo JText::_('ROUND_BACK') ?>"/></a>
        </div>
    </div>
    </td></tr>
<?php
//$z=(($liga[0]->teil)/2)*($runde-1);
// Teilnehmerschleife
$w=0;
$z2=0;
$zz=0;
for ($y=0; $y< ($liga[0]->teil)/2; $y++){

if (isset($paar[$y]->htln)) {  // Leere Begegnungen ausblenden ?>
    <tr>
        <th colspan="2" class="paarung2">
        <?php
        $edit=0;
        $medit=0;
        // Meldenden einfügen wenn Runde eingegeben wurde
        if (isset($einzel[$w]->paar) AND $einzel[$w]->paar == ($y+1)) { ?>
            <div class="run_admit"><label for="name" class="hasTip"><img  src="<?php echo $imageDir; ?>edit_f2.png"  class="CLMTooltip" title="<?php echo JText::_('REPORTED_BY').' '.$summe[$z2]->name; ?>" /></label>
            </div>
        <?php }
        if (isset($paar[$y]->hpublished) AND $paar[$y]->hpublished == 1) { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&tlnr=<?php echo $paar[$y]->htln; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $paar[$y]->hname; ?></a>
        <?php } else {
        if (isset($paar[$y]->hname)){
        echo $paar[$y]->hname;
        }} ?>
        </th>
        <th class="paarung">
        <?php if (isset($dwzgespielt[$zz]->dwz) AND $dwzgespielt[$zz]->paar == ($y+1) AND $paar[$y]->htln !=0 AND $paar[$y]->gtln != 0)
                { echo round($dwzgespielt[$zz]->dwz); }
                else { if(isset($paar[$y]->htln) AND isset($dwz[($paar[$y]->htln)])) { echo round($dwz[($paar[$y]->htln)]); }} ?>
        </th>
        <th class="paarung">
        <?php
        // Ergebnis Mannschaft
        $paar_exist = 0;
        if ($summe[$z2]->sum !="" AND $summe[$z2]->paarung == ($y+1)) {
            $paar_exist = 1;
            echo $summe[$z2]->sum.' : '.$summe[$z2+1]->sum;
			if (($runden_modus == 4 OR $runden_modus == 5) AND ($summe[$z2]->sum == $summe[$z2+1]->sum)) $remis_com = 1; else $remis_com = 0;
            if ($summe[$z2]->dwz_editor !="") { $medit++; }
             }
        else { ?> : <?php }
        $z2=$z2+2; ?>
        </th>
        <th class="paarung2">
        <?php // Name Gastmannschaft
        if (isset($paar[$y]->gpublished) AND $paar[$y]->gpublished == 1) { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&tlnr=<?php echo $paar[$y]->gtln; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $paar[$y]->gname; ?></a>
        <?php } else {
        if (isset($paar[$y]->gname)) {
        echo $paar[$y]->gname;
        }} ?>
        </th>
        <th class="paarung">
        <?php if (isset($dwzgespielt[$zz]->dwz) AND $dwzgespielt[$zz]->paar == ($y+1) AND $paar[$y]->htln !=0 AND $paar[$y]->gtln != 0)
                { echo round($dwzgespielt[$zz]->gdwz);
                    $zz++;
                }
                else { if(isset($paar[$y]->gtln) AND isset($dwz[($paar[$y]->gtln)])) { echo round($dwz[($paar[$y]->gtln)]); }} ?>
        </th>
    </tr>
<?php
}
if (isset($einzel[$w]->paar) AND $einzel[$w]->paar == ($y+1)) {
// Bretter
for ($x=0; $x<$liga[0]->stamm; $x++) {

if ($x%2 != 0) { $zeilenr = zeile1; }
	else { $zeilenr = zeile2; }
	if ($einzel[$w]->ergebnis != 8) {        //mtmt
?>
    <tr class="<?php echo $zeilenr; ?>">
    <td class="paarung"><div><?php echo $einzel[$w]->brett; ?></div></td>
    <td class="paarung2"><div><?php if ($einzel[$w]->zps =="ZZZZZ") {echo "N.N.";} else { ?><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $liga[0]->sid; ?>&zps=<?php echo $einzel[$w]->zps; ?>&mglnr=<?php echo $einzel[$w]->spieler; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $einzel[$w]->hname; } ?></div></td>
    <td class="paarung"><div><?php echo $einzel[$w]->hdwz; ?></a></div></td>
        <?php if ($einzel[$w]->dwz_edit !="") { $edit++; ?>
    <td class="paarung"><div><b><?php echo $einzel[$w]->dwz_text; ?><font size="1"><br>( <?php echo $erg_text[$einzel[$w]->ergebnis]->erg_text; ?> )</font></b></div></td>
        <?php } else { ?>
    <td class="paarung"><div><b><?php echo $erg_text[$einzel[$w]->ergebnis]->erg_text; ?></b></div></td>
        <?php } ?>
    <td class="paarung2"><div><?php if ($einzel[$w]->gzps =="ZZZZZ") {echo "N.N.";} else { ?><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $liga[0]->sid; ?>&zps=<?php echo $einzel[$w]->gzps; ?>&mglnr=<?php echo $einzel[$w]->gegner; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $einzel[$w]->gname; } ?></div></td>
    <td class="paarung"><div><?php echo $einzel[$w]->gdwz; ?></a></div></td>
    </tr>
<?php }
$w++; }
 
if ($edit > 0 OR $medit >0) { ?>
	<tr><td colspan ="6"><?php if ($medit >0 AND $edit =="0"){ echo JText::_('CHIEF_EDIT_TEAM'); }
	else { echo JText::_('CHIEF_EDIT_SINGLE'); }
		echo JText::_('BREACH_TO') ?>
	<?php if($edit >0) { ?><br><?php echo JText::_('CHIEF_EDIT_DWZ') ?></b><?php } ?>
	</td></tr>
<?php } elseif ($remis_com == 1) { ?>
	<tr><td colspan ="6"><?php  if ($paar[$y]->ko_decision == 1) { //1
									if ($paar[$y]->wertpunkte > $paar[$y]->gwertpunkte) echo JText::_('ROUND_DECISION_WP_HEIM')." ".$paar[$y]->wertpunkte." : ".$paar[$y]->gwertpunkte." für ".$paar[$y]->hname; 
									else echo JText::_('ROUND_DECISION_WP_GAST')." ".$paar[$y]->gwertpunkte." : ".$paar[$y]->wertpunkte." für ".$paar[$y]->gname; } 
								if ($paar[$y]->ko_decision == 2) echo JText::_('ROUND_DECISION_BLITZ_HEIM')." ".$paar[$y]->hname;
								if ($paar[$y]->ko_decision == 3) echo JText::_('ROUND_DECISION_BLITZ_GAST')." ".$paar[$y]->gname; 
								if ($paar[$y]->ko_decision == 4) echo JText::_('ROUND_DECISION_LOS_HEIM')." ".$paar[$y]->hname;
								if ($paar[$y]->ko_decision == 5) echo JText::_('ROUND_DECISION_LOS_GAST')." ".$paar[$y]->gname; ?>		
	</td></tr>
<?php } ?>

<?php if ($paar[$y]->comment != "") { ?>
<tr><td colspan ="6"><?php  echo JText::_('PAAR_COMMENT').$paar[$y]->comment; ?>		
	</td></tr>
<?php } ?>

<tr><td colspan ="6" class="noborder">&nbsp;</td></tr>
<?php } elseif ((isset($paar[$y]->gpublished) AND $paar[$y]->gpublished == 1 AND $paar[$y]->hpublished == 1) AND ($paar_exist== 0)) { ?>
    <tr><td colspan ="7" align="left"><?php echo JText::_('NO_RESULT_YET') ?></td></tr>
    <?php } elseif ($paar[$y]->comment != "") { ?>
	<tr><td colspan ="6"><?php  echo JText::_('PAAR_COMMENT').$paar[$y]->comment; ?></td></tr>
	<?php } else { ?><tr><td colspan ="7" class="noborder">&nbsp;</td></tr><?php }
}
?>
</table>

<div class="legend">
    <p><img   src="<?php echo $imageDir; ?>cancel_f2.png" /> = <?php echo JText::_('HIDE_DETAILS') ?></p>
    <p><img   src="<?php echo $imageDir; ?>edit_f2.png" /> = <?php echo JText::_('REPORTED_BY') ?></p>
</div>

<?php
// Rangliste
// Konfigurationsparameter auslesen
$config		= &JComponentHelper::getParams( 'com_clm' );
$rang_runde	= $config->get('fe_runde_rang',1);

if (($rang_runde =="1") AND ($liga[0]->runden_modus != 4 AND $liga[0]->runden_modus != 5)) { 

$lid		= $liga[0]->id; 
$sid		= JRequest::getInt('saison','1');
$punkte		= $this->punkte;
$spielfrei	= $this->spielfrei;

// Spielfreie Teilnehmer finden //
$diff = $spielfrei[0]->count; ?>

<br>
<div id="rangliste">
<table cellpadding="0" cellspacing="0" class="rangliste">
	<?php 
	if ($liga[0]->liga_mt == 0) { $columns = 4;
		if ( $liga[0]->b_wertung > 0) $columns++; }
	else { $columns = 3;
		if ( $liga[0]->tiebr1 > 0)  $columns++; 
		if ( $liga[0]->tiebr2 > 0)  $columns++; 
		if ( $liga[0]->tiebr3 > 0)  $columns++;  } ?>
	<tr><th colspan="<?php echo $columns+(($liga[0]->teil-$diff) * $liga[0]->durchgang); ?>"><?php echo JText::_('RANGLISTE').' '.JText::_('AFTER').' '.$liga[$runde-1]->rname; ?></th></tr><?php //} ?>

	<th class="rang"><div><?php echo JText::_('RANG') ?></div></th>
	<th class="team"><div><?php echo JText::_('TEAM') ?></div></th>
	<?php if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) { // vollrundig
	// erster Durchgang
		for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { ?>
			<th class="rnd"><div><?php echo $rnd+1;?></div></th>
		<?php }
//  zweiter Durchgang 
	if ($liga[0]->durchgang > 1) { for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { ?>
		<th class="rnd"><div><?php echo $rnd+1; ?></div></th>
		<?php }}} ?>
	<?php if ($liga[0]->runden_modus == 3) {    // Schweizer System
		for ($rnd=0; $rnd < $liga[0]->runden ; $rnd++) { ?>
			<th class="rndch"><div><?php echo $rnd+1;?></div></th>
		<?php }} ?>
	<th class="mp"><div><?php echo JText::_('MP') ?></div></th>
	<?php if ( $liga[0]->liga_mt == 0) { ?>
		<th class="bp"><div><?php echo JText::_('BP') ?></div></th>
	<?php } ?>
	<?php if ( $liga[0]->b_wertung > 0) { ?><th class="bp"><div><?php echo JText::_('BW') ?></div></th><?php } ?>
	<?php if ( $liga[0]->tiebr1 > 0) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr1) ?></div></th><?php } ?>
	<?php if ( $liga[0]->tiebr2 > 0) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr2) ?></div></th><?php } ?>
	<?php if ( $liga[0]->tiebr3 > 0) { ?><th class="bp"><div><?php echo JText::_('MTURN_TIEBRS_'.$liga[0]->tiebr3) ?></div></th><?php } ?>
</tr>

<?php
// Anzahl der Teilnehmer durchlaufen
for ($x=0; $x< ($liga[0]->teil)-$diff; $x++){
// Farbgebung der Zeilen //
if ($x%2 != 0) { $zeilenr	= "zeile2";
		$zeilenr_dg2	= "eile2_dg2";}
	else { $zeilenr		= "zeile1";
		$zeilenr_dg2	= "zeile1_dg2";}
?>
<tr class="<?php echo $zeilenr; ?>">
<td class="rang<?php 
	if($x < $liga[0]->auf) { echo "_auf"; }
	if($x >= $liga[0]->auf AND $x < ($liga[0]->auf + $liga[0]->auf_evtl)) { echo "_auf_evtl"; }
	if($x >= ($liga[0]->teil-$liga[0]->ab)) { echo "_ab"; }
	if($x >= ($liga[0]->teil-($liga[0]->ab_evtl + $liga[0]->ab)) AND $x < ($liga[0]->teil-$liga[0]->ab) ) { echo "_ab_evtl"; }
	?>"><?php echo $x+1; ?></td>
	<td class="team">
	<?php if ($punkte[$x]->published ==1) { ?>
	<div><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $lid; ?>&tlnr=<?php echo $punkte[$x]->tln_nr; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $punkte[$x]->name; ?></a></div>
	<div class="dwz"><?php echo "( ".(int)$dwz[($punkte[$x]->tln_nr)]." )"; ?></div>
	<?php } else { ?>
	<div><?php	echo $punkte[$x]->name; ?></div>
	<div class="dwz"><?php	echo "( ".(int)$dwz[($punkte[$x]->tln_nr)]." )"; } ?></div>
	</td>
<?php
// Anzahl der Runden durchlaufen 1.Durchgang
$runden = CLMModelRunde::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,1,$liga[0]->runden_modus);
$count = 0;
if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) { 
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr; ?>"><?php 
	if ($punkte[$y]->tln_nr > $runden[0]->tln_nr) {
		if ($runde != "" AND $runden[($punkte[$y]->tln_nr)-2]->runde <= $runde) {
		echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte; }
		if ($runde == "") { echo $runden[($punkte[$y]->tln_nr)-2]->brettpunkte; }
		}
	if ($punkte[$y]->tln_nr < $runden[0]->tln_nr) {
		if ($runde != "" AND $runden[($punkte[$y]->tln_nr)-1]->runde <= $runde) {
		echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte; }
		if ($runde == "") { echo $runden[($punkte[$y]->tln_nr)-1]->brettpunkte; }
		} ?>
	</td>
	<?php }}}
if ($liga[0]->runden_modus == 3) { 
	for ($y=0; $y< $liga[0]->runden; $y++) { ?>
			<td class="<?php echo $zeilenr; ?>"><?php 
			if ($runden[$y]->name == "spielfrei") echo "  +";
			elseif (!isset($runden[$y])) echo " ";
			//else echo $runden[$y]->rankingpos."/".$runden[$y]->brettpunkte;  
			else echo $runden[$y]->brettpunkte." (".$runden[$y]->rankingpos.")";  ?>
			</td>
			<?php }
	}
// Anzahl der Runden durchlaufen 2.Durchgang
	if ($liga[0]->durchgang > 1) {
		$runden_dg2 = CLMModelRunde::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,2,$liga[0]->runden_modus);
	for ($y=0; $y< $liga[0]->teil-$diff; $y++) {
		if ($y == $x) { ?><td class="trenner">X</td><?php } else { ?>
	<td class="<?php echo $zeilenr_dg2; ?>"><?php 
	if ($punkte[$y]->tln_nr > $runden_dg2[0]->tln_nr) {
		echo $runden_dg2[($punkte[$y]->tln_nr)-2]->brettpunkte;
		}
	if ($punkte[$y]->tln_nr < $runden_dg2[0]->tln_nr) {
		echo $runden_dg2[($punkte[$y]->tln_nr)-1]->brettpunkte;
		} ?>
	</td>
	<?php }}}
// Ende Runden
?>
	<td class="mp"><div><?php echo $punkte[$x]->mp; ?></div></td>
	<?php if ( $liga[0]->liga_mt == 0) { ?>
	<td class="bp"><div><?php echo $punkte[$x]->bp; ?></div></td>
	<?php } ?>
	<?php if ( $liga[0]->b_wertung > 0) { ?><td class="bp"><div><?php echo $punkte[$x]->wp; ?></div></td><?php } ?>
	<?php if ( $liga[0]->tiebr1 > 0) { ?><td class="bp"><div><?php echo CLMText::tiebrFormat($liga[0]->tiebr1, $punkte[$x]->sumtiebr1); ?></div></td><?php } ?>
	<?php if ( $liga[0]->tiebr2 > 0) { ?><td class="bp"><div><?php echo CLMText::tiebrFormat($liga[0]->tiebr2, $punkte[$x]->sumtiebr2); ?></div></td><?php } ?>
	<?php if ( $liga[0]->tiebr3 > 0) { ?><td class="bp"><div><?php echo CLMText::tiebrFormat($liga[0]->tiebr3, $punkte[$x]->sumtiebr3); ?></div></td><?php } ?>
</tr>
<?php }
// Ende Teilnehmer
?>
</table>
<?php if ($diff == 1 AND $liga[0]->ab ==1 ) 
	{echo JText::_(ROUND_NO_RELEGATED_TEAM); }
	if ($diff == 1 AND $liga[0]->ab >1 ) 
	{echo JText::_(ROUND_LESS_RELEGATED_TEAM); }
	?>
</div>
<?php } // Ende Rangliste
?>
<?php // Wenn SL_OK dann Erklärung für Haken anzeigen (nur wenn Staffelleiter eingegeben ist)
 if (isset($liga[0]->mf_name)) {
 if (isset($ok[0]->sl_ok) AND $ok[0]->sl_ok > 0) { ?>

<div class="legend"><p><img src="<?php echo $imageDir; ?>accept.png" width="16" height="16"/> = <?php echo JText::_('CHIEF_OK') ?></p></div>
<?php } } ?>

<br>
<?php } ?>


<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

<div class="clr"></div>
</div>
</div>