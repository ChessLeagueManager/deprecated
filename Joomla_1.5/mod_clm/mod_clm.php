<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// angemeldet
require_once (dirname(__FILE__).DS.'helper.php');

$par_mt_type = $params->def('mt_type', 0);
$par_statistik = $params->def('statistik', 1);
$par_dwzliga = $params->def('dwzliga', 1);
$par_termine = $params->def('termine', 1);
$par_vereine = $params->def('vereine', 1);

$link	= modCLMHelper::getLink($params);
$count	= modCLMHelper::getCount($params);
$runden	= modCLMHelper::getRunde($params);

require(JModuleHelper::getLayoutPath('mod_clm'));


