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

class CLMViewUsers
{
function setUsersToolbar()
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

	JToolBarHelper::title( JText::_( 'TITLE_USER' ), 'clm_headmenu_benutzer.png' );
if (CLM_usertype === 'admin') {
	JToolBarHelper::custom('copy_saison','copy.png','copy_f2.png','USER_VORSAISON',false);
			}
	JToolBarHelper::custom('send','send.png','send_f2.png','USER_ACCOUNT',false);
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
	JToolBarHelper::deleteList();
	JToolBarHelper::editListX();
	JToolBarHelper::addNewX();
	JToolBarHelper::help( 'screen.clm.user' );
	}

function users( &$rows, &$lists, &$pageNav, $option )
	{
		global $mainframe;
		CLMViewUsers::setUsersToolbar();
		$user =& JFactory::getUser();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');
		JHTML::_('behavior.tooltip');
		?>
		<form action="index.php?option=com_clm&section=users" method="post" name="adminForm">

		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
		// eigenes Dropdown Menue
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['vid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['usertype'];
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
						<?php echo JHTML::_('grid.sort',   'USER', 'name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="10%">
						<?php echo JHTML::_('grid.sort',   'USER_FUNCTION', 'd.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="22%">
						<?php echo JHTML::_('grid.sort',   'VEREIN', 'b.Vereinname', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHTML::_('grid.sort',   'SAISON', 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="3%">
						<?php echo JHTML::_('grid.sort',   'USER_ACTIVE', 'u.lastvisitDate', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>

					<th width="3%">
						<?php echo JHTML::_('grid.sort',   'USER_MAIL', 'a.aktive', @$lists['order_Dir'], @$lists['order'] ); ?>
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
					<td colspan="12">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];

				$link 		= JRoute::_( 'index.php?option=com_clm&section=users&task=edit&cid[]='. $row->id );

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
						if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) {
							echo $row->name;
						} else {
							?>
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'USER_EDIT' );?>::<?php echo $row->name; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->name; ?></a></span>
							<?php
						}
						?>
					</td>

					<td align="center">
						<?php echo $row->funktion;?>
					</td>

					<td align="center">
						<?php echo $row->verein;?>
					</td>
					<td align="center">
						<?php echo $row->saison;?>
					</td>

					<td align="center">
						<?php if ($row->date=='0000-00-00 00:00:00' OR !$row->date) 
							{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }?>
					</td>

					<td align="center">
						<?php if ($row->aktive=='1') 
							{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }
						else 	{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }?>
					</td>

					<td align="center">
						<?php echo $published;?>
					</td>
	<td class="order">
	
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

function setUserToolbar()
	{

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { 
			$text = JText::_( 'Edit' );
		} else { 
			$text = JText::_( 'New' );
		}
		// Menubilder laden
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title(  JText::_( 'USER' ).': <small><small>[ '. $text.' ]</small></small>', 'clm_headmenu_benutzer.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}

function user( &$row,$lists, $option )
	{
		CLMViewUsers::setUserToolbar();
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
			if (form.pid.value =="0") {
			// do field validation
			if (form.name.value == "") {
				alert( "<?php echo JText::_( 'USER_NAME_ANGEBEN', true ); ?>" );
			} else if (form.username.value == "") {
				alert( "<?php echo JText::_( 'USER_USER_ANGEBEN', true ); ?>" );
			} else if (form.email.value == "") {
				alert( "<?php echo JText::_( 'USER_MAIL_ANGEBEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','user_clm') == 0 ) {
				alert( "<?php echo JText::_( 'USER_FUNKTION_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','zps') == 0 ) {
				alert( "<?php echo JText::_( 'USER_VEREIN_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'USER_SAISON_AUSWAEHLEN', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
						}
			else {
			// do field validation
			if ( getSelectedValue('adminForm','user_clm') == 0 ) {
				alert( "<?php echo JText::_( 'USER_FUNKTION_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','zps') == 0 ) {
				alert( "<?php echo JText::_( 'USER_VEREIN_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'USER_SAISON_AUSWAEHLEN', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
				}
		}
		//-->
		</script>

		<form action="index.php" method="post" name="adminForm">

		<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'USER_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'USER_NAME' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="30" maxlength="60" value="<?php echo $row->name; ?>" /><?php echo JText::_( 'USER_EXAMPLE_NAME' );?>
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="username"><?php echo JText::_( 'USER' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="username" id="username" size="30" maxlength="60" value="<?php echo $row->username; ?>" /><?php echo JText::_( 'USER_EXAMPLE_USERNAME' );?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'USER_MAIL' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="email" id="email" size="30" maxlength="60" value="<?php echo $row->email; ?>" /><?php echo JText::_( 'USER_EXAMPLE_MAIL' );?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'USER_TELEFON' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tel_fest" id="tel_fest" size="30" maxlength="60" value="<?php echo $row->tel_fest; ?>" /><?php echo JText::_( 'USER_EXAMPLE_PHONE' );?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'USER_MOBILE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tel_mobil" id="tel_mobil" size="30" maxlength="60" value="<?php echo $row->tel_mobil; ?>" /><?php echo JText::_( 'USER_EXAMPLE_MOBILE' );?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="user_clm"><?php echo JText::_( 'USER_FUNCTION' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['user_clm']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="verein"><?php echo JText::_( 'VEREIN' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['verein']; ?>
			</td>
		</tr>

		<tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sid"><?php echo JText::_( 'SAISON' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['saison']; ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'PUBLISHED' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['published']; ?>
			</td>
		</tr>
		<tr>
<!--			<td class="key" nowrap="nowrap"><label for="aktive"><?php echo JText::_( 'USER_MAIL' ).' : '; ?></label>
			</td>
			<td>
			<?php //echo $lists['aktive']; ?>
			</td>
		</tr>
-->

		</table>
		</fieldset>
		</div>

 <div class="col width-50">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'USER_BEMERKUNGEN' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'USER_OEFFENTLICH' ); ?></legend>
	<br>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="2" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'USER_INTERN' ); ?></legend>
	<br>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="2" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
<?php if( JRequest::getVar( 'task') =='add') { ?>
<br>
  <fieldset class="adminform">
	<table class="adminlist">
	<legend><?php echo JText::_( 'USER_LINE01' ); ?></legend>
	<?php echo JText::_( 'USER_LINE02' ); ?>
	<br><?php echo JText::_( 'USER_LINE03' ); ?>.
	<br><br>
	<tr>
	<td width="100%" valign="top">
		<?php echo $lists['jid']; ?>
	</td>
	</tr>
	</table>
   </fieldset>
<?php } else { ?>
<input type="hidden" name="pid" value="0" />
<?php } ?>
  </div>
		<div class="clr"></div>


		<input type="hidden" name="section" value="users" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="jid" id="jid" value="<?php echo $row->jid; ?>" />
		<input type="hidden" name="aktive" value="<?php echo $row->aktive; ?>" />
		<input type="hidden" name="script_task" value="<?php echo JRequest::getVar( 'task'); ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
} ?>