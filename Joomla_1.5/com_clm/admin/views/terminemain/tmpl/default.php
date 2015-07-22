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

	//Ordering allowed ?
	$ordering = ($this->param['order'] == 'ordering');

?>
<form action="index.php" method="post" name="adminForm">

	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->param['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
					echo "&nbsp;&nbsp;&nbsp;".JHTML::_('grid.state',  $this->param['state'] );
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
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->termine ); ?>);" />
				</th>
				<th width="6%">
					<?php echo JHTML::_('grid.sort', JText::_('TERMINE_DATUM'), 'a.startdate', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('TERMINE_TASK'), 'a.name', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort', JText::_('TERMINE_DESCRIPTION'), 'a.beschreibung', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>

				<th>
					<?php echo JHTML::_('grid.sort', JText::_('TERMINE_EVENT_LINK'), 'a.event_link', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>

				<th>
					<?php echo JHTML::_('grid.sort', JText::_('TERMINE_ADRESS'), 'a.address', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('grid.sort', JText::_('TERMINE_KATEGORIE'), 'category', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				<th width="16%">
					<?php echo JHTML::_('grid.sort', JText::_('TERMINE_HOST'), 'hostname', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>

				<th width="3%">
					<?php echo JHTML::_('grid.sort', JText::_('TERMINE_PUBLISHED'), 'a.published', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
			</tr>
		</thead>
			
		<tbody>
		<?php
		
		$k = 0;
		
		$n=count( $this->termine );
		foreach ($this->termine as $i => $value) {
			$row = &$value;
			
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
                
				<td align="left">
					<?php echo JHTML::_( 'date', $row->startdate, JText::_('DATE_FORMAT_CLM'));?>
					<?php if ($row->enddate != 0) { echo "&nbsp;-&nbsp;". JHTML::_( 'date', $row->enddate, JText::_('DATE_FORMAT_CLM')); }?>
				</td>

				<td>
					<?php
					if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) ) {
						echo $row->name;
					} else {
						$adminLink = new AdminLink();
						$adminLink->view = "termineform";
						$adminLink->more = array('task' => 'edit', 'id' => $row->id);
						$adminLink->makeURL();
					
						?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT' );?>::<?php echo $row->name; ?>">
							<a href="<?php echo $adminLink->url; ?>">
								<?php echo $row->name; ?>
							</a>
						</span>
						<?php
					}
					?>
				</td>
				<td align="justify">
					<?php echo $row->beschreibung;?>
				</td>
				<td align="center">
					<a href="<?php echo $row->event_link; ?>" target="_blank"><?php echo $row->event_link; ?></a>
				</td>
				
				<td align="center">
					<?php echo $row->address;?>
				</td>
				
				<td align="center">
					<?php echo $row->category;?>
				</td>
				<td align="center">
					<?php echo $row->hostname;?>
				</td>
				<td align="center">
					<?php echo $published;?>
				</td>
				
				<td align="center">
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
			
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
			
	</table>

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="terminemain" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="terminemain" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->param['id']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>
