<?php

/**
  * @ CLM Extern Component
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

class CLM_EXTController extends JController
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
