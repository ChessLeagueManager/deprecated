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

class CLMModelMeldeliste extends JModel
{
	function _getCLMLiga( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$man	= JRequest::getInt('man','1');
	$layout	= JRequest::getVar('layout');
	$gid	= JRequest::getInt('gid');

		// TODO: Cache on the fingerprint of the arguments
		$db	= JFactory::getDBO();
		$id	= @$options['id'];

 	if($layout =="rangliste"){
		$query = "SELECT a.name as vname, r.Gruppe as gruppe "
			." FROM #__clm_vereine as a"
			." LEFT JOIN #__clm_rangliste_name as r ON r.id =".$gid
			." WHERE a.sid = $sid AND a.zps = '$zps' AND r.id = $gid "
			." AND a.published = 1 "
			;
				}
	else {
		$query = "SELECT a.name as man_name, l.name as liga, a.man_nr,l.id as lid, " 
			." l.stamm, l.ersatz,l.rang "
			." FROM #__clm_mannschaften as a"
			." LEFT JOIN #__clm_liga as l ON l.id = a.liga AND l.sid = a.sid  "
			." WHERE a.sid = $sid AND a.zps = '$zps' AND a.man_nr = $man AND a.published = 1 "
			;
		}
	return $query;
	}
	function getCLMLiga( $options=array() )
	{
		$query	= $this->_getCLMLiga( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMSpieler( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$man	= JRequest::getInt('man','1');
	$layout	= JRequest::getVar('layout');
	$gid	= JRequest::getInt('gid');
		// TODO: Cache on the fingerprint of the arguments
		$db	= JFactory::getDBO();
		$id	= @$options['id'];
	// Konfigurationsparameter auslesen
	$config = &JComponentHelper::getParams( 'com_clm' );
	$val=$config->get('meldeliste',1);

	if($layout =="rangliste"){

	$sql = " SELECT * "
		." FROM #__clm_rangliste_name"
		." WHERE id =".$gid
		." AND sid = ".$sid
		;
	$db->setQuery($sql);
	$gid	= $db->loadObjectList();

	$melde = explode ("-",$gid[0]->Meldeschluss);
	$jahr = $melde[0];

	$geb = "";
	$ges = "";
	if ($gid[0]->alter_grenze == "1") {
		$geb = " AND a.Geburtsjahr < ".($jahr - $gid[0]->alter);
		}
	if ($gid[0]->alter_grenze == "2") {
		$geb = " AND a.Geburtsjahr > ".($jahr - ( $gid[0]->alter + 1));
		}
	if ($gid[0]->geschlecht == 1) {
		$ges = " AND a.Geschlecht = 'W' ";
		}
	if ($gid[0]->geschlecht == 2) {
		$ges = " AND a.Geschlecht = 'M' ";
		}

	$query = " SELECT a.sid,a.ZPS,a.Mgl_Nr,a.PKZ,a.DWZ,a.DWZ_Index,a.Geburtsjahr,a.Spielername"
		." FROM #__clm_dwz_spieler as a"
		." WHERE a.ZPS = '$zps'"
		." AND sid =".$sid
		.$geb.$ges
		." ORDER BY a.DWZ DESC, a.DWZ_Index ASC, a.Spielername ASC "
		;
			}
	else {
	if ($val == 1) { $order = "Spielername ASC"; }
	else { $order = "DWZ DESC";}
		$query = "SELECT Spielername as name, Mgl_Nr as id, DWZ as dwz" 
			." FROM #__clm_dwz_spieler "
			." WHERE zps = '$zps'  "
			." AND sid = ".$sid              //klkl
			." ORDER BY $order "
			;
		}
	return $query;
	}
	function getCLMSpieler( $options=array() )
	{
		$query	= $this->_getCLMSpieler( $options );
		$result = $this->_getList( $query );
	return @$result;
	}

	function _getCLMCount( &$options )
	{
	$zps = JRequest::getVar('zps');

		// TODO: Cache on the fingerprint of the arguments
		$db	= JFactory::getDBO();
		$id	= @$options['id'];
 
		$query = "SELECT COUNT(ZPS) as zps " 
			." FROM #__clm_dwz_spieler "
			." WHERE zps = '$zps'  "
			;
	return $query;
	}
	function getCLMCount( $options=array() )
	{
		$query	= $this->_getCLMCount( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	// Prüfen ob Meldeliste schon abgegeben wurde
	function _getCLMAccess ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$man	= JRequest::getInt('man','1');
	$layout	= JRequest::getVar('layout');
	$gid	= JRequest::getInt('gid');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];
	if($layout =="rangliste"){
	$query	= "SELECT COUNT(snr) as snr "
		." FROM #__clm_meldeliste_spieler as a"
		." WHERE a.sid = $sid AND a.zps = '$zps' AND a.mnr = $man "
		;
				}
	else {
	$query	= "SELECT COUNT(snr) as snr "
		." FROM #__clm_meldeliste_spieler as a"
		." WHERE a.sid = $sid AND a.zps = '$zps' AND a.mnr = $man "
		;
		}
	return $query;
	}

	function getCLMAccess ( $options=array() )
	{
		$query	= $this->_getCLMAccess( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function _getCLMAbgabe ( &$options )
	{
	$sid	= JRequest::getInt('saison','1');
	$zps	= JRequest::getVar('zps');
	$man	= JRequest::getInt('man','1');
	$layout	= JRequest::getVar('layout');
	$gid	= JRequest::getInt('gid','1');

		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	if($layout =="rangliste"){
	$query	= "SELECT id "
		." FROM #__clm_rangliste_id "
		." WHERE sid = $sid AND zps = '$zps' AND gid = $gid "
		;
				}
	else {
	$query	= "SELECT id, liste "
		." FROM #__clm_mannschaften "
		." WHERE sid = $sid AND zps = '$zps' AND man_nr = $man "
		;
		}
	return $query;
	}

	function getCLMAbgabe ( $options=array() )
	{
		$query	= $this->_getCLMAbgabe( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	// Prüfen ob User berechtigt ist zu melden
	function _getCLMClmuser ( &$options )
	{
	$user	= & JFactory::getUser();
	$jid	= $user->get('id');
	$sid	= JRequest::getInt('saison','1');


		$db	= JFactory::getDBO();
		$id	= @$options['id'];

	$query	= "SELECT zps,published "
		." FROM #__clm_user "
		." WHERE jid = $jid "
		." AND sid = $sid "
		;
	return $query;
	}

	function getCLMClmuser ( $options=array() )
	{
		$query	= $this->_getCLMClmuser( $options );
		$result = $this->_getList( $query );
		return @$result;
	}

	function Sortierung ( $cids ) {

	$zps 	= JRequest::getVar('zps');
	$db	= JFactory::getDBO();
	$sid	= JRequest::getInt('saison','1');      //klkl

	$query = "SELECT Spielername as name, Mgl_Nr as id, DWZ as dwz" 
		." FROM #__clm_dwz_spieler "
		." WHERE zps = '$zps'  "
		." AND sid = ".$sid                        //klkl
		." AND Mgl_Nr IN ($cids) "
		." ORDER BY DWZ DESC, Spielername ASC "
		;
	$db->setQuery( $query );
	$sort = $db->loadObjectList();

	return $sort;
	}

}
?>
