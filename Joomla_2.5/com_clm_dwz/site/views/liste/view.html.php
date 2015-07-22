<?php
/**
*	name					view.html.php
*	description			view
*
*	start					30.11.2010
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

class clm_dwzViewListe extends JViewLegacy
{
	function display($tpl = null) {
		
		$model		= $this->getModel();
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$document->setTitle($mainframe->getCfg('sitename')." - ".JText::_('DWZ_LISTE'));
		$document->addStyleSheet('components'.DS.'com_dwzliste'.DS.'css'.DS.'style.css');
		
		$this->assignRef('params', $model->params);
		$this->assignRef('order', $model->order);
		
		$this->assignRef('date', $model->date);
		$this->assignRef('data', $model->data);

		$this->assignRef('url', $model->url);
		
		parent::display($tpl);
	
	
	}
	
}
?>
