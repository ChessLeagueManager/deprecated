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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableCLMSaisons extends JTable
{

	var $id			= null;
	var $name		= '';
	var $published		= 0;
	var $archiv		= 0;
	var $bemerkungen	= '';
	var $bem_int		= '';
	var $checked_out	= 0;
	var $checked_out_time	= 0;
	var $ordering		= null;
	var $datum		= '';
	
	function __construct( &$_db ) {
		parent::__construct( '#__clm_saison', 'id', $_db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		// check for valid client name
		if (trim($this->name == '')) {
			$this->setError(JText::_( 'BNR_CLIENT_NAME' ));
			return false;
		}

		// check for valid client contact
/**		if (trim($this->sid == '')) {
			$this->setError(JText::_( 'Saison muss angegeben werden !' ));
			return false;
		}

**/		

		return true;
	}
}
