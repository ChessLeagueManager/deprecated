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

// Variablen holen
$sid = JRequest::getInt( 'saison', '1' ); 
$zps = JRequest::getVar( 'zps','1');
$man = JRequest::getInt( 'man' ); 

// Login Status prüfen
$clmuser 	= $this->clmuser;
$user		=& JFactory::getUser();
	global $mainframe;
	$link = 'index.php';
// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	$conf_meldeliste=$config->get('conf_meldeliste',1);

if ($conf_meldeliste != 1) {
	$msg = JText::_( 'CLUB_LIST_DISABLED');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
			}
if (!$user->get('id')) {
	$msg = JText::_( 'CLUB_LIST_LOGIN' );
	$mainframe->redirect( $link, $msg );
 			}
if ($clmuser[0]->published < 1) {
	$msg = JText::_( 'CLUB_LIST_ACCOUNT' );
	$mainframe->redirect( $link, $msg );
				}
if ($clmuser[0]->zps <> $zps) {
	$msg = JText::_( 'CLUB_LIST_FALSE' );
	$mainframe->redirect( $link, $msg );
				}

if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps){

// Prüfen ob Datensatz schon vorhanden ist
$access		= $this->access;
$abgabe		= $this->abgabe;

if ($abgabe[0]->id < 1) {
	$msg = JText::_( 'CLUB_LIST_TEAM_DISABLED' );
	$mainframe->redirect( $link, $msg );
 			}
if ($abgabe[0]->liste > 0) {
	$msg = JText::_( 'CLUB_LIST_ALREADY_EXIST' );
	$mainframe->redirect( $link, $msg );
 			}
// NICHT vorhanden
else {

// Stylesheet laden

require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

// Variablen initialisieren
$liga 		= $this->liga;
$spieler	= $this->spieler;
$count		= $this->count;
?>
<div id="clm">
<div id="meldeliste">
<div class="componentheading"><?php echo JText::_('CLUB_LIST_LIST') ?> <?php echo $liga[0]->man_name; ?></div>
<br>
<div id="desc">
<h4><?php echo JText::_('CLUB_LIST_NOTE') ?></h4>
<ol>
<li><?php echo JText::_('CLUB_LIST_1') ?></li>
<li><?php echo JText::_('CLUB_LIST_2') ?></li>
<li><?php echo JText::_('CLUB_LIST_3') ?></li>
<li><?php echo JText::_('CLUB_LIST_4') ?></li>
<li><?php echo JText::_('CLUB_LIST_5') ?></li>
</ol>
<?php echo JText::_('CLUB_LIST_PLANNED') ?>
</div>
<?php /** echo "<br>Saison ".$sid;
echo "<br>zps ".$zps;
echo "<br>man ".$man;
echo "<br>Stamm ".$liga[0]->stamm;
echo "<br>Ersatz ".$liga[0]->ersatz;
echo "<br>published ".$clmuser[0]->published;
**/
?>
<br>


<form action="index.php?option=com_clm&amp;view=meldeliste&amp;layout=order" method="post" name="adminForm">
<center>
<table class="adminlist" cellpadding="0" cellspacing="0">
<tr> 
	<th class="anfang"><b><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $spieler ); ?>);" /></b></th> 
	<th class="anfang"><?php echo JText::_('CLUB_LIST_NAME') ?></th>
	<th class="anfang"><?php echo JText::_('CLUB_LIST_DWZ') ?></th>
</tr>

<?php $i = 0;
	foreach($spieler as $spieler){
		$checked = JHTML::_('grid.checkedOut',   $spieler, $i );
	?>
	<tr>
	<td id="cb<?php echo $i; ?>" name="cb<?php echo $i; ?>" ><?php echo $checked; ?>
	</td>
	<td>
		<input type="hidden" name="mglnr<?php echo $i+1; ?>" value="<?php echo $spieler->id; ?>" />
		<?php echo $spieler->name; ?>
	</td>
	<td>
		<?php echo $spieler->dwz; ?>
	</td>
	</tr>
	<?php $i++;} ?> 
	</table>
<br>
<input type="submit" value=" <?php echo JText::_('CLUB_LIST_SORT') ?> ">
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="saison" value="<?php echo $sid; ?>" />
		<input type="hidden" name="lid" value="<?php echo $liga[0]->lid; ?>" />
		<input type="hidden" name="zps" value="<?php echo $zps; ?>" />
		<input type="hidden" name="man_nr" value="<?php echo $man; ?>" />
		<input type="hidden" name="stamm" value="<?php echo $liga[0]->stamm; ?>" />
		<input type="hidden" name="ersatz" value="<?php echo $liga[0]->ersatz; ?>" />
		<input type="hidden" name="man_name" value="<?php echo $liga[0]->man_name; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
<?php }} ?>
</center>
<br>
</div></div>
<?php	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
?>