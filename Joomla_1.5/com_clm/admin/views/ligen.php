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

class CLMViewLigen
{
function setLigenToolbar()
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

	JToolBarHelper::title( JText::_( 'TITLE_LEAGUES' ), 'clm_headmenu_liga.png' );
	JToolBarHelper::custom('paarung','edit.png','edit_f2.png',JText::_( 'LEAGUE_BUTTON_1' ),false);
	JToolBarHelper::custom('wertpunkte','default.png','apply_f2.png','LEAGUE_BUTTON_W',false);  //klkl
		
if (CLM_usertype === 'admin') {
	JToolBarHelper::custom('runden','back.png','edit_f2.png',JText::_( 'LEAGUE_BUTTON_2' ),false);
	JToolBarHelper::custom('del_runden','cancel.png','unarchive_f2.png',JText::_( 'LEAGUE_BUTTON_3' ),false);
	}
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
if (CLM_usertype === 'admin') {
	JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', JText::_( 'LEAGUE_BUTTON_4' ) );
	JToolBarHelper::custom('remove','delete.png','delete_f2.png',JText::_( 'LEAGUE_BUTTON_5' ),false);
	}
	// JToolBarHelper::editListX();
if (CLM_usertype === 'admin') {
	JToolBarHelper::custom('add','new.png','new_f2.png',JText::_( 'LEAGUE_BUTTON_6' ),false);
		}
	JToolBarHelper::help( 'screen.clm.liga' );
	}

function ligen(&$rows, &$lists, &$pageNav, &$option)
	{
	global $mainframe;
	CLMViewLigen::setLigenToolbar();
	$user =& JFactory::getUser();
	// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	$val	= $config->get('menue',1);

	//Ordering allowed ?
	$ordering = ($lists['order'] == 'a.ordering');

	JHTML::_('behavior.tooltip');
	?>
	<form action="index.php?option=com_clm&section=ligen" method="post" name="adminForm">
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
		echo "&nbsp;&nbsp;&nbsp;".$lists['state'];
	?>
	</td>
	</tr>
	</table>

	<table class="adminlist">
		<thead>
		<tr>
		<th width="10">
			<?php echo JText::_( 'NUM' ); ?>		</th>
		<th width="10">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_LEAGUE' ), 'a.name', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="9%">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_SEASON' ), 'c.name', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="9%">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_ROUNDS' ), 'a.runden', @$lists['order_Dir'], @$lists['order'] ); ?><br />(<?php echo JText::_( 'LEAGUE_OVERVIEW_DG' ); ?>)</th>
		<th width="9%">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_TEAMS' ), 'a.teil', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_STAMM' ), 'a.stamm', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_ERSATZ' ), 'a.ersatz', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="3%">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_SL' ), 'a.sl', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="4%">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_MAIL' ), 'a.mail', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		<th width="4%">
			<?php echo JHTML::_('grid.sort', JText::_( 'LEAGUE_OVERVIEW_HINT' ), 'a.bemerkungen', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>

		<th width="6%">
		<?php echo JHTML::_('grid.sort',   'Published', 'a.published', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
<?php if (CLM_usertype === 'admin') { ?>
		<th width="8%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort',   'Order', 'a.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
			<?php echo JHTML::_('grid.order',  $rows ); ?>		</th>
<?php	} ?>
		<th width="1%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort',   'ID', 'a.id', @$lists['order_Dir'], @$lists['order'] ); ?>		</th>
		</tr>
		</thead>

		<tfoot>
		<tr>
		<td colspan="16">
			<?php echo $pageNav->getListFooter(); ?>		</td>
		</tr>
		</tfoot>

		<tbody>
		<?php
		$k = 0;
	if ($val == 1) { $menu ='index.php?option=com_clm&section=runden&liga=';
				}
		else { $menu ='index.php?option=com_clm&section=ligen&task=edit&cid[]=';
			}

		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link = JRoute::_( $menu . $row->id );
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$published 	= JHTML::_('grid.published', $row, $i );
			?>
			<tr class="<?php echo 'row'. $k; ?>">
			<td align="center">
				<?php echo $pageNav->getRowOffset( $i ); ?>			</td>
			<td>
				<?php echo $checked; ?>
            </td>
			<td>
				<?php
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) {
				echo $row->name;} 
				else {	
				
					$ligenedit ='index.php?option=com_clm&section=ligen&task=edit&cid[]=' . $row->id;
					
					?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'LEAGUE_OVERVIEW_TIP' );?>::<?php echo $row->name; ?>">
					<a href="<?php echo $ligenedit; ?>">
						<?php echo $row->name; ?>
					</a>
				<?php } ?>
				</span>
				<?php //} ?>			
            </td>
			<td align="center"><?php echo $row->saison;?></td>
			<td align="center">
            <a href="<?php echo $link; ?>">
				<?php if ( $row->durchgang > 1 ) { echo $row->durchgang."&nbsp;x&nbsp;"; } ?><?php echo $row->runden."&nbsp;".JText::_( 'SWT_RUNDEN' );?>
            </a><?php if ($row->rnd == '0') { ?><br /><?php echo '('.JText::_( 'LEAGUE_OVERVIEW_NOTCREATED' ).')';?><?php }?>
            </td>
			<td align="center"><?php echo $row->teil;?></td>
	 	 	<td align="center"><?php echo $row->stamm;?></td>
			<td align="center"><?php echo $row->ersatz;?></td>
			<td align="center"><?php echo $row->sl;?></td>
			<td align="center">
				<?php if ($row->mail == '1') 
				{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }
				else 	{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }?>			</td>
			<td align="center">
				<?php if ($row->bemerkungen <> '') 
				{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }
				else 	{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }?>			</td>

			<td align="center">
				<?php echo $published;?>			</td>
<?php if (CLM_usertype === 'admin') { ?>
	<td class="order">
	<span><?php echo $pageNav->orderUpIcon($i, ($row->liga == @$rows[$i-1]->liga), 'orderup()', 'Move Up', $ordering ); ?></span>
	<span><?php echo $pageNav->orderDownIcon($i, $n, ($row->liga == @$rows[$i+1]->liga), 'orderdown()', 'Move Down', $ordering ); ?></span>
	<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
	<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />					</td>
<?php		} ?>
					<td align="center">
						<?php echo $row->id; ?>					</td>
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


function setLigaToolbar()
	{
	if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
	else { $text = JText::_( 'New' );}
	
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
	JToolBarHelper::title( JText::_( 'LEAGUE_BUTTON_7' ).': <small><small>[ '. $text.' ]</small></small>', 'clm_headmenu_liga.png' );
	JToolBarHelper::save( 'save' );
	if (JRequest::getVar( 'task') == 'new') {
	JToolBarHelper::apply( 'apply' );
	}
	JToolBarHelper::cancel();
	}

function liga(&$row, $lists, $option )
	{
	CLMViewLigen::setLigaToolbar();
	JRequest::setVar( 'hidemainmenu', 1 );

	// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	$rang	= $config->get('rangliste',0);
	$sl_mail= $config->get('sl_mail',0);
	?>
	<?php 
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $row->params);
	$row->params = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$row->params[substr($value,0,$ipos)] = substr($value,$ipos+1);
		}
	}	
	if (!isset($row->params[btiebr1]) OR $row->params[btiebr1] == 0) {   //Standardbelegung
		$row->params[btiebr1] = 1;
		$row->params[btiebr2] = 2;
		$row->params[btiebr3] = 3;
		$row->params[btiebr4] = 4;
	}
	if (!isset($row->params[bnhtml]) OR $row->params[bnhtml] == 0) {   //Standardbelegung
		$row->params[bnhtml] = round(($row->teil)/2);
	}
	if (!isset($row->params[bnpdf]) OR $row->params[bnpdf] == 0) {   //Standardbelegung
		$row->params[bnpdf] = round(40/($row->stamm+1));
	}
	
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
				alert( "<?php echo JText::_( 'LEAGUE_HINT_1', true ); ?>" );
			} else if ( getSelectedValue('adminForm','sid') == 0 ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_2', true ); ?>" );
			} else if (form.stamm.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_3', true ); ?>" );
			} else if (form.ersatz.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_4', true ); ?>" );
			} else if (form.teil.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_5', true ); ?>" );
			} else if (form.runden.value == "") {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_6', true ); ?>" );
			} else if ( getSelectedValue('adminForm','durchgang') == "" ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_7', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>

 <form action="index.php" method="post" name="adminForm">
  <div class="col width-60">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_DATA' ); ?></legend>
      <table class="adminlist">

	<tr>
	<td width="20%" nowrap="nowrap">
	<label for="name"><?php echo JText::_( 'LEAGUE_NAME' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="name" id="name" size="30" maxlength="30" value="<?php echo $row->name; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="sl"><?php echo JText::_( 'LEAGUE_CHIEF' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['sl']; ?>
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="saison"><?php echo JText::_( 'LEAGUE_SEASON' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['saison']; ?>
	</td>

	<td nowrap="nowrap">
	<label for="rang"><?php echo JText::_( 'LEAGUE_LIST_TYPE' ); ?></label>
	</td><td colspan="2">
	<?php if ($rang == 0) { ?>
	<?php echo $lists['gruppe']; ?>
	</td>
	</tr>
	<?php } if ($rang == 1) { echo JText::_( 'LEAGUE_LIST_TYPE_DEFAULT_RANK' ); ?>
	</td>
	</tr>
	<input type="hidden" name="rang" value="1" />
	<?php }
	if ($rang == 2) { echo JText::_( 'LEAGUE_LIST_TYPE_DEFAULT_LIST' ); ?>
	</td>
	</tr>
	<input type="hidden" name="rang" value="0" />
	<?php } ?>

	<tr>
	<td nowrap="nowrap">
	<label for="teil"><?php echo JText::_( 'LEAGUE_TEAMS' ); ?></label>
	</td><td colspan="5">
	<input class="inputbox" type="text" name="teil" id="teil" size="4" maxlength="4" value="<?php echo $row->teil; ?>" />
	</td>
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="stammspieler"><?php echo JText::_( 'LEAGUE_PLAYERS_1' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="stamm" id="stamm" size="4" maxlength="4" value="<?php echo $row->stamm; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="erstatzspieler"><?php echo JText::_( 'LEAGUE_PLAYERS_2' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="ersatz" id="ersatz" size="4" maxlength="4" value="<?php echo $row->ersatz; ?>" />
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="runden"><?php echo JText::_( 'LEAGUE_ROUNDS' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="runden" id="runden" size="4" maxlength="4" value="<?php echo $row->runden; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="durchgang"><?php echo JText::_( 'LEAGUE_DG' ); ?></label>
	</td><td colspan="2">
		<select name="durchgang" id="durchgang" value="<?php echo $row->durchgang; ?>" size="1">
		<option <?php if ($row->durchgang < 2) {echo 'selected="selected"';} ?>>1</option>
		<option <?php if ($row->durchgang == 2) {echo 'selected="selected"';} ?>>2</option>
		</select>
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="runden_modus"><?php echo JText::_( 'LEAGUE_PAIRING_MODE' ); ?></label>
	</td><td colspan="2">
		<select name="runden_modus" id="runden_modus" value="<?php echo $row->runden_modus; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="1" <?php if ($row->runden_modus == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_PAIRING_MODE_2' );?></option>
		<option value="2" <?php if ($row->runden_modus == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_PAIRING_MODE_3' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="heim"><?php echo JText::_( 'LEAGUE_HOME' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['heim']; ?>
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="ersatz_regel"><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL' ); ?></label>
	</td><td colspan="5">
		<select name="ersatz_regel" id="ersatz_regel" value="<?php echo $row->ersatz_regel; ?>" size="1">
		<!--<option>- wählen -</option>-->
		<option value="0" <?php if ($row->ersatz_regel == 0) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL_0' );?></option>
		<option value="1" <?php if ($row->ersatz_regel == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'LEAGUE_ERSATZ_REGEL_1' );?></option>
		</select>
	</td>
	</tr>
	
      </table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_VALUATION' ); ?></legend>
      <table class="adminlist">

	<tr>
	<td nowrap="nowrap">&nbsp;</td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_1' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_2' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_3' );?></td>
	<td><?php echo JText::_( 'LEAGUE_VALUATION_4' );?></td>
	</tr>
	
	<tr>
	<td nowrap="nowrap">
	<label for="punkte_modus"><?php echo JText::_( 'LEAGUE_MATCH_VALUATION' ); ?></label>
	</td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="sieg" id="sieg" size="4" maxlength="4" value="<?php if($row->sieg !=""){ echo $row->sieg;} else { echo "1";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="remis" id="remis" size="4" maxlength="4" value="<?php if($row->remis !=""){ echo $row->remis;} else { echo "0.5";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="nieder" id="nieder" size="4" maxlength="4" value="<?php if($row->nieder !=""){ echo $row->nieder;} else { echo "0";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="antritt" id="antritt" size="4" maxlength="4" value="<?php if($row->antritt !=""){ echo $row->antritt;} else { echo "0";}; ?>" /></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="man_punkte"><?php echo JText::_( 'LEAGUE_TEAM_POINTS' ); ?></label>
	</td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_sieg" id="man_sieg" size="4" maxlength="4" value="<?php if($row->man_sieg !=""){ echo $row->man_sieg;} else { echo "2";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_remis" id="man_remis" size="4" maxlength="4" value="<?php if($row->man_remis !=""){ echo $row->man_remis;} else { echo "1";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_nieder" id="man_nieder" size="4" maxlength="4" value="<?php if($row->man_nieder !=""){ echo $row->man_nieder;} else { echo "0";}; ?>" /></td>
	<td>&nbsp;&nbsp;&nbsp;<input class="inputbox" type="text" name="man_antritt" id="man_antritt" size="4" maxlength="4" value="<?php if($row->man_antritt !=""){ echo $row->man_antritt;} else { echo "0";}; ?>" /></td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="sieg_bed"><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="sieg_bed" id="sieg_bed" value="<?php echo $row->sieg_bed; ?>" size="1">
		<option value="1" <?php if ($row->sieg_bed == 1) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS_1' );?></option>
		<option value="2" <?php if ($row->sieg_bed == 2) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_WINNING_CONDITIONS_2' );?></option>
		</select>
	</td>
	<td nowrap="nowrap">
	<label for="b_wertung"><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS' ); //klkl?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="b_wertung" id="b_wertung" value="<?php echo $row->b_wertung; ?>" size="1">
		<option value="0" <?php if ($row->b_wertung == 0) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_0' );?></option>
		<option value="3" <?php if ($row->b_wertung == 3) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_3' );?></option>
		<option value="4" <?php if ($row->b_wertung == 4) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_SCORE_CONDITIONS_4' );?></option>
		</select>
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="auf"><?php echo JText::_( 'LEAGUE_UP' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="auf" id="auf" size="10" maxlength="10" value="<?php echo $row->auf; ?>" />
	</td>

	<td nowrap="nowrap">
	<label for="color_auf"><?php echo JText::_( 'LEAGUE_UP_POSSIBLE' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="auf_evtl" id="auf_evtl" size="10" maxlength="10" value="<?php echo $row->auf_evtl; ?>" />
	</td>
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="ab"><?php echo JText::_( 'LEAGUE_DOWN' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="ab" id="ab" size="10" maxlength="10" value="<?php echo $row->ab; ?>" />
	</td>

	<td nowrap="nowrap">
	<label for="color_ab"><?php echo JText::_( 'LEAGUE_DOWN_POSSIBILE' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
	<input class="inputbox" type="text" name="ab_evtl" id="ab_evtl" size="10" maxlength="10" value="<?php echo $row->ab_evtl; ?>" />
	</td>
	</tr>
      </table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_BOARD_VALUATION' ); ?></legend>
      <table class="adminlist">
	<tr>
	<td nowrap="nowrap">
	<label for="params[btiebr1]"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION1' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params[btiebr1]" id="params[btiebr1]" value="<?php echo $row->params[btiebr4]; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params[btiebr1] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params[btiebr2]"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION2' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params[btiebr2]" id="params[btiebr2]" value="<?php echo $row->params[btiebr2]; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params[btiebr2] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="params[btiebr3]"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION3' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params[btiebr3]" id="params[btiebr3]" value="<?php echo $row->params[btiebr3]; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params[btiebr3] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params[btiebr4]"><?php echo JText::_( 'LEAGUE_BOARD_VALUATION4' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params[btiebr4]" id="params[btiebr4]" value="<?php echo $row->params[btiebr4]; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params[btiebr4] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="params[btiebr5]"><?php echo JText::_( 'LEAGUE_BOARD_COLUMN5' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params[btiebr5]" id="params[btiebr5]" value="<?php echo $row->params[btiebr5]; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params[btiebr5] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	<td nowrap="nowrap">
	<label for="params[btiebr6]"><?php echo JText::_( 'LEAGUE_BOARD_COLUMN6' ); ?></label>
	</td><td colspan="2">&nbsp;&nbsp;
		<select name="params[btiebr6]" id="params[btiebr6]" value="<?php echo $row->params[btiebr6]; ?>" size="1">
		<?php for ($x=0; $x<10; $x++) { ?> 
		<option value="<?php echo $x; ?>" <?php if ($row->params[btiebr6] == $x) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_BOARD_VALUATION_'.$x );?></option>
		<?php } ?>
		</select>
	</td>	
	</tr>

	<tr>
	<td nowrap="nowrap">
	<label for="params[bnhtml]"><?php echo JText::_( 'LEAGUE_BOARD_POSITIONS_LIST' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="params[bnhtml]" id="params[bnhtml]" size="2" maxlength="2" value="<?php echo $row->params[bnhtml]; ?>" />
	</td>
	<td nowrap="nowrap">
	<label for="params[bnpdf]"><?php echo JText::_( 'LEAGUE_BOARD_POSITIONS_PDF' ); ?></label>
	</td><td colspan="2">
	<input class="inputbox" type="text" name="params[bnpdf]" id="params[bnpdf]" size="2" maxlength="2" value="<?php echo $row->params[bnpdf]; ?>" />
	</td>
	</tr>
	
      </table>
  </fieldset>
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_PREFERENCES' ); ?></legend>
      <table class="adminlist">

    <tr>
	<td nowrap="nowrap" colspan="2">
	<label for="anzeige_ma"><?php echo JText::_( 'LEAGUE_SHOW_PLAYERLIST' ); ?></label>
	</td><td colspan="4">
	<?php echo $lists['anzeige_ma']; ?>
	</td>
	</tr>

    <tr>
	<td nowrap="nowrap">
	<label for="params[pgntype]"><?php echo JText::_( 'LEAGUE_PGN_TYPE' ); ?></label>
	</td><td colspan="5">
		<select name="params[pgntype]" id="params[pgntype]" value="<?php echo $row->params[pgntype]; ?>" size="1">
		<option value="0" <?php if ($row->params[pgntype] == 0) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_NO' );?></option>
		<option value="1" <?php if ($row->params[pgntype] == 1) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_LEAGUE_NAME' );?></option>
		<option value="2" <?php if ($row->params[pgntype] == 2) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_SHORT_LEAGUE_NAME' );?></option>
		<option value="3" <?php if ($row->params[pgntype] == 3) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_TEAM_NAMES' );?></option>
		<option value="4" <?php if ($row->params[pgntype] == 4) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_SHORT_TEAM_NAMES' );?></option>
		<option value="5" <?php if ($row->params[pgntype] == 5) {echo 'selected="selected"';}  ?>><?php echo JText::_( 'LEAGUE_PGN_ALL_SHORT_NAMES' );?></option>
		</select>
	</td>
	</tr>
	<tr>
	<td nowrap="nowrap">
	<label for="name"><?php echo JText::_( 'LEAGUE_SHORT_NAME' ); ?></label>
	</td><td colspan="5">
	<input class="inputbox" type="text" name="params[pgnlname]" id="params[pgnlname]" size="30" maxlength="30" value="<?php echo $row->params[pgnlname]; ?>" />
	</td>
	</tr>
	
    <tr>
	<td nowrap="nowrap">
	<label for="mail"><?php echo JText::_( 'LEAGUE_MAIL' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['mail']; ?>
	</td>

<?php if ($sl_mail == "1") { ?>
	<td nowrap="nowrap">
	<label for="sl_mail"><?php echo JText::_( 'LEAGUE_MAIL_CHIEF' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['sl_mail']; ?>
	</td>
	</tr>
<?php } else { ?>
	<td colspan="3"></td>
	</tr>
	<input type="hidden" name="sl_mail" value="0" />
<?php } ?>
	<tr>
	<td nowrap="nowrap">
	<label for="order"><?php echo JText::_( 'LEAGUE_ORDERING' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['order']; ?>
	</td>
	<td nowrap="nowrap">
	<label for="published"><?php echo JText::_( 'LEAGUE_PUBLISHED' ); ?></label>
	</td><td colspan="2">
	<?php echo $lists['published']; ?>
	</td>
	</tr>


	</table>
  </fieldset>
  </div>

  <div class="col width-40">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'LEAGUE_HINT' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'LEAGUE_HINT_PUBLIC' ); ?></legend>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="4" style="width:90%"><?php echo str_replace('&','&amp;',$row->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'LEAGUE_HINT_INTERNAL' ); ?></legend>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="4" style="width:90%"><?php echo str_replace('&','&amp;',$row->bem_int);?></textarea>
	</td>
	</tr>
	</table>
	
  </fieldset>
  
    <fieldset>
  	<legend><?php echo JText::_( 'LEAGUE_HINTS' ); ?></legend>
  	<b><?php echo JText::_( 'LEAGUE_HINTS_PAIRING_MODE' ); ?></b>
  	
  	<?php echo JText::_( 'LEAGUE_HINTS_1' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_2' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_3' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_4' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_5' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_6' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_7' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_8' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_9' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_10' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_11' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_12' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_13' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_14' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_15' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_16' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_17' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_18' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_19' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_20' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_21' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_22' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_23' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_24' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_25' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_26' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_27' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_28' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_30' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_31' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_32' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_33' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_34' ); ?>
  	<?php echo JText::_( 'LEAGUE_HINTS_35' ); ?>
	<?php echo JText::_( 'LEAGUE_HINTS_36' ); ?>
 
  	</legend>

  </div>

<div class="clr"></div>

	<input type="hidden" name="section" value="ligen" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="sid_alt" value="<?php echo $row->sid; ?>" />
	<input type="hidden" name="cid" value="<?php echo $row->cid; ?>" />
	<input type="hidden" name="client_id" value="<?php echo $row->cid; ?>" />
	<input type="hidden" name="rnd" value="<?php echo $row->rnd; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<?php }}
?>