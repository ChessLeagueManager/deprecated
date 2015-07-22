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

class CLMViewElobase
{
function setElobaseToolbar($execute,$upload)
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

	JToolBarHelper::title(   JText::_( 'TITLE_ELOBASE' ), 'clm_headmenu_elobase.png' );	
	JToolBarHelper::custom('CLM2EB2','upload.png','upload_f2.png','ELOBASE_FILE_CREATE',false);
	JToolBarHelper::help( 'screen.clm.info' );
	}

function Elobase ( $MaxLigen, $Username, $Verband, $Ligen, $Jahre )
	{
	// Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$upload	= $config->get('upload_sql',0);
	$execute= $config->get('execute_sql',0);
	$lv	= $config->get('lv',705);

	CLMViewElobase::setElobaseToolbar($execute,$upload);
?>
<form action="index.php" name="adminForm" method="post" enctype="multipart/form-data" >

<div class="col width-40">

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'ELOABSE_HINT_1' ); ?></legend>
	<?php echo JText::_( 'ELOABSE_HINT_2' ); ?>
	</fieldset>

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'ELOABSE_HINT_3' ); ?></legend>
	<?php echo JText::_( 'ELOABSE_HINT_4' ); ?>
	</fieldset>

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'ELOABSE_HINT_5' ); ?></legend>
	<?php echo JText::_( 'ELOABSE_HINT_6' ); ?>
	</fieldset>

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'ELOABSE_HINT_7' ); ?></legend>
	<table >
		<?php $datei = CLMControllerElobase::download();
		for ($x=0; $x< count($datei); $x++ ) { ?>
		<tr><td><a href="components/com_clm/elobase/<?php echo $datei[$x]; ?>" target="_blank"><?php echo $datei[$x]; ?></a></td></tr>
		<?php } ?>
	</table>
	</fieldset>
</div>


<div class="col width-60">

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'ELOABSE_HINT_8' ); ?></legend>
<?php
   if ($Ligen) { foreach ($Ligen as $Irow)
   echo '      <input type="hidden" name="LiAs['.$Irow->id.']" value="'.$Irow->name.'">
'; }
?>
      <table class="adminlist">
	<tr>
	<td class="key" width="20%" nowrap="nowrap"><?php echo JText::_( 'ELOABSE_HINT_9' ); ?></td>
	<td class="key" width="20%" nowrap="nowrap" colspan=3><input type="text" name="Turniername" size="40" maxlength="40"></td>
	</tr>
      <tr>
	<td class="key" width="20%" nowrap="nowrap"><?php echo JText::_( 'ELOABSE_HINT_10' ); ?></td>
	<td class="key" width="20%" nowrap="nowrap" colspan=3><select name="Tag">
	<option selected="Selected" value="0"><?php echo JText::_( 'ELOABSE_HINT_11' ); ?></option>
        <option>1</option>
	<option>2</option>
	<option>3</option>
	<option>4</option>
	<option>5</option>
        <option>6</option>
	<option>7</option>
	<option>8</option>
	<option>9</option>
	<option>10</option>
        <option>11</option>
	<option>12</option>
	<option>13</option>
	<option>14</option>
	<option>15</option>
        <option>16</option>
	<option>17</option>
	<option>18</option>
	<option>19</option>
	<option>20</option>
        <option>21</option>
	<option>22</option>
	<option>23</option>
	<option>24</option>
	<option>25</option>
        <option>26</option>
	<option>27</option>
	<option>28</option>
	<option>29</option>
	<option>30</option>
        <option>31</option>
      </select>.
      <select name="Monat">
        <option selected="selected" value="0"><?php echo JText::_( 'ELOABSE_HINT_12' ); ?></option>
        <option value=1><?php echo JText::_( 'ELOABSE_HINT_13' ); ?></option>
        <option value=2><?php echo JText::_( 'ELOABSE_HINT_14' ); ?></option>
        <option value=3><?php echo JText::_( 'ELOABSE_HINT_15' ); ?></option>
        <option value=4><?php echo JText::_( 'ELOABSE_HINT_16' ); ?></option>
        <option value=5><?php echo JText::_( 'ELOABSE_HINT_17' ); ?></option>
        <option value=6><?php echo JText::_( 'ELOABSE_HINT_18' ); ?></option>
        <option value=7><?php echo JText::_( 'ELOABSE_HINT_19' ); ?></option>
        <option value=8><?php echo JText::_( 'ELOABSE_HINT_20' ); ?></option>
        <option value=9><?php echo JText::_( 'ELOABSE_HINT_21' ); ?></option>
        <option value=10><?php echo JText::_( 'ELOABSE_HINT_22' ); ?></option>
        <option value=11><?php echo JText::_( 'ELOABSE_HINT_23' ); ?></option>
        <option value=12><?php echo JText::_( 'ELOABSE_HINT_24' ); ?></option>
      </select>
      <select name="Jahr">
        <option selected="selected" value="0">Jahr</option>
<?php foreach ($Jahre as $Jahr) echo '        <option>'.$Jahr.'</option>
'; ?>
        
      </select>
      <br><input type=radio name="Alter" value="normal" checked="checked"><?php echo JText::_( 'ELOABSE_HINT_25' ); ?>
      <br><input type=radio name="Alter" value="Zukunft"><?php echo JText::_( 'ELOABSE_HINT_26' ); ?>
      <br><input type=radio name="Alter" value="alt"><?php echo JText::_( 'ELOABSE_HINT_27' ); ?></td>
	</tr>
	<tr>
	<td class="key" width="20%" nowrap="nowrap"><?php echo JText::_( 'ELOABSE_HINT_28' ); ?></td>
	<td class="key" width="20%" nowrap="nowrap"><input value="CLM" type="text" name="Code" size="3" maxlength="3"></td>
	<td class="key" width="20%" nowrap="nowrap"><?php echo JText::_( 'ELOABSE_HINT_29' ); ?></td>
	<td class="key" width="20%" nowrap="nowrap"><input type="text" name="Verband" size="3" maxlength="3" value="<?php echo $Verband; ?>"></td>
	</tr>

	<tr>
	<td class="key" width="20%" nowrap="nowrap"><?php echo JText::_( 'ELOABSE_HINT_30' ); ?></td>
	<td class="key" width="20%" nowrap="nowrap" colspan=3><input type="text" name="Auswerter" size="40" maxlength="40" value="<?php echo $Username; ?>"></td>
	</tr>
      </table>

<br>
      <table class="adminlist">
        <tr>
	<th class="key" width="20%" nowrap="nowrap"><?php echo JText::_( 'ELOABSE_HINT_31' ); ?></th>
	<th class="key" width="20%" nowrap="nowrap"><?php echo JText::_( 'ELOABSE_HINT_32' ); ?></th>
	</tr>
<?php
  for ($i=0;$i<$MaxLigen;$i++)
   {
   ?>
	<tr>
	</tr>
        <tr>
	<td class="key" width="20%" nowrap="nowrap"><select name="LiId[]">
          <option selected="Selected" disabled="disabled" value="0"><?php echo JText::_( 'ELOABSE_HINT_33' ); ?></option>
<?php
   foreach ($Ligen as $Irow)
   echo '          <option value="'.$Irow->id.'">'.$Irow->name.'</option>
';
   ?>
          </select>
	</td>
          <td class="key" width="20%" nowrap="nowrap"><input name="LiNa[]" type="text" size="40" maxlength="40"></td>
	</tr>
<?php
   }
  ?>      </table>
	</fieldset>
</div>

		<input type="hidden" name="section" value="elobase" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
	<div class="clr"></div>
</form>
<?php }} ?>