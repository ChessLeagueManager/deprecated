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

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');

// Konfigurationsparameter auslesen
$itemid 		= JRequest::getVar( 'Itemid' );
$spRang		= JRequest::getVar( 'spRang' ,0);	//Sonderranglisten

$turnierid		= JRequest::getInt('turnier','1');
$config	= &JComponentHelper::getParams( 'com_clm' );
$pdf_melde = $config->get('pdf_meldelisten',1);
$fixth_ttab = $config->get('fixth_ttab',1);
	
// CLM-Container
echo '<div id="clm"><div id="turnier_tabelle">';
	
?>
<!--neue Ausgabe: pdf-Liste -->
<div class="pdf"><a href="index.php?option=com_clm&amp;view=turnier_tabelle&amp;format=clm_pdf&amp;layout=tabelle&amp;turnier=<?php echo $turnierid;?>&amp;spRang=<?php echo $spRang ?>&amp;Itemid=99"><img src="<?php echo $imageDir.'pdf_button.png'; ?>" title="<?php echo JText::_('TOURNAMENT_TABLE_PRINT'); ?>"  class="CLMTooltip" /></a></div>
<?php 
	
// Componentheading
if($spRang != 0){			//Sonderranglisten
	$heading = $this->turnier->name.": ".$this->turnier->spRangName." ".JText::_('TOURNAMENT_TABLE'); 
} else {
	$heading = $this->turnier->name.": ".JText::_('TOURNAMENT_TABLE');
}
echo CLMContent::componentheading($heading);

// Navigationsmenu
require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

if ( $this->turnier->published == 0) { 

	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

} elseif ($spRang == 0 and $this->turnier->playersCount < $this->turnier->teil) { //Änderung wegen Sonderranglisten

	echo CLMContent::clmWarning(JText::_('TOURNAMENT_PLAYERLISTNOTCOMPLETE')."<br/>".JText::_('TOURNAMENT_NORANKINGEXISTING'));

} elseif($this->turnier->typ == 3) { // KO-System

	echo CLMContent::clmWarning(JText::_('TOURNAMENT_TABLENOTAVAILABLE'));

} elseif ($spRang != 0 and $this->turnier->playersCount == 0 ) { //Hinzugefügt wegen Sonderranglisten
	
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_SPECIALRANKING_NOPLAYERS'));

} else {

	$turParams = new JParameter($this->turnier->params);

	// Table
	echo '<table cellpadding="0" cellspacing="0" id="turnier_tabelle"';
	if ($fixth_ttab =="1") { echo 'class="tableWithFloatingHeader"'; };

		// header
		echo '><tr>';
			echo '<th class="rang">'.JText::_('TOURNAMENT_RANKABB').'</th>';
			if ($turParams->get('displayPlayerTitle', 1) == 1) {
				echo '<th class="titel">'.JText::_('TOURNAMENT_TITLE').'</th>';
			}
			echo '<th class="name_float">'.JText::_('TOURNAMENT_PLAYERNAME').'</th>';
			if ($turParams->get('displayPlayerClub', 1) == 1) {
				echo '<th class="verein">'.JText::_('TOURNAMENT_CLUB').'</th>';
			}
			echo '<th class="twz">'.JText::_('TOURNAMENT_TWZ').'</th>';
			echo '<th class="fw_col">'.JText::_('TOURNAMENT_POINTS_ABB').'</th>';
			// mgl. Feinwertungen
			for ($f=1; $f<=3; $f++) {
				$fwFieldName = 'tiebr'.$f;
				if ($this->turnier->$fwFieldName > 0) {
					echo '<th class="fw_col">'.JText::_('TOURNAMENT_TIEBR_ABB_'.$this->turnier->$fwFieldName).'</th>';
				}
			}
		echo '<tr />';
		
		// alle Spieler durchgehen
		for ($p=0; $p<$this->turnier->playersCount; $p++) {

			if ($p%2 != 0) { 
				$zeilenr = "zeile1"; 
			} else { 
				$zeilenr = "zeile2"; 
			}

			echo '<tr class="'.$zeilenr.'">';
				echo '<td class="rang">'.CLMText::getPosString($this->players[$p]->rankingPos).'</td>';
				
				if ($turParams->get('displayPlayerTitle', 1) == 1) {
					echo '<td align="center" class="name_float">'.$this->players[$p]->titel.'</td>';
				}
				echo '<td class="verein">';
					$link = new CLMcLink();
					$link->view = 'turnier_player';
					$link->more = array('turnier' => $this->turnier->id, 'snr' => $this->players[$p]->snr, 'Itemid' => $itemid );
					$link->makeURL();
					echo $link->makeLink($this->players[$p]->name);
				echo '</td>';
				if ($turParams->get('displayPlayerClub', 1) == 1) {
					if ($this->tourn_linkclub == 1) {
						$link = new CLMcLink();
						$link->view = 'verein';
						$link->more = array('saison' => $this->players[$p]->sid, 'zps' => $this->players[$p]->zps, 'Itemid' => $itemid );
						$link->makeURL();
						echo '<td class="name_float">'.$link->makeLink($this->players[$p]->verein).'</td>';
					} else {
						echo '<td class="name_float">'.$this->players[$p]->verein.'</td>';
					}
				}
				echo '<td class="twz">'.CLMText::formatRating($this->players[$p]->twz).'</td>';
				echo '<td class="fw_col">'.$this->players[$p]->sum_punkte.'</td>';
				// mgl. Feinwertungen
				for ($f=1; $f<=3; $f++) {
					$fwFieldName = 'tiebr'.$f;
					$plTiebrField = 'sumTiebr'.$f;
					if ($this->turnier->$fwFieldName > 0) {
						echo '<td class="fw_col">'.CLMtext::tiebrFormat($this->turnier->$fwFieldName, $this->players[$p]->$plTiebrField).'</td>';
					}
				}
			echo '<tr />';
		}
		// ende alle Spieler



	echo '</table>';
		

}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
?>