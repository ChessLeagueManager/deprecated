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

?>
<form action="index.php" method="post" name="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->form['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_vid').value='0';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
					echo "&nbsp;&nbsp;&nbsp;".CLMForm::selectVerein('filter_vid', $this->param['vid'], TRUE);
				?>
			</td>
		</tr>
		</table>

			<table class="adminlist">
			<thead>
				<tr>
					<th width="10">
						<?php echo JText::_( 'NUM' ); ?>
					</th>
					<th width="10">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->turplayers ); ?>);" />
					</th>
					<?php
					if ($this->turnier->typ != 3) { 
					?>
						<th width="3%">
							<?php echo JHTML::_('grid.sort', JText::_('RANKING_POS_ABB'), 'rankingPos', $this->param['order_Dir'], $this->param['order'] ); ?>
						</th>
					<?php
					}
					?>
					<th width="3%">
						<?php echo JHTML::_('grid.sort', JText::_('CLM_NUMBER_ABB'), 'snr', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo JHTML::_('grid.sort', JText::_('PLAYER_TITLE'), 'titel', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_('NAME'), 'name', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="30%">
						<?php echo JHTML::_('grid.sort', JText::_('CLUB'), 'verein', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo JHTML::_('grid.sort', JText::_('FEDERATION'), 'twz', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo JHTML::_('grid.sort', JText::_('TWZ'), 'twz', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo JHTML::_('grid.sort', JText::_('RATING'), 'NATrating', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					<th width="5%">
						<?php echo JHTML::_('grid.sort', JText::_('FIDE_ELO'), 'FIDEelo', $this->param['order_Dir'], $this->param['order'] ); ?>
					</th>
					
					<th width="5%">
						<?php 
						if ($this->turnier->typ != 3) { // Punkte
							echo JHTML::_('grid.sort', JText::_('POINTS'), 'sum_punkte', $this->param['order_Dir'], $this->param['order'] ); 
						} else {
							echo JHTML::_('grid.sort', JText::_('TOURNAMENT_SUCCESS'), 'koRound', $this->param['order_Dir'], $this->param['order'] ); 
						}
						?>
					</th>
					
					<?php
						// nicht KO
						if ($this->turnier->typ != 3) { 
							// Feinwertungen
							// alle Feinwertungen durchgehen
							for ($f=1; $f<=3; $f++) {
								$fieldName = 'tiebr'.$f;
								if ($this->turnier->$fieldName != 0) {
									echo '<th width="5%">';
									echo JText::_('TIEBR_'.$this->turnier->$fieldName);
									echo '</th>';
								}
							}
						}
					?>
					
					<th width="8%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('ORDER'), 'ordering', $this->param['order_Dir'], $this->param['order'] ); ?>
						<?php echo JHTML::_('grid.order', $this->turplayers ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="17">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			
			$n=count( $this->turplayers );
			foreach ($this->turplayers as $i => $value) {
				$row = &$value;
			// for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				// $row = &$rows[$i];
				
				$link 		= JRoute::_( 'index.php?option=com_clm&section=t&task=edit&cid[]='. $row->id );
				$checked 	= JHTML::_('grid.checkedout',   $row, $i );
				$published 	= JHTML::_('grid.published', $row, $i );
				?>
				<tr class="<?php echo 'row'. $k; ?>">
					<td align="center">
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<?php
					if ($this->turnier->typ != 3) { 
					?>
						<td align="center">
							<b>
							<?php
							if ($row->rankingPos > 0) {
								echo $row->rankingPos.'.';
							} else {
								echo '';
							}
							?>
							</b>
						</td>
					<?php
					}
					?>
					<td align="center">
						<?php echo $row->snr;?>
					</td>
					<td align="center">
						<?php echo $row->titel;?>
					</td>
					<td align="left">
						<?php 
						
						// admin/tl kann Spieler editieren
						if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
							$adminLink = new AdminLink();
							$adminLink->view = "turplayeredit";
							$adminLink->more = array('playerid' => $row->id);
							$adminLink->makeURL();
							?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT' );?>">
								<a href="<?php echo $adminLink->url; ?>">
									<?php echo $row->name; ?>
								</a>
							</span>
						<?php
						} else {
							echo $row->name;
						}
						?>
					</td>
					<td align="left">
						<?php echo $row->verein; ?>
					</td>
					<td align="center">
						<?php echo $row->FIDEcco; ?>
					</td>
					
					<td align="center">
						<b>
						<?php 
						if ($row->twz > 0) {
							echo $row->twz;
						} else {
							echo '-';
						}
						?>
						</b>
					</td>
					<td align="center">
						<?php 
						if ($row->NATrating > 0) {
							echo $row->NATrating;
						} else {
							echo '-';
						}
						?>
					</td>
					<td align="center">
						<?php 
						if ($row->FIDEelo > 0) {
							echo $row->FIDEelo;
						} else {
							echo '-';
						}
						?>
					</td>
					
						<?php 
						if ($this->turnier->typ != 3) { // Punkte
							echo '<td align="right"><b>';
							echo $row->sum_punkte; 
						} else {
							echo '<td align="center"><b>';
							echo JText::_('TOURNAMENT_KOSTATUS_'.$row->koStatus);
							echo '<br />';
							if ($row->koRound != 0) {
								echo JText::_('ROUND_KO_'.$row->koRound);
							}
						}	
						?>
						</b>
					</td>
					<?php
						// nicht KO
						if ($this->turnier->typ != 3) { 
							// alle Feinwertungen durchgehen
							for ($f=1; $f<=3; $f++) {
								$fieldName = 'tiebr'.$f;
								if ($this->turnier->$fieldName != 0) {
									$sumFieldname = 'sumTiebr'.$f;
									echo '<td width="5%" align="right">';
									echo CLMText::tiebrFormat($this->turnier->$fieldName, $row->$sumFieldname);
									echo '</td>';
								}
							}
						}
						
					?>
					
					<td class="order" width="10%">
						<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup()', 'Move Up', $this->param['order'] ); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdown()', 'Move Down', $this->param['order'] ); ?></span>
						<?php $disabled = $this->param['order'] ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
					</td>

				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>

		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="view" value="turplayers" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="turplayers" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->param['id']; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>

</form>
