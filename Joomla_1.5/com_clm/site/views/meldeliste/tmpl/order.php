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
JRequest::checkToken() or die( 'Invalid Token' );

global $mainframe;

// Stylesheet laden

require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Variablen holen
$sid 		= JRequest::getInt('saison','1');
$lid 		= JRequest::getInt('lid','1');
$zps 		= JRequest::getVar('zps');
$man 		= JRequest::getInt('man_nr');
$stamm 		= JRequest::getInt('stamm');
$ersatz		= JRequest::getInt('ersatz');
$cid 		= JRequest::getVar('cid', array(), '', 'array');
$man_name 	= JRequest::getVar('man_name');

JArrayHelper::toInteger($cid);
$cids = implode( ',', $cid );
// Login Status prüfen
// Prüfen ob Datensatz schon vorhanden ist
	$db			= JFactory::getDBO();
	$query	= "SELECT id, liste "
		." FROM #__clm_mannschaften "
		." WHERE sid = $sid AND zps = '".$zps."' AND man_nr = $man AND published = 1 "
		;
	$db->setQuery( $query );
	$test=$db->loadObjectList();

if ($test[0]->id < 1) {
	$link = 'index.php?option='.$option.'&view=info';
	$msg = JText::_( 'CLUB_LIST_TEAM_DISABLED' );
	$mainframe->redirect( $link, $msg );
 			}
if ($test[0]->liste > 0) {
	$link = 'index.php?option='.$option.'&view=info';
	$msg = JText::_( 'CLUB_LIST_ALREADY_EXIST' );
	$mainframe->redirect( $link, $msg );
 			}

// NICHT vorhanden //
?>
<div id="clm">
<div id="meldeliste">
<div class="componentheading"><?php echo JText::_('CLUB_LIST_SORT_LIST') ?> <?php echo $man_name; ?></div>
<br>
<div id="desc">
<h4><?php echo JText::_('CLUB_LIST_NOTE') ?></h4>
<ol>
<li><?php echo JText::_('CLUB_LIST_HINT_S1') ?></li>
<li><?php echo JText::_('CLUB_LIST_HINT_S2') ?></li>
<li><?php echo JText::_('CLUB_LIST_HINT_S3') ?></li>
<li><?php echo JText::_('CLUB_LIST_HINT_S4') ?></li>
</ol>
<?php echo JText::_('CLUB_LIST_PLANNED') ?>
</div>
<?php 

?>
<?php $sort= CLMModelMeldeliste::Sortierung ($cids); ?>
<br>
<script type="text/javascript"><!--
        function Tausch ( idA, idB )
        {
          // Name tauschen
          var nameA = document.getElementById ( "name" + idA );
          var nameB = document.getElementById ( "name" + idB );
          tmp = nameA.innerHTML;
          nameA.innerHTML = nameB.innerHTML;
          nameB.innerHTML = tmp;

          // DWZ tauschen
          var dwzA = document.getElementById ( "dwz" + idA );
          var dwzB = document.getElementById ( "dwz" + idB );
          tmp = dwzA.innerHTML;
          dwzA.innerHTML = dwzB.innerHTML;
          dwzB.innerHTML = tmp;

          // mglnr tauschen
          var mglnrA = document.getElementById ( "mglnr" + idA );
          var mglnrB = document.getElementById ( "mglnr" + idB );
          tmp = mglnrA.innerHTML;
          mglnrA.innerHTML = mglnrB.innerHTML;
          mglnrB.innerHTML = tmp;

          // (hidden) Mgl_Nr tauschen
          var hiddenA = document.getElementsByName ( "hidden_mglnr" + idA ) [0];
          var hiddenB = document.getElementsByName ( "hidden_mglnr" + idB ) [0];
          tmp = hiddenA.value;
          hiddenA.value = hiddenB.value;
          hiddenB.value = tmp;
        }

        function NachUnten ( $id, $nofocus )
        {
		if ( document.getElementsByName ( "hidden_mglnr" + ( $id + 1 ) ).length )
          {
            Tausch ( $id, $id + 1 );
		if ( arguments.length == 2 ) 
		document.getElementById ( "runter" + ( $id + 1 ) ).focus ();
          }
        }

        function NachOben ( $id, $nofocus )
        {
          if ( $id > 1 )
          {
            Tausch ( $id - 1, $id );
		if ( arguments.length == 2 ) 
		document.getElementById ( "hoch" + ( $id - 1 ) ).focus ();
          }
        }
--></script>

<form action="index.php?option=com_clm&amp;view=meldeliste&amp;layout=sent" method="post" name="adminForm">
<center>
<table class="adminlist" cellpadding="0" cellspacing="0">
	<tr> 
		<th class="anfang" width="5"><?php echo JText::_('CLUB_LIST_NR') ?></th>
		<th class="anfang"><?php echo JText::_('CLUB_LIST_NAME') ?></th>
		<th class="anfang" width="20"><?php echo JText::_('CLUB_LIST_DWZ') ?></th>
		<th class="anfang" width="20"><?php echo JText::_('CLUB_LIST_MGL') ?></th>
		<th class="anfang" width="20"><?php echo JText::_('CLUB_LIST_SORT_DIR') ?></th>
	</tr>

<?php $i = 0;
	foreach($cid as $cid){ 
	if ($i< ($stamm+$ersatz)) {
	?>
	<tr>
		<td><?php echo $i+1; ?></td>
		<td><span id="name<?php echo $i+1; ?>"><?php echo $sort[$i]->name; ?></span><input type="hidden" name="hidden_mglnr<?php echo $i+1; ?>" value="<?php echo $sort[$i]->id; ?>" /></td>
		<td id="dwz<?php echo $i+1; ?>"><?php echo $sort[$i]->dwz; ?></td>
		<td id="mglnr<?php echo $i+1; ?>"><?php echo $sort[$i]->id; ?></td>
		<td>&nbsp;&nbsp;<a href="javascript:NachOben(<?php echo $i+1; ?>);" id="hoch<?php echo $i+1; ?>"><img  src="administrator/images/uparrow.png" alt="NachOben" /></a>&nbsp;&nbsp;&nbsp;<a href="javascript:NachUnten(<?php echo $i+1; ?>);" id="runter<?php echo $i+1; ?>"><img  src="administrator/images/downarrow.png" alt="NachUnten" /></a></td>
	</tr>
	<?php $i++;}} ?> 
</table>
<br />
	<input type="submit" value=" <?php echo JText::_('CLUB_LIST_SEND') ?> ">
		<input type="hidden" name="sid" value="<?php echo $sid; ?>" />
		<input type="hidden" name="liga" value="<?php echo $lid; ?>" />
		<input type="hidden" name="man_nr" value="<?php echo $man; ?>" />
		<input type="hidden" name="zps" value="<?php echo $zps; ?>" />
		<input type="hidden" name="stamm" value="<?php echo $stamm; ?>" />
		<input type="hidden" name="ersatz" value="<?php echo $ersatz; ?>" />
		<input type="hidden" name="man_name" value="<?php echo $man_name; ?>" />
		<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</center>
<br>
</div>
</div>
<?php	require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>