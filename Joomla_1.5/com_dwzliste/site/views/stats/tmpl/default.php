<?php
/**
*	name					default.php
*	description			Template Default für stats
*
*	start					07.12.2010
*	last edit			07.12.2010
*	done					start
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

	
	$statsheading = $this->params->get('statsheading', '');
	if ($statsheading != "") {
		echo '<div class="componentheading">'.str_replace('{datum}', $this->date, $statsheading).'</div>';
		echo '<div id="dwz_liste"><div id="spieler">';
	}
	
	
	echo '<table width="100%" class="'.$this->params->get('classtable', '').'">';
	
	
	echo '<tr><th colspan="2" align="left">';
	echo JText::_('PLAYERS_ALL');
	if ($this->params->get('includestatusp', 0) == 0) {
		echo '&nbsp;('.JText::_('INC_STATUSP_0').')';
	}
	echo '</th></tr>';
	
	// countPlayersAll
	echo '<tr>';
		echo '<td width="80%">'.JText::_('PLAYERS_LISTED').'</td>';
		echo '<td align="right">'.$this->stats['countPlayersAll'].'</th>';
	echo '</tr>';
	
	// countPlayersDWZ
	echo '<tr>';
		echo '<td>'.JText::_('PLAYERS_DWZ').'</td>';
		echo '<td align="right">'.$this->stats['countPlayersDWZ'].'</th>';
	echo '</tr>';
	
	// DWZAverage
	echo '<tr>';
		echo '<td>'.JText::_('DWZ_AVERAGE').'</td>';
		echo '<td align="right">'.$this->stats['DWZAverage'].'</th>';
	echo '</tr>';
			
	// DWZTop
	echo '<tr>';
		echo '<td>'.JText::_('DWZ_TOP').'</td>';
		echo '<td align="right">'.$this->stats['DWZRangeTop'].'</th>';
	echo '</tr>';
	
	// DWZLow
	echo '<tr>';
		echo '<td>'.JText::_('DWZ_LOW').'</td>';
		echo '<td align="right">'.$this->stats['DWZRangeLow'].'</th>';
	echo '</tr>';
	echo '</table>';
	
	
	// TopIntervals	
	if (count($this->topIntervals) > 0) {
		echo '<table width="100%" class="'.$this->params->get('classtable', '').'">';
		echo '<tr><th colspan="2" align="left">'.JText::_('TOP_INTERVALS').'</th></tr>';
		// TopIntervals
		foreach ($this->topIntervals as $key => $value)	{
			echo '<tr>';
				echo '<td width="80%">Top '.$value['string'].'</td>';
				echo '<td align="right">'.$value['value'].'</th>';
			echo '</tr>';
		}
		echo '</table>';
	}
	
	
	
	// ELO
	if ($this->params->get('showstatselo', 1) == 1) { // anzeigen?
		echo '<table width="100%" class="'.$this->params->get('classtable', '').'">';
		// Header
		echo '<tr><th colspan="2" align="left">'.JText::_('STATS_ELO').'</th></tr>';
		// countPlayersTitle
		echo '<tr>';
			echo '<td width="80%">'.JText::_('PLAYERS_TITLE').'</td>';
			echo '<td align="right">'.$this->stats['countPlayersTitle'].'</th>';
		echo '</tr>';
		// countPlayersELO
		echo '<tr>';
			echo '<td>'.JText::_('PLAYERS_ELO').'</td>';
			echo '<td align="right">'.$this->stats['countPlayersELO'].'</th>';
		echo '</tr>';
		// ELOAverage
		echo '<tr>';
			echo '<td>'.JText::_('ELO_AVERAGE').'</td>';
			echo '<td align="right">'.$this->stats['ELOAverage'].'</th>';
		echo '</tr>';
		// ELOTop
		echo '<tr>';
			echo '<td>'.JText::_('ELO_TOP').'</td>';
			echo '<td align="right">'.$this->stats['ELORangeTop'].'</th>';
		echo '</tr>';
		
		// ELOLow
		echo '<tr>';
			echo '<td>'.JText::_('ELO_LOW').'</td>';
			echo '<td align="right">'.$this->stats['ELORangeLow'].'</th>';
		echo '</tr>';
		echo '</table><br />';
	}
	
		echo '<br>';
	
	// DSB-Box
	if ($this->params->get('showlinkdsb', 1) == 1) {
		echo '<div class="normal"><table width="100%" class="'.$this->params->get('classtable', '').'">';
			echo '<tr>';
				echo '<td>'.JText::_('DATA_BY_DSB').'</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>'.JText::_('DWZ_LISTE').': <a href="'.$this->url.'" target="_blank">'.$this->url.'</a></th>';
			echo '</tr>';
		echo '</table></div><br />';
	}
		
	echo '<div id="cop">';
	DWZText::printCreatedBy();
	echo '</div>';
		
	?>

</div>
</div>


