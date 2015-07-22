<?php
/**
*	name					view.html.php
*	description			view
*
*	start					01.12.2010
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

class DWZListeViewSpieler extends JView
{
	function display($tpl = null) {
		
		$model		= &$this->getModel();
		
		$mainframe = JFactory::getApplication();
		$document =& JFactory::getDocument();
		if (isset($model->playerData[3])) {
			$document->setTitle($mainframe->getCfg('sitename')." - ".JText::_('DWZ_RECORD').": ".$model->playerData[3]);
		} else {
			$document->setTitle($mainframe->getCfg('sitename')." - ".JText::_('DWZ_RECORD').": ".JText::_('NO_DATA'));
		}
		$document->addStyleSheet('components'.DS.'com_dwzliste'.DS.'css'.DS.'style.css');
		
		
		$this->assignRef('params', $model->params);
		
		$this->assignRef('date', $model->date);
		
		$this->assignRef('playerData', $model->playerData);
		$this->assignRef('fideData', $model->fideData);
		
		$this->assignRef('rows', $model->rows);

		$this->assignRef('url', $model->url);
		
		parent::display($tpl);
	
	
	}
	
}
?>
