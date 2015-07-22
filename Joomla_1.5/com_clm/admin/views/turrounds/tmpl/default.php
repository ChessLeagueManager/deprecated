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

	<table class="adminlist">
		
		<thead>
			<tr>
				<th width="10">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="10">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->turrounds ); ?>);" />
				</th>
				<th width="3%">
					<?php echo JHTML::_('grid.sort',   JText::_('ROUND_NR'), 'nr', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JText::_('ROUND'); ?>
				</th>
				
				<th width="10%">
					<?php echo JText::_('DATE'); ?>
				</th>
				
				<th width="10%">
					<?php echo JText::_('MATCH_COUNT'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('MATCHES'); ?>
				</th>
				
				<th width="10%">
					<?php echo JHTML::_('grid.sort',   JText::_('CLM_PUBLISHED'), 'published', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				
				<th width="10%">
					<?php echo JText::_('ENTRY_ENABLED'); ?>
				</th>
				
				<th width="10%">
					<?php echo JHTML::_('grid.sort',   JText::_('TOURNAMENT_DIRECTOR')."<br />".JText::_('APPROVAL'), 'tl_ok', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
				
				<th width="1%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'ID', 'a.id', $this->param['order_Dir'], $this->param['order'] ); ?>
				</th>
			</tr>
		</thead>
		
		<tbody>
		<?php
		$k = 0;
		
		$n=count( $this->turrounds );
		foreach ($this->turrounds as $i => $value) {
			$row = &$value;
			$checked 	= JHTML::_('grid.checkedout',   $row, ($i-1) );
			$published 	= JHTML::_('grid.published', $row, ($i-1) );
			?>
			
			<tr class="<?php echo 'row'. $k; ?>">
				
				<td align="center">
					<?php echo $this->pagination->getRowOffset( $i )-1; ?>
				</td>
				
				<td>
					<?php echo $checked; ?>
				</td>
				
				<td align="center">
					<?php echo $row->nr;?>
				</td>
				
				<td>
					<?php
					if ( JTable::isCheckedOut($this->user->get('id'), $row->checked_out) ) {
						echo $row->name;
					} else {
						$adminLink = new AdminLink();
						$adminLink->view = "turroundform";
						$adminLink->more = array('task' => 'edit', 'turnierid' => $this->param['id'], 'roundid' => $row->id);
						$adminLink->makeURL();
						echo '<span class="editlinktip hasTip" title="'.JText::_( 'EDIT' ).'">';
						echo '<a href="'.$adminLink->url.'">'.$row->name.'</a>';
						echo '</span>';
					}
					?>
				</td>
				
				<td align="center">
					<?php echo JHTML::_( 'date', $row->datum, JText::_('DATE_FORMAT_CLM'));?>
				</td>
				
				<td align="center">
					<?php 
						$adminLink = new AdminLink();
						$adminLink->view = "turroundmatches";
						$adminLink->more = array('turnierid' =>  $this->param['id'], 'roundid' => $row->id);
						$adminLink->makeURL();
						echo '<a href="'.$adminLink->url.'">'.CLMText::sgpl($row->countMatches, JText::_('MATCH'), JText::_('MATCHES')).'</a>';
					?>
				</td>
				
				<td align="center">
					<?php 
						echo $row->countAssigned."&nbsp;".JText::_('MATCHES_ASSIGNED');
						echo '<br />'.$row->countResults."&nbsp;".JText::_('MATCHES_PLAYED');
					
					
					?>
				</td>
				
				<td align="center">
					<?php echo $published;?>
				</td>
				
				<td align="center">
					<?php 
						// tl_ok/director approval
						if ($row->abgeschlossen == '1') { 
							echo '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.($i-1).'\', \'disbale\')" title="'.JText::_('DISABLE_ENTRY').'"><img width="16" height="16" src="images/apply_f2.png" /></a>';
						} else {
							echo '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.($i-1).'\', \'enable\')" title="'.JText::_('ENABLE_ENTRY').'"><img width="16" height="16" src="images/cancel_f2.png" /></a>';
						}
					?>
				</td>
				
				<td align="center">
					<?php 
						// tl_ok/director approval
						if ($row->tl_ok == '1') { 
							echo '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.($i-1).'\', \'unapprove\')" title="'.JText::_('REMOVE_APPROVAL').'"><img width="16" height="16" src="images/apply_f2.png" /></a>';
						} else {
							echo '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.($i-1).'\', \'approve\')" title="'.JText::_('SET_APPROVAL').'"><img width="16" height="16" src="images/cancel_f2.png" /></a>';
						}
					?>
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
				<td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	
	</table>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turrounds" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="turrounds" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->param['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->param['order_Dir']; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->param['id']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>
