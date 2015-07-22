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
$item		= JRequest::getInt('Itemid','1');
$liga		= $this->liga;
$termin		= $this->termin;
$dwzschnitt	= $this->dwzschnitt;
$dwzgespielt= $this->dwzgespielt;
$paar		= $this->paar;
$summe		= $this->summe;
$rundensumme= $this->rundensumme;
$sub_liga	= $this->sub_liga;
$sub_msch	= $this->sub_msch;
$sub_rnd	= $this->sub_rnd;
$runden_modus = $liga[0]->runden_modus;

$runde_t = $liga[0]->runden + 1;  
// Test alte/neue Standardrundenname bei 2 Durchgängen
if ($liga[0]->durchgang > 1) {
	if ($termin[$runde_t-1]->name == JText::_('ROUND').' '.$runde_t) {  //alt
		for ($xr=0; $xr< ($liga[0]->runden); $xr++) { 
				$termin[$xr]->name = JText::_('ROUND').' '.($xr+1)." (".JText::_('PAAR_HIN').")";
				$termin[$xr+$liga[0]->runden]->name = JText::_('ROUND').' '.($xr+1)." (".JText::_('PAAR_RUECK').")";
		}
    }
}

// Browsertitelzeile setzen
$doc =& JFactory::getDocument();
$daten['title'] = JText::_('PAAR_OVERVIEW').' '.$liga[0]->name;
$doc->setHeadData($daten);

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');

// Konfigurationsparameter auslesen
$config			= &JComponentHelper::getParams( 'com_clm' );
$fe_runde_tln	= $config->get('fe_runde_tln',1);
?>

<div id="clm">
<div id="paarungsliste">

<div class="componentheading">
<?php echo JText::_('PAAR_OVERVIEW') ?> : <?php echo $liga[0]->name; ?>

<div id="pdf">
<div class="pdf"><a href="index.php?option=com_clm&amp;view=paarungsliste&amp;format=clm_pdf&amp;layout=paar&amp;saison=<?php echo $liga[0]->sid ?>&amp;liga=<?php echo $liga[0]->id ?>"><img src="<?php echo $imageDir; ?>pdf_button.png" alt="PDF" class="CLMTooltip" title="<?php echo JText::_('PAAR_PDF') ?>" /></a>
</div></div></div>
<div class="clr"></div>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>
<?php
if ( !$liga OR $liga[0]->published == "0") {
echo "<br>".CLMContent::clmWarning(JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD'))."<br>"; }

else {
    if ($fe_runde_tln =="1") {
		$ohne_tln = "8";
	}
	else {
		$ohne_tln = "6";
	}

	$item		= JRequest::getInt('Itemid','1');

	// Array für DWZ Schnitt setzen
	$dwz = array();
	for ($y=1; $y< ($liga[0]->teil)+1; $y++){
	$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; } ?>

<br>
<?php
// Rundenschleife
$z=0;
$z2=0;
//echo $z2;
$sum_paar=0;
$rund_sum=0;
$term = 0;
if ( $liga[0]->durchgang > 1) { ?><h4><?php echo JText::_('PAAR_HIN') ?></h4><?php } ?>

<?php for ($x=0; $x< ($liga[0]->runden); $x++){
if ($termin[$term]->published =="1") {
?>

<table cellpadding="0" cellspacing="0" class="paarungsliste">
<tr>
<td colspan="<?php echo $ohne_tln; ?>">
<?php
//echo "_!_".$rundensumme[$rund_sum]->nr.'__!__'.($x+1);
// Wenn Rundensumme existiert dann  Rundensymbol (Lupe) anzeigen
if ($rundensumme[$rund_sum]->nr == ($x+1) ) { ?>
<div>
<div class="left">
<?php 
if ($termin[$term]->bemerkungen <> "") { ?>
	<span class="editlinktip hasTip"><img  src="<?php echo $imageDir; ?>con_info.png" class="CLMTooltip" title="<?php echo JText::_( 'CHIEF_NOTE') ?>" /></span><?php }
// Wenn SL_OK dann Haken anzeigen
if ($rundensumme[$rund_sum]->sl_ok > 0) { ?>
	<span class="editlinktip hasTip"><img  src="<?php echo $imageDir; ?>accept.png" class="CLMTooltip" title="<?php echo JText::_( 'CHIEF_OK') ?>" />
<?php } ?></span><b>&nbsp;<?php if ($termin[$term]->datum AND $termin[$term]->nr == ($x+1)) { echo JHTML::_('date',  $termin[$term]->datum, JText::_('%d. %B %Y')); $term++;}
else {  }?></b>
</div>

<div class="paa_titel"><a href="index.php?option=com_clm&amp;view=runde&amp;liga=<?php echo $liga[0]->id ?>&amp;runde=<?php echo $x+1; ?>&amp;saison=<?php echo $liga[0]->sid; ?>&amp;dg=1&amp;Itemid=<?php echo $item; ?>"> <?php echo $termin[$term-1]->name; ?><img width="16" height="16" src="<?php echo $imageDir; ?>lupe.png" /></a></div> <?php
$rund_sum++; }

else { ?>
<div class="left"><b><?php echo $termin[$x]->datum;?></b></div>
<div style="text-align: right; padding: 0 10px 0 0;"><b><?php echo $termin[$x]->name; ?></b></div>
<?php }
////////////////////////////
?>
</div>
</td></tr>
<tr>
	<th class="paar"><?php echo JText::_('PAAR') ?></th>
	<?php if ($fe_runde_tln =="1") { ?>
	<th class="tln"><?php echo JText::_('TLN') ?></th>
    <?php } ?>
	<th class="heim"><?php echo JText::_('HOME') ?></th>
	<th class="dwz"><?php echo JText::_('DWZ') ?></th>
	<th class="erg"><?php echo JText::_('RESULT') ?></th>
	<?php if ($fe_runde_tln =="1") { ?>
	<th class="tln"><?php echo JText::_('TLN') ?></th>
    <?php } ?>
	<th class="gast"><?php echo JText::_('GUEST') ?></th>
	<th class="dwz"><?php echo JText::_('DWZ') ?></th>
</tr>
<?php
// Teilnehmerschleife
for ($y=0; $y< ($liga[0]->teil)/2; $y++){
	if ($paar[$z]->runde > ($x+1)) break;
	if (!isset($paar[$z])) break; 
if ($y%2 != 0) { $zeilenr = zeile2; }
	else { $zeilenr = zeile1; } ?>

<tr class="<?php echo $zeilenr; ?>">
<td class="paar"><?php echo $paar[$z]->paar; ?></td>
	<?php if ($fe_runde_tln =="1") { ?>
    <td class="tln"><?php echo $paar[$z]->tln_nr; ?></td>
    <?php } ?>
<td class="heim">
<?php if ($paar[$z]->hpublished == 1) { ?>
<a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&tlnr=<?php echo $paar[$z]->htln; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $paar[$z]->hname; ?></a><?php }
else { echo $paar[$z]->hname; } ?>
</td>
<td class="dwz">
	<?php if ($dwzgespielt[$z2]->dwz AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 1 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ echo round($dwzgespielt[$z2]->dwz); }
		else { echo round($dwz[($paar[$z]->htln)]); } ?></td>
<?php
// Wenn Paarung existiert dann Ergebnis-Summen anzeigen
while ( $summe[$sum_paar]->runde < ($x+1) ) $sum_paar++;
if ( $summe[$sum_paar]->runde == ($x+1) AND $summe[$sum_paar]->paarung == ($y+1)) { ?>
<td class="erg"><?php echo $summe[$sum_paar]->sum.' : '.$summe[$sum_paar+1]->sum; 
if (($runden_modus == 4 OR $runden_modus == 5) AND ($summe[$sum_paar]->sum == $summe[$sum_paar+1]->sum) AND ($summe[$sum_paar]->sum > 0)) $remis_com = 1; else $remis_com = 0;
            ?></td>
<?php $sum_paar = $sum_paar+2; 

}
else { ?><td class="erg"> : </td> <?php } 
////////////////////////////
?>
	<?php if ($fe_runde_tln =="1") { ?>
	<td class="tln"><?php echo $paar[$z]->gtln; ?></td>
    <?php } ?>
<td class="gast">
<?php if ($paar[$z]->gpublished == 1) { ?>
<a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $liga[0]->sid; ?>&liga=<?php echo $liga[0]->id; ?>&tlnr=<?php echo $paar[$z]->gtln; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $paar[$z]->gname; ?></a><?php }
else { echo $paar[$z]->gname; } ?>
</td>

<td class="dwz">
	<?php if ($dwzgespielt[$z2]->dwz AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 1 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ echo round($dwzgespielt[$z2]->gdwz);
			$z2++;
		}
		else { echo round($dwz[($paar[$z]->gtln)]); } ?></td>
</td></tr>
<?php //echo "paar: "; var_dump($paar[$z]); 
//die(); 
if ($remis_com == 1) { $remis_com = 0; ?>
	<tr class="<?php echo $zeilenr; ?>">
	<td class="paar"><?php echo $paar[$z]->paar; ?></td>
	<td colspan ="7"><?php  if ($paar[$z]->ko_decision == 1) {
									if ($paar[$z]->wertpunkte > $paar[$z]->gwertpunkte) echo JText::_('ROUND_DECISION_WP_HEIM')." ".$paar[$z]->wertpunkte." : ".$paar[$z]->gwertpunkte." für ".$paar[$z]->hname; 
									else echo JText::_('ROUND_DECISION_WP_GAST')." ".$paar[$z]->gwertpunkte." : ".$paar[$z]->wertpunkte." für ".$paar[$z]->gname; }
								if ($paar[$z]->ko_decision == 2) echo JText::_('ROUND_DECISION_BLITZ_HEIM')." ".$paar[$z]->hname;
								if ($paar[$z]->ko_decision == 3) echo JText::_('ROUND_DECISION_BLITZ_GAST')." ".$paar[$z]->gname; 
								if ($paar[$z]->ko_decision == 4) echo JText::_('ROUND_DECISION_LOS_HEIM')." ".$paar[$z]->hname;
								if ($paar[$z]->ko_decision == 5) echo JText::_('ROUND_DECISION_LOS_GAST')." ".$paar[$z]->gname; ?>		
	</td></tr>
<?php }  ?>

<?php if ($paar[$z]->comment != "") { ?>
<tr class="<?php echo $zeilenr; ?>">
<td class="paar"><?php echo $paar[$z]->paar; ?></td>
<td colspan ="7"><?php  echo JText::_('PAAR_COMMENT').$paar[$z]->comment; ?>		
	</td></tr>
<?php }  ?>

</tr>
<?php $z++; } ?>
</table><br>
<?php } else { ?>
<table cellpadding="0" cellspacing="0" class="paarungsliste">
<tr>
<td colspan="<?php echo $ohne_tln; ?>"><b>
<div>
<div class="left">
<?php
if ($rundensumme[$rund_sum]->nr == ($x+1) ) { $rund_sum++; }
if ($termin[$term]->datum AND $termin[$term]->nr == ($x+1)) { echo JHTML::_('date',  $termin[$term]->datum, JText::_('%d %B %Y')); $term++;} ?>
</div>
<div style="text-align: right; padding: 0 10px 0 0;"> <?php echo $termin[$x]->name; ?></div>
</b>
</td>
</tr>
<?php
for ($y=0; $y< ($liga[0]->teil)/2; $y++){
	if ( $summe[$sum_paar]->runde == ($x+1) AND $summe[$sum_paar]->paarung == ($y+1)) { $sum_paar = $sum_paar+2; }
	if ($dwzgespielt[$z2]->dwz AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 1 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0) { $z2++; }
	$z++;
	} ?>
<tr><td>
<?php echo CLMContent::clmWarning(JText::_('PAAR_UNPUBLISHED')); ?>
</td></tr>
</table>
<br>
<?php
}}

///////////////////////
// zweiter Durchgang //
///////////////////////

if ( $liga[0]->durchgang > 1) { ?>
<br><h4><?php echo JText::_('PAAR_RUECK') ?></h4>


<?php
for ($x=0; $x< ($liga[0]->runden); $x++){
if ($termin[$term]->published =="1") {
?>

<table cellpadding="0" cellspacing="0" class="paarungsliste">
<tr>
<td colspan="<?php echo $ohne_tln; ?>">
<?php 
// Wenn Rundensumme existiert dann  Rundensymbol (Lupe) anzeigen
if ($rundensumme[$rund_sum]->nr == ($x+1+$liga[0]->runden) ) { ?>
<div>
<div class="left">
<?php 
if ($termin[$term]->bemerkungen <> "") { ?>
	<span class="editlinktip hasTip"><img  src="<?php echo $imageDir; ?>con_info.png" class="CLMTooltip" title="<?php echo JText::_( 'CHIEF_NOTE') ?>" /></span><?php }
// Wenn SL_OK dann Haken anzeigen
if ($rundensumme[$rund_sum]->sl_ok > 0) { ?>
	<span class="editlinktip hasTip"><img  src="<?php echo $imageDir; ?>accept.png" class="CLMTooltip" title="<?php echo JText::_( 'CHIEF_OK') ?>" />
<?php } ?></span><b>&nbsp;<?php if ($termin[$term]->datum AND $termin[$term]->nr == ($x+1+$liga[0]->runden)) { echo JHTML::_('date',  $termin[$term]->datum, JText::_('%d %B %Y')); $term++;}
else {  }?></b>	
</div>

<div class="paa_titel"><a href="index.php?option=com_clm&amp;view=runde&amp;liga=<?php echo $liga[0]->id ?>&amp;runde=<?php echo $x+1; ?>&amp;saison=<?php echo $liga[0]->sid; ?>&amp;dg=2&amp;Itemid=<?php echo $item; ?>"> <?php echo $termin[$term-1]->name; ?><img width="16" height="16" src="<?php echo $imageDir; ?>lupe.png" /></a></div> <?php
$rund_sum++; }

else {	?>
<div class="left"><b><?php echo $termin[$x]->datum;?></b></div>
<div style="text-align: right; padding: 0 10px 0 0;"><b> <?php echo $termin[$x]->name; ?></b></div>
<?php } 
/////////////////////////////
?>
<div class="clr"></div>
</div>
</td>
<tr>
	<th class="paar"><?php echo JText::_('PAAR') ?></th>
	<th class="tln"><?php echo JText::_('TLN') ?></th>
	<th class="heim"><?php echo JText::_('HOME') ?></th>
	<th class="dwz"><?php echo JText::_('DWZ') ?></th>
	<th class="erg"><?php echo JText::_('RESULT') ?></th>
	<th class="tln"><?php echo JText::_('TLN') ?></th>
	<th class="gast"><?php echo JText::_('GUEST') ?></th>
	<th class="dwz"><?php echo JText::_('DWZ') ?></th>
</tr>
<?php

// Teilnehmerschleife
for ($y=0; $y< ($liga[0]->teil)/2; $y++){
	if ($paar[$z]->runde > ($x+1)) break;
	if (!isset($paar[$z])) break; 
if ($y%2 != 0) { $zeilenr = $farbe_zeile1; }
	else { $zeilenr = $farbe_zeile2; } ?>

<tr>
<td class="paar"><?php echo $paar[$z]->paar; ?></td>
<td class="tln"><?php echo $paar[$z]->tln_nr; ?></td>
<td class="heim">
<?php if ($paar[$z]->hpublished == 1) { ?>
<a href="index.php?option=com_clm&amp;view=mannschaft&amp;saison=<?php echo $liga[0]->sid; ?>&amp;liga=<?php echo $liga[0]->id; ?>&amp;tlnr=<?php echo $paar[$z]->htln; ?>"><?php echo $paar[$z]->hname; ?></a><?php }
else { echo $paar[$z]->hname; } ?>
</td>
<td class="dwz">
	<?php if ($dwzgespielt[$z2]->dwz AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 2 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ echo round($dwzgespielt[$z2]->dwz); }
		else { echo round($dwz[($paar[$z]->htln)]); } ?>
</td>
<?php
// Wenn Paarung existiert dann Ergebnis-Summen anzeigen
while ( $summe[$sum_paar]->runde < ($x+1) ) $sum_paar++;
if ( $summe[$sum_paar]->runde == ($x+1) AND $summe[$sum_paar]->paarung == ($y+1)) { ?>
<td class="erg"><?php echo $summe[$sum_paar]->sum.' : '.$summe[$sum_paar+1]->sum; ?></td>
<?php $sum_paar = $sum_paar+2;

}
else { ?><td class="erg"> : </td><?php }
/////////////////////////////
?>
<td class="tln"><?php echo $paar[$z]->gtln; ?></td>
<td class="gast">
<?php if ($paar[$z]->gpublished == 1) { ?>
<a href="index.php?option=com_clm&amp;view=mannschaft&amp;saison=<?php echo $liga[0]->sid; ?>&amp;liga=<?php echo $liga[0]->id; ?>&amp;tlnr=<?php echo $paar[$z]->gtln; ?>"><?php echo $paar[$z]->gname; ?></a><?php }
else { echo $paar[$z]->gname; } ?>
</td>
<td class="dwz">
	<?php if ($dwzgespielt[$z2]->dwz AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 2 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0)
		{ echo round($dwzgespielt[$z2]->gdwz);
			$z2++;
		}
		else { echo round($dwz[($paar[$z]->gtln)]); } ?></td>
</td>
</tr>
<?php $z++; } ?>
</table>
<br>
<?php } else { ?>
<table cellpadding="0" cellspacing="0" class="paarungsliste">
<tr>
<td colspan="<?php echo $ohne_tln; ?>"><b>
<div><div class="left">
<?php
if ($rundensumme[$rund_sum]->nr == ($x+1+$liga[0]->runden) ) { $rund_sum++; }
if ($termin[$term]->datum AND $termin[$term]->nr == ($x+1+$liga[0]->runden)) { echo JHTML::_('date',  $termin[$term]->datum, JText::_('%d %B %Y')); $term++;} ?>
</div><div style="text-align: right; padding: 0 10px 0 0;"> <?php echo $termin[$x]->name; ?></div></b>
</td>
</tr>
<?php
for ($y=0; $y< ($liga[0]->teil)/2; $y++){
	if ( $summe[$sum_paar]->runde == ($x+1) AND $summe[$sum_paar]->paarung == ($y+1)) { $sum_paar = $sum_paar+2; }
	if ($dwzgespielt[$z2]->dwz AND $dwzgespielt[$z2]->runde == ($x+1) AND $dwzgespielt[$z2]->paar == ($y+1) AND $dwzgespielt[$z2]->dg == 2 AND $paar[$z]->hmnr !=0 AND $paar[$z]->gmnr != 0) { $z2++; }
	$z++;
	} ?>
<tr><td><?php echo CLMContent::clmWarning(JText::_('PAAR_UNPUBLISHED')); ?></td></tr>
</table>

<?php
}}} ?>

<div class="legend">
<p><img src="<?php echo $imageDir; ?>accept.png" width="16" height="16"/> = <?php echo JText::_('CHIEF_OK') ?></p>
<p><img  src="<?php echo $imageDir; ?>con_info.png" width="16" height="16"/> = <?php echo JText::_('CHIEF_NOTE') ?></p>
<p><img src="<?php echo $imageDir; ?>lupe.png" width="16" height="16"/> = <?php echo JText::_('CHIEF_DETAIL') ?></p>
</div>
<br />
<?php } ?>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

<div class="clr"></div>
</div>
</div>