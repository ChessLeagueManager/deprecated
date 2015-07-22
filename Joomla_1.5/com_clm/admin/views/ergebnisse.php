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

class CLMViewErgebnisse
{
function setErgebnisseToolbar($val, $rows, $f_lid, $f_runde, $f_dg)
	{
	// Menubilder laden
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');

	if ($val == 1) {
		if ($f_lid >0) { $msg = $rows[0]->liga ;
			if ($f_runde >0) { $msg = $msg.', '.JText::_( 'TITLE_RESULTS_4').$rows[0]->runde; }
			if ($rows[0]->durchgang > 1 AND $f_dg >0) { $msg = $msg.', '.JText::_( 'TITLE_RESULTS_5').$rows[0]->dg;}
				}
		else {
			if ($f_runde >0) { $msg = JText::_( 'TITLE_RESULTS_6').$rows[0]->runde; }
			else { $msg = JText::_( 'TITLE_RESULTS_7');}
			}
	JToolBarHelper::title( JText::_( 'TITLE_RESULTS_2').$msg.JText::_('TITLE_RESULTS_3'), 'clm_settings.png' ); }
	else {	JToolBarHelper::title( JText::_( 'TITLE_RESULTS_1' ), 'generic.png' ); }
		JToolBarHelper::custom('heim_kampflos','send.png','send_f2.png',JText::_('RESULTS_HEIM_KL'),false);
		JToolBarHelper::custom('gast_kampflos','send.png','send_f2.png',JText::_('RESULTS_GAST_KL'),false);
		JToolBarHelper::custom('wertung','send.png','send_f2.png',JText::_('RESULTS_CHANGE_VALUATION'),false);
		JToolBarHelper::editListX();
		JToolBarHelper::deleteList();
	if ($val == 1) { JToolBarHelper::custom('back','cancel.png','download_f2.png',JText::_('MEMBER_BUTTON_BACK'),false); }
		JToolBarHelper::help( 'screen.clm.ergebnisse' );
	}

function ergebnisse ( &$rows, &$lists, &$pageNav, $option )
	{
	global $mainframe;
	$f_lid		= $mainframe->getUserStateFromRequest( "$option.filter_lid",'filter_lid',0,'int' );
	$f_runde	= $mainframe->getUserStateFromRequest( "$option.filter_runde",'filter_runde',0,'int' );
	$f_dg		= $mainframe->getUserStateFromRequest( "$option.filter_dg",'filter_dg',0,'int' );
	// Konfigurationsparameter auslesen
	$config		= &JComponentHelper::getParams( 'com_clm' );
	$val		= $config->get('menue',1);
	$dropdown	= $config->get('dropdown',1);

	CLMViewErgebnisse::setErgebnisseToolbar($val, $rows, $f_lid, $f_runde, $f_dg);
	$user =& JFactory::getUser();
	//Ordering allowed ?
	$ordering = ($lists['order'] == 'a.ordering');

	JHTML::_('behavior.tooltip');
	?>
	<form action="index.php?option=com_clm&section=ergebnisse" method="post" name="adminForm">

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
		//////// eigenes Dropdown Menue /////////////////
		if ($val ==0 OR ( $val ==1 AND $dropdown == 1)) {
			echo "&nbsp;&nbsp;&nbsp;".$lists['sid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['lid'];
			echo "&nbsp;&nbsp;&nbsp;".$lists['runde'];
				}
			if ($rows[0]->durchgang >1) { ?>
			<select name="filter_dg" id="filter_dg" class="inputbox" size="1" onchange="document.adminForm.submit();">
			<option value="- DG -" <?php if ( $f_dg == 0) { ?> selected="selected" <?php } ?>><?php echo JText::_( 'RESULTS_FILTER_DG' );?></option>
			<option value="1"  <?php if ( $f_dg == 1) { ?> selected="selected" <?php } ?>>DG 1</option>
			<option value="2" <?php if ( $f_dg == 2) { ?> selected="selected" <?php } ?>>DG 2</option>
			</select>
		<?php } ?>
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
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_HOME' ), 'hname', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_GUEST' ), 'gname', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th width="15%">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_LEAGUE' ), 'a.lid', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th width="3%">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_ROUND' ), 'a.runde', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th width="3%">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_PAIRING' ), 'a.paar', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th width="3%">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_DG' ), 'a.dg', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_SEASON' ), 's.name', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th width="5%">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_COMMIT' ), 'a.gemeldet', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th width="12%">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_BY' ), 'u.name', @$lists['order_Dir'], @$lists['order'] ); ?>
				</th>
				<th width="2%">
					<?php echo JHTML::_('grid.sort', JText::_( 'RESULTS_OVERVIEW_ID' ), 'a.id', @$lists['order_Dir'], @$lists['order'] ); ?>
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

		$link 		= JRoute::_( 'index.php?option=com_clm&section=ergebnisse&task=edit&cid[]='. $row->id );
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
					if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) OR $row->gemeldet =="1" OR ($row->sid_pub =="0" AND $val =="0")) {
						echo $row->hname;
					} else {
						?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'RESULTS_OVERVIEW_EDIT_TIP' );?>::<?php echo $row->hname.' - '.$row->gname.JText::_( 'RESULTS_OVERVIEW_EDIT_TIP_2' ).$row->runde.JText::_( 'RESULTS_OVERVIEW_EDIT_TIP_3' ).$row->paar; ?>">
						<a href="<?php echo $link; ?>">
							<?php echo $row->hname; ?></a></span>
						<?php
					}
					?>
				</td>
				<td align="center">
					<?php echo $row->gname;?>
				</td>
				<td align="center">
					<?php echo $row->liga;?>
				</td>
				<td align="center">
					<?php echo $row->runde;?>
				</td>
				<td align="center">
					<?php echo $row->paar;?>
				</td>
				<td align="center">
					<?php echo $row->dg;?>
				</td>
				<td align="center">
					<?php echo $row->saison;?>
				</td>
					<td align="center">
					<?php if ($row->gemeldet > 0) 
						{ ?><img width="16" height="16" src="images/apply_f2.png" /> <?php }
					else 	{ ?><img width="16" height="16" src="images/cancel_f2.png" /> <?php }
					if ($row->dwz_editor > 0) { ?> 
					<img width="16" height="16" src="images/extensions_f2.png" /><?php } 
					?>
				</td>
				<td align="center">
					<?php if ($row->gemeldet ==1) { echo JText::_( 'RESULTS_OVERVIEW_FREE' ); }
						else { echo $row->uname; } ?>
				</td>
				<td align="center">
					<?php echo $row->id;?>
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

function setErgebnisToolbar($runde)
	{
		if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
			else { $text = JText::_( 'New' );}
		JToolBarHelper::title(  JText::_( 'TITLE_RESULTS_8').' '.$runde[0]->hname.' - '.$runde[0]->gname .': <small><small>[ '. $text.' ]</small></small>' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
function Ergebnis( &$row, $runde, $heim, $hcount, $gast, $gcount, $bretter, $ergebnis, $option, $hvoraufstellung, $gvoraufstellung)
	{
		CLMViewErgebnisse::setErgebnisToolbar($runde);
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
	?>

	<form action="index.php" method="post" name="adminForm">

	<div class="col width-60">
	<fieldset class="adminform">
	<legend><?php 
	if ($runde[0]->dg == 1) {
	echo $runde[0]->lname.' '.JText::_('TITLE_RESULTS_6').' '.$runde[0]->runde.', '.JText::_('TITLE_RESULTS_9').' '.$runde[0]->paar;
				}
	else {
	echo $runde[0]->lname.' '.JText::_('TITLE_RESULTS_4').' '.$runde[0]->runde.', '.JText::_('TITLE_RESULTS_9').' '.$runde[0]->paar.', '.JText::_('TITLE_RESULTS_10').$runde[0]->dg ;
	} ?>
	</legend>
	<table class="admintable">

	<tr>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'RESULTS_DETAILS_BOARD' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo $runde[0]->hname; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $runde[0]->gname; ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'RESULTS_DETAILS_RESULT' ); ?></td>
	<tr>
<?php 	for ($i=0; $i<$runde[0]->stamm; $i++) { ?>
	
	<tr>
		<td class="key" nowrap="nowrap">
		  <label for="sid">
			<?php echo JText::_('RESULTS_DETAILS_NO').'&nbsp;&nbsp;'.($i+1).'&nbsp;&nbsp;'; ?>
		  </label>
		</td>
		<td class="key" nowrap="nowrap">
		  <select size="1" name="<?php echo 'heim'.($i+1); ?>" id="<?php echo 'heim'.($i+1); ?>">
 		<option value="0"><?php echo JText::_('RESULTS_DETAILS_DD_1');?></option>

			<?php for ($x=0; $x < $hcount[0]->hcount; $x++){
			if ($runde[0]->rang !="0") {
				if (($heim[$x]->mnr >= $runde[0]->hmnr AND $heim[$x]->Rang < 1000 ) OR ($heim[$x]->mnr == $runde[0]->hmnr)){ ?>
			 <option value="<?php echo $heim[$x]->mgl_nr.'-'.$heim[$x]->zps; ?>"
			 	<?php if (($bretter AND ((int)$heim[$x]->mgl_nr) == ((int)$bretter[$i]->spieler) AND $heim[$x]->zps == $bretter[$i]->zps)
			 		// Bedingungen Voreinstellung
			 			OR (!$bretter AND $x == $i AND $bretter[$i]->zps !="ZZZZZ")
			 			OR (!$bretter AND ($hvoraufstellung[$i]->snr-1) == $x AND $bretter[$i]->zps !="ZZZZZ"))
			 				{ ?> selected="selected" <?php } ?>>
			 	<?php echo $heim[$x]->rmnr.'&nbsp;-&nbsp;'.$heim[$x]->snr.'&nbsp;&nbsp;';
			 		if($heim[$x]->snr < 1000) { echo "&nbsp;&nbsp;&nbsp;&nbsp;";};
			 		if($heim[$x]->snr < 10) { echo "&nbsp;&nbsp;";}; 
			 	echo $heim[$x]->name; ?>
			 </option>
			<?php }}
			else { ?>
			 <option value="<?php echo $heim[$x]->mgl_nr.'-'.$heim[$x]->zps; ?>"
			 	<?php if (($bretter AND ((int)$heim[$x]->mgl_nr) == ((int)$bretter[$i]->spieler) AND $heim[$x]->zps == $bretter[$i]->zps)
			 		// Bedingungen Voreinstellung
			 			OR (!$bretter AND $x == $i AND $bretter[$i]->zps !="ZZZZZ")
			 			OR (!$bretter AND ($hvoraufstellung[$i]->snr-1) == $x AND $bretter[$i]->zps !="ZZZZZ"))
			 				{ ?> selected="selected" <?php } ?>>
			 	<?php echo $heim[$x]->mnr.'&nbsp;-&nbsp;'.$heim[$x]->snr.'&nbsp;&nbsp;';
			 		if($heim[$x]->snr < 10) { echo "&nbsp;&nbsp;";}; 
			 	echo $heim[$x]->name; ?>
			 </option> 
			<?php }} ?>
		 <option value="99999-ZZZZZ"<?php if ($bretter[$i]->zps =="ZZZZZ"){ ?> selected="selected"<?php } ?>>&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('RESULTS_DETAILS_NOT_NOMINATED'); ?></option>
		  </select>
		</td>

		<td class="key" nowrap="nowrap">
		  <select size="1" name="<?php echo 'gast'.($i+1); ?>" id="<?php echo 'gast'.($i+1); ?>">
 		<option value="0"><?php echo JText::_('RESULTS_DETAILS_DD_2');?></option>
			<?php for ($x=0; $x < $gcount[0]->gcount; $x++) {
			if ($runde[0]->rang !="0") {
				if (($gast[$x]->mnr >= $runde[0]->gmnr AND $gast[$x]->Rang < 1000 ) OR ($gast[$x]->mnr == $runde[0]->gmnr)){ ?>
			<option value="<?php echo $gast[$x]->mgl_nr.'-'.$gast[$x]->zps; ?>" 
				 <?php if (($bretter AND ((int)$gast[$x]->mgl_nr) == ((int)$bretter[$i]->gegner) AND $gast[$x]->zps == $bretter[$i]->gzps)
				 	 // Bedingungen Voreinstellung
						OR (!$bretter AND $x == $i AND $bretter[$i]->gzps !="ZZZZZ")			 			
						OR (!$bretter AND ($gvoraufstellung[$i]->snr-1) == $x AND $bretter[$i]->gzps !="ZZZZZ"))
			 				{ ?> selected="selected" <?php } ?>>
				 <?php echo $gast[$x]->rmnr.'&nbsp;-&nbsp;'.$gast[$x]->snr.'&nbsp;&nbsp;';
				 	if($gast[$x]->snr < 1000) { echo "&nbsp;&nbsp;&nbsp;&nbsp;";};
				 	if($gast[$x]->snr < 10) { echo "&nbsp;&nbsp;";};
				 		echo $gast[$x]->name; ?>
			</option> 
			<?php }}
			else { ?>
			<option value="<?php echo $gast[$x]->mgl_nr.'-'.$gast[$x]->zps; ?>" 
			 	<?php if (($bretter AND ((int)$gast[$x]->mgl_nr) == ((int)$bretter[$i]->gegner) AND $gast[$x]->zps == $bretter[$i]->gzps)
			 		// Bedingungen Voreinstellung
			 			OR (!$bretter AND $x == $i AND $bretter[$i]->gzps !="ZZZZZ")
			 			OR (!$bretter AND ($gvoraufstellung[$i]->snr-1) == $x AND $bretter[$i]->gzps !="ZZZZZ"))
			 			{ ?> selected="selected" <?php } ?>>
			 	<?php echo $gast[$x]->mnr.'&nbsp;-&nbsp;'.$gast[$x]->snr.'&nbsp;&nbsp;';
			 		if($gast[$x]->snr < 10) { echo "&nbsp;&nbsp;";};
			 			echo $gast[$x]->name; ?>
			 </option> 
			 <?php }} ?>
			 <option value="99999-ZZZZZ"<?php if ($bretter[$i]->gzps =="ZZZZZ"){ ?> selected="selected"<?php } ?>>&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('RESULTS_DETAILS_NOT_NOMINATED'); ?></option>
		  </select>
		</td>

		<td class="key" nowrap="nowrap">
		  <select size="1" name="<?php echo 'ergebnis'.($i+1); ?>" id="<?php echo 'ergebnis'.($i+1); ?>">
			<option value="8"><?php echo JText::_('RESULTS_DETAILS_DD_3');?></option>
			<?php for ($x=0; $x < 9; $x++) { ?>
			 <option value="<?php echo ($ergebnis[$x]->id); ?>" 
			 <?php if ($runde[0]->gemeldet > 0 AND isset($bretter[$i]->ergebnis) AND (((int)$ergebnis[$x]->id)-1) == ((int)$bretter[$i]->ergebnis)) { ?> selected="selected" <?php } ?>>
			 <?php echo $ergebnis[$x]->erg_text; ?></option> 
			<?php }	?>
		  </select>
		</td>

	</tr>
<?php } ?> 
		</table>
		</fieldset>
		</div>

		<div class="col width-40">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RESULTS_DETAILS_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_BY' ); ?></label>
			</td>
			<td>
			<?php if (!$runde[0]->melder) { echo JText::_('RESULTS_DETAILS_NOTHING'); }
				else { echo $runde[0]->melder; } ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_DATE' ); ?></label>
			</td>
			<td>
			<?php if ($runde[0]->zeit != "0000-00-00 00:00:00") {echo JHTML::_('date',  $runde[0]->zeit, JText::_('DATE_FORMAT_LC2'));} 
			else { echo JText::_('RESULTS_DETAILS_NOTHING'); } ?>

			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_LAST_EDIT_BY' ); ?></label>
			</td>
			<td>
			<?php if (!$runde[0]->editor) { echo "---"; }
				else { echo $runde[0]->name_editor; }?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_DATE' ); ?></label>
			</td>
			<td>
			<?php if ($runde[0]->edit_zeit != "0000-00-00 00:00:00") {echo JHTML::_('date',  $runde[0]->edit_zeit, JText::_('DATE_FORMAT_LC2'));} 
			else { echo "---"; } ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_EVALUATION_EDITED_BY' ); ?></label>
			</td>
			<td>
			<?php if (!$runde[0]->dwz_editor) { echo JText::_('RESULTS_DETAILS_NOTHING'); }
				else { echo $runde[0]->dwz_editor; }?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_DATE' ); ?></label>
			</td>
			<td>
			<?php if ($runde[0]->dwz_zeit != "0000-00-00 00:00:00") {echo JHTML::_('date',  $runde[0]->dwz_zeit, JText::_('DATE_FORMAT_LC2'));} 
			else { echo JText::_('RESULTS_DETAILS_NOTHING'); } ?>
			</td>
		</tr>

		</table>
		</fieldset>
		</div>

		<?php if (($runde[0]->runden_modus == 4) OR ($runde[0]->runden_modus == 5)) {    // KO System ?>	
	<div class="col width-60">
	<fieldset class="adminform">
	<legend><?php 
		echo JText::_( 'RESULTS_MT_KO_LEGEND' ); //" KO-System: Feinwertung ";
	 ?>
	</legend>
	<table class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="ko_decision"><?php echo JText::_( 'RESULTS_MT_KO_DECISION' ); ?></label>
			</td>
			<td class="key" nowrap="nowrap">
			<select name="ko_decision" id="ko_decision" value="<?php echo $runde[0]->ko_decision; ?>" size="1">
			<!--<option>- w�hlen -</option>-->
			<option value="1" <?php if ($runde[0]->ko_decision == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BW' );?></option>
			<option value="2" <?php if ($runde[0]->ko_decision == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BLITZ' ).$runde[0]->hname;?></option>
			<option value="3" <?php if ($runde[0]->ko_decision == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BLITZ' ).$runde[0]->gname;?></option>
			<option value="4" <?php if ($runde[0]->ko_decision == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_LOS' ).$runde[0]->hname;?></option>
			<option value="5" <?php if ($runde[0]->ko_decision == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_LOS' ).$runde[0]->gname;?></option>
			</select>
			</td>
		</tr>
	</table>
	</fieldset>
	</div>		
<?php } ?> 

<?php // Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$pcomment = $config->get('kommentarfeld',0);
	if (($pcomment == 1) OR ($pcomment == 2 AND ($runde[0]->runden_modus == 4 OR $runde[0]->runden_modus == 5))) {    // Kommentarfeld ?>			
	<div class="col width-60">
	  <fieldset class="adminform">
		<legend><?php echo JText::_( 'RESULTS_COMMENT_LEGEND' ); ?></legend>
		<table class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="comment"><?php echo JText::_( 'RESULTS_COMMENT' ); ?></label>
			</td>
			<td class="inputbox" nowrap="nowrap" width="100%" valign="top">
			<textarea name="comment" id="comment" cols="40" rows="3" style="width:90%"><?php echo str_replace('&','&amp;',$runde[0]->comment);?></textarea>
			</td>
		</tr>
		</table>
	  </fieldset>	
	</div>		
<?php } ?> 		
		
	
	<div class="clr"></div>

		<input type="hidden" name="section" value="ergebnisse" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="sid" value="<?php echo $runde[0]->sid; ?>" />
		<input type="hidden" name="lid" value="<?php echo $runde[0]->lid; ?>" />
		<input type="hidden" name="rnd" value="<?php echo $runde[0]->runde; ?>" />
		<input type="hidden" name="paarung" value="<?php echo $runde[0]->paar; ?>" />
		<input type="hidden" name="dg" value="<?php echo $runde[0]->dg; ?>" />
		<input type="hidden" name="gemeldet" value="<?php echo $runde[0]->gemeldet; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hzps" value="<?php echo $runde[0]->hzps; ?>" />
		<input type="hidden" name="gzps" value="<?php echo $runde[0]->gzps; ?>" />
		<input type="hidden" name="id" value="<?php echo $runde[0]->id; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}

function setWertungToolbar($row)
	{
		JToolBarHelper::title(  JText::_( 'TITLE_EDIT_EVALUATION' ));
		JToolBarHelper::custom('save_wertung','save.png','save_f2.png',JText::_( 'EVALUATION_CHANGE'),false);
		JToolBarHelper::custom('delete_wertung','delete.png','delete_f2.png',JText::_( 'EVALUATION_DELETE'),false);
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.edit' );
	}
		
function Wertung( &$row, $runde, $bretter, $ergebnis, $option, $lists)
	{
		CLMViewErgebnisse::setWertungToolbar($row);
		JRequest::setVar( 'hidemainmenu', 1 );
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
	?>

	<div class="col width-100">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'EVALUATION_HINT_1'); ?></legend>
	<?php echo JText::_( 'EVALUATION_HINT_2'); ?>
	<br>
	<?php echo JText::_( 'EVALUATION_HINT_3'); ?>
	</fieldset>
	</div>

	<form action="index.php" method="post" name="adminForm">

	<div>
	<div class="col width-100">
	<fieldset class="adminform">
	<legend><?php 
	if ($runde[0]->dg == 1) {
		echo $runde[0]->lname.' : '.JText::_('EVALUATION_ROUND').' '.$runde[0]->runde.', '.JText::_('EVALUATION_PAIRING').' '.$runde[0]->paar;
				}
	else { 
		echo $runde[0]->lname.' : '.JText::_('EVALUATION_ROUND').' '.$runde[0]->runde.', '.JText::_('EVALUATION_PAIRING').' '.$runde[0]->paar.', '.JText::_('EVALUATION_DG').' '.$runde[0]->dg ;
	} ?>
	</legend>
	<table class="admintable">
	<tr>
		<td class="key" nowrap="nowrap" width="25">Brett</td>
		<td class="key" nowrap="nowrap"><?php echo $runde[0]->hname; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $runde[0]->gname; ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'EVALUATION_RESULT'); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'EVALUATION_RESULT_TO'); ?></td>
	<tr>
<?php 	for ($i=0; $i<$runde[0]->stamm; $i++) { ?>
	<tr>
		<td class="key" nowrap="nowrap">
		  <label for="sid">
			<?php echo JText::_( 'RESULTS_DETAILS_NO').'&nbsp;&nbsp;'.($i+1).'&nbsp;&nbsp;'; ?>
		  </label>
		</td>
		<td class="key" nowrap="nowrap">
		<?php echo $bretter[$i]->hname; ?>
		</td>

		<td class="key" nowrap="nowrap">
		<?php echo $bretter[$i]->gname; ?>
		</td>

		<td class="key" nowrap="nowrap">
			<?php for ($x=0; $x < 9; $x++) { ?>
			<?php if ($runde[0]->gemeldet > 0 AND (((int)$ergebnis[$x]->id)-1) == ((int)$bretter[$i]->ergebnis)) { echo $ergebnis[$x]->erg_text;}    }	?>
		</td>

		<td class="key" nowrap="nowrap">
		  <select size="1" name="<?php echo 'ergebnis'.($i+1); ?>" id="<?php echo 'ergebnis'.($i+1); ?>">
			<option value="-1" selected="selected"><?php echo JText::_( 'RESULTS_DETAILS_DD_3'); ?></option>
			<?php for ($x=0; $x < 9; $x++) { ?>
			 <option value="<?php echo ($ergebnis[$x]->eid); ?>" <?php if ($runde[0]->dwz_edit > 0 AND isset($bretter[$i]->dwz_edit) AND (($ergebnis[$x]->id)-1) == ((int)$bretter[$i]->dwz_edit)) { ?> selected="selected" <?php } ?>><?php echo $ergebnis[$x]->erg_text; ?></option>
			<?php } ?>
		  </select>
		</td>

	</tr>
<?php } ?> 
	<tr>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'EVALUATION_RESULT_MANUALLY'); ?>
		</td>
		<td class="key" nowrap="nowrap">
		</td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'EVALUATION_BRETTPUNKTE_MANUALLY'); //klkl ?>
		</td>
		<td class="key" nowrap="nowrap">
		<?php echo $lists['weiss'].' - '.$lists['schwarz']; ?>
		</td>
		<td class="key" nowrap="nowrap">
		</td>
	</tr>
	<?php if ($runde[0]->b_wertung > 0) { 
	$ww_erg = $lists['weiss_w'];
	$sw_erg = $lists['schwarz_w']; ?>
	<tr>
		<td class="key" nowrap="nowrap">
		</td>
		<td class="key" nowrap="nowrap">
		</td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'EVALUATION_WERTPUNKTE_MANUALLY'); //klkl ?>
		</td>
		<td class="key" nowrap="nowrap">
		<input class="inputbox" type="text" name="ww_erg" id="ww_erg" size="8" maxlength="8" value="<?php echo $ww_erg; ?>" />
		<?php echo "   -   "; //klkl ?>
		<input class="inputbox" type="text" name="sw_erg" id="sw_erg" size="8" maxlength="8" value="<?php echo $sw_erg; ?>" />
		</td>
		<td class="key" nowrap="nowrap">
		</td>
	</tr>
	<?php } ?>
	
		</table>
		</fieldset>
		</div>

		<div class="col width-100">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'RESULTS_DETAILS_DETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_BY' ); ?></label>
			</td>
			<td>
			<?php if (!$runde[0]->melder) { echo JText::_( 'RESULTS_DETAILS_NOTHING'); }
				else { echo $runde[0]->melder; } ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_DATE' ); ?></label>
			</td>
			<td>
			<?php if ($runde[0]->zeit != "0000-00-00 00:00:00") {echo JHTML::_('date',  $runde[0]->zeit, JText::_('DATE_FORMAT_LC2'));} 
			else {  echo JText::_( 'RESULTS_DETAILS_NOTHING'); } ?>

			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_LAST_EDIT_BY' ); ?></label>
			</td>
			<td>
			<?php if (!$runde[0]->editor) {  echo JText::_( 'RESULTS_DETAILS_NOTHING'); }
				else { echo $runde[0]->name_editor; }?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'am : ' ); ?></label>
			</td>
			<td>
			<?php if ($runde[0]->edit_zeit != "0000-00-00 00:00:00") {echo JHTML::_('date',  $runde[0]->edit_zeit, JText::_('DATE_FORMAT_LC2'));} 
			else { echo "---"; } ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'EVALUATION_RESULT_EDITED_BY' ); ?></label>
			</td>
			<td>
			<?php if (!$runde[0]->dwz_editor) { echo JText::_( 'RESULTS_DETAILS_NOTHING'); }
				else { echo $runde[0]->dwz_editor; }?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'RESULTS_DETAILS_DATE' ); ?></label>
			</td>
			<td>
			<?php if ($runde[0]->dwz_zeit != "0000-00-00 00:00:00") {echo JHTML::_('date',  $runde[0]->dwz_zeit, JText::_('DATE_FORMAT_LC2'));} 
			else { echo JText::_( 'RESULTS_DETAILS_NOTHING'); } ?>
			</td>
		</tr>

		</table>
		</fieldset>
		</div>
		</div>
	<div class="clr"></div>

		<input type="hidden" name="section" value="ergebnisse" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="sid" value="<?php echo $runde[0]->sid; ?>" />
		<input type="hidden" name="lid" value="<?php echo $runde[0]->lid; ?>" />
		<input type="hidden" name="rnd" value="<?php echo $runde[0]->runde; ?>" />
		<input type="hidden" name="paarung" value="<?php echo $runde[0]->paar; ?>" />
		<input type="hidden" name="dg" value="<?php echo $runde[0]->dg; ?>" />

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hzps" value="<?php echo $runde[0]->hzps; ?>" />
		<input type="hidden" name="gzps" value="<?php echo $runde[0]->gzps; ?>" />
		<input type="hidden" name="id" value="<?php echo $runde[0]->id; ?>" />
		<input type="hidden" name="cid" value="<?php echo $runde[0]->id; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
?>