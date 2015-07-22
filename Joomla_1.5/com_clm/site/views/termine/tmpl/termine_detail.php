<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Fjodor Sch�fer
 * @email ich@vonfio.de
*/

defined('_JEXEC') or die('Restricted access');

$sid		= JRequest::getInt('saison','1');
$nr			= JRequest::getVar('nr');
$itemid		= JRequest::getInt('Itemid');

$termine_detail	= $this->termine_detail;

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');

// Browsertitelzeile setzen
$doc =& JFactory::getDocument();
$daten['title'] = JText::_('TERMINE_HEAD');
$doc->setHeadData($daten);
           
 // Datumsberechnungen
$startdate[0] = strtotime($termine_detail[0]->startdate);
$enddate[0] = strtotime($termine_detail[0]->enddate);
    
$arrWochentag = array( "Monday" => "Montag", "Tuesday" => "Dienstag", "Wednesday" => "Mittwoch", "Thursday" => "Donnerstag", "Friday" => "Freitag", "Saturday" => "Samstag", "Sunday" => "Sonntag", );
       
?>
<div id="clm">
<div id="termine">
    <div class="componentheading"><?php echo JText::_('TERMINE_HEAD') ?></div>
    
	<!-- Navigationsmenu -->
    <?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>
    <br />
    
    <?php if ($termine_detail[0]->published == 0) {	?>
    <div class="wrong"><?php echo JText::_('NO_ROUNDS') ?></div>
    <?php  } else {  ?>
    	<table>
        	<tr>
            	<td width="200"><?php echo JText::_('TERMINE_TITLE') ?></td>
            	<td><?php echo $termine_detail[0]->name; ?></td>
            </tr>
            <?php if ($termine_detail[0]->hostname <>'') { ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_HOST') ?></td>
				<?php if (strlen($termine_detail[0]->host) == 5) { ?>
            	<td><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $termine_detail[0]->host; if ($itemid <>'') { echo "&Itemid=". $itemid; } ?>"><?php echo $termine_detail[0]->hostname; ?></a></td>
        		<?php } else { ?>
            	<td><?php echo $termine_detail[0]->hostname; ?></td>
        		<?php } ?>
            </tr>
            <?php } if ($termine_detail[0]->address <> '') { ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_ADRESS') ?></td>
            	<td><?php echo $termine_detail[0]->address; ?></td>
            </tr>
            <?php } if ($termine_detail[0]->event_link <>'') { ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_EVENT_LINK') ?></td>
            	<td><a href="<?php echo $termine_detail[0]->event_link; ?>"><?php echo $termine_detail[0]->event_link; ?></a></td>
            </tr>
            <?php } if ($termine_detail[0]->category <> '') { ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_KATEGORIE') ?></td>
            	<td><?php echo $termine_detail[0]->category; ?></td>
            </tr>
            <?php } ?>
        	<tr>
            	<td><?php echo JText::_('TERMINE_DATUM') ?></td>
            	<td>
					<?php  echo $arrWochentag[date("l",$startdate[0])]. ",&nbsp;". JHTML::_( 'date', $termine_detail[0]->startdate, JText::_('DATE_FORMAT_CLM'));
					 if ($termine_detail[0]->enddate != 0) { echo "&nbsp;-&nbsp;". $arrWochentag[date("l",$enddate[0])]. ",&nbsp;". JHTML::_( 'date', $termine_detail[0]->enddate, JText::_('DATE_FORMAT_CLM')); } ?></td>
            </tr>
        </table>
        
		<?php if ($termine_detail[0]->beschreibung <>"") {	?>
        <table>
        	<tr>
            	<td colspan="2"><?php echo JText::_('TERMINE_DESC') ?></td>
            </tr>
        	<tr>
            	<td colspan="2"><?php echo $termine_detail[0]->beschreibung; ?></td>
            </tr>
        </table>
		<?php  } ?>
    <?php  } ?>
    
    <a href="index.php?option=com_clm&amp;view=termine&amp;saison=<?php echo $sid; if ($itemid <>'') { echo "&Itemid=". $itemid; } ?>"><?php echo JText::_('TERMINE_BACK') ?></a>
    
    <br>
    <br>
    <?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>
    <div class="clr"></div>
</div>
</div>
