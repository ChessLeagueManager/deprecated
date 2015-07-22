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
JHtml::_('behavior.tooltip', '.CLMTooltip', $params);
 

$liga		= $this->liga;
$dwz		= $this->dwz;
$spieler	= $this->spieler;
$sub_liga	= $this->sub_liga;
$sub_msch	= $this->sub_msch;
$sub_rnd	= $this->sub_rnd;
$sid		= JRequest::getInt( 'saison','1');
$lid		= JRequest::getInt('liga','1');
$item		= JRequest::getInt('Itemid','1');

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');

	// Browsertitelzeile setzen
	$doc =& JFactory::getDocument();
	$daten['title'] = JText::_('DWZ_LIGA').' '.$dwz[0]->name; 
	$doc->setHeadData($daten);
	
?>

<div id="clm">
<div id="dwz_liga">
<?php echo CLMContent::componentheading(JText::_('DWZ_LIGA').'&nbsp;'.$dwz[0]->name); ?>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

<?php
$dwz_date_new = $this->dwz_date_new;		
if ($dwz_date_new[0]->nr_aktion  == 101) $hint_dwznew = JText::_('DWZ_COMMENT_RUN').' '.utf8_decode(JText::_('ON_DAY')).' '.JHTML::_('date',  $dwz_date_new[0]->datum, JText::_('%d. %B %Y ,  %H %M')); 
if ($dwz_date_new[0]->nr_aktion  == 102) $hint_dwznew = JText::_('DWZ_COMMENT_DEL').' '.utf8_decode(JText::_('ON_DAY')).' '.JHTML::_('date',  $dwz_date_new[0]->datum, JText::_('%d. %B %Y ,  %H %M')); 
if (!isset($dwz_date_new[0]->nr_aktion)) $hint_dwznew = JText::_('DWZ_COMMENT_UNCLEAR');  

if ($dwz[0]->dsb_datum  > 0) $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_RUN').' '.utf8_decode(JText::_('ON_DAY')).' '.JHTML::_('date',  $dwz[0]->dsb_datum, JText::_('%d. %B %Y')); 
if (($dwz[0]->dsb_datum == 0) || (!isset($dwz))) $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_UNCLEAR');  

if ( !$dwz OR $dwz[0]->published == "0") { echo '<br><div class="wrong">'. JText::_('NOT_PUBLISHED').'<br>'.JText::_('GEDULD') .'</div><br>'; } 
else {

?>

<?php if (!$liga) { echo "<br>".CLMContent::clmWarning(JText::_('DWZ_NO_RESULTS'))."<br>"; } 
	elseif ($liga[0]->anzeige_ma == 1 ) { echo "<br>".CLMContent::clmWarning(JText::_('TEAM_FORMATION_BLOCKED'))."<br>"; } 
else {

$count = 0;
$x = 0; ?>
<!-- ///////////////////// DWZ Auswertung ///////////////// -->

<table cellpadding="0" cellspacing="0" class="dwz_liga">
<?php foreach ($liga as $liga) {
$x++;
if ($x%2 == 0) { $zeilenr = zeile1; }
	else { $zeilenr = zeile2; }

if ($liga->tln_nr > $count) {
if ($x!=1){
if ($spieler[$count-1]->count > 0) {
?>
<tr class="ende">
	<td colspan="2" align="right">&#216;</td>
	<td><?php echo round($spieler[$count-1]->dsbDWZ / $spieler[$count-1]->count); ?></td>
	<td><?php echo round($spieler[$count-1]->punkte / $spieler[$count-1]->count, 3); ?></td>
	<td><?php echo round($spieler[$count-1]->we / $spieler[$count-1]->count,3); ?></td>
	<td><?php echo round($spieler[$count-1]->efaktor / $spieler[$count-1]->count,1); ?></td>
	<td><?php echo round($spieler[$count-1]->leistung / $spieler[$count-1]->count); ?></td>
	<td><?php echo round($spieler[$count-1]->niveau / $spieler[$count-1]->count); ?></td>
	<td><?php echo round($spieler[$count-1]->punkte / $spieler[$count-1]->count,2); ?></td>
	<td><?php echo round($spieler[$count-1]->dwz / $spieler[$count-1]->count); ?></td>
</tr>
<?php }} ?>
</table>
<br>

<table cellpadding="0" cellspacing="0" class="dwz_liga">
<tr>
<th><?php echo $liga->tln_nr;?></th>
<th colspan="10"><a href="index.php?option=com_clm&amp;view=mannschaft&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?>&amp;tlnr=<?php echo $liga->tln_nr; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $liga->name;?></a></th>
</tr>
<tr>
	<td><?php echo JText::_('DWZ_NR') ?></td>
	<td><?php echo JText::_('DWZ_NAME') ?></td>
	<td><a title="<?php echo $hint_dwzdsb; ?>" class="CLMTooltip"><?php echo JText::_('DWZ_OLD') //klkl ?></a></td>
	<td><?php echo JText::_('DWZ_W') ?></td>
	<td><?php echo JText::_('DWZ_WE') ?></td>
	<td><?php echo JText::_('DWZ_EF') ?></td>
	<td><?php echo JText::_('DWZ_RATING') ?></td>
	<td><?php echo JText::_('DWZ_LEVEL') ?></td>
	<td><?php echo JText::_('DWZ_POINTS') ?></td>
	<td colspan="2"><a title="<?php echo $hint_dwznew; ?>" class="CLMTooltip"><?php echo JText::_('DWZ_NEW') //klkl ?></a></td>
</tr>

<?php } ?>
<tr class="<?php echo $zeilenr; ?>">
    <td><?php if($liga->rang =="1") { echo $liga->mnr.'-';} echo $liga->snr;?></td>
    <td><a href="index.php?option=com_clm&amp;view=spieler&amp;saison=<?php echo $sid; ?>&amp;zps=<?php echo $liga->zps; ?>&amp;mglnr=<?php echo $liga->mgl_nr; ?>&amp;Itemid=<?php echo $item; ?>"><?php echo $liga->Spielername;?></a></td>
    <td><?php echo $liga->dsbDWZ.'-'.$liga->DWZ_Index;?></td>
    <td><?php echo $liga->Punkte;?></td>
    <td><?php echo $liga->We;?></td>
    <td><?php echo $liga->EFaktor;?></td>
    <td><?php  if($liga->Punkte == $liga->Partien AND $liga->Niveau == $liga->Leistung AND $liga->Punkte !=0) { echo 667+$liga->Leistung.' &sup2;'; $ex=1;} else { 
    if ( $liga->Leistung == 0 ) { echo "-";}
    else { echo $liga->Leistung; }
    } ?></td>
    <td><?php echo $liga->Niveau;?></td>
    
    <?php  $Pkt = explode (".", $liga->Punkte);
        if ($Pkt[1] != "0") {
            if ($Pkt[0] != "0") { ?>
            <td><?php echo $Pkt[0].'&frac12;  /  '.$liga->Partien;?></td>
            <?php } else { ?>
            <td><?php echo '&frac12;  /  '.$liga->Partien;?></td>
            <?php }}
        else { ?>
    <td><?php echo $Pkt[0].'  /  '.$liga->Partien;?></td>
     <?php } ?>
    <?php if ($liga->DWZ > 0) { ?>
    <td><?php echo $liga->DWZ.'-'.$liga->I0;?></td>
    <?php } 
	if ($liga->dsbDWZ >0 AND $liga->DWZ == 0) { ?>
		<td><?php echo $liga->dsbDWZ.'-'.$liga->DWZ_Index;?></td>
		<?php }
	if ($liga->dsbDWZ  == 0 AND $liga->DWZ == 0) { ?>
		<td><?php echo JText::_('DWZ_REST') ?></td>
		<?php } ?>
    <td><?php $dwzdifferenz = $liga->DWZ - $liga->dsbDWZ; 
    if ( $dwzdifferenz > 0 ) { echo "+" . $dwzdifferenz; } else { echo $dwzdifferenz; } ?></td>
</tr>

<?php
$count= $liga->tln_nr;

 }
if ($spieler[$count-1]->count > 0) {
?>
<tr class="ende">
	<td colspan="2" align="right">&#216;</td>
	<td><?php echo round($spieler[$count-1]->dsbDWZ / $spieler[$count-1]->count); ?></td>
	<td><?php echo round($spieler[$count-1]->punkte / $spieler[$count-1]->count, 3); ?></td>
	<td><?php echo round($spieler[$count-1]->we / $spieler[$count-1]->count,3); ?></td>
	<td><?php echo round($spieler[$count-1]->efaktor / $spieler[$count-1]->count,1); ?></td>
	<td><?php echo round($spieler[$count-1]->leistung / $spieler[$count-1]->count); ?></td>
	<td><?php echo round($spieler[$count-1]->niveau / $spieler[$count-1]->count); ?></td>
	<td><?php echo round($spieler[$count-1]->punkte / $spieler[$count-1]->count,2); ?></td>
	<td><?php echo round($spieler[$count-1]->dwz / $spieler[$count-1]->count); ?></td>
</tr>
<?php } ?>
</table>
<?php }} ?>
<?php if($ex >0) { echo '<div class="hint">'.JText::_('LEAGUE_RATING_IMPOSSIBLE').'</div><br>';} ?>

<?php echo '<div class="hint">'.$hint_dwzdsb.'</div>'; ?>
<?php echo '<div class="hint">'.$hint_dwznew.'</div><br>'; ?>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
<div class="clr"></div>
</div>
</div>