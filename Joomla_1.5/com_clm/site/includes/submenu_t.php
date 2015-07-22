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

$itemid	= JRequest::getVar( 'Itemid' );

// Konfigurationsparameter auslesen
$config	= &JComponentHelper::getParams( 'com_clm' );
$fe_submenu_t = $config->get('fe_submenu_t',1);

?>

<script type="text/javascript">
<?php include_once (JPATH_COMPONENT.DS.'javascript'.DS.'suckerfish.js');			
?>
</script>
<?php // Prüfen, ob active
$url = $_SERVER["REQUEST_URI"];

if ($fe_submenu_t == 1)  { 

// Datenbank einlesen
include (JPATH_COMPONENT.DS.'models'.DS.'quickmenu_t.php');	

?>
<div id="clm_suckerfish">
    <ul>
        <li class="<?php if ( strpos ($url, "turnier_info") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=turnier_info&turnier=<?php echo $this->turnier->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" class="<?php echo $class; ?>"><?php echo JText::_('TOURNAMENT_INFO'); ?></a></li>
        
		<li class="<?php if ( strpos ($url, "turnier_tabelle") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=turnier_tabelle&turnier=<?php echo $this->turnier->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" class="<?php echo $class; ?>"><?php echo JText::_('TOURNAMENT_TABLE'); ?></a>
		<?php if (count($sub_spRang) > 0){ 
		echo "<ul>";
		foreach ($sub_spRang as $sub_spRang_item) { $cnt++ ?>
			<li><a href="index.php?option=com_clm&amp;view=turnier_tabelle&turnier=<?php echo $this->turnier->id; ?>&spRang=<?php echo $sub_spRang_item->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" class="<?php echo $class; ?>"><?php echo $sub_spRang_item->name." ".JText::_('TOURNAMENT_TABLE'); ?></a></li>
        <?php }
		echo "</ul>"; } ?>
		</li>
        
		<li class="<?php if ( strpos ($url, "turnier_rangliste") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=turnier_rangliste&turnier=<?php echo $this->turnier->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('TOURNAMENT_RANKING'); ?></a>
		<?php if (count($sub_spRang) > 0){ 
		echo "<ul>";
		foreach ($sub_spRang as $sub_spRang_item) { $cnt++ ?>
			<li><a href="index.php?option=com_clm&amp;view=turnier_rangliste&turnier=<?php echo $this->turnier->id; ?>&spRang=<?php echo $sub_spRang_item->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" class="<?php echo $class; ?>"><?php echo $sub_spRang_item->name." ".JText::_('TOURNAMENT_RANKING'); ?></a></li>
		<?php }
		echo "</ul>"; } ?>
		</li>
		
        <li class="<?php if ( strpos ($url, "turnier_teilnehmer") or strpos ($url, "turnier_player") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=turnier_teilnehmer&turnier=<?php echo $this->turnier->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('TOURNAMENT_PARTICIPANTLIST'); ?></a></li>
        <li class="<?php if ( strpos ($url, "turnier_paarungsliste") or strpos ($url, "turnier_runde") ){ echo "active"; } ?>">
        <a href="index.php?option=com_clm&amp;view=turnier_paarungsliste&turnier=<?php echo $this->turnier->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" class="<?php echo $class; ?>"><?php echo JText::_('TOURNAMENT_PAIRINGLIST'); ?></a></li>
    </ul>
<div class="clr"></div>
</div>
<?php } ?>
<br />