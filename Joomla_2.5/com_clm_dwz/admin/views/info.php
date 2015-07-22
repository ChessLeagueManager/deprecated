<?php

/**
 * @ CLM DWZ Component
 * @Copyright (C) 2012 Fred Baumgarten. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://sv-hennef.de
 * @author Fred Baumgarten
 * @email dc6iq@gmx.de
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class CLM_DWZViewInfo extends JViewLegacy
{

function display ()
{
	JToolBarHelper::title( JText::_( 'CLM DWZ Komponente' ), 'generic.png' );
	JToolBarHelper::help( 'screen.clm_dwz.info' );
?>
<fieldset class="adminform">
	<legend>Informationen</legend>
	<style type="text/css">table { width:90%; }</style>
		<table class="admintable">
		<tbody>
			<tr>
			<td>
			<h2>Eine Komponente zur Darstellung von DWZ-Daten</h2>
			<br>von Fred Baumgarten [sv-hennef.de] - dc6iq@gmx.de
			<br><br>
			<b>Projekt Homepage :  </b> http://www.fishpoke.de<br>
			<br>
			<h2>Einstellungen erfolgen über die Parameter des CLM DWZ Moduls !</h2>
			</td>
			</tr>
		</tbody>
		</table>
</fieldset>

<?php }} ?>
