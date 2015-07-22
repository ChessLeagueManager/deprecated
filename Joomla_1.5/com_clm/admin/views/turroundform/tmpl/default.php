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

		<div class="col width-50">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'ROUND_NAME' ); ?>:</label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="50" maxlength="60" value="<?php echo $this->roundData->name; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap">
			<label for="contact"><?php echo JText::_( 'ROUND_NR' ); ?>:</label>
			</td>
			<td>
			<input class="inputbox" type="text" name="nr" id="nr" size="50" maxlength="60" value="<?php echo $this->roundData->nr; ?>" />
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
			<label for="datum">
				<?php echo JText::_( 'DATE' ); ?>:
			</label>
			</td>
			<td>
			<?php echo JHTML::_('calendar', $this->roundData->datum, 'datum', 'datum', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19')); ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'CLM_PUBLISHED' ); ?>:</label>
			</td>
			<td>
			<?php echo CLMForm::radioPublished ('published', $this->roundData->published); ?>
			</td>
		</tr>
		
		<tr>
			<td class="key" nowrap="nowrap"><label for="verein"><?php echo JText::_( 'ENTRY_ENABLED' ); ?></label>
			</td>
			<td>
			<?php echo JHTML::_('select.booleanlist',  'abgeschlossen', 'class="inputbox"', $this->roundData->abgeschlossen ); ?>

			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="mf"><?php echo JText::_('TOURNAMENT_DIRECTOR')." ".JText::_( 'APPROVAL' ); ?>:</label>
			</td>
			<td>
			<?php echo JHTML::_('select.booleanlist',  'tl_ok', 'class="inputbox"', $this->roundData->tl_ok ); ?>
			</td>
		</tr>


		</table>
		</fieldset>
		</div>

 <div class="col width-50">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'NOTES' ); ?></legend>
	<table class="adminlist">
	<legend><?php echo JText::_( 'NT_PUBLIC' ); ?></legend>
	<br>
	<tr>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$this->roundData->bemerkungen);?></textarea>
	</td>
	</tr>
	</table>

	<table class="adminlist">
	<tr><legend><?php echo JText::_( 'NT_INTERNAL' ); ?></legend>
	<br>
	<td width="100%" valign="top">
	<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$this->roundData->bem_int);?></textarea>
	</td>
	</tr>
	</table>
  </fieldset>
  </div>


	<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turroundform" />
	<input type="hidden" name="turnierid" value="<?php echo $this->param['turnierid']; ?>" />
	<input type="hidden" name="roundid" value="<?php echo $this->param['roundid']; ?>" />
	<input type="hidden" name="controller" value="turroundform" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>
