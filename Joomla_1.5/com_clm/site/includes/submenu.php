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

$document = &JFactory::getDocument();

// Konfigurationsparameter auslesen
$config	= &JComponentHelper::getParams( 'com_clm' );
$fe_submenu = $config->get('fe_submenu',1);

// CSS
$cssDir = JURI::base().'components/com_clm/includes';
$document->addStyleSheet( $cssDir.'/clm_content.css', 'text/css', null, array() );

$itemid	= JRequest::getVar( 'Itemid' );

if ($fe_submenu == 1)  {

// Datenbank einlesen
include (JPATH_COMPONENT.DS.'models'.DS.'quickmenu.php');	

 // Prüfen, ob active
$url = $_SERVER["REQUEST_URI"]; ?>

<script type="text/javascript">
<!-- Vereinsliste
function goto(form) { var index=form.select.selectedIndex
if (form.select.options[index].value != "0") {
location=form.select.options[index].value;}}
//-->

<?php include_once (JPATH_COMPONENT.DS.'javascript'.DS.'suckerfish.js');			
?>
</script>
<div id="clm_suckerfish">
    <ul>
        <li><span><?php echo JText::_('SUBMENU_LEAGUE'); ?></span>
        <ul>
        <?php foreach ($sub_liga as $sub_liga) { $cnt++ ?>
        <li><a href="index.php?option=com_clm&amp;view=rangliste&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $sub_liga->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $sub_liga->name; ?></a></li>
        <?php } ?>
        </ul></li>
        
        <?php if ( strpos ($url, "info") ){ echo ""; }  // Wenn Info-View nichts anzeigen
		else { ?>  
        <li><span><?php echo JText::_('SUBMENU_MSCH'); ?></span>
        <ul>
        <?php foreach ($sub_msch as $sub_msch) { $cnt++ ?>
        <li><a href="index.php?option=com_clm&amp;view=mannschaft&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?>&amp;tlnr=<?php echo $sub_msch->tln_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $sub_msch->name; ?></a></li>
        <?php } ?>
        </ul></li>
        
        <li class="<?php if ( strpos ($url, "paarungsliste") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=paarungsliste&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('SUBMENU_PAAR'); ?></a>
        <ul>
        <li><a href="index.php?option=com_clm&amp;view=aktuell_runde&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('ROUND_CURRENT'); ?></a></li>
        <?php foreach ($sub_runden as $sub_runden) { $cnt++;
				if ($sub_runden->nr > $sub_runden->runden) {    //klkl
					$sub_liga_durchgang = "2";
					$sub_runden_nr = $sub_runden->nr - $sub_runden->runden; }
				else {
					$sub_liga_durchgang = "1";                  //klkl
					$sub_runden_nr = $sub_runden->nr; } ?>
        <li><a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?>&amp;runde=<?php echo $sub_runden_nr; ?>&amp;dg=<?php echo $sub_liga_durchgang; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $sub_runden->name; ?></a></li>
        <?php } ?>
        </ul></li>
        
        <li class="<?php if ( strpos ($url, "dwz_liga") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=dwz_liga&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('SUBMENU_DWZMSCH'); ?></a></li>
        
        <li class="<?php if ( strpos ($url, "statistik") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $sid; ?>&amp;liga=<?php echo $lid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('SUBMENU_STATS'); ?></a></li>
        
        <li class="<?php if ( strpos ($url, "termine") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=termine&amp;saison=<?php echo $sid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('SUBMENU_TERMINE'); ?></a></li>
        
        <?php } ?>
    </ul>
       
    <div class="saisonlist">
    <form name="form1">
    	<select name="select" onchange="goto(this.form)" class="selectlist">
            <?php for ($i = 0; $i < $count_saisonlist; $i++) { ?>
            	<option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=info&saison=<?php echo $saisonlist[$i]->id; ?>&amp;Itemid=<?php echo $itemid; ?>"
				<?php if ($saisonlist[$i]->id == $sid) { echo 'selected="selected"'; } ?>><?php echo $saisonlist[$i]->name; ?> </option>
            <?php } ?>
   		</select>
	</form>
	</div>
<div class="clr"></div>
</div>
<?php } ?>