<?php // no direct access
defined('_JEXEC') or die('Restricted access');

	// Prüfen ob die Extern Komponente existiert
	$path	= JPATH_ROOT.DS.'administrator'.DS.'components'.DS;
	$backup	= $path.'com_clm_ext';
	jimport('joomla.filesystem.file');
 	if (!JFolder::exists($backup)){
 		JError::raiseNotice( 6000,  JText::_( 'CLM Extern Modul : Die CLM Extern Komponente ist nicht installiert ! Keine Anzeige möglich !' ));
 	} else {
	
	// Basis-Konfigurationsparameter auslesen
	$ext_url0= $params->get('URL','www.fishpoke.de');
	$saison	= $params->get('sid',2);
	$auto	= $params->get('auto',0);
	$marke	= $params->get('marke',0);
	$keyword	= $params->get('keyword','');
	$mcolor	= $params->get('mcolor','0');
	if ($marke == 0) { $mcolor = '0'; }
	if ($mcolor == '0') { $keyword = ''; }
	
	if (!class_exists('Net_IDNA_php4')) {
		include_once('idna_convert.class.php'); }
	// Instantiate it (depending on the version you are using) with
	$IDN = new idna_convert(); 
	// The work string
	$url1 = $ext_url0;
	// Encode it to its punycode presentation
	$ext_url = $IDN->encode($url1);


if($auto =="0") {
	$lid	= $params->get('lid',22);

	$lids = explode(";", $lid);
		$sql[] = $link[3];
	for ($x=0;$x < count($lids); $x++) {
		$liga[]		= $lids[$x];
		$lid_name[]	= $params->get('lid_'.(1+$x),'Liga 1*');
		$lid_runde[]	= $params->get('lid_r'.(1+$x),'1');
		$lid_dg[]	= $params->get('lid_d'.(1+$x),'1');
	}
	}
$url		= ereg_replace ( '\'', '', JRequest::getVar('url'));
//JError::raiseNotice( 6000,  JText::_( '5 !--'.$saison.'---'.$lid.'--'.$ext_url.'---'.$liga_name[0].'-->ID-->'.$url ));

$ext_view	= JRequest::getVar( 'ext_view' );
$view		= JRequest::getVar( 'view' );
$lid		= JRequest::getVar( 'liga');
$runde		= JRequest::getVar( 'runde');
$dg		= JRequest::getVar( 'dg' );
$itemid		= JRequest::getVar( 'Itemid' );
$sid		= JRequest::getVar( 'saison');
  
?>
<ul class="menu">
<?php
for( $x=0; $x < count($lids); $x++) {
// Haupttlinks des Menüs
?>
	<li id="current" class="first_link">
	<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=rangliste&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison;?>&amp;liga=<?php echo $liga[$x];?>&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>"
	<?php if ($liga[$x] == $lid AND $ext_view == 'rangliste') {echo ' class="active_link"';} ?>>
	<span><?php echo $lid_name[$x]; ?></span>
	</a>
<?php 
// Unterlinks falls Link angeklickt
if ($lid == $liga[$x] AND $saison == $sid AND $ext_view == 'rangliste' AND $view=='clm_ext' AND ($ext_url == $url OR $ext_url == substr($url,1,strlen($url)-2))) { ?>
	<ul>
		<li class="first_link liga<?php echo $liga; ?>">
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=paarungsliste&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>">
		<span>Paarungsliste</span></a>
		</li>
	<?php for ($y=0; $y < $lid_runde[$x]; $y++) { ?>
		<li>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=runde&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=1&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>">
		<span>Runde <?php echo $y+1; if ($lid_dg[$x] >1) {echo " (Hinrunde)";}?></span></a>
		</li>
	<?php } $cnt = $y;
	if ($lid_dg[$x] > 1) {
	for ($y=0; $y < $lid_runde[$x]; $y++) { ?>
		<li <?php if ($view == 'runde') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=runde&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=2&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>" 
		<?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span>Runde <?php echo $y+1; ?> (Rückrunde)</span></a>
		</li>
	<?php }} ?>
		<li <?php if ($view == 'dwz_liga') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=dwz_liga&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'dwz_liga') { ?> class="active_link" <?php } ?>>
		<span>DWZ Mannschaften</span></a>
		</li>

		<li <?php if ($view == 'statistik') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=statistik&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'statistik') { ?> class="active_link" <?php } ?>>
		<span>Ligastatistiken</span></a>
		</li>
	</ul>
	<?php } ?>
	</li>
<!-- Unterlink angeklickt -->
<?php //if ($liga[$x] == $lid AND $ext_view != 'rangliste' AND $view=='clm_ext' AND $ext_url == $url){ 
	if ($liga[$x] == $lid AND $saison == $sid AND $ext_view != 'rangliste' AND $view=='clm_ext' AND ($ext_url == $url OR $ext_url == substr($url,1,strlen($url)-2))) { ?>
	<li class="parent active">
	<ul>
		<li <?php if ($ext_view == 'paarungsliste') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=paarungsliste&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($ext_view == 'paarungsliste') { ?> class="active_link" <?php } ?>>
		<span>Paarungsliste</span></a>
		</li>
	<?php for ($y=0; $y < $lid_runde[$x]; $y++) { ?>
		<li <?php if ($ext_view == 'runde' AND $dg == 1) { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=runde&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=1&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>" 
		<?php if ($ext_view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span>Runde <?php echo $y+1; if ($lid_dg[$x] >1) {echo " (Hinrunde)";}?></span></a>
		</li>
	<?php } $cnt = $y;
	if ($lid_dg[$x] > 1) {
	for ($y=0; $y < $lid_runde[$x]; $y++) { ?>
		<li <?php if ($ext_view == 'runde' AND $dg == 2) { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=runde&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=2&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>" 
		<?php if ($ext_view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span>Runde <?php echo $y+1; ?> (Rückrunde)</span></a>
		</li>
	<?php }} ?>
		<li <?php if ($ext_view == 'dwz_liga') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=dwz_liga&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($ext_view == 'dwz_liga') { ?> class="active_link" <?php } ?>>
		<span>DWZ Mannschaften</span></a>
		</li>

		<li <?php if ($ext_view == 'statistik') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm_ext&amp;view=clm_ext&amp;ext_view=statistik&amp;url=<?php echo "'".$ext_url."'"; ?>&amp;saison=<?php echo $saison; ?>&amp;liga=<?php echo $liga[$x]; ?>&amp;keyword=<?php echo $keyword;?>&amp;mcolor=<?php echo $mcolor;?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($ext_view == 'statistik') { ?> class="active_link" <?php } ?>>
		<span>Ligastatistiken</span></a>
		</li>
	</ul>
	</li>
<?php							}
			} ?>
</ul>
	<?php } ?>