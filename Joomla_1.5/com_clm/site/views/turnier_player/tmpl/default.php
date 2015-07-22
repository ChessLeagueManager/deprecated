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


// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

	
// Konfigurationsparameter auslesen
$itemid 		= JRequest::getVar( 'Itemid' );
$config	= &JComponentHelper::getParams( 'com_clm' );
$pdf_melde = $config->get('pdf_meldelisten',1);
$turParams = new JParameter($this->turnier->params);
	
// CLM-Container
echo '<div id="clm"><div id="turnier_player">';
	
	
// Componentheading
$heading = $this->turnier->name.": ".JText::_('TOURNAMENT_PARTICIPANTINFO');
echo CLMContent::componentheading($heading);

// Navigationsmenu
require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu_t.php');


if ( $this->turnier->published == 0) { 

	echo CLMContent::clmWarning(JText::_('TOURNAMENT_NOTPUBLISHED')."<br/>".JText::_('TOURNAMENT_PATIENCE'));

} else {

	?>
    
	<?php if ($this->playerPhoto != '') { ?>
	<div class="tp_col_playerPhoto"><img src="<?php echo JRoute::_('index.php?view=image&format=raw&type=img&id='.$this->playerPhoto.'&option=com_joomgallery' ,TRUE,-1); ?>"></div>
	<?php } ?>
    
	<div <?php if ($this->playerPhoto != '') { ?> class="tp_col_playerInfo_small" <?php } else { ?> class="tp_col_playerInfo_large" <?php } ?>>
	<table cellpadding="0" cellspacing="0" class="turnier_rangliste">
    
			<tr>
				<th align="left" colspan="2" class="anfang"><?php echo JText::_('TOURNAMENT_PLAYERDATA'); ?></th>
			</tr>
			
			<?php
			if ($this->player->titel != '') {
			?>
				<tr>
					<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_TITLE') ?>:</td>
					<td><?php echo $this->player->titel ?></td>
				</tr>
			<?php
			}
			?>
			
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_PLAYERNAME') ?>:</td>
				<td><?php echo $this->player->name ?></td>
			</tr>
			
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_CLUB') ?>:</td>
				<td>
					<?php 
					if ($this->tourn_linkclub == 1) {
						$link = new CLMcLink();
						$link->view = 'verein';
						$link->more = array('saison' => $this->player->sid, 'zps' => $this->player->zps, 'Itemid' => $itemid );
						$link->makeURL();
						echo $link->makeLink($this->player->verein);
					} else {
						echo $this->player->verein;
					}
					?>
				</td>
			</tr>
			
			<?php if ($turParams->get('playerViewDisplaySex',0)  == 1  AND $this->player->geschlecht != '') { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_PLAYER_SEX') ?>:</td>
				<td><?php echo $this->player->geschlecht; ?></td>
			</tr>
			<?php } ?>
			<?php if ($turParams->get('playerViewDisplayBirthYear',0) == 1  AND $this->player->birthYear != '0000') { ?>
			<tr>
				<td align="left" class="tp_col_1"><?php echo JText::_('TOURNAMENT_PLAYER_BIRTH_YEAR') ?>:</td>
				<td><?php echo $this->player->birthYear; ?></td>
			</tr>
			<?php } ?>
			</table>
	
	<div class="clear"></div>
	<?php
	if (count($this->matches) > 0) {
	?>
	
		<table cellpadding="0" cellspacing="0" class="turnier_rangliste">
		
		<tr>
			<th align="left" colspan="5" class="anfang"><?php echo JText::_('TOURNAMENT_MATCHES'); ?></th>
		</tr>
		
		<?php
		// alle Matches...
		foreach ($this->matches as $value) {
			
			// Zeile
			echo '<tr>';
				
				// Runde
				echo '<td class="tp_col_1">';
					$link = new CLMcLink();
					$link->view = 'turnier_runde';
					$link->more = array('turnier' => $this->turnier->id, 'runde' => $value->runde, 'Itemid' => $itemid );
					$link->makeURL();
					echo $link->makeLink($value->roundName);
				echo "</td>";
				
				if ($value->ergebnis != 8) { // kein spielfrei eingetragen
				
					// Farbe
					echo '<td class="tp_col_2">';
					if ($value->heim == 1) {
						echo JText::_('TOURNAMENT_WHITE_ABB');
					} else {
						echo JText::_('TOURNAMENT_BLACK_ABB');
					}
					echo "</td>";
					
					// Gegner
					echo '<td class="tp_col_3">';
						$link = new CLMcLink();
						$link->view = 'turnier_player';
						$link->more = array('turnier' => $this->turnier->id, 'snr' => $value->gegner, 'Itemid' => $itemid );
						$link->makeURL();
						echo $link->makeLink($value->oppName);
					echo "</td>";
					
					// TWZ
					echo '<td class="tp_col_4">'.CLMText::formatRating($value->oppTWZ)."</td>";
					
					// Ergebnis
					if ($value->ergebnis != '') {
						echo '<td class="tp_col_5">';
						if ($value->pgn == '' OR !$this->pgnShow) {
							echo CLMText::getResultString($value->ergebnis, 0);
						} else {
							echo '<span class="editlinktip hasTip" title="'.JText::_( 'PGN_SHOWMATCH' ).'">';
								echo '<a onclick="startPgnMatch('.$value->id.', \'pgnArea\');" class="pgn">'.CLMText::getResultString($value->ergebnis, 0).'</a>';
							echo '</span>';
							?>
							<input type='hidden' name='pgn[<?php echo $value->id; ?>]' id='pgnhidden<?php echo $value->id; ?>' value='<?php echo $value->pgn; ?>'>
							<?php
						}
						
						if ($this->turnier->typ == 3 AND ($value->tiebrS > 0 OR $value->tiebrG > 0)) {
							echo '<br /><small>'.$value->tiebrS.':'.$value->tiebrG.'</small>';
						}
						echo '</td>';
					} else {
						echo '<td class="tp_col_5">'.JText::_('TOURNAMENT_MATCHNOTYETPLAYED')."</td>";
					}
			
				} else { // spielfrei
					echo '<td class="tp_col_2">-</td><td class="tp_col_3">-</td><td class="tp_col_4">-</td><td class="tp_col_5">'.JText::_('RESULT_BYE').'</td>';
				}
			
			echo '</tr>';
			// Ende der Zeile
		
		}
		
		// Abschlußzeile
		echo '<tr class="ende">';
			// total
			echo '<td  colspan="3">&nbsp;&nbsp;&nbsp;';
				echo JText::_('TEAM_TOTAL');
			echo '</td>';
			// TWZ-Schnitt
			echo '<td class="tp_col_4">';
				if ($this->player->countTWZplayers > 0) {
					echo '&Oslash;&nbsp;'.floor($this->player->sumTWZ/$this->player->countTWZplayers);
					if ($this->player->countTWZplayersNone > 0) {
						echo '<br />('.CLMText::sgpl($this->player->countTWZplayers, JText::_('TOURNAMENT_PLAYER'), JText::_('TOURNAMENT_PLAYERS'), $complete_string = TRUE).')';
					}
				} else {
					echo '-';
				}
			echo '</td>';
			// Pkt
			echo '<td class="tp_col_5">';
				echo $this->player->sum_punkte." / ".$this->player->countMatchesPlayed;
				echo '<br />'.CLMText::getPosString($this->player->rankingPos, 2);
			echo '</td>';
		
		
		
		
		echo '</tr>';
		
		?>
		</table>

		<!--Bereich für pgn-Viewer-->
		<span id="pgnArea"></span>

		</td>
	</tr>
	</table>

	<?php	
	} ?>
	</div>
<?php	
}

require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
echo '</div></div>';
?>