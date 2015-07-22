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
$turnierid		= JRequest::getInt('turnier','1');
$config	= &JComponentHelper::getParams( 'com_clm' );
$pdf_melde = $config->get('pdf_meldelisten',1);

// CLM-Container
echo '<div id="clm"><div id="turnier_paarungsliste">';

?>
<!--neue Ausgabe: pdf-Liste -->
<div class="pdf"><a href="index.php?option=com_clm&amp;view=turnier_paarungsliste&amp;format=clm_pdf&amp;layout=paarungsliste&amp;turnier=<?php echo $turnierid;?>&amp;Itemid=99"><img src="<?php echo $imageDir.'pdf_button.png'; ?>" title="<?php echo JText::_('TOURNAMENT_PAIRINGLIST_PRINT'); ?>"  class="CLMTooltip" /></a></div>
<?php 

// componentheading vorbereiten
$heading = $this->turnier->name.": ".JText::_('TOURNAMENT_PAIRINGLIST');
echo CLMContent::componentheading($heading);

// Navigationsmenu
require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

if ( $this->turnier->published == 0) { 
	
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

} elseif ($this->turnier->rnd == 0) {
	
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOROUNDS'));

} else {

	$turParams = new JParameter($this->turnier->params);

	// alle Runden durchgehen
	foreach ($this->rounds as $value) {
		
		// published?
		if ($value->published == 1) {
			
			// Table aufziehen
			echo '<table cellpadding="0" cellspacing="0" class="runde">';
			
			// Kopfzeile
			echo '<tr><td colspan="9">';
				echo '<div style="text-align:left; padding-left:1%">';
					echo '<b>';
					echo $value->name;
					if ($value->datum != "0000-00-00" AND $turParams->get('displayRoundDate', 1) == 1) {
						echo ',&nbsp;'.JHTML::_('date',  $value->datum, JText::_('%d. %B %Y'));
					}
					echo '</b>';
				echo '</div>';
			echo '</td></tr>';
			// Ende Kopfzeile
		
			// Spaltenüberschriften
			?>
			<tr>
				<th align="center" width="40%"><?php echo JText::_('TOURNAMENT_WHITE'); ?></th>
				<th align="center" width="10%"><?php echo JText::_('TOURNAMENT_TWZ'); ?></th>
				<th align="center" width="5%">-</th>
				<th align="center" width="40%"><?php echo JText::_('TOURNAMENT_BLACK'); ?></th>
				<th align="center" width="10%"><?php echo JText::_('TOURNAMENT_TWZ'); ?></th>
				<th align="center" width="5%"><?php echo JText::_('RESULT'); ?></th>
			</tr>
			<?php
		
			// alle Matches eintragen
			$m=0; // CounterFlag für Farbe
			foreach ($this->matches[$value->nr] as $matches) {
				
				$m++;
				// Farbe
				if ($m%2 != 0) { 
					$zeilenr = "zeile1"; 
				} else { 
					$zeilenr = "zeile2"; 
				}
				
				if ( ($matches->spieler != 0 AND $matches->gegner != 0) OR $matches->ergebnis != NULL) {
					echo '<tr class="'.$zeilenr.'">';
						echo '<td>';
						if (isset($this->players[$matches->spieler]->name)) {
							$link = new CLMcLink();
							$link->view = 'turnier_player';
							$link->more = array('turnier' => $this->turnier->id, 'snr' => $matches->spieler, 'Itemid' => $itemid);
							$link->makeURL();
							echo $link->makeLink($this->players[$matches->spieler]->name);
						}
						echo '</td>';
						echo '<td align="center">'.CLMText::formatRating($this->players[$matches->spieler]->twz).'</td>';
						echo '<td align="center">-</td>';
						echo '<td>';
						if (isset($this->players[$matches->gegner]->name)) {
							$link = new CLMcLink();
							$link->view = 'turnier_player';
							$link->more = array('turnier' => $this->turnier->id, 'snr' => $matches->gegner, 'Itemid' => $itemid);
							$link->makeURL();
							echo $link->makeLink($this->players[$matches->gegner]->name);
						}
						echo '</td>';
						echo '<td align="center">'.CLMText::formatRating($this->players[$matches->gegner]->twz).'</td>';
						if ($matches->ergebnis != NULL) {
							echo '<td align="center">';
							if ($matches->pgn == '' OR !$this->pgnShow) {
								echo CLMText::getResultString($matches->ergebnis);
							} else {
								echo '<span class="editlinktip hasTip" title="'.JText::_( 'PGN_SHOWMATCH' ).'">';
									echo '<a onclick="startPgnMatch('.$matches->id.', \'pgnArea'.$value->nr.'\');" class="pgn">'.CLMText::getResultString($matches->ergebnis).'</a>';
								echo '</span>';
								?>
								<input type='hidden' name='pgn[<?php echo $matches->id; ?>]' id='pgnhidden<?php echo $matches->id; ?>' value='<?php echo $matches->pgn; ?>'>
								<?php
							}
							
							// echo CLMText::getResultString($matches->ergebnis);
							if ($this->turnier->typ == 3 AND ($matches->tiebrS > 0 OR $matches->tiebrG > 0)) {
								echo '<br /><small>'.$matches->tiebrS.':'.$matches->tiebrG.'</small>';
							}
							echo '</td>';
						} else {
							echo '<td align="center"></td>';
						}
					echo '</tr>';
				}
				
			}
			
			// tl_ok? Haken anzeigen!
			if ($this->displayTlOK AND $value->tl_ok > 0) {
				echo '<tr><td colspan="9">';
					echo '<div style="float:right; padding-right:1%;"><label for="name" class="hasTip" title="'.JText::_('TOURNAMENT_ROUNDOK').'"><img  src="'.CLMImage::imageURL('accept.png').'" /></label></div>';
				echo '</td></tr>';
			}
			
			
			echo '</table>';
		
			// Bereich für pgn-Viewer
			echo '<span id="pgnArea'.$value->nr.'"></span>';
		
			echo '<br>';
		
		} else {
			echo '<table cellpadding="0" cellspacing="0" class="runde">';
			echo '<tr><td colspan="9"><div style="text-align:left; padding-left:1%"><b>'.$value->name.'</b>&nbsp;&nbsp;&nbsp;</div></tr>';
			echo '<tr><td><font color="#ff0000">'.JText::_('TOURNAMENT_ROUNDNOTPUBLISHED').'</font></td></tr>';
			echo '</table><br>';
		}
	
	}
	

}

	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 

echo '</div></div>';
?>