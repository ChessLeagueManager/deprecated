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

class CLMViewRunden
{
function setRundenToolbar()
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

		JToolBarHelper::title( JText::_( 'TITLE_RUNDE' ), 'clm_settings.png' );
		JToolBarHelper::custom('dwz_del','cancel.png','unarchive_f2.png','RUNDE_DWZ_DELETE',false);
		JToolBarHelper::custom('dwz_start','default.png','apply_f2.png','RUNDE_DWZ_APPLY',false);
		JToolBarHelper::custom('check','preview.png','upload_f2.png','RUNDE_CHECK',false);
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		JToolBarHelper::deleteList();
		// JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::help( 'screen.clm.runde' );
	}

function runden( &$rows, &$lists, &$pageNav, $option )
	{
		global $mainframe;
		CLMViewRunden::setRundenToolbar();
		$user =& JFactory::getUser();
	// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	$val=$config->get('menue',1);
	$dropdown=$config->get('dropdown',1);

		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

		JHTML::_('behavior.tooltip');

	if($rows[0]->sid_pub =="0" AND $val !=0) {
	JError::raiseNotice( 6000,  JText::_( 'RUNDE_ERROR_SAISON_UNPUBLISHED' ));
	}
		?>
		<form action="index.php?option=com_clm&section=runden" method="post" name="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
		// eigenes Dropdown Menue
		if ($val ==0 OR ( $val ==1 AND $dropdown == 1)) {
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['lid'];
					}
			echo "&nbsp;&nbsp;&nbsp;".$lists['state'];
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
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort',   'RUNDE', 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo JHTML::_('grid.sort',   'RUNDE_NR', 'a.nr', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>

					<th width="7%">
						<?php echo JHTML::_('grid.sort',   'DATE', 'a.datum', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHTML::_('grid.sort',   'ERGEBNISSE', 'e.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="15%">
						<?php echo JHTML::_('grid.sort',   'RUNDE_LIGA', 'd.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHTML::_('grid.sort',   'RUNDE_SAISON', 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="4%">
						<?php echo JHTML::_('grid.sort',   'RUNDE_MELDUNG', 'a.meldung', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="4%">
						<?php echo JHTML::_('grid.sort',   'RUNDE_SL_OK', 'a.sl_ok', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="4%">
						<?php echo JHTML::_('grid.sort',   'RUNDE_INFO', 'a.bemerkungen', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="4%">
						<?php echo JHTML::_('grid.sort',   'RUNDE_DWZ', 'a.dwz', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>

					<th width="6%">
						<?php echo JHTML::_('grid.sort',   'PUBLISHED', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="8%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',   'ORDER', 'a.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
						<?php echo JHTML::_('grid.order',  $rows ); ?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo JHTML::_('grid.sort',   'ID', 'a.id', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="13">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;

			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];
			if ($val == 0) { $menu = 'index.php?option=com_clm&section=runden&task=edit&cid[]='. $row->id; }
			else {
				if ($row->durchgang >1 ) {
					if($row->nr > $row->runden) {$menu ='index.php?option=com_clm&section=ergebnisse&runde='.($row->nr-$row->runden).'&dg='.$row->durchgang;}
					else { $menu ='index.php?option=com_clm&section=ergebnisse&runde='.($row->nr).'&dg=1'; }
					}
				else {
					$menu ='index.php?option=com_clm&section=ergebnisse&runde='.($row->nr).'&dg=1';
				}}

				$link 		= JRoute::_( $menu );
				$checked 	= JHTML::_('grid.checkedout',   $row, $i );
				$published 	= JHTML::_('grid.published', $row, $i );

				?>
				<tr class="<?php echo 'row'. $k; ?>">


					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>

					<td>
						<?php echo $checked; ?>
					</td>

					<td>
						<?php
						if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) OR ($row->sid_pub =="0" AND $val !=0)) {
							echo $row->name;
						} else {
							?>
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Runde' );?>::<?php echo $row->name; ?>">
							<a href="index.php?option=com_clm&section=runden&task=edit&cid[]=<?php echo $row->id; ?>">
								<?php echo $row->name; ?></a></span>
							<?php
						}
						?>
					</td>

					<td align="center">
						<?php echo $row->nr;?>
					</td>
					<td align="center">
						<?php echo $row->datum;?>
					</td>
					<td align="center">
						<a href="<?php echo $link; ?>"><?php echo JText::_( 'ERGEBNISSE' );?></a>
					</td>
					<td align="center">
						<?php echo $row->liga_name;?>
					</td>
					<td align="center">
						<?php echo $row->saison;?>
					</td>
					<td align="center">
						<?php if ($row->meldung=='1') 
							{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php if ($row->sl_ok=='1') 
							{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }?>
					</td>
					<td align="center">
						<?php if ($row->bemerkungen<>'') 
							{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }?>
					</td>

					<td align="center">
						<?php 	if ($row->dwz =='1') 
								{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }
							if ($row->dwz =='0')
								{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }
							if ($row->dwz =='2')
								{ ?><img width="16" height="16" src="/administrator/components/com_clm/images/lupe.png" /> <?php }
							?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>

	<td class="order">
	<span><?php echo $pageNav->orderUpIcon($i, ($row->liga == @$rows[$i-1]->liga), 'orderup()', 'Move Up', $ordering ); ?></span>
	<span><?php echo $pageNav->orderDownIcon($i, $n, ($row->liga == @$rows[$i+1]->liga), 'orderdown()', 'Move Down', $ordering ); ?></span>
	<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
	<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
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
			</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

function setRundeToolbar()
	{

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
		JToolBarHelper::title(  JText::_( 'RUNDE' ).': <small><small>[ '. $text.' ]</small></small>' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
function runde( &$row,$lists, $option )
	{
		CLMViewRunden::setRundeToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		?>
	<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name.value == "") {
				alert( "<?php echo JText::_( 'RUNDE_NAME_ANGEBEN', true ); ?>" );
			} else if (form.nr.value == "") {
				alert( "<?php echo JText::_( 'RUNDE_NUMMER_ANGEBEN', true ); ?>" );
			} else if (form.datum.value == "") {
				alert( "<?php echo JText::_( 'RUNDE_DATE_ANGEBEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'RUNDE_SAISON_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','liga') == 0 ) {
				alert( "<?php echo JText::_( 'RUNDE_LIGA_AUSWAEHLEN', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>

		<form action="index.php" method="post" name="adminForm">

		<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RUNDE_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RUNDE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="50" maxlength="60" value="<?php echo $row->name; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
			<label for="contact"><?php echo JText::_( 'RUNDE_NR' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="nr" id="nr" size="50" maxlength="60" value="<?php echo $row->nr; ?>" />
			</td>
		</tr>

		   <tr>
            		<td width="100" align="right" class="key">
                	<label for="datum">
                    	<?php echo JText::_( 'DATE' ); ?>:
                	</label>
            		</td>
            		<td>
                	<?php echo JHTML::_('calendar', $row->datum, 'datum', 'datum', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19')); ?>
            		</td>
        		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo JText::_( 'RUNDE_SAISON' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['saison']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" ><label for="liga"><?php echo JText::_( 'RUNDE_LIGA' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['liga']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="verein"><?php echo JText::_( 'RUNDE_MELDUNG_MOEGLICH' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['complete']; ?>

			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="mf"><?php echo JText::_( 'RUNDE_SL_FREIGABE' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['slok']; ?>
			</td>
		</tr>
		
		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'PUBLISHED' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['published']; ?>
			</td>
		</tr>


		</table>
		</fieldset>
		</div>

 <div class="col width-50">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'RUNDE_BEMERKUNGEN' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'RUNDE_OEFFENTLICH' ); ?></legend>
	<br>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'RUNDE_INTERN' ); ?></legend>
	<br>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
  </div>


		<div class="clr"></div>

		<input type="hidden" name="section" value="runden" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
		<input type="hidden" name="client_id" value="<?php echo $row->cid; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="slok_old" value="<?php echo $row->sl_ok; //klkl ?>" /> 
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

function setDWZToolbar()
	{
	JToolBarHelper::title(  JText::_( 'DWZ Auswertung erfolgreich !' ));
	JToolBarHelper::custom('dwz_cancel','cancel.png','cancel_f2.png','Zurück zu Spieltage',false);
	}
		
function setDWZSToolbar()
	{
	JToolBarHelper::title(  JText::_( 'DWZ Saison-Auswertung erfolgreich !' ));
	JToolBarHelper::custom('dwz_cancels','cancel.png','cancel_f2.png','Zurück zu Saison',false);
	}	
function dwz( $option, $dwz, $sid, $lid )
	{
	if ($dwz==0) CLMViewRunden::setDWZToolbar();
	if ($dwz==1) CLMViewRunden::setDWZSToolbar();

	$db 		=& JFactory::getDBO();
	if ($dwz==0) {
		// Angaben für Liga und Saison wenn Ligaauswertung
		$query	= "SELECT a.sid, a.name as liga, s.name as saison "
		." FROM #__clm_liga as a "
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid "
		." WHERE a.id =".$lid
		;
	} 
	if ($dwz==1) {
		// Angaben für Saison wenn Saisonauswertung
		$query	= "SELECT id as sid, name as saison "
		." FROM #__clm_saison "
		." WHERE id =".$sid
		;
	}
		$db->setQuery($query);
		$row = $db->loadObjectList();

	?>
		<form action="index.php" method="post" name="adminForm">

		<div class="col width-65">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RUNDE_AUSWERTUNG' ); ?></legend>
		<br>
		<?php require_once(JPATH_COMPONENT.DS.'controllers'.DS.'runden.php');
			CLMControllerRunden::dwz($dwz, $sid, $lid); ?>
		</fieldset>
		</div>
		<?php if ($dwz =="0") { ?>
		<div class="col width-35">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RUNDE_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RUNDE_LIGA' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $row[0]->liga; ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RUNDE_SAISON' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $row[0]->saison; ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RUNDE_TURNIER' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $row[0]->sid; ?>
			</td>
		</tr>
		</table>
		</fieldset>
		</div>
		<?php } ?>
		<?php if ($dwz =="1") { ?>
		<div class="col width-35">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RUNDE_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RUNDE_SAISON' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $row[0]->saison; ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RUNDE_TURNIER' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $row[0]->sid; ?>
			</td>
		</tr>
		</table>
		</fieldset>
		</div>
		<?php } ?>	
		<input type="hidden" name="section" value="runden" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
?>