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

class CLMViewVereine
{
function setVereineToolbar()
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

		JToolBarHelper::title( JText::_( 'TITLE_VEREIN' ), 'clm_headmenu_vereine.png' );
	if (CLM_usertype === 'admin') {
		JToolBarHelper::custom('copy_saison','copy.png','copy_f2.png','VEREIN_BUTTON_COPY_LAST_YEAR',false);
			}
	//if (CLM_sl_count !== '0' OR CLM_usertype === 'admin') {
	  if (CLM_usertype === 'admin' || CLM_usertype === 'sl' ) {

		JToolBarHelper::custom('gruppen','send.png','send_f2.png','VEREIN_BUTTON_GROUP_EDIT',false);
		JToolBarHelper::custom('rangliste','send.png','send_f2.png','VEREIN_BUTTON_RANG_EDIT',false);
		}
	if (CLM_usertype === 'admin' OR CLM_usertype === 'dv' OR CLM_usertype === 'dwz') {
		JToolBarHelper::custom('dwz','send.png','send_f2.png','VEREIN_BUTTON_MEMBER_EDIT',false);
		}
	if (CLM_usertype === 'admin' || CLM_usertype === 'dv' || CLM_usertype === 'sl') {
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		}
	if (CLM_usertype === 'admin') {
		JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'VEREIN_BUTTON_COPY' );
		JToolBarHelper::custom('remove','delete.png','delete_f2.png','VEREIN_BUTTON_DEL',false);
	}
		JToolBarHelper::editListX();
	if (CLM_usertype === 'admin') {
		JToolBarHelper::custom('add','new.png','new_f2.png','VEREIN_BUTTON_NEW',false);
		}
		JToolBarHelper::help( 'screen.clm.verein' );
	}

function vereine ( &$rows, &$lists, &$pageNav, $option )
	{
		global $mainframe;
		CLMViewVereine::setVereineToolbar();
		$user =& JFactory::getUser();
		// Ordering allowed ?
		$ordering = ($lists['order'] == 'a.ordering');

		JHTML::_('behavior.tooltip');
		?>
		<form action="index.php?option=com_clm&section=vereine" method="post" name="adminForm">

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
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
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
						<?php echo JHTML::_('grid.sort',   'VEREIN', 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHTML::_('grid.sort',   'VEREIN_ZPS', 'a.zps', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="22%">
						<?php echo JHTML::_('grid.sort',   'VEREIN_HOMEPAGE', 'a.homepage', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="11%">
						<?php echo JHTML::_('grid.sort',   'SAISON', 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>
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

				$link 		= JRoute::_( 'index.php?option=com_clm&section=vereine&task=edit&cid[]='. $row->id );

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
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'VEREIN_EDIT' );?>::<?php echo $row->name.$row->man_nr; ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $row->name; ?></a></span>
							<?php
						}
						?>
					</td>

					<td align="center">
						<?php echo $row->zps;?>
					</td>
					<td align="center">
						<a href="<?php echo $row->homepage; ?>" target="_blank"><?php echo $row->homepage; ?></a>
					</td>
                    
					<td align="center">
						<?php echo $row->saison;?>
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
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

function setVereinToolbar()
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

		$zps = $row->zps;
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
		JToolBarHelper::title(  JText::_( 'VEREIN' ).': <small><small>[ '. $text.' ]</small></small>', 'clm_headmenu_vereine.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
function verein( &$row, $lists, $option )
	{
		$url = $_SERVER["REQUEST_URI"];
		$ipos = strpos($url,'administrator');
		$rurl = substr($url,0,$ipos);
		$surl = $_SERVER["SERVER_NAME"];
		JToolBarHelper::preview( 'http://'.$surl.$rurl.'index.php?option=com_clm&view=verein&saison='.$row->sid.'&amp;zps='.$row->zps);
		CLMViewVereine::setVereinToolbar();
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		?>
	<script language="javascript" type="text/javascript">
		<!--
		function Tausch (x)
		{
			var w = document.getElementById(x).selectedIndex;
			var selected_text = document.getElementById(x).options[w].text;

			//document.getElementById('name').innerHTML=selected_text;
			document.getElementById('name').value=selected_text;
		}

		function VSTausch (x)
		{
			var w = document.getElementById(x).selectedIndex;
			var selected_text = document.getElementById(x).options[w].text;

			//document.getElementById('name').innerHTML=selected_text;
			document.getElementById('vs').value=selected_text;
		}

		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name.value == "") {
				alert( "<?php echo JText::_( 'VEREIN_NAME_ANGEBEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','zps') == 0 ) {
				alert( "<?php echo JText::_( 'VEREIN_ZPS_AUSWAEHLEN', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'VEREIN_SAISON_AUSWAEHLEN', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>

		<form action="index.php" method="post" name="adminForm">

		<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'VEREIN_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="zps"><?php echo JText::_( 'VEREIN_ZPS' ).' : '; ?></label>
			</td>
			<td>
			<?php echo $lists['verein']; ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'VEREIN' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="50" maxlength="60" value="<?php echo $row->name; ?>" />
			</td>
		</tr>

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


<tr><td colspan="2"><hr></td></tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'VEREIN_SPIELLOKAL' ).' : '; ?></label>
			</td>
			<td>
			<?php  echo JText::_( 'CLM_KOMMA' ) . "<br><br>"; ?>
			<textarea class="inputbox" name="lokal" id="lokal" cols="40" rows="2" style="width:100%"><?php echo $row->lokal; ?></textarea>
			<br><?php echo JText::_( 'VEREIN_3_LINES' ); ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'VEREIN_ADRESSE' ).' : '; ?></label>
			</td>
			<td>
			<textarea class="inputbox" name="adresse" id="adresse" cols="40" rows="2" style="width:100%"><?php echo $row->adresse; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'VEREIN_TERMINE' ).' : '; ?></label>
			</td>
			<td>
			<textarea class="inputbox" name="termine" id="termine" cols="40" rows="4" style="width:99%"><?php echo $row->termine; ?></textarea>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="lokal"><?php echo JText::_( 'VEREIN_HOMEPAGE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="homepage" id="homepage"  size="50" value="<?php echo $row->homepage; ?>" />
		</tr>

        <tr><td colspan="2"><hr></td></tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="vs"><?php echo JText::_( 'VEREIN_MANAGER' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="vs" id="vs" size="50" maxlength="100" value="<?php echo $row->vs; ?>" />
			<?php echo $lists['vl']; ?>
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="vs_mail"><?php echo JText::_( 'VEREIN_MAIL' ). ' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="vs_mail" id="vs_mail" size="50" maxlength="100" value="<?php echo $row->vs_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="vs_tel"><?php echo JText::_( 'VEREIN_PHONE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="vs_tel" id="vs_tel" size="50" maxlength="100" value="<?php echo $row->vs_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="tl"><?php echo JText::_( 'VEREIN_DIRECTOR').' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tl" id="tl" size="50" maxlength="100" value="<?php echo $row->tl; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="tl_mail"><?php echo JText::_( 'VEREIN_MAIL' ). ' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tl_mail" id="tl_mail" size="50" maxlength="100" value="<?php echo $row->tl_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="tl_tel"><?php echo JText::_( 'VEREIN_PHONE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="tl_tel" id="tl_tel" size="50" maxlength="100" value="<?php echo $row->tl_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="jw"><?php echo JText::_( 'VEREIN_JUNIOR' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="jw" id="jw" size="50" maxlength="100" value="<?php echo $row->jw; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="jw_mail"><?php echo JText::_( 'VEREIN_MAIL' ). ' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="jw_mail" id="jw_mail" size="50" maxlength="100" value="<?php echo $row->jw_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="jw_tel"><?php echo JText::_( 'VEREIN_PHONE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="jw_tel" id="jw_tel" size="50" maxlength="100" value="<?php echo $row->jw_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="pw"><?php echo JText::_( 'VEREIN_SPOKESMAN' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="pw" id="pw" size="50" maxlength="100" value="<?php echo $row->pw; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="pw_mail"><?php echo JText::_( 'VEREIN_MAIL' ). ' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="pw_mail" id="pw_mail" size="50" maxlength="100" value="<?php echo $row->pw_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="pw_tel"><?php echo JText::_( 'VEREIN_PHONE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="pw_tel" id="pw_tel" size="50" maxlength="100" value="<?php echo $row->pw_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="kw"><?php echo JText::_( 'VEREIN_TREASURER' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="kw" id="kw" size="50" maxlength="100" value="<?php echo $row->kw; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="kw_mail"><?php echo JText::_( 'VEREIN_MAIL' ). ' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="kw_mail" id="kw_mail" size="50" maxlength="100" value="<?php echo $row->kw_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="kw_tel"><?php echo JText::_( 'VEREIN_PHONE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="kw_tel" id="kw_tel" size="50" maxlength="100" value="<?php echo $row->kw_tel; ?>" />
			</td>
		</tr>
<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sw"><?php echo JText::_( 'VEREIN_SENIOR' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="sw" id="sw" size="50" maxlength="100" value="<?php echo $row->sw; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sw_mail"><?php echo JText::_( 'VEREIN_MAIL' ). ' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="sw_mail" id="sw_mail" size="50" maxlength="100" value="<?php echo $row->sw_mail; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap"><label for="sw_tel"><?php echo JText::_( 'VEREIN_PHONE' ).' : '; ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="sw_tel" id="sw_tel" size="50" maxlength="100" value="<?php echo $row->sw_tel; ?>" />
			</td>
		</tr>


		</table>
		</fieldset>
		</div>

 <div class="col width-50">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'VEREIN_BEMERKUNGEN' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'VEREIN_OEFFENTLICH' ); ?></legend>
	<br>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'VEREIN_INTERN' ); ?></legend>
	<br>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
  </div>
		<div class="clr"></div>

		<input type="hidden" name="section" value="vereine" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
		<input type="hidden" name="client_id" value="<?php echo $row->cid; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
} ?>