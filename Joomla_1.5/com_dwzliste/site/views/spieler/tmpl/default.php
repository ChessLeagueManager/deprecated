<?php
/**
*	name					default.php
*	description			Template Default für spieler
*
*	start					01.12.2010
*	last edit			05.12.2010
*	done					printCreatedBy
*
*	complete				no
*	todo					-
*	wanted				-
*	notes					-
*
*	author				Helge Frowein
*	(c)					2010 VTT Champions
*/
defined('_JEXEC') or die('Restricted access');

// Daten vorhanden?
if (count($this->rows) > 0) {

	$playerheading = $this->params->get('playerheading', '');
	if ($playerheading != "") {
		// Spielernamen
		$string = $playerheading;
		$string = str_replace('{name}', DWZText::formatName($this->playerData[3], $this->params->get('formatname', 0)), $string);
		$string = str_replace('{datum}', $this->date, $string);
		echo '<div id="dwz_liste"><div id="spieler">';
		echo '<div class="componentheading">'.$string.'</div>';
	}
	
	// Spieler-Info
	echo '<div class="normal"><table width="100%" class="'.$this->params->get('classtable', '').'">';
		
		// playerData
		if ($this->params->get('showrowname', 1) == 1) {
			echo '<tr>';
				echo '<th width="50%">'.JText::_('DB_PLAYERNAME').' ('.JText::_('PLAYER_SEX').'):</th>';
				echo '<td>'.DWZText::formatName($this->playerData[3], $this->params->get('formatname', 0)).'&nbsp;('.strtolower($this->playerData[4]).')</td>';
			echo '</tr>';
		}
	
		// DWZ
		echo '<tr>';
			// DWZ und Index
			echo '<th>'.JText::_('DWZ_CURRENT').' ('.JText::_('EVALUATION_LAST').'):</th>';
			echo '<td>'.$this->playerData[8].'&nbsp;-&nbsp;'.$this->playerData[9].'&nbsp;('.DWZText::formatLastEvaluation($this->playerData[7]).')</td>';
		echo '</tr>';
	
		// ELO
		if ($this->params->get('showrowelo', 1) == 1) {
			echo '<tr>';
				echo '<th>'.JText::_('FIDE_ELO').', '.JText::_('FIDE_TITLE').':</th>';
				echo '<td>';
				if ($this->fideData[0] != 0) {
					echo $this->fideData[0];
				} else {
					echo '-';
				}
				if ($this->fideData[2] != '') {
					echo ",&nbsp;".$this->fideData[2];
				}
				echo '</td>';
			echo '</tr>';
		}
	
	
	echo '</table></div><br />';
	
	
	// Liste
	echo '<table width="100%" class="'.$this->params->get('classtable', '').'">';
	
	// Headers
	echo '<tr>';
		
		// Rank
		$showentynum = $this->params->get('showentrynum', 1);
		if ($showentynum == 1) {
			echo '<th align="center"> </th>';
		}
		
		// Turniername
		echo '<th align="center">'.JText::_('TOURNAMENT').'</th>';
		
		// Pkt
		echo '<th align="center">'.JText::_('POINTS').'</th>';
		
		// Partien
		echo '<th align="center">'.JText::_('GAMES').'</th>';
	
		// Erwartung
		$showexpect = $this->params->get('showexpect', 1);
		if ($showexpect == 1) {
			echo '<th align="center">'.JText::_('EXPECTATION').'</th>';
		}
		
		// gegner
		$showopponent = $this->params->get('showopponent', 1);
		if ($showopponent == 1) {
			echo '<th align="center">'.JText::_('OPPONENT').'</th>';
		}
		
		// Leistung
		echo '<th align="center">'.JText::_('PERFORMANCE').'</th>';
	
		// DWZ
		echo '<th align="center" colspan="3">'.JText::_('DWZ').'</th>';
	
	echo '</tr>';
	
	$classrow1 = $this->params->get('classrow1', '');
	$classrow2 = $this->params->get('classrow2', '');
	
	
	
	// alle Einträge durchgehen
	foreach ($this->rows as $key => $value) {
	
		if ($key%2 == 0) { // gerade 
			$class = $classrow1; 
		} else { 
			if ($classrow2 != '') {
				$class = $classrow2; 
			} else {
				$class = $classrow1;
			}
		}
		echo '<tr class='.$class.'>';
	
			// Eintrag Nr.
			if ($showentynum == 1) {
				echo '<td>'.$value[0].'</td>';
			}
			
			// Turniername
			echo '<td>'.utf8_encode($value[2]).'</td>';
			
			// Pkt
			echo '<td align="center">'.$value[3].'</td>';
			
			// Partien			
			echo '<td align="center">'.$value[4].'</td>';
			
			// Erwartung
			if ($showexpect == 1) {
				echo '<td align="center">'.$value[5].'</td>';
			}
			
			// Gegner
			if ($showopponent == 1) {
				echo '<td align="center">'.$value[6].'</td>';
			}
			
			// Leistung
			echo '<td align="center">'.$value[7].'</td>';
			
			// DWZ
			echo '<td align="center">'.$value[8].'</td>';
			echo '<td align="center">-</td>';
			echo '<td align="center">'.$value[9].'</td>';
	
		echo '<tr/>';
	
	}
		
		
	echo '</table><br />';
		
	// DSB-Box
	if ($this->params->get('showlinkdsb', 1) == 1) {
		echo '<div class="normal"><table width="100%" class="'.$this->params->get('classtable', '').'">';
			echo '<tr>';
				echo '<td>'.JText::_('DATA_BY_DSB').'</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>'.JText::_('DWZ_RECORD').': <a href="'.$this->url.'" target="_blank">'.$this->url.'</a></th>';
			echo '</tr>';
		echo '</table></div><br />';
	}
	
	echo '<div id="cop">';
	DWZText::printCreatedBy();
	echo '</div>';
	
		
} else {

	echo JText::_('NO_DATA');

}
		
?>

</div>
</div>



