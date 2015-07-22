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

class TableCLMTurnier_Teilnehmer extends JTable
{

	var $id			= null;
	var $sid		= null;
	var $turnier		= '';
	var $snr		= '';
	var $mgl_nr		= '';
	var $zps		= '';
	var $status		= '';
	var $DWZ		= '';
	var $sum_punkte		= '';
	var $sum_bhlz		= '';
	var $sum_sobe		= '';
	var $I0			= '';
	var $Punkte		= 0;
	var $Partien		= 0;
	var $We			= 0;
	var $Leistung		= 0;
	var $EFaktor		= 0;
	var $Niveau		= 0;
	var $published		= 0;
	var $checked_out	= 0;
	var $checked_out_time	= 0;
	var $ordering		= null;


	function __construct( &$_db ) {
		parent::__construct( '#__clm_turniere_tlnr', 'id', $_db );
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

		return true;
	}
}
