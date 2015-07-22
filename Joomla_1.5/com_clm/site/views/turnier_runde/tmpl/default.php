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
$turnierid		= JRequest::getInt('turnier','1');
$itemid = JRequest::getVar( 'Itemid' );
$config	= &JComponentHelper::getParams( 'com_clm' );
$pdf_melde = $config->get('pdf_meldelisten',1);

echo "<div id='clm'><div id='turnier_runde'>";


// componentheading vorbereiten
$heading = $this->turnier->name;

?>
<!--neue Ausgabe: pdf-Liste -->
<div class="pdf"><a href="index.php?option=com_clm&amp;view=turnier_runde&amp;format=clm_pdf&amp;layout=runde&amp;turnier=<?php echo $turnierid;?>&amp;runde=<?php echo $this->round->nr;?>&amp;Itemid=99"><img src="<?php echo $imageDir.'pdf_button.png'; ?>" title="<?php echo JText::_('TOURNAMENT_ROUND_PRINT'); ?>"  class="CLMTooltip" /></a></div>
<?php 
	
// Turnier unveröffentlicht?
if ( $this->turnier->published == 0) { 

	echo CLMContent::componentheading($heading);
	
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

// Runden nicht erstellt
} elseif ($this->turnier->rnd == 0) {

	echo CLMContent::componentheading($heading);
	
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOROUNDS'));

} elseif ($this->round->published != 1) {

	echo CLMContent::componentheading($heading);
	
	echo CLMContent::clmWarning(JText::_('TOURNAMENT_ROUNDNOTPUBLISHED'));

// Turnier/Runde kann ausgegeben werden
} else {


	$turParams = new JParameter($this->turnier->params);

	$heading .= ": ".JText::_('TOURNAMENT_ROUND')." ".$this->round->nr;

	if ($this->round->datum != "0000-00-00" AND $turParams->get('displayRoundDate', 1) == 1) {
		$heading .=  ',&nbsp;'.JHTML::_('date',  $this->round->datum, JText::_('%d. %B %Y')); 
	}

	if ($this->turnier->dg > 1 AND $this->round->dg == 1) { 
		$heading .=  "&nbsp;(".JText::_('TOURNAMENT_STAGE_1').")";
	}

	if ($this->turnier->dg > 1 AND $this->round->dg == 2) { 
		$heading .=  "&nbsp;(".JText::_('TOURNAMENT_STAGE_2').")";
	}

	echo CLMContent::componentheading($heading);
	
	// Navigationsmenu
	require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');

	// Table aufziehen
	echo '<table cellpadding="0" cellspacing="0" class="runde">';

	// Kopfzeile
	echo '<tr><td colspan="9">';
		echo '<div style="text-align:left; padding-left:1%">';
			echo '<b>'.$this->round->name.'</b>';
		echo '</div>';
	echo '</td></tr>';
	// Ende Kopfzeile


	// headers
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
	
	// alle Matches durchgehen

	foreach ($this->matches as $value) {
	
		// Farbe

		if ($value->brett%2 != 0) { 
			$zeilenr = "zeile1"; 
		} else { 
			$zeilenr = "zeile2"; 
		}

		if ( ($value->spieler != 0 AND $value->gegner != 0) OR $value->ergebnis != NULL) {

			echo '<tr class="'.$zeilenr.'">';
				echo '<td>';
				if (isset($value->wname)) {
					$link = new CLMcLink();
					$link->view = 'turnier_player';
					$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->spieler, 'Itemid' => $itemid);
					$link->makeURL();
					echo $link->makeLink($value->wname);
				}
				echo '</td>';
				echo '<td align="center">'.CLMText::formatRating($value->wtwz).'</td>';
				echo '<td align="center">-</td>';
				echo '<td>';
				if (isset($value->sname)) {
					$link = new CLMcLink();
					$link->view = 'turnier_player';
					$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->gegner, 'Itemid' => $itemid);
					$link->makeURL();
					echo $link->makeLink($value->sname);
				}
				echo '</td>';
				echo '<td align="center">'.CLMText::formatRating($value->stwz).'</td>';
				if ($value->ergebnis != NULL) {
					echo '<td align="center">';
					if ($value->pgn == '' OR !$this->pgnShow) {
						echo CLMText::getResultString($value->ergebnis);
					} else {
						echo '<span class="editlinktip hasTip" title="'.JText::_( 'PGN_SHOWMATCH' ).'">';
							echo '<a onclick="startPgnMatch('.$value->id.', \'pgnArea\');" class="pgn">'.CLMText::getResultString($value->ergebnis).'</a>';
						echo '</span>';
						?>
						<input type='hidden' name='pgn[<?php echo $value->id; ?>]' id='pgnhidden<?php echo $value->id; ?>' value='<?php echo $value->pgn; ?>'>
						<?php
					}
					if ($this->turnier->typ == 3 AND ($value->tiebrS > 0 OR $value->tiebrG > 0)) {
						echo '<br /><small>'.$value->tiebrS.':'.$value->tiebrG.'</small>';
					}
					echo '</td>';
					?>
					<?php
				} else {
					echo '<td align="center"></td>';
				}
				
			echo '</tr>';
		}
	
	}
	
			// tl_ok? Haken anzeigen!
	if ($this->displayTlOK AND $this->round->tl_ok > 0) {
		echo '<tr><td colspan="9">';
			echo '<div style="float:right; padding-right:1%;"><label for="name" class="hasTip" title="'.JText::_('TOURNAMENT_ROUNDOK').'"><img  src="'.CLMImage::imageURL('accept.png').'" /></label></div>';
		echo '</td></tr>';
	}

	echo '</table>';

	?>
	
	<!--Bereich für pgn-Viewer-->
	<span id="pgnArea"></span>

	<?php

	if ($this->round->bemerkungen != '') {
		echo "<div id='desc'>";
		echo CLMText::formatNote($this->round->bemerkungen);
		echo "</div>";
	}
}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
	
?>