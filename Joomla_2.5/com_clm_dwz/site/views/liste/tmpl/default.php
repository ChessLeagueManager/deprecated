<?php
/**
*	name					default.php
*	description			Template Default für liste
*
*	start					30.11.2010
*	last edit			12.12.2010
*	done					Umbau auf $this-Date; Einbau order
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
?>

<script type="text/javascript">

function tableOrdering( order, dir, task ) {
	var form = document.siteForm;

	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.siteForm.submit( task );
}
</script>

<div id="dwz_liste">
<div id="dwzansicht">

<form action="index.php" method="post" name="siteForm">

<?php

	
	$listheading = $this->params->get('listheading', '1');
	if ($listheading != "") {
		echo '<div class="componentheading">'.str_replace('{datum}', $this->date, $listheading).'</div>';
	}
	
	$offercolorder = $this->params->get('offercolorder', 0);
	
	if (count($this->data) > 0) {
	
		echo '<table width="100%" class="'.$this->params->get('classtable', '').'">';
		
		// Headers
		echo '<tr>';
			
			// Rank
			$showrank = $this->params->get('showrank', 1);
			if ($showrank == 1) {
				echo '<th align="center">'.JText::_('NUMBER_ABB').'</th>';
			}
			
			// Titel
			$showtitle = $this->params->get('showtitle', 1);
			if ($showtitle == 1) {
				echo '<th align="center">'.JText::_('FIDE_TITLE').'</th>';
			}
		
			// Name
			echo '<th align="center">';
			if ($offercolorder == 0) {
				echo JText::_('PLAYERNAME');
			} else {
				echo JHTML::_('grid.sort', JText::_('PLAYERNAME'), 'name', $this->order['dir'], $this->order['by']);
			}
			echo '</th>';
			
			// Geburtsjahr
			$showbirthyear = $this->params->get('showbirthyear', 0);
			if ($showbirthyear == 1) {
				echo '<th align="center">';
				if ($offercolorder == 0) {
					echo JText::_('YEAROFBIRTH');
				} else {
					echo JHTML::_('grid.sort', JText::_('YEAROFBIRTH'), 'year', $this->order['dir'], $this->order['by']);
				}
				echo '</th>';
			}
			
			// Geschlecht	
			$showsex = $this->params->get('showsex', 2);
			if ($showsex >= 1) {
				echo '<th align="center">';
				if ($offercolorder == 0) {
					echo JText::_('MASC_FEM');
				} else {
					echo JHTML::_('grid.sort', JText::_('MASC_FEM'), 'mascfem', $this->order['dir'], $this->order['by']);
				}
				echo '</th>';
			}
		
			// letzte Auswertung
			$showevaluation = $this->params->get('showevaluation', 1);
			if ($showevaluation == 1) {
				echo '<th align="center">';
				if ($offercolorder == 0) {
					echo JText::_('EVALUATION');
				} else {
					echo JHTML::_('grid.sort', JText::_('EVALUATION'), 'eval', $this->order['dir'], $this->order['by']);
				}
				echo '</th>';
			}
			
			// DWZ
			echo '<th align="center" colspan="3">';
			if ($offercolorder == 0) {
				echo JText::_('DWZ');
			} else {
				echo JHTML::_('grid.sort', JText::_('DWZ'), 'dwz', $this->order['dir'], $this->order['by']);
			}
			echo '</th>';
		
			// ELO
			$showelo = $this->params->get('showelo', 1);
			if ($showelo == 1) {
				echo '<th align="center">';
				if ($offercolorder == 0) {
					echo JText::_('FIDE_ELO');
				} else {
					echo JHTML::_('grid.sort', JText::_('FIDE_ELO'), 'elo', $this->order['dir'], $this->order['by']);
				}
				echo '</th>';
			}
			
			// Status
			$showstatus = $this->params->get('showstatus', 1);
			if ($showstatus == 1) {
				echo '<th align="center">';
				if ($offercolorder == 0) {
					echo JText::_('STATUS');
				} else {
					echo JHTML::_('grid.sort', JText::_('STATUS'), 'status', $this->order['dir'], $this->order['by']);
				}
				echo '</th>';
			}
		
		
		echo '</tr>';
		
		$classrow1 = $this->params->get('classrow1', '');
		$classrow2 = $this->params->get('classrow2', '');
		
		
		$counter = 0;
		
		foreach ($this->data as $key => $value) {
			$counter++;	
		
			if ($counter%2 == 0) { // gerade 
				$class = $classrow1; 
			} else { 
				if ($classrow2 != '') {
					$class = $classrow2; 
				} else {
					$class = $classrow1;
				}
			}
			echo '<tr class='.$class.'>';
				
				// Rank
				if ($showrank == 1) {
					echo '<td align="right">'.$counter.'</td>';
				}
				
				// Titel
				if ($showtitle == 1) {
					echo '<td align="center">'.$value[6].'</td>';
				}
				
				// Name
				echo '<td align="left">';
				echo '<a href="'.JRoute::_('index.php?option=com_dwzliste&view=spieler&zps='.$this->params->get('zps', '22059').'&mglnr='.$value[1]).'">';
				echo DWZText::formatName($value[3], $this->params->get('formatname', 0));
				echo '</a></td>';
			
				if ($showbirthyear == 1) {
					echo '<td align="center">'.$value[5].'</td>';
				}
			
				// Geschlecht
				if ($showsex == 1) { // Spalte komplett
					echo '<td align="center">'.strtolower($value[4]).'</td>';
				} elseif ($showsex == 2) { // nur 'w'
					echo '<td align="center">';
					if ($value[4] == "W") echo JText::_('ONLY_FEM');
					echo '</td>';
				}
			
				// letzte Auswertung
				if ($showevaluation == 1) {
					echo '<td align="center">'.DWZText::formatLastEvaluation($value[7]).'</td>';
				}
			
				// DWZ & Auswertungen - in 3 Spalten
				if (is_numeric($value[8])) { // Zahl
					// DWZ
					echo '<td align="right">'.$value[8].'</td>';
					// spacer
					echo '<td align="center">-</td>';
					// Auswertungen
					echo '<td align="right">'.$value[9].'</td>';
				} elseif ($value[8] == "Restpartien") {
					echo '<td align="center" colspan="3">'.JText::_('LEFTOVER_GAMES').'</td>';
				} else {
					echo '<td align="center" colspan="3">- - -</td>';
				}
				
				
				// ELO
				if ($showelo == 1) {
					if (is_numeric($value[10])) {
						echo '<td align="center">'.$value[10].'</td>';
					} else {
						echo '<td align="center">- - -</td>';
					}
				}
				
				// Status
				if ($showstatus == 1) {
					echo '<td align="center">'.$value[2].'</td>';
				}
				
			
			echo '</tr>';
		
		}
			
		echo '</table><br />';
		
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
		
	}
		
	echo '<div id="cop">';
	DWZText::printCreatedBy();
	echo '</div>';
		
	?>

	<input type="hidden" name="option" value="com_dwzliste" />
	<input type="hidden" name="view" value="liste" />
	<input type="hidden" name="layout" value="default" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order['by']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->order['dir']; ?>" />


</form>

</div>
</div>


