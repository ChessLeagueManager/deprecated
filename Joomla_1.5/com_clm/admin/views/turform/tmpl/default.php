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

$turParams = new JParameter($this->turnier->params);

?>

<form action="index.php" method="post" name="adminForm">

  <div class="col width-50">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'DETAILS' ); ?></legend>
      <table class="paramlist admintable">

	<tr>
		<td width="40%" class="paramlist_key">
			<label for="name"><?php echo JText::_( 'TOURNAMENT_NAME' ); ?>:</label>
		</td>
		<td class="paramlist_value">
			<input class="inputbox" type="text" name="name" id="name" size="40" maxlength="60" value="<?php echo $this->turnier->name; ?>" />
		</td>
	</tr>
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="category">
				<?php echo JText::_( 'CATEGORY_ALLTIME' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['catidAlltime']; ?>
		</td>
	</tr>
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="category">
				<?php echo JText::_( 'CATEGORY_EDITION' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['catidEdition']; ?>
		</td>
	</tr>
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="saison">
				<?php echo JText::_( 'SEASON' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['sid']; ?>
		</td>
	</tr>
	
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="dateStart">
				<?php echo JText::_( 'TOURNAMENT_DAYSTART' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo JHTML::_('calendar', $this->turnier->dateStart, 'dateStart', 'dateStart', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19')); ?>
		</td>
	</tr>
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="dateEnd">
				<?php echo JText::_( 'TOURNAMENT_DAYEND' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo JHTML::_('calendar', $this->turnier->dateEnd, 'dateEnd', 'dateEnd', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19')); ?>
		</td>
	</tr>
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="modus">
				<?php echo JText::_( 'MODUS' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php
			// nur, wenn Runden noch nicht erstellt
			if ($this->turnier->rnd == NULL) {
				echo $this->form['modus'];
			} else {
				echo JText::_('MODUS_TYP_'.$this->turnier->typ)." (".JText::_('ROUNDS_CREATED')."!)";
				echo CLMForm::hidden('typ', $this->turnier->typ); // damit JavaScript funktioniert
			}
			?>
		</td>
	</tr>
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="runden">
				<?php echo JText::_( 'ROUNDS_COUNT' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<span id="formRoundscountValue">
			<?php
			// nur, wenn Runden noch nicht erstellt
			if ($this->turnier->rnd == NULL) {
				if ($this->turnier->typ != 2 AND $this->turnier->typ != 3) {
					echo '<input class="inputbox" type="text" name="runden" id="runden" size="10" maxlength="5" value="'.$this->turnier->runden.'" />';
				}
			} else {
				echo $this->turnier->runden;
				echo CLMForm::hidden('runden', $this->turnier->runden); // damit JavaScript funktioniert
			}
			?>
			</span>
			<span id="formRoundscountText">
			<?php
			// nur, wenn Runden noch nicht erstellt
			if ($this->turnier->rnd == NULL) {
				if ($this->turnier->typ == 2 OR $this->turnier->typ == 3) {
					echo $this->turnier->runden." (".JText::_('ROUNDS_COUNT_GENERATED').")";
				}
			}
			?>
			</span>
		</td>
	</tr>


	<tr>
		<td width="40%" class="paramlist_key">
			<label for="dg">
				<?php echo JText::_( 'STAGE_COUNT' ); ?>:
			</label>
		</td>
		<td class="paramlist_value" id="formStagecount">
			<?php
			// nur, wenn Runden noch nicht erstellt
			if ($this->turnier->rnd == NULL) {
				if ($this->turnier->typ != 1 AND $this->turnier->typ != 3) {
					echo $this->form['dg'];
				} else {
					echo '-';
					echo CLMForm::hidden('dg', $this->turnier->dg); // damit JavaScript funktioniert
				}
			} else {
				echo $this->turnier->dg;
				echo CLMForm::hidden('dg', $this->turnier->dg); // damit JavaScript funktioniert
			}
			?>
		</td>
	</tr>

	<tr>
		<td width="40%" class="paramlist_key">
			<label for="teil">
				<?php echo JText::_( 'PARTICIPANT_COUNT' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php
			if ($this->turnier->rnd == NULL) {
			?>
				<input class="inputbox" onchange="showFormRoundscount();" type="text" name="teil" id="teil" size="10" maxlength="5" value="<?php echo $this->turnier->teil; ?>" />
			<?php
			} else {
				echo $this->turnier->teil;
				echo CLMForm::hidden('teil', $this->turnier->teil); // damit JavaScript funktioniert
			}
			?>
		</td>
	</tr>

	<tr>
		<td width="40%" class="paramlist_key">
			<label for="tiebreakers">
				<?php echo JText::_( 'TIEBREAKERS' ); ?>:
			</label>
		</td>
		<td class="paramlist_value" id="formTiebreakers">
			<?php
			if ($this->turnier->typ != 3) {
				echo '1. '.$this->form['tiebr1'].'<br />';
				echo '2. '.$this->form['tiebr2'].'<br />';
				echo '3. '.$this->form['tiebr3'].'<br />';
			} else {
				echo '-';
			}
			?>
		</td>
	</tr>
	<tr>
		<td class="paramlist_key">
			<?php echo JText::_('OPTION_TIEBREAKERSFIDECORRECT'); ?>:
		</td>
		<td class="paramlist_value">
			<?php echo JHTML::_('select.booleanlist', 'params[optionTiebreakersFideCorrect]', 'class="inputbox"', $turParams->get('optionTiebreakersFideCorrect', 0)); ?>
		</td>
	</tr>

	<tr>
		<td width="40%" class="paramlist_key">
			<?php echo JText::_('OPTION_USEASTWZ'); ?>:
		</td>
		<td class="paramlist_value">
			<?php 
			$options = array();
			$options[0] = JText::_('OPTION_USEASTWZ_0');
			$options[1] = JText::_('OPTION_USEASTWZ_1');
			$options[2] = JText::_('OPTION_USEASTWZ_2');
			$optionlist = array();
			foreach ($options as $key => $val) {
				$optionlist[]	= JHTML::_('select.option', $key, $val, 'id', 'name' );
			}
			echo JHTML::_('select.genericlist', $optionlist, 'params[useAsTWZ]', 'class="inputbox"', 'id', 'name', $turParams->get('useAsTWZ', 0)); ?>
		</td>
	</tr>

	</table>
  </fieldset>
  
  <fieldset class="adminform">
  <legend><?php echo JText::_( 'QUALIFICATION' ); ?></legend>
      <table class="paramlist admintable">
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('LEAGUE_UP'); ?>:
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="params[qualiUp]" id="params[qualiUp]" size="2" maxlength="2" value="<?php echo $turParams->get('qualiUp', 0); ?>" />
				</td>
				<td class="paramlist_key">
					<?php echo JText::_('LEAGUE_UP_POSSIBLE'); ?>:
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="params[qualiUpPoss]" id="params[qualiUpPoss]" size="2" maxlength="2" value="<?php echo $turParams->get('qualiUpPoss', 0); ?>" />
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('LEAGUE_DOWN'); ?>:
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="params[qualiDown]" id="params[qualiDown]" size="2" maxlength="2" value="<?php echo $turParams->get('qualiDown', 0); ?>" />
				</td>
				<td class="paramlist_key">
					<?php echo JText::_('LEAGUE_DOWN_POSSIBILE'); ?>:
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="params[qualiDownPoss]" id="params[qualiDownPoss]" size="2" maxlength="2" value="<?php echo $turParams->get('qualiDownPoss', 0); ?>" />
				</td>
			</tr>
  
  
		</table>
	</fieldset>
  
  
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'PERSONALIA' ); ?></legend>
      <table class="paramlist admintable">
	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="tl">
				<?php echo JText::_( 'TOURNAMENT_DIRECTOR' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['tl']; ?>
		</td>
	</tr>


	<tr>
		<td width="40%" class="paramlist_key">
			<label for="bezirkVer">
				<?php echo JText::_( 'DISTRICT_EVENT' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['bezirkTur']; ?>
		</td>
	</tr>

	
	<tr>
		<td width="40%" class="paramlist_key">
			<label for="vereinZPS">
				<?php echo JText::_( 'ORGANIZER' ); ?>/<?php echo JText::_( 'HOSTER' ); ?>:
			</label>
		</td>
		<td class="paramlist_value">
			<?php echo $this->form['vereinZPS']; ?>
		</td>
	</tr>
	
			</table>
		</fieldset>
	</div>

	<div class="col width-50">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_( 'STATUS' ); ?></legend>
		
			<table class="paramlist admintable">
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="published">
							<?php echo JText::_( 'PUBLISHED' ); ?>:
						</label>
					</td>
					<td class="paramlist_value">
						<?php echo $this->form['published']; ?>
					</td>
				</tr>
			</table>
	
		</fieldset>
	</div>
	
	
	<div class="col width-50">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_( 'DISPLAY_OPTIONS' ); ?></legend>
		
			<table class="paramlist admintable">
				
				<tr>
					<td width="40%" class="paramlist_key">
						<?php echo JText::_('OPTION_ADDCATTONAME'); ?>:
					</td>
					<td class="paramlist_value">
						<?php
						$options = array();
						$options[0] = JText::_('OPTION_ADDCATTONAME_0');
						$options[1] = JText::_('OPTION_ADDCATTONAME_1');
						$options[2] = JText::_('OPTION_ADDCATTONAME_2');
						$optionlist = array();
						foreach ($options as $key => $val) {
							$optionlist[]	= JHTML::_('select.option', $key, $val, 'id', 'name' );
						}
						echo JHTML::_('select.genericlist', $optionlist, 'params[addCatToName]', 'class="inputbox"', 'id', 'name', $turParams->get('addCatToName', 0));
						?>
					</td>
				</tr>
				
				<tr>
					<td width="40%" class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYROUNDDATE'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayRoundDate]', 'class="inputbox"', $turParams->get('displayRoundDate', 1)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERSNR'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayPlayerSnr]', 'class="inputbox"', $turParams->get('displayPlayerSnr', 1)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERTITLE'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayPlayerTitle]', 'class="inputbox"', $turParams->get('displayPlayerTitle', 1)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERCLUB'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayPlayerClub]', 'class="inputbox"', $turParams->get('displayPlayerClub', 1)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERRATING'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayPlayerRating]', 'class="inputbox"', $turParams->get('displayPlayerRating', 0)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERELO'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayPlayerElo]', 'class="inputbox"', $turParams->get('displayPlayerElo', 0)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERFIDELINK'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayPlayerFideLink]', 'class="inputbox"', $turParams->get('displayPlayerFideLink', 0)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYPLAYERFEDERATION'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayPlayerFederation]', 'class="inputbox"', $turParams->get('displayPlayerFederation', 0)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_DISPLAYTLOK'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[displayTlOK]', 'class="inputbox"', $turParams->get('displayTlOK', $this->params['tourn_showtlok'])); ?>
					</td>
				</tr>
			</table>
	
		</fieldset>
	</div>

	<div class="col width-50">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_( 'ADDITIONAL_OPTIONS' ); ?></legend>
		
			<table class="paramlist admintable">
	
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PGNINPUT'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[pgnInput]', 'class="inputbox"', $turParams->get('pgnInput', 1)); ?>
					</td>
				</tr>
			
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PGNPUBLIC'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[pgnPublic]', 'class="inputbox"', $turParams->get('pgnPublic', 1)); ?>
					</td>
				</tr>
			
			
			</table>
	
		</fieldset>
	</div>
	
	<div class="col width-50">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_( 'PLAYER_VIEW_DISPLAY_OPTIONS' ); ?></legend>
		
			<table class="paramlist admintable">
	
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PLAYER_VIEW_DISPLAY_SEX'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[playerViewDisplaySex]', 'class="inputbox"', $turParams->get('playerViewDisplaySex', 1)); ?>
					</td>
				</tr>
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_PLAYER_VIEW_DISPLAY_BIRTH_YEAR'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[playerViewDisplayBirthYear]', 'class="inputbox"', $turParams->get('playerViewDisplayBirthYear', 1)); ?>
					</td>
				</tr>
				
			
			
			</table>
	
		</fieldset>
	</div>
	
	<div class="col width-50">
		<fieldset class="adminform">
	
			<legend><?php echo JText::_( 'JOOMGALLERY_OPTIONS' ); ?></legend>
		
			<table class="paramlist admintable">
	
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_JOOMGALLERY_DISPLAY_PLAYER_PHOTOS'); ?>:
					</td>
					<td class="paramlist_value">
						<?php echo JHTML::_('select.booleanlist', 'params[joomGalleryDisplayPlayerPhotos]', 'class="inputbox"', $turParams->get('joomGalleryDisplayPlayerPhotos', 0)); ?>
					</td>
				</tr>
			
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_JOOMGALLERY_CATEGORY_ID'); ?>:
					</td>
					<td class="paramlist_value">
						<input class="inputbox" type="text" name="params[joomGalleryCatId]" id="params[joomGalleryCatId]" value="<?php echo $turParams->get('joomGalleryCatId', ''); ?>" />
					</td>
				</tr>
                
				<tr>
					<td class="paramlist_key">
						<?php echo JText::_('OPTION_JOOMGALLERY_PHOTOS_WIDTH'); ?>:
					</td>
					<td class="paramlist_value">
						<input class="inputbox" type="text" name="params[joomGalleryPhotosWidth]" id="params[joomGalleryPhotosWidth]" value="<?php echo $turParams->get('joomGalleryPhotosWidth', ''); ?>" />
					</td>
				</tr>
			
			
			</table>
	
		</fieldset>
	</div>

	<div class="col width-50">
		<fieldset class="adminform">
			
			<legend><?php echo JText::_( 'NOTES' ); ?></legend>
		
			<table class="paramlist admintable">
				<legend><?php echo JText::_( 'NT_PUBLIC' ); ?></legend>
				<tr>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$this->turnier->bemerkungen);?></textarea>
					</td>
				</tr>
			</table>
		
			<table class="adminlist">
				<legend><?php echo JText::_( 'NT_INTERNAL' ); ?></legend>
				<tr>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$this->turnier->bem_int);?></textarea>
					</td>
				</tr>
			</table>
		
		</fieldset>
	</div>


<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turform" />
	<input type="hidden" name="id" value="<?php echo $this->turnier->id; ?>" />
	<input type="hidden" name="controller" value="turform" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>
