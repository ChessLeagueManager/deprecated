<?php
/**
*	name					view.html.php
*	description			view stats
*
*	start					07.12.2010
*	last edit			29.12.2010
*	done					stylesheet
*
*	complete				no
*	todo					-
*	wanted				-
*	notes					-
*
*	author				Helge Frowein
*	(c)					2010
*/
jimport( 'joomla.application.component.view');

class DWZListeViewStats extends JView
{
	function display($tpl = null) {
		
		$model		= &$this->getModel();
		
		$mainframe = JFactory::getApplication();
		$document =& JFactory::getDocument();
		$document->setTitle($mainframe->getCfg('sitename')." - ".JText::_('DWZ_STATS'));
		$document->addStyleSheet('components'.DS.'com_dwzliste'.DS.'css'.DS.'style.css');
		
		$this->assignRef('date', $model->date);
		
		$this->assignRef('params', $model->params);
		
		$this->assignRef('stats', $model->stats);
		$this->assignRef('topIntervals', $model->topIntervals);


		$this->assignRef('url', $model->url);
		
		parent::display($tpl);
	
	
	}
	
}
?>
