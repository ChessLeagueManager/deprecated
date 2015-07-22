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

class CLMViewDB
{
function setDBToolbar($execute,$upload)
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

	JToolBarHelper::title(   JText::_( 'TITLE_DATABASE' ), 'clm_headmenu_datenbank.png' );
if (CLM_admin === 'admin') {
	JToolBarHelper::custom('convert_db','refresh.png','refresh_f2.png',JTEXT::_('DB_BUTTON_ADAPT'),false);
}

if (CLM_admin === 'admin') {
	JToolBarHelper::custom('liga_export','download.png','download_f2.png',JTEXT::_('DB_BUTTON_EXPORT'),false);
	JToolBarHelper::custom('import','upload.png','upload_f2.png',JTEXT::_('DB_BUTTON_IMPORT'),false);
	JToolBarHelper::custom('delete','delete.png','delete_f2.png',JTEXT::_('DB_BUTTON_DEL'),false);
	}
if ($execute == 1) {
if (CLM_admin === 'admin') {
	JToolBarHelper::custom('sql_db','apply.png','apply_f2.png',JTEXT::_('DB_BUTTON_SQL_EXECUTE'),false);
		}}
if ($upload == 1) {
if (CLM_admin === 'admin') {
	JToolBarHelper::custom('upload_jfile','upload.png','upload_f2.png',JTEXT::_('DB_BUTTON_FILE_UPLOAD'),false);
		}}
	JToolBarHelper::custom('update_clm','default.png','default_f2.png',JTEXT::_('DB_BUTTON_DWZ_DB_UPDATE'),false);
	JToolBarHelper::help( 'screen.clm.info' );
	}

function DB ( &$rows, &$lists, &$pageNav )
	{
	// Konfigurationsparameter auslesen
	$config		= &JComponentHelper::getParams( 'com_clm' );
	$upload		= $config->get('upload_sql',0);
	$execute	= $config->get('execute_sql',0);
	$lv		= $config->get('lv',705);
	$version	= $config->get('version',0);

	if($version =="0"){$db_version = "deutsche";}
	if($version =="1"){$db_version = "niederländische";}

	CLMViewDB::setDBToolbar($execute,$upload);
?>
<form action="index.php" name="adminForm" method="post" enctype="multipart/form-data" >

<div class="col width-40">
	<fieldset>
	<legend><?php echo JText::_( 'DB_ATT' ); ?></legend>
	<font color="#ff0000"><b><?php echo JText::_( 'DB_ATT_1' ); ?></b></font><br>
	<?php echo JText::_( 'DB_ATT_2' ); ?><br><?php echo JText::_( 'DB_ATT_3' ); ?>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_HINT'); ?></legend>
	<?php echo JTEXT::_('DB_HINT_1');
	echo $db_version;
	echo JTEXT::_('DB_HINT_2');
	echo JTEXT::_('DB_HINT_3');
	
	if($version =="1"){
	echo JTEXT::_('DB_HINT_4');
	echo JTEXT::_('DB_HINT_5');
	echo JTEXT::_('DB_HINT_6');
	echo JTEXT::_('DB_HINT_7');
	echo JTEXT::_('DB_HINT_8');
	echo JTEXT::_('DB_HINT_9');
	echo JTEXT::_('DB_HINT_10');
	echo JTEXT::_('DB_HINT_11');
	echo JTEXT::_('DB_HINT_12');
	echo JTEXT::_('DB_HINT_13');
	} ?>
	<?php if($version =="0"){
	echo JTEXT::_('DB_HINT_14');
	echo JTEXT::_('DB_HINT_15');
	} ?>
	<br><br>
<?php if($version =="0"){
	echo JTEXT::_('DB_HINT_16');
	} ?>

<?php if($version =="1"){ 
	echo JTEXT::_('DB_HINT_17');
	} ?>

	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_STATUS_1'); ?></legend>
	<?php echo JTEXT::_('DB_STATUS_2').' '; if ($upload == 1) { ?><font color="#00ff00"><?php echo JTEXT::_('DB_STATUS_AKTIV'); } 
				 else { ?><font color="#ff0000"><?php echo JTEXT::_('DB_STATUS_INAKTIV'); } ?></font>
	<br>
	<?php echo JTEXT::_('DB_STATUS_3').' '; if ($execute == 1) { ?><font color="#00ff00"><?php echo JTEXT::_('DB_STATUS_AKTIV');; } 
				 else { ?><font color="#ff0000"><?php echo JTEXT::_('DB_STATUS_INAKTIV'); } ?></font></b>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_DWZ_1'); ?></legend>
	<?php echo JTEXT::_('DB_DWZ_2'); ?><a href="http://www.schachbund.de/dwz/db/download.html"> http://www.schachbund.de/dwz/db/download.html</a>
	<?php echo JTEXT::_('DB_DWZ_3'); ?><a href="http://www.schachbund.de/dwz/db/download/LV-<?php echo substr($lv, 0, 1); ?>-sql.zip"> http://www.schachbund.de/dwz/db/download/LV-<?php echo substr($lv, 0, 1); ?>-sql.zip</a>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_SQL_1'); ?></legend>
	<?php echo JTEXT::_('DB_SQL_2'); ?>
	<table >
		<?php $datei = CLMControllerDB::files();
		for ($x=0; $x< count($datei); $x++ ) { ?>
		<tr><td><a href="components/com_clm/upload/<?php echo $datei[$x]; ?>" target="_blank"><?php echo $datei[$x]; ?></a></td></tr>
		<?php } ?>
	</table>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_EXPORT_1'); ?></legend>
	<?php echo JTEXT::_('DB_EXPORT_2'); ?>
	<table >
<?php $export_files = CLMControllerDB::export_files();
	for ($x=0; $x< count($export_files); $x++ ) { ?>
	<tr><td><a href="components/com_clm/upload/<?php echo $export_files[$x]; ?>" target="_blank"><?php echo $export_files[$x]; ?></a></td></tr>
<?php } ?>
	</table>
	</fieldset>
</div>


<div class="col width-60">

<?php if ($upload == 1) { ?>
		<fieldset>
		<legend><?php echo JTEXT::_('DB_UPLOAD_1'); ?></legend>
			 <input type="file" name="datei" />
		</fieldset>
	<?php } ?>


<?php if ($execute == 1) { ?>
<br>
	<fieldset>
	<legend><?php echo JTEXT::_('DB_SQL_DEL_UP'); ?></legend>
	<div>
	<table>
	<tr>
	<td class="key" nowrap="nowrap">
	  <select size="1" name="sql_datei">
		<option value="0"><?php echo JTEXT::_('DB_SQL_DEL_UP_1'); ?></option>
		<option value="all"><?php echo JTEXT::_('DB_SQL_DEL_UP_2'); ?></option>
		<?php for ($x=0; $x < count($datei); $x++) { ?>
		 <option value="<?php echo $datei[$x]; ?>"><?php echo $datei[$x]; ?></option> 
		<?php }	?>
	  </select>
	</td>
	</tr>
	<tr><td colspan="3">  </td></tr>
	<tr><td colspan="3">  </td></tr>
	<tr>
	<td colspan="3"><?php echo JTEXT::_('DB_DEL_OLD_DATA_PAR'); ?></td>
	</tr>
	<tr>
	<td>
		<input type="checkbox" id="sql_del" name="sql_del" value="1" /><?php echo JTEXT::_('DB_DEL_OLD_DATA'); ?></td>
		<td>     </td>
		<td><?php echo JTEXT::_('DB_DEL_OLD_DATA_HINT'); ?></td>
	</tr>
	<tr>
	</table>
	</div>
	</fieldset>
<?php } ?>
 
<br>
	<fieldset>
	<legend><?php echo JTEXT::_('DB_UPDATE'); ?></legend>
	<div>
	<table>
	<tr>
	<td>
		<input type="checkbox" id="incl_p" name="incl_p" value="1" /><?php echo JTEXT::_('DB_UPDATE_INCL_P'); ?></td>
		<td>     </td>
		<td><?php echo JTEXT::_('DB_UPDATE_INCL_P_HINT'); ?></td>
	</tr>
	<tr>
	</table>
	</div>
	</fieldset>
	
<?php if (CLM_admin === 'admin') { ?>
 
<br>
	<fieldset>
	<legend><?php echo JTEXT::_('DB_LIGA_EXPORT'); ?></legend>
<?php $liga_export = CLMControllerDB::liga(); ?>
	<div>
	<table>
	<tr>
	<td>
	  <select size="1" name="liga_export">
		<option value="0"><?php echo JTEXT::_('DB_LIGA_EXPORT_1'); ?></option>
		<option value="all"><?php echo JTEXT::_('DB_LIGA_EXPORT_"'); ?></option>
		<?php for ($x=0; $x < count($liga_export); $x++) { ?>
		 <option value="<?php echo $liga_export[$x]->id; ?>"><?php echo $liga_export[$x]->name; ?></option>
		<?php } ?>
	  </select>
	</td>
		<td><?php echo JTEXT::_('DB_LIGA_EXPORT_3'); ?></td>
	</tr>
	<tr>
	<td>
		<input type="checkbox" id="cb1" name="clm_user_exp" value="1" /><?php echo JTEXT::_('DB_LIGA_EXPORT_4'); ?></td>
		<td><?php echo JTEXT::_('DB_LIGA_EXPORT_5'); ?></td>
	</tr>
	<tr>
	<td width="155">
		<input type="checkbox" id="cb2" name="clm_joomla_exp" value="1" /><?php echo JTEXT::_('DB_LIGA_EXPORT_6'); ?></td>
		<td><?php echo JTEXT::_('DB_LIGA_EXPORT_7'); ?></td>
	</tr>
	<tr>
	<td>
		<input type="checkbox" id="cb3" name="clm_sql" value="1" /><?php echo JTEXT::_('DB_LIGA_EXPORT_8'); ?></td>
		<td><?php echo JTEXT::_('DB_LIGA_EXPORT_9'); ?></td>
	</tr>

	<tr>
	<td>
	<input class="inputbox" type="text" name="bem" id="bem" cols="15" rows="1" maxlength="50" style="width:100%"><?php echo str_replace('&','&amp;',$row->bem_int);?></input>
	</td>
	<td><?php echo JTEXT::_('DB_LIGA_EXPORT_10'); ?></td>
	</tr>
	</table>
	</div>

	<div>
	</div>
	</fieldset>

<br>
	<fieldset>
	<legend><?php echo JTEXT::_('DB_LIGA_EXPORT_11'); ?></legend>
	<tr>
	<td class="key" nowrap="nowrap">
	  <select size="1" name="delete_export">
		<option value="0"><?php echo JTEXT::_('DB_LIGA_EXPORT_12'); ?></option>
		<option value="all"><?php echo JTEXT::_('DB_LIGA_EXPORT_13'); ?></option>
		<?php for ($x=0; $x < count($export_files); $x++) { ?>
		 <option value="<?php echo $export_files[$x]; ?>"><?php echo $export_files[$x]; ?></option> 
		<?php }	?>
	  </select>
	</td>
	</tr>
	</fieldset>

<br>
<?php $saison_import = CLMControllerDB::saison(); ?>
	<fieldset>
	<legend><?php echo JTEXT::_('DB_LIGA_IMPORT'); ?></legend>
	<table>
	<tr>
	<td class="key" nowrap="nowrap">
	  <select size="1" name="import">
		<option value="0"><?php echo JTEXT::_('DB_LIGA_IMPORT_1'); ?></option>
		<?php for ($x=0; $x < count($export_files); $x++) { ?>
		 <option value="<?php echo $export_files[$x]; ?>"><?php echo $export_files[$x]; ?></option> 
		<?php }	?>
	  </select>
	</td>
	<td><?php echo JTEXT::_('DB_LIGA_IMPORT_2'); ?></td>
	</tr>

	<tr>
	<td class="key" nowrap="nowrap">
	  <select size="1" name="liga_import">
		<option value="0"><?php echo JTEXT::_('DB_LIGA_IMPORT_3'); ?></option>
		<option value="new"><?php echo JTEXT::_('DB_LIGA_IMPORT_4'); ?></option>
		<?php for ($x=0; $x < count($liga_export); $x++) { ?>
		 <option value="<?php echo $liga_export[$x]->id; ?>"><?php echo $liga_export[$x]->name; ?></option>
		<?php } ?>
	  </select>
	</td>
	<td><?php echo JTEXT::_('DB_LIGA_IMPORT_5'); ?></td>
	</tr>
	<tr>
	<td class="key" nowrap="nowrap">
	  <select size="1" name="saison_import">
		<option value="0"><?php echo JTEXT::_('DB_LIGA_IMPORT_6'); ?></option>
		<?php for ($x=0; $x < count($saison_import); $x++) { ?>
		 <option value="<?php echo $saison_import[$x]->id; ?>"><?php echo $saison_import[$x]->name; ?></option>
		<?php } ?>
	  </select>
	</td>
	<td><?php echo JTEXT::_('DB_LIGA_IMPORT_7'); ?></td>
	</tr>
	<tr>
	<td>
		<input type="checkbox" id="cb_imp1" name="imp_user" value="1" /><?php echo JTEXT::_('DB_LIGA_IMPORT_8'); ?></td>
		<td><?php echo JTEXT::_('DB_LIGA_IMPORT_9'); ?></td>
	</tr>
	<td>
		<input type="checkbox" id="cb_pub" name="imp_pub" value="1" /><?php echo JTEXT::_('DB_LIGA_IMPORT_10'); ?></td>
		<td><?php echo JTEXT::_('DB_LIGA_IMPORT_11'); ?></td>
	</tr>
	<tr>
	<td>
		<input type="checkbox" id="override" name="override" value="1" /><?php echo JTEXT::_('DB_LIGA_IMPORT_12'); ?></td>
		<td><?php echo JTEXT::_('DB_LIGA_IMPORT_13'); ?></td>
	</tr>

	</table>
	</fieldset>
<?php } ?>
</div>

		<input type="hidden" name="section" value="db" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
	<div class="clr"></div>
</form>
<?php	}
}
?>