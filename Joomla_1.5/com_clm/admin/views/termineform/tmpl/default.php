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

	<fieldset class="adminform">
		<legend><?php echo JText::_( 'DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'TERMINE_TASK' ); ?>:</label>
			</td>
			<td>
			<input class="inputbox" type="text" name="name" id="name" size="50" maxlength="60" value="<?php echo $this->termine->name; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'TERMINE_HOST' ); ?>:</label>
			</td>
			<td>
			<?php echo $this->form['vereinZPS']; ?>
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
			<label for="adresse">
			<?php echo JText::_( 'TERMINE_ADRESS' ); ?>:
			</label>
			</td>
			<td>
			<input class="inputbox" type="text" name="address" id="address" size="50" maxlength="60" value="<?php echo $this->termine->address; ?>" />
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
			<label for="event_link">
			<?php echo JText::_( 'TERMINE_EVENT_LINK' ); ?>:
			</label>
			</td>
			<td>
			<input class="inputbox" type="text" name="event_link" id="event_link" size="60" maxlength="100" value="<?php echo $this->termine->event_link; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'TERMINE_KATEGORIE' ); ?>:</label>
			</td>
			<td>
			<input class="inputbox" type="text" name="category" id="category" size="32" maxlength="60" value="<?php echo $this->termine->category; ?>" />
			</td>
		</tr>
        
		<tr>
			<td width="100" align="right" class="key">
			<label for="datum">
			<?php echo JText::_( 'TERMINE_STARTDATE' ); ?>:
			</label>
			</td>
			<td>
			<?php echo JHTML::_('calendar', $this->termine->startdate, 'startdate', 'startdate', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19')); ?>
			</td>
		</tr>
        
		<tr>
			<td width="100" align="right" class="key">
			<label for="datum">
			<?php echo JText::_( 'TERMINE_ENDDATE' ); ?>:
			</label>
			</td>
			<td>
			<?php echo JHTML::_('calendar', $this->termine->enddate, 'enddate', 'enddate', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19')); ?>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'PUBLISHED' ); ?></label>
			</td>
			<td>
			<?php echo $this->form['published']; ?>
			</td>
		</tr>
		</table>
	</fieldset>
	
    <fieldset class="adminform">
		<legend><?php echo JText::_( 'TERMINE_DESCRIPTION' ); ?></legend>
		<textarea class="inputbox" name="beschreibung" id="beschreibung" cols="50" rows="10" style="width:99%"><?php echo str_replace('&','&amp;',$this->termine->beschreibung);?></textarea>
	</fieldset>
	
	<div class="clr"></div>

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="termineform" />
	<input type="hidden" name="id" value="<?php echo $this->termine->id; ?>" />
	<input type="hidden" name="controller" value="termineform" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>
