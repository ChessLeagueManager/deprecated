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

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class CLMModelTurnier_Tabelle extends JModel {
	
	function __construct() {
		
		parent::__construct();

		$this->turnierid = JRequest::getInt('turnier', 0);
		$this->spRang = JRequest::getInt('spRang', 0); 		//Sonderranglisten

		$this->_getTurnierData();

		$this->_getTurnierPlayers();

	}
	
	
	
	function _getTurnierData() {
	
		$query = "SELECT *"
			." FROM #__clm_turniere"
			." WHERE id = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->turnier = $this->_db->loadObject();

		// turniernamen anpassen?
		$turParams = new JParameter($this->turnier->params);
		$addCatToName = $turParams->get('addCatToName', 0);
		if ($addCatToName != 0 AND ($this->turnier->catidAlltime > 0 OR $this->turnier->catidEdition > 0)) {
			$this->turnier->name = CLMText::addCatToName($addCatToName, $this->turnier->name, $this->turnier->catidAlltime, $this->turnier->catidEdition);
		}

	}
	
	
	function _getTurnierPlayers() {
	
		$query = "SELECT rankingPos, snr, name, birthYear, geschlecht, sid, zps, verein, twz, titel, sum_punkte, sumTiebr1, sumTiebr2, sumTiebr3"
			." FROM `#__clm_turniere_tlnr`"
			." WHERE turnier = ".$this->turnierid
			.$this->_getSpecialRankingWhere()		//Sonderranglisten
			." ORDER BY rankingPos ASC, sum_punkte DESC, sumTiebr1 DESC, sumTiebr2 DESC, sumTiebr3 DESC, snr ASC";
			;
		
		$this->_db->setQuery($query);
		$this->players = $this->_db->loadObjectList();
		
		$this->turnier->playersCount = count($this->players);
		
		
		//RankingPos neu berechnen für Sonderranglisten
		if($this->turnier->playersCount != 0){
			if($this->spRang != 0){
				$spRankingPos = 0;		
				$rankingPosBefor = 0;	
				foreach($this->players as $key => $player) {
					if($rankingPosBefor != $player->rankingPos){
						$spRankingPos++;
					}
					$rankingPosBefor = $player->rankingPos;
					$this->players[$key]->rankingPos = $spRankingPos;
				}
			}
		}
	}
	
	
	//Sonderranglisten
	function _getSpecialRankingWhere()	{
		$where = "";
		if($this->spRang != 0){
		
			$query = "	SELECT 
							`name`, `use_rating_filter`, `rating_type`, `rating_higher_than`, `rating_lower_than`, `use_birthYear_filter`, `birthYear_younger_than`, `birthYear_older_than`, `use_sex_filter`, `sex`, `use_zps_filter`, `zps_higher_than`, `zps_lower_than` 
						FROM
							`#__clm_turniere_sonderranglisten`
						WHERE
							`turnier` = ".$this->turnierid." AND 
							`id` = ".$this->spRang;
						
			$this->_db->setQuery($query);
			$this->spRank = $this->_db->loadObject();
			$this->turnier->spRangName = $this->spRank->name;
		
			if($this->spRank->use_rating_filter == 1){
				if($this->spRank->rating_type == 0){
					$where = $where ." AND NATrating >= '".$this->spRank->rating_higher_than."'"
									." AND NATrating <= '".$this->spRank->rating_lower_than."'"
									." AND FIDEelo >= '".$this->spRank->rating_higher_than."'"
									." AND FIDEelo <= '".$this->spRank->rating_lower_than."'";
				} elseif($this->spRank->rating_type == 1){
					$where = $where ." AND NATrating >= '".$this->spRank->rating_higher_than."'"
									." AND NATrating <= '".$this->spRank->rating_lower_than."'";
				} elseif($this->spRank->rating_type == 2){
					$where = $where ." AND FIDEelo >= '".$this->spRank->rating_higher_than."'"
									." AND FIDEelo <= '".$this->spRank->rating_lower_than."'";
				}
			}
			if($this->spRank->use_birthYear_filter == 1){
				$where = $where ." AND birthYear >= '".$this->spRank->birthYear_younger_than."'"
								." AND birthYear <= '".$this->spRank->birthYear_older_than."'";			
			}
			if($this->spRank->use_sex_filter == 1){
				if($this->spRank->sex == 'M'){
					$where = $where ." AND geschlecht = 'M'";
				} elseif($this->spRank->sex == 'W'){
					$where = $where ." AND geschlecht = 'W'";	
				}
			}
			if($this->spRank->use_zps_filter == 1){
				$where = $where ." AND zps >= '".$this->spRank->zps_higher_than."'"
								." AND zps <= '".$this->spRank->zps_lower_than."'";			
			}
			
		}
		return $where;
	}
	
}
?>
