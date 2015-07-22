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

class CLMViewDWZ
{

function setDWZToolbar($vname)
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

		JToolBarHelper::title(  JText::_( 'TITLE_MEMBER'),'clm_headmenu_mitglieder' );
	if (CLM_usertype === 'admin' OR CLM_usertype === 'dv' OR CLM_usertype === 'dwz') {
		JToolBarHelper::custom( 'spieler_delete', 'trash.png', 'trash_f2.png', JText::_( 'MEMBER_BUTTON_DEL'),false );
	}
		JToolBarHelper::custom( 'nachmeldung_delete', 'trash.png', 'trash_f2.png', JText::_( 'MEMBER_BUTTON_DEL_NACH'),false );
		JToolBarHelper::custom( 'nachmeldung', 'apply.png', 'apply_f2.png', JText::_( 'MEMBER_BUTTON_NACH'),false );
		JToolBarHelper::custom( 'daten_edit', 'apply.png', 'apply_f2.png', JText::_( 'MEMBER_BUTTON_EDIT'),false );
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}

function DWZ( $spieler,$verein,$lists, $pageNav, $option )
	{
		CLMViewDWZ::setDWZToolbar($vname);
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		?>

<script language="javascript" type="text/javascript">
	<!--
	function edit()
	{
	var task 	= document.getElementsByName ( "task") [0];
	var pre_task 	= document.getElementsByName ( "pre_task") [0];
	task.value 	= "add";
	pre_task.value 	= "add";
	document.adminForm.submit();
	}

	function submitbutton(pressbutton) {
		var form = document.adminForm;
		var pre_task = document.getElementsByName ( "pre_task") [0];

	else {
		if (pre_task.value == 'add') {
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.filter_vid.value == "0") {
			alert( "<?php echo JText::_( 'MEMBER_JS_1', true ); ?>" );
		} else if (form.filter_sid.value == "0") {
			alert( "<?php echo JText::_( 'MEMBER_JS_2', true ); ?>" );
		} else if (form.filter_gid.value == "0") {
			alert( "<?php echo JText::_( 'MEMBER_JS_3', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
		}
		else {
			submitform( pressbutton );
		}
	}
	}
-->
</script>

<form action="index.php" method="post" name="adminForm">

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'MEMBER_TABLE_DATA' ); ?></legend>
		<?php echo $lists['vid'];  ?>&nbsp;&nbsp;
		<?php echo $lists['mgl'];  ?>&nbsp;&nbsp;
		<?php global $mainframe;
		$filter_sort	= $mainframe->getUserStateFromRequest( "$option.filter_mgl",'filter_sort',0,'string' ); ?>
		<select name="filter_sort" id="filter_sort" class="inputbox" size="1" onchange="document.adminForm.submit();">
		<option value="0"  <?php if ($filter_sort =="0") { ?>selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_DD_1');?></option>
		<option value="(0+Mgl_Nr) DESC" <?php if ($filter_sort =="(0+Mgl_Nr) DESC") { ?>selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_DD_2');?></option>
		<option value="(0+Mgl_Nr) ASC" <?php if ($filter_sort =="(0+Mgl_Nr) ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_DD_3');?></option>
		<option value="Spielername DESC" <?php if ($filter_sort =="Spielername DESC") { ?>selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_DD_4');?></option>
		<option value="Spielername ASC" <?php if ($filter_sort =="Spielername ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_DD_5');?></option>
		<option value="DWZ DESC" <?php if ($filter_sort =="DWZ DESC") { ?>selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_DD_6');?></option>
		<option value="DWZ ASC" <?php if ($filter_sort =="DWZ ASC") { ?>selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_DD_7');?></option>
		</select>
	</fieldset>

<?php	 $filter_vid	= $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' ); ?>

	<div class="col width-40">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'MEMBER_TABLE_1' ); ?></legend>
	<?php if ($filter_vid !="0") { ?>
	<table class="admintable">
		<tr>
			<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'MEMBER_TABLE_2' ); ?></th>
			<th width="20%" class="key" nowrap="nowrap"><?php echo JText::_( 'MEMBER_TABLE_3' ); ?></th>
			<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'MEMBER_TABLE_4' ); ?></th>
			<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'MEMBER_TABLE_5' ); ?></th>
			<th width="3%" class="key" nowrap="nowrap"><?php echo JText::_( 'MEMBER_TABLE_31' ); ?></th>
		</tr>
	<?php	for ($x=0; $x <count($verein);$x++) { ?>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
				<?php echo $x+1; ?>
			</td>
			<td class="key" width="20%" nowrap="nowrap">
				<?php echo $verein[$x]->Spielername; ?>
			</td>
			<td class="key" width="20%" nowrap="nowrap">
				<?php echo $verein[$x]->Mgl_Nr; ?>
			</td>
			<td class="key" width="20%" nowrap="nowrap">
				<?php echo $verein[$x]->DWZ; ?>
			</td>
			<td class="key" width="20%" nowrap="nowrap">
				<?php echo $verein[$x]->Status; ?>
			</td>
		</tr>
	<?php  } ?>
	</table>
	<?php } else { echo JText::_( 'MEMBER_TABLE_6' ); } ?>
	</fieldset>
	</div>

	<div class="col width-60">
	<div>
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'MEMBER_TABLE_7' ); ?></legend>
	<?php echo JText::_( 'MEMBER_TABLE_8' ); ?>

	</fieldset>
	</div>

	<div>
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'MEMBER_TABLE_9' ); ?></legend>
	<?php if ($filter_vid !="0") { ?>
	<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'MEMBER_TABLE_10' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="40" maxlength="60" <?php if ($filter_mgl !="0") {?> value="<?php echo $spieler[0]->Spielername ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_( 'MEMBER_TABLE_11' ); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="mglnr"><?php echo JText::_( 'MEMBER_TABLE_12' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="mglnr" id="mglnr" size="7" maxlength="7" <?php if ($filter_mgl !="0") {?> value="<?php echo $spieler[0]->Mgl_Nr ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_( 'MEMBER_TABLE_13' ); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="dwz"><?php echo JText::_( 'MEMBER_TABLE_14' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="dwz" id="dwz" size="7" maxlength="4" <?php if ($filter_mgl !="0") {?> value="<?php echo $spieler[0]->DWZ ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_( 'MEMBER_TABLE_15' ); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="dwz_index"><?php echo JText::_( 'MEMBER_TABLE_16' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="dwz_index" id="dwz_index" size="7" maxlength="4" <?php if ($filter_mgl !="0") {?> value="<?php echo $spieler[0]->DWZ_Index ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_( 'MEMBER_TABLE_17' ); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="geschlecht"><?php echo JText::_( 'MEMBER_TABLE_18' ); ?></label>
			</td>
			<td>
				<select size="1" name="geschlecht" id="geschlecht">
				<option value="0" <?php if ($spieler[0]->Geschlecht !="M" AND $spieler[0]->Geschlecht !="W"){ ?> selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_TABLE_19' ); ?></option>
				<option value="M" <?php if ($spieler[0]->Geschlecht =="M"){ ?> selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_TABLE_20' ); ?></option> 
				<option value="W" <?php if ($spieler[0]->Geschlecht =="W"){ ?> selected="selected"<?php } ?>><?php echo JText::_( 'MEMBER_TABLE_21' ); ?></option> 
			</td>
			<td>Bspl. W</td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="geburtsjahr"><?php echo JText::_( 'MEMBER_TABLE_22' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="geburtsjahr" id="geburtsjahr" size="7" maxlength="4" <?php if ($filter_mgl !="0") {?> value="<?php echo $spieler[0]->Geburtsjahr ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_( 'MEMBER_TABLE_23' ); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="status"><?php echo JText::_( 'MEMBER_TABLE_32' ); ?></label>
			</td>
			<td>
			<input class="inputbox" type="text" name="status" id="status" size="1" maxlength="1" <?php if ($filter_mgl !="0") {?> value="<?php echo $spieler[0]->Status ?>"<?php }?>/>
			</td>
			<td><?php echo JText::_( 'MEMBER_TABLE_33' ); ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo JText::_( 'MEMBER_TABLE_24' ); ?></td>
			<td><?php echo JText::_( 'MEMBER_TABLE_25' ); ?></td>
		</tr>

	</table>
	<?php } else { echo JText::_( 'MEMBER_TABLE_26' ); } ?>
	</fieldset>
	</div>

	<div>
	<?php 	$zps = $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
		$spl = CLMControllerDWZ::spieler($zps); ?>
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'MEMBER_TABLE_27' ); ?></legend>
	<?php if ($filter_vid !="0") { ?>
		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap">
	  			<select size="1" name="spieler">
					<option value="0"><?php echo JText::_( 'MEMBER_TABLE_28' ); ?></option>
				<?php for ($x=0; $x < count($spl); $x++) { ?>
		 		<option value="<?php echo $spl[$x]->Mgl_Nr; ?>"><?php echo $spl[$x]->Spielername; ?></option> 
				<?php }	?>
	  			</select>
			</tr>
		</table>
	<?php } else { echo JText::_( 'MEMBER_TABLE_29' ); } ?>
	</fieldset>
	</div>

<?php 	if (CLM_usertype === 'admin' OR CLM_usertype === 'dv' OR CLM_usertype === 'dwz') { ?>
	<div>
	<?php 	$zps = $mainframe->getUserStateFromRequest( "$option.filter_vid",'filter_vid',0,'var' );
		$spl = CLMControllerDWZ::spieler($zps); ?>
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'MEMBER_TABLE_30' ); ?></legend>
	<?php if ($filter_vid !="0") { ?>
		<table class="admintable">
			<tr>
				<td class="key" nowrap="nowrap">
	  			<select size="1" name="del_spieler">
					<option value="0"><?php echo JText::_( 'MEMBER_TABLE_28' ); ?></option>
				<?php for ($x=0; $x < count($verein); $x++) { ?>
		 		<option value="<?php echo $verein[$x]->Mgl_Nr; ?>"><?php echo $verein[$x]->Mgl_Nr.' - '.$verein[$x]->Spielername; ?></option> 
				<?php }	?>
	  			</select>
			</tr>
		</table>
	<?php } else { echo JText::_( 'MEMBER_TABLE_29' ); } ?>
	</fieldset>
	</div>
<?php } ?>
	</div>

		<div class="clr"></div>
		<?php if (!isset($verein[0]->sid)) $verein[0]->sid = $lists['saison'][0]->id; ?>
		<input type="hidden" name="section" value="dwz" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="zps" value="<?php echo $filter_vid; ?>" />
		<input type="hidden" name="mgl" value="<?php echo $filter_mgl; ?>" />

		<input type="hidden" name="sid" value="<?php echo $verein[0]->sid; ?>" />
		<input type="hidden" name="task" value="" />

		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
?>