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
 
$lid		= JRequest::getInt('liga','1'); 
$sid		= JRequest::getInt('saison','1');
$runde		= JRequest::getInt('runde');
$item		= JRequest::getInt('Itemid','1');
$liga		= $this->liga;
$punkte		= $this->punkte;
$spielfrei	= $this->spielfrei;
$dwzschnitt	= $this->dwzschnitt;
$sub_liga	= $this->sub_liga;
$sub_msch	= $this->sub_msch;
$sub_rnd	= $this->sub_rnd;

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');

echo '<div id="clm"><div id="rangliste">';

if ( !$liga OR $liga[0]->published == "0") {
echo "<div id='wrong'>".JText::_('NOT_PUBLISHED')."<br>".JText::_('GEDULD')."</div>";
} else {

	// Browsertitelzeile setzen
	$doc =& JFactory::getDocument();
	$daten['title'] = JText::_('RANGLISTE').' '.$liga[0]->name;
	$doc->setHeadData($daten);

	// Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$pdf_melde = $config->get('pdf_meldelisten',1);

		// Userkennung holen
	$user	=& JFactory::getUser();
	$jid	= $user->get('id');

	// Array für DWZ Schnitt setzen
	$dwz = array();
	for ($y=1; $y< ($liga[0]->teil)+1; $y++){
	$dwz[$dwzschnitt[($y-1)]->tlnr] = $dwzschnitt[($y-1)]->dwz; }

	// Spielfreie Teilnehmer finden //
	$diff = $spielfrei[0]->count;
?>

<div class="componentheading">
<?php echo JText::_('RANGLISTE'); echo "&nbsp;".$liga[0]->name; ?>
<div id="pdf">
<!--<img src="printButton.png" alt="drucken"  /></a>-->
<div class="pdf"><a href="index.php?option=com_clm&amp;view=rangliste&amp;format=clm_pdf&amp;layout=rang&amp;saison=<?php echo $sid;?>&amp;liga=<?php echo $lid ?>&amp;Itemid=99"><img src="<?php echo $imageDir.'pdf_button.png'; ?>" alt="PDF"  title="<?php echo JText::_('RANGLISTE_PRINT_TABLE'); ?>" class="CLMTooltip" /></a></div>
<?php if ($pdf_melde == 1) { ?>
<!--neue Ausgabe: Saisonstart-->
<div class="pdf"><a href="index.php?option=com_clm&amp;view=rangliste&amp;format=clm_pdf&amp;layout=start&amp;saison=<?php echo $sid;?>&amp;liga=<?php echo $lid ?>&amp;Itemid=99"><img src="<?php echo $imageDir.'pdf_button.png'; ?>" title="<?php echo JText::_('RANGLISTE_PRINT_TEAM_LISTING'); ?>"  class="CLMTooltip" /></a></div>
<!--neue Ausgabe: Ligaheft-->
<?php if ($jid !="0") { ?>
<div class="pdf"><a href="index.php?option=com_clm&amp;view=rangliste&amp;format=clm_pdf&amp;layout=heft&o_nr=1&amp;saison=<?php echo $sid;?>&amp;liga=<?php echo $lid ?>&amp;Itemid=99"><img src="<?php echo $imageDir.'pdf_button.png'; ?>" title="<?php echo JText::_('RANGLISTE_PRINT_LIGAHEFT_1'); ?>"  class="CLMTooltip" /></a></div>
<?php } ?>
<div class="pdf"><a href="index.php?option=com_clm&amp;view=rangliste&amp;format=clm_pdf&amp;layout=heft&o_nr=0&amp;saison=<?php echo $sid;?>&amp;liga=<?php echo $lid ?>&amp;Itemid=99"><img src="<?php echo $imageDir.'pdf_button.png'; ?>" title="<?php echo JText::_('RANGLISTE_PRINT_LIGAHEFT'); ?>"  class="CLMTooltip" /></a></div>
<?php } ?>
</div></div>
<div class="clr"></div>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>

<br>
<table cellpadding="0" cellspacing="0" class="rangliste">
<tr>
	<th class="rang"><div><?php echo JText::_('RANG') ?></div></th>
	<th class="team"><div><?php echo JText::_('TEAM') ?></div></th>
	<?php if ($liga[0]->runden_modus == 1 OR $liga[0]->runden_modus == 2) {    // vollrundig
	// erster Durchgang 
		for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { ?>
			<th class="rnd"><div><?php echo $rnd+1;?></div></th>
		<?php }
	// zweiter Durchgang 
		if ($liga[0]->durchgang > 1) { for ($rnd=0; $rnd < $liga[0]->teil-$diff ; $rnd++) { ?>
			<th class="rnd"><div><?php echo $rnd+1; ?></div></th>
		<?php }}} ?>
	<?php if ($liga[0]->runden_modus == 3) { 				// Schweizer System
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
// Fargebung der Zeilen //
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
	<div class="dwz"><?php	echo "( ".(int)$dwz[($punkte[$x]->tln_nr)]." )"; ?></div>
	<?php } ?>
	</td>

<?php
// Anzahl der Runden durchlaufen 1.Durchgang
$runden = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,1,$liga[0]->runden_modus);
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
		$runden_dg2 = CLMModelRangliste::punkte_tlnr($sid,$lid,$punkte[$x]->tln_nr,2,$liga[0]->runden_modus);
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


<?php if ( ($liga[0]->sl <> "") or ($liga[0]->bemerkungen <> "") ) { ?>
<div id="desc">
    
    <?php if ( $liga[0]->sl <> "" ) { ?>
    <div class="ran_chief">
        <div class="ran_chief_left"><?php echo JText::_('CHIEF') ?></div>
        <div class="ran_chief_right"><?php echo $liga[0]->sl; ?> | <?php echo JHTML::_( 'email.cloak', $liga[0]->email ); ?></div>	
	</div>
	<div class="clr"></div>
    <?php  } ?>
    
    <?php // Kommentare zur Liga
    if ($liga[0]->bemerkungen <> "") { ?>
    <div class="ran_note">
        <div class="ran_note_left"><?php echo JText::_('NOTICE') ?></div>
        <div class="ran_note_right"><?php echo nl2br($liga[0]->bemerkungen); ?></div>
    </div>
    <div class="clr"></div>
	<?php  } /*echo JHTMLContent::prepare($liga[0]->bemerkungen); */?>

	<?php 
	if ($diff == 1 AND $liga[0]->ab ==1 ) { echo JText::_(ROUND_NO_RELEGATED_TEAM); }
	if ($diff == 1 AND $liga[0]->ab >1 ) { echo JText::_(ROUND_LESS_RELEGATED_TEAM); }
	?>
</div>
<?php }  } ?>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>


<div class="clr"></div>
</div>
</div>