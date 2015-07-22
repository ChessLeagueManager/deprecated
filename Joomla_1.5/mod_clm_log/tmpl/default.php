<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<!-- ///////// Published  ???  -->
<?php if (!$data OR $data[0]->published < 1) { ?>
Ihr Account ist noch nicht aktiviert oder wurde von einem Administrator gesperrt ! <?php } 

// Published OK !
else {
	// Konfigurationsparameter auslesen
	$config 		= &JComponentHelper::getParams( 'com_clm' );
	$conf_meldeliste	= $config->get('conf_meldeliste',1);
	$conf_vereinsdaten	= $config->get('conf_vereinsdaten',1);
	$conf_ergebnisse	= $config->get('conf_ergebnisse',1);
	$meldung_heim		= $config->get('meldung_heim',1);
	$meldung_verein		= $config->get('meldung_verein',1);
	
	if ($altItemid	!= ''){
		$itemid = $altItemid;
	}

$document = &JFactory::getDocument();
$cssDir = JURI::base().'modules'.DS.'mod_clm_log';
$document->addStyleSheet( $cssDir.DS.'sliders.css', 'text/css', null, array() );

$cmd	= JRequest::getCmd('view');
$lay	= JRequest::getCmd('layout');
$off="-1";
$cnt=0;

if ($conf_meldeliste == 1 AND $rangliste) {$cnt++;}
if ($conf_meldeliste == 1 AND $meldeliste) {$cnt++;}

if($cmd=="meldeliste" AND $layout=="") { $off =1;}
if($cmd=="meldeliste" AND $layout=="rangliste") { $off =1;}
if($cmd=="vereinsdaten") { $off =$cnt+1;}
if($cmd=="meldung") { $off =0;}
?>

<div><h4>Hallo SF <?php echo $data[0]->name.' !'; ?></h4></div>
<?php jimport('joomla.html.pane');
$pane =& JPane::getInstance('sliders', array('startOffset'=>$off));
echo $pane->startPane( 'pane');
	echo $pane->startPanel( 'Ergebnisse melden', 'panel1' );
	
		$vorher = 999;
		foreach ($liga as $liga ) {
			if ($vorher > $liga->runde AND $liga->dg == 1) {
			  echo "<h4>".$liga->name; if ($params->get('klasse') == 1) { echo ' - '.$liga->lname; } echo '</h4>'; }
			$vorher = $liga->runde;
			// Wenn NICHT gemeldet dann Runde anzeigen
			if ($liga->gemeldet < 1 AND ($liga->liste > 0 OR ($liga->rang == 1 AND isset($liga->gid)))) {
			  if (!($liga->meldung == 0 AND $params->get('runden') == 0)) {
			?>
			<a class="link" href="index.php?option=com_clm&amp;view=meldung&amp;saison=<?php echo $liga->sid;?>&amp;liga=<?php echo $liga->liga; ?>&amp;runde=<?php echo $liga->runde; ?>&amp;tln=<?php echo $liga->tln_nr; ?>&amp;paar=<?php echo $liga->paar; ?>&dg=<?php echo $liga->dg; ?>&amp;Itemid=<?php echo $itemid; ?>">
				<?php echo "Runde ".($liga->runde); if ($liga->dg > 1) echo " Rückrunde"; if (($liga->dg == 1) and ($liga->durchgang > 1)) echo " Hinrunde";?>
			</a>
			<br>
		<?php }}}

	echo $pane->endPanel();
	if ($conf_meldeliste == 1) {
	if ($meldeliste) {
	echo $pane->startPanel( 'Meldeliste abgeben', 'panel2' );

		foreach ($meldeliste as $meldeliste){ ?>
		<div>
			<a href="index.php?option=com_clm&view=meldeliste&saison=<?php echo $meldeliste->sid; ?>&zps=<?php echo $meldeliste->zps; ?>&man=<?php echo $meldeliste->man_nr; ?>&amp;Itemid=<?php echo $itemid; ?>"><?php echo $meldeliste->name; ?></a> - <?php echo $meldeliste->liganame; ?> 
		</div>
		<?php }
	
	echo $pane->endPanel();
	}
	if ($rangliste) {
	echo $pane->startPanel( 'Rangliste abgeben', 'panel3' );

		foreach ($rangliste as $rangliste){
		if ($rangliste->id == "") { ?>
		<div>
			<a href="index.php?option=com_clm&view=meldeliste&layout=rangliste&saison=<?php echo $rangliste->sid; ?>&zps=<?php echo $rangliste->zps; ?>&gid=<?php echo $rangliste->gid; ?>&amp;Itemid=<?php echo $itemid; ?>"><?php echo $rangliste->gruppe; ?></a> 
		</div>
		<?php }}
	
	echo $pane->endPanel();
	}}
	
	if ($conf_vereinsdaten == 1 AND $par_vereinsdaten == 1) {
	echo $pane->startPanel( 'Vereinsdaten ändern', 'panel4' );
	?>
		<div>
		<a href="index.php?option=com_clm&view=verein&saison=<?php echo $data[0]->sid; ?>&zps=<?php echo $data[0]->zps; ?>&layout=vereinsdaten&amp;Itemid=<?php echo $itemid; ?>"><?php echo $data[0]->vname; ?></a>
		</div>
		<?php 

	echo $pane->endPanel();
	}

echo $pane->endPane();

} ?>
