<?php



// no direct access

defined('_JEXEC') or die('Restricted access');



class modCLMTermineHelper

{

	function getRunde(&$params)

	{

	global $mainframe;

	$db	= JFactory::getDBO();

	$par_liste 			= $params->def('liste', 0);
	if ($par_liste == 0 ) {
		$now = date("Y-m-d");
	}
	

		$query = " (SELECT li.sid, li.datum AS datum, li.datum AS enddatum,  li.name, li.nr, li.liga AS typ_id, t.id, t.name AS typ, t.durchgang AS durchgang, t.runden AS runden, t.published, li.id AS ligarunde "

				." FROM #__clm_runden_termine AS li "

				." LEFT JOIN #__clm_liga AS t ON t.id = li.liga WHERE t.published != '0' AND datum >= '$now' )"

				." UNION ALL"


				." (SELECT '1', e.startdate AS datum,  e.enddate AS enddatum, e.name, '1', '', e.id, e.category AS typ, '1', '', e.published, 'event' AS ligarunde "

				." FROM #__clm_termine AS e "

				." WHERE e.published != '0' AND startdate >= '$now' )"

				

				." UNION ALL"

				

				." (SELECT tu.sid, tu.datum AS datum, '1',  tu.name, tu.nr, tu.turnier AS typ_id, b.id, b.name AS typ, tu.dg AS durchgang, '', b.published, '' "

				." FROM #__clm_turniere_rnd_termine AS tu "

				." LEFT JOIN #__clm_turniere AS b ON b.id = tu.turnier WHERE b.published != '0' "
				
				." AND datum >= '$now' )"

				." ORDER BY datum ASC"

				;

		

	$db->setQuery( $query );

	$runden = $db->loadObjectList();;

	 

	return $runden;

	}

}