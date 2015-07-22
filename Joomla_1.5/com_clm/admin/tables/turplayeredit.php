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

class TableCLMTurPlayerEdit extends JTable
{

	var $id				= null;
	var $turnier		= '';
	var $snr			= '';
	var $name			= '';
	var $birthYear		= '';
	var $geschlecht		= '';
	var $verein			= '';
	var $NATrating		= '';
	var $FIDEelo		= '';
	var $titel			= '';
	var $twz			= '';
	var $sum_punkte		= '';
	var $koStatus		= '';
	var $sumTiebr1		= 0;
	var $sumTiebr2		= 0;
	var $sumTiebr3		= 0;


	function __construct( &$_db ) {
		parent::__construct( '#__clm_turniere_tlnr', 'id', $_db );
	}

	
	function check() {

		if (trim($this->name) == '') { // Name vorhanden
			$this->setError( CLMText::errorText('NAME', 'MISSING') );
			return false;
		} elseif (!is_numeric($this->NATrating)) { // TWZ = Zahl
			$this->setError( CLMText::errorText('RATING', 'NOTANUMBER') );
			return false;
		} elseif (!is_numeric($this->FIDEelo)) { // TWZ = Zahl
			$this->setError( CLMText::errorText('FIDE_ELO', 'NOTANUMBER') );
			return false;
		} elseif (!is_numeric($this->twz)) { // TWZ = Zahl
			$this->setError( CLMText::errorText('TWZ', 'NOTANUMBER') );
			return false;
		}

		return true;
	}
}
