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


/**
 * Turnier
*/
	
class CLMTournament {

	function __construct($turnierid, $getData = FALSE) {
		// $turnierid übergibt id des Turniers
		// $getData, ob die Turneirdaten aus clm_turniere sofort ausgelesen werden sollen

		// DB
		$this->_db				= & JFactory::getDBO();
		
		// turnierid
		$this->turnierid = $turnierid;	
	
		// get data?
		if ($getData) {
			$this->_getData();
		}
	
	}


	function _getData() {
	
		$this->data = & JTable::getInstance( 'turniere', 'TableCLM' );
		$this->data->load($this->turnierid);
	
	}


	/**
	* check, ob User Zugriff hat
	* drei Zugangsmöichgkeiten - aller per Default auf TRUE
	*/
	function checkAccess($usertype_admin = TRUE, $usertype_tl = TRUE, $id_tl = TRUE) {
	
		// admin?
		if ($usertype_admin AND CLM_usertype == 'admin') {
			return TRUE;
		}
		// tl?
		if ($usertype_tl AND CLM_usertype == 'tl') {
			return TRUE;
		}
		// tournament->tl
		if ($id_tl AND CLM_ID == $this->data->tl) {
			return TRUE;
		}
		// nichts hat zugetroffen
		return FALSE;
	
	}

	function getPlayersIn() {
	
		// Anzahl gemeldeter Spieler
		$query = "SELECT COUNT(*) FROM `#__clm_turniere_tlnr`"
				. " WHERE turnier = ".$this->turnierid
				;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	
	}
	
	
	/**
	* check, ob ein Turnier schon gestartet wurde
	* indem die Gesamtzahl von Spielern errungener Punkte ermittelt wird
	* TODO: später durch ein Flag in der DB ersetzen
	*/
	function checkTournamentStarted() {
	
		// Ergebnisse gemeldet
		$query = "SELECT COUNT(*) FROM `#__clm_turniere_rnd_spl`"
			." WHERE turnier = ".$this->turnierid
			." AND ergebnis IS NOT NULL"
			;
		$this->_db->setQuery($query);
		if ($this->_db->loadResult() > 0) {
			$this->started = TRUE;
		} else {
			$this->started = FALSE;
		}
	
	}
	
	
	/**
	* check, ob die Startnummern des Teilnehmerfeldes korrekt vergeben sind
	* liest folgende Werte aus:
	* - maxSnr:			maximale Startnummer
	* - minSnr:			minimale Startnummer
	* - distinctSnr:	Anzahl unterschiedliche Startnummern
	* - countSnr:		Anzahl Startnummern gesamt
	* folgende Checks:
	* - erste Startnummer > 1
	* - letzte Startnummer > Teilnehmerzahl
	* - gibt es doppelte Startnummern
	*/
	function checkCorrectSnr() {
	
		$query = 'SELECT MAX(snr) AS maxSnr, MIN(snr) AS minSnr, COUNT(DISTINCT(snr)) AS distinctSnr, COUNT(snr) AS countSnr'
			. ' FROM #__clm_turniere_tlnr'
			. ' WHERE turnier = '.$this->turnierid
			;
		$this->_db->setQuery($query);
		$this->checkSnr = $this->_db->loadObject();
		if ($this->checkSnr->minSnr > 1 OR $this->checkSnr->maxSnr > $this->data->teil OR $this->checkSnr->distinctSnr != $this->checkSnr->countSnr) {
			return FALSE;
		} 
	
		return TRUE;
	
	}
	
	
	/**
	* errechnet/aktualisiert Rangliste/Punktesummen eines Turniers
	*/
	function calculateRanking() {
	
		// Parameter auslesen, für FIDE-Ranglistenkorrektur
		$query = 'SELECT `params`'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->turnierid
			;
		$this->_db->setQuery($query);
		$turParams = new JParameter($this->_db->loadResult());
		$paramTBFideCorrect = $turParams->get('optionTiebreakersFideCorrect', 0);
		$query = 'SELECT dg, runden'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->turnierid
			;
		$this->_db->setQuery($query);
		$dg = $this->data->dg;
		$runden = $this->data->runden;

		// alle FW in Array schreiben
		$arrayFW = array();
		for ($tb=1; $tb<=3; $tb++) {
			$fieldname = 'tiebr'.$tb;
			$arrayFW[$tb] = $this->data->$fieldname;
		}
	
		// für alle Spieler Datensätze mit Summenwert 0 anlegen
		// TODO: da gab es einen eigenen PHP-Befehl für?!
		$array_PlayerPunkte = array();
		$array_PlayerPunkteTB = array(); // Punkte, die für Feinwertungen herangezogen werden
		$array_PlayerBuch = array();
		$array_PlayerBuchOpp = array();
		$array_PlayerSoBe = array();
		$array_PlayerBuSum = array();
		$array_PlayerWins = array();
		for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
			$array_PlayerPunkte[$s] = 0;
			$array_PlayerPunkteTB[$s] = 0;
			$array_PlayerBuch[$s] = 0;
			$array_PlayerSoBe[$s] = 0;
			$array_PlayerSoBeMin[$s] = 999;
			$array_PlayerBuSum[$s] = 0;
			$array_PlayerBuSumMin[$s] = 999;
			$array_PlayerWins[$s] = 0;
		}
	
		// alle Matches in DatenArray schreiben
		$query = "SELECT tln_nr, gegner, dg, runde, ergebnis FROM `#__clm_turniere_rnd_spl`"
				. " WHERE turnier = ".$this->turnierid." AND ergebnis IS NOT NULL"
				;
		$this->_db->setQuery( $query );
		$matchData = $this->_db->loadObjectList();
		
		// Punkte/Siege
		// alle Matches durchgehen -> Spieler erhalten Punkte und Wins
		foreach ($matchData as $key => $value) {
			if ($value->ergebnis == 2) { // remis
				$array_PlayerPunkte[$value->tln_nr] += .5;
				$array_PlayerPunkteTB[$value->tln_nr] += .5;
			} elseif ($value->ergebnis == 1 OR $value->ergebnis == 5) { // Sieger
				$array_PlayerPunkte[$value->tln_nr] += 1;
				$array_PlayerWins[$value->tln_nr] += 1;
				if ($value->ergebnis == 5 AND $paramTBFideCorrect == 1) { // kampflos gewonnen und FIDE-Korrektur eingestellt?
					$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
				} else {
					$array_PlayerPunkteTB[$value->tln_nr] += 1;
				}
			} elseif ($value->ergebnis == 4 AND $paramTBFideCorrect == 1) { // kampflos verloren und FIDE-Korrektur eingestellt?
				$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
			} elseif ($value->ergebnis == 8 AND $paramTBFideCorrect == 1) { // spielfrei und FIDE-Korrektur eingestellt?
				$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
			} elseif ($value->ergebnis == 3 AND $paramTBFideCorrect == 1) { // Ergebnis 0-0 und FIDE-Korrektur eingestellt?
				$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
			} elseif ($value->ergebnis == 6 AND $paramTBFideCorrect == 1) { // kampflos beide verloren -:- und FIDE-Korrektur eingestellt?
				$array_PlayerPunkteTB[$value->tln_nr] += .5; // FW-Korrektur Teil 1
			}
		}
	
		// Buchholz & Sonneborn-Berger
		// erneut alle Matches durchgehen -> Spieler erhalten Feinwertungen
		foreach ($matchData as $key => $value) {
			// Buchholz
			if (in_array(1, $arrayFW) OR in_array(2, $arrayFW) OR in_array(11, $arrayFW) OR in_array(12, $arrayFW)) { // beliebige Buchholz als TieBreaker gewünscht?
				if ($value->ergebnis < 3 OR $paramTBFideCorrect == 0) {
					$array_PlayerBuchOpp[$value->tln_nr][] = $array_PlayerPunkteTB[$value->gegner]; // Array mit Gegnerwerten - für Streichresultat
				} else { //Ranglistenkorrektur nach FIDE (Teil 2)
					$query = "SELECT tln_nr, gegner, dg, runde, ergebnis FROM `#__clm_turniere_rnd_spl`"
					. " WHERE turnier = ".$this->turnierid
					. " AND tln_nr = ".$value->tln_nr
					. " AND ergebnis IS NOT NULL"
					. " ORDER BY dg ASC, runde ASC"
					;
					$this->_db->setQuery( $query );
					$matchDataSnr = $this->_db->loadObjectList();
					$PlayerPunkteKOR = 0;
					foreach ($matchDataSnr as $key => $valuesnr) {
						if (($valuesnr->dg < $value->dg) OR ($valuesnr->dg == $value->dg AND $valuesnr->runde < $value->runde)) {
							if ($valuesnr->ergebnis == 1) $PlayerPunkteKOR += 1; // Sieg
							elseif ($valuesnr->ergebnis == 2) $PlayerPunkteKOR += .5; // remis
							elseif ($valuesnr->ergebnis == 5) $PlayerPunkteKOR += 1; // Sieg kampflos
						}
					}	
					if (($value->ergebnis == 4) OR ($value->ergebnis == 8)) { $PlayerPunkteKOR += 1; }// Gegner gewinnt kampflos oder spielfrei
	  				if (($value->ergebnis == 3) OR ($value->ergebnis == 6)) { $PlayerPunkteKOR += 1; }// Gegner verliert auch kampflos, ist aber egal
					$PlayerPunkteKOR += 0.5 * (($runden * $dg) - (($value->dg - 1) * $runden) - $value->runde);
					$array_PlayerBuchOpp[$value->tln_nr][] = $PlayerPunkteKOR; // Array mit Gegnerwerten - für Streichresultat
				}
			}
			
			// Sonneborn-Berger
			if (in_array(3, $arrayFW) OR in_array(13, $arrayFW)) { // SoBe als ein TieBreaker gewünscht?
				if ($value->ergebnis == 2) { // remis
					$array_PlayerSoBe[$value->tln_nr] += ($array_PlayerPunkteTB[$value->gegner]/2);
					if ($array_PlayerPunkteTB[$value->gegner]/2 < $array_PlayerSoBeMin[$value->tln_nr]) $array_PlayerSoBeMin[$value->tln_nr] = $array_PlayerPunkteTB[$value->gegner]/2; 
				} elseif (($value->ergebnis == 5)  AND $paramTBFideCorrect == 1) { // kampflos und FIDE-Korrektur
					//Ranglistenkorrektur nach FIDE (Teil 2)
					$query = "SELECT tln_nr, gegner, dg, runde, ergebnis FROM `#__clm_turniere_rnd_spl`"
					. " WHERE turnier = ".$this->turnierid
					. " AND tln_nr = ".$value->tln_nr
					. " AND ergebnis IS NOT NULL"
					. " ORDER BY dg ASC, runde ASC"
					;
					$this->_db->setQuery( $query );
					$matchDataSnr = $this->_db->loadObjectList();
					$PlayerPunkteKOR = 0;
					foreach ($matchDataSnr as $key => $valuesnr) {
						if (($valuesnr->dg < $value->dg) OR ($valuesnr->dg == $value->dg AND $valuesnr->runde < $value->runde)) {
							if ($valuesnr->ergebnis == 1) $PlayerPunkteKOR += 1; // Sieg
							elseif ($valuesnr->ergebnis == 2) $PlayerPunkteKOR += .5; // remis
							elseif ($valuesnr->ergebnis == 5) $PlayerPunkteKOR += 1; // Sieg kampflos
						}
					}	
					$PlayerPunkteKOR += 0.5 * (($runden * $dg) - (($value->dg - 1) * $runden) - $value->runde);
					$array_PlayerSoBe[$value->tln_nr] += $PlayerPunkteKOR;
					if ($PlayerPunkteKOR < $array_PlayerSoBeMin[$value->tln_nr]) $array_PlayerSoBeMin[$value->tln_nr] = $PlayerPunkteKOR;
						} elseif ($value->ergebnis == 1 OR $value->ergebnis == 5) { // Sieger
					$array_PlayerSoBe[$value->tln_nr] += $array_PlayerPunkteTB[$value->gegner];
					if ($array_PlayerPunkteTB[$value->gegner] < $array_PlayerSoBeMin[$value->tln_nr]) $array_PlayerSoBeMin[$value->tln_nr] = $array_PlayerPunkteTB[$value->gegner]; 
				} elseif ($value->ergebnis == 0) { // Verlust
					$array_PlayerSoBeMin[$value->tln_nr] = 0; 
				} elseif ($value->ergebnis == 4) { // Verlust kampflos
					$array_PlayerSoBeMin[$value->tln_nr] = 0; 
				} elseif ($value->ergebnis == 8) { // spielfrei
					$array_PlayerSoBeMin[$value->tln_nr] = 0; 
				} elseif ($value->ergebnis == 3) { // Verlust beide 0-0
					$array_PlayerSoBeMin[$value->tln_nr] = 0; 
				} elseif ($value->ergebnis == 6) { // Verlust kampflos beide -:-
					$array_PlayerSoBeMin[$value->tln_nr] = 0; 
				}
			}
		}
		// Sonneborn-Berger mit Streichresultat
		if (in_array(13, $arrayFW)) { // als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerSoBe[$s] -= $array_PlayerSoBeMin[$s];
			}
		} 
	
		// Buchholz
		if (in_array(1, $arrayFW)) { // normale Buchholz als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]);
			}
		} elseif (in_array(11, $arrayFW)) { // Buchholz mit Streichresultat
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerBuch[$s] = array_sum($array_PlayerBuchOpp[$s]) - min($array_PlayerBuchOpp[$s]);
			}
		}
			
		// BuchholzSumme
		if ((in_array(2, $arrayFW)) OR (in_array(12, $arrayFW))) { // Buchholz-Summe als TieBreaker gewünscht?
			// erneut alle Matches durchgehen -> Spieler erhalten Buchholzsummen
			foreach ($matchData as $key => $value) {
				if ($value->gegner >= 1) {
					$array_PlayerBuSum[$value->tln_nr] += $array_PlayerBuch[$value->gegner];
					if ($array_PlayerBuSumMin[$value->tln_nr] > $array_PlayerBuch[$value->gegner]) 
							$array_PlayerBuSumMin[$value->tln_nr] = $array_PlayerBuch[$value->gegner];
				} else $array_PlayerBuSumMin[$value->tln_nr] = 0;
			}
		}
		// BuchholzSumme mit Streichresultat
		if (in_array(12, $arrayFW)) { // als TieBreaker gewünscht?
			for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
				$array_PlayerBuSum[$s] -= $array_PlayerBuSumMin[$s];
			}
		} 
	
		// alle Spieler durchgehen und updaten (kein vorheriges Löschen notwendig)
		for ($s=1; $s<= $this->data->teil; $s++) { // alle Startnummern durchgehen
			// den TiebrSummen ihre Werte zuordnen
			for ($tb=1; $tb<=3; $tb++) {
				$fieldname = 'tiebr'.$tb;
				switch ($this->data->$fieldname) {
					case 1: // buchholz
						$sumTiebr[$tb] = $array_PlayerBuch[$s];
						break;
					case 2: // bhhlz.-summe
						$sumTiebr[$tb] = $array_PlayerBuSum[$s];
						break;
					case 3: // sobe
						$sumTiebr[$tb] = $array_PlayerSoBe[$s];
						break;
					case 4: // wins
						$sumTiebr[$tb] = $array_PlayerWins[$s];
						break;
					case 11: // bhhlz mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuch[$s];
						break;
					case 12: // bhhlz.-summe mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerBuSum[$s];
						break;
					case 13: // sobe mit 1 streichresultat
						$sumTiebr[$tb] = $array_PlayerSoBe[$s];
						break;
					default:
						$sumTiebr[$tb] = 0;
				}
			}
			
			
			$query = "UPDATE #__clm_turniere_tlnr"
					. " SET sum_punkte = ".$array_PlayerPunkte[$s].", sum_wins = ".$array_PlayerWins[$s].", "
					. " sumTiebr1 = ".$sumTiebr[1].", sumTiebr2 = ".$sumTiebr[2].", sumTiebr3 = ".$sumTiebr[3]
					. " WHERE turnier = ".$this->turnierid
					. " AND snr = ".$s
					;
			$this->_db->setQuery($query);
			$this->_db->query();
		}
	
	}

	
	function setRankingPositions() {
	
		$query = "SELECT id"
			." FROM `#__clm_turniere_tlnr`"
			." WHERE turnier = ".$this->turnierid
			." ORDER BY sum_punkte DESC, sumTiebr1 DESC, sumTiebr2 DESC, sumTiebr3 DESC, snr ASC"
			;
		
		$this->_db->setQuery( $query );
		$players = $this->_db->loadObjectList();
	
		$table	=& JTable::getInstance( 'turnier_teilnehmer', 'TableCLM' );
		// rankingPos umsortieren
		$rankingPos = 0;
		// alle Spieler durchgehen
		foreach ($players as $value) {
			$rankingPos++;
			$table->load($value->id);
			$table->rankingPos = $rankingPos;
			$table->store();
		}
	
	}
	
	
	function makePlusTln() {
	
		if ($this->data->typ != 1) {
			JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'WRONGMODUS') );
			return FALSE;
			
		} elseif ($this->checkTournamentStarted()) {
			JError::raiseNotice(500, CLMText::errorText('TOURNAMENT', 'ALREADYSTARTED') );
			return FALSE;
		}
	
		$query = "UPDATE #__clm_turniere"
				. " SET teil = teil + 1"
				. " WHERE id = ".$this->turnierid
				;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			JError::raiseNotice(500, JText::_('DB_ERROR') );
			return FALSE;
		}
		
		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::_('PARTICIPANT_COUNT_RAISED_TO').": ".($this->data->teil+1) );
		
		return TRUE;
	
	}
	
	
}
?>