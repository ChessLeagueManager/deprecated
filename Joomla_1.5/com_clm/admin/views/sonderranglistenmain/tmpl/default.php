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
	<tbody>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td><?php echo ($this->lists['filter_saison']); ?>
			</td>
			<td><?php echo ($this->lists['filter_turnier']); ?>
			</td>
		</tr>
	</tbody>
</table>
<?php if( !$this->turnierExists ) {
	JError::raiseWarning( 500, $row->name.": ".JText::_( 'SPECIALRANKINGS_WARNING_NO_TOURNEMENT' ) );
} ?>
<div id="editcell"> 
	<table class="adminlist">
		<thead>
			<tr>
				<th width="10"><?php echo JText::_( 'NUM' ); ?></th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->sonderranglisten ); ?>);" />
				</th>
				<th width="" class="title"><?php echo JHTML::_('grid.sort',   JText::_('SPECIALRANKINGS_NAME'), 'name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="" class="title"><?php echo JHTML::_('grid.sort',   JText::_('SPECIALRANKINGS_TOURNEMENT_NAME'), 'turnier', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="20"><?php echo JHTML::_('grid.sort',   JText::_('CLM_PUBLISHED'), 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th width="100" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   JText::_('ORDER'), 'ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					<?php echo JHTML::_('grid.order',  $this->sonderranglisten ); ?>
				</th>
				<th width="10" nowrap="nowrap"><?php echo JHTML::_('grid.sort',   'ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$n=count( $this->sonderranglisten );
		$disabled = $this->ordering ?  '' : 'disabled="disabled"';
		foreach ($this->sonderranglisten as $i => $value) {
			$row = &$value;
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$published 	= JHTML::_('grid.published', $row, $i );
			?>
			<tr class="<?php echo 'row'. $k; ?>">
				<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
				<td align="center"><?php echo $checked; ?></td>
				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) ) {
						echo $row->name;
					} else {
						
						$adminLink = new AdminLink();
						$adminLink->view = "sonderranglistenform";
						$adminLink->more = array('task' => 'edit', 'layout' => 'form', 'hidemainmenu' => 1, 'cid' => $row->id);
						$adminLink->makeURL();
					
						?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'SPECIALRANKINGS_EDIT' );?>::<?php echo $row->name; ?>">
							<a href="<?php echo $adminLink->url; ?>">
								<?php echo $row->name; ?>
							</a>
						</span>
						<?php 
					} 
					?>				
				</td>
				<td>
					<?php
					
					// Abfrage, ob Turnier ausgecheckt ist, fehlt noch.
					
					/*if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) ) {
						echo $row->name;
					} else {*/
					
						
						$adminLink = new AdminLink();
						$adminLink->view = "turform";
						$adminLink->more = array('task' => 'edit', 'id' => $row->turnier);
						$adminLink->makeURL();
					
						?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'SPECIALRANKINGS_TOURNAMENT_EDIT' );?>::<?php echo $row->turniername; ?>">
							<a href="<?php echo $adminLink->url; ?>">
								<?php echo $row->turniername; ?>
							</a>
						</span>
						<?php 
					//} 
					?>				
				</td>
				<td align="center"><?php echo $published;?></td>
				<td class="order" align="center">
					<div style="float:left; width:20px;"><?php echo $this->pagination->orderUpIcon($i, (isset($this->sonderranglisten[$i-1]->turnier) AND $row->turnier == $this->sonderranglisten[$i-1]->turnier), 'orderup', 'Move Up', $this->ordering); ?></div>
					<div style="float:left; width:20px;"><?php echo $this->pagination->orderDownIcon($i, $n, (isset($this->sonderranglisten[$i+1]->turnier) AND $row->turnier == $this->sonderranglisten[$i+1]->turnier), 'orderdown', 'Move Down', $this->ordering); ?></div>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align:center;" />
				</td>
				<td align="center"><?php echo $row->id; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>
</div> 

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="sonderranglistenmain" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order'] ; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir'] ; ?>" />
	<input type="hidden" name="controller" value="sonderranglistenmain" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>
