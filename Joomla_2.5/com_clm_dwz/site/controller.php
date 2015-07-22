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
jimport('joomla.application.component.controller');

class CLM_DWZController extends JControllerLegacy
{
	function display()
	{
		// Setzt einen Standard view
		if ( ! JRequest::getCmd( 'view') ) {
			JRequest::setVar('view', 'categories' );
		}
		parent::display();
	}
}
