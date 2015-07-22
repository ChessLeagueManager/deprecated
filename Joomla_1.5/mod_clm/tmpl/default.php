<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

$liga	= JRequest::getVar( 'liga');
$runde	= JRequest::getVar( 'runde');
$view	= JRequest::getVar( 'view' );
$dg		= JRequest::getVar( 'dg' );
$itemid	= JRequest::getVar( 'Itemid' );
$sid	= JRequest::getInt('saison','1');

foreach ($link as $link1) {
  if ($link1->id == $liga) {
	$runde_t = $link1->runden + 1;  
// Test alte/neue Standardrundenname bei 2 Durchgängen
	if ($link1->durchgang > 1) {
		if ($runden[$runde_t-1]->name == JText::_('ROUND').' '.$runde_t) {  //alt
			for ($xr=0; $xr< ($link1->runden); $xr++) { 
					$runden[$xr]->name = JText::_('ROUND').' '.($xr+1)." (".JText::_('PAAR_HIN').")";
					$runden[$xr+$link1->runden]->name = JText::_('ROUND').' '.($xr+1)." (".JText::_('PAAR_RUECK').")";
			}
		}
	}
}}
$config	= &JComponentHelper::getParams( 'com_clm' );
$pdf_melde = $config->get('pdf_meldelisten',1);

$par_mt_type = $params->def('mt_type', 0);

?>
<ul class="menu">

	<?php if ( $par_vereine == 1 ) { ?>
    <li <?php if ($view == 'vereinsliste') { ?> id="current" class="active" <?php } ?>>
        <a href="index.php?option=com_clm&view=vereinsliste&saison=<?php echo $link[0]->sid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'vereinsliste') { ?> class="active_link" <?php } ?>>
        <span>Vereine</span></a>
    </li>
    <?php } ?>
            
    <?php if ( $par_termine == 1 ) { ?>
    <li <?php if ($view == 'termine') { ?> id="current" class="active" <?php } ?>>
        <a href="index.php?option=com_clm&amp;view=termine&amp;saison=<?php echo $link[0]->sid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'termine') { ?> class="active_link" <?php } ?>>
        <span>Termine</span></a>
    </li>
    <?php } ?>

<?php 
// $link=$this->link;
foreach ($link as $link) {
// Haupttlinks des Menüs
?>
	<li <?php if ($liga == $link->id AND $itemid == 21) { ?> id="current" class="first_link" <?php } ?>>
	<?php $itemidn = 21;
		if ($link->runden_modus == 1 OR $link->runden_modus == 2 OR $link->runden_modus == 3) $view21 = "rangliste";
	    if ($link->runden_modus == 4 OR $link->runden_modus == 5) $view21 = "paarungsliste"; ?>
	<a href="index.php?option=com_clm&amp;view=<?php echo $view21;?>&amp;saison=<?php echo $link->sid;?>&amp;liga=<?php echo $link->id;?><?php if ($itemidn <>'') { echo "&Itemid=".$itemidn; } ?>"
	<?php if ($liga == $link->id AND $view == $view21 ) {echo ' class="active_link"';} ?>>
	<span><?php echo $link->name; ?></span>
	</a>

        
<?php 
// Unterlinks falls Link angeklickt
if ($liga == $link->id AND $view == $view21) { ?>
	<ul>
		<?php if ( $par_mt_type == 0 ) { ?>
		<li class="first_link liga<?php echo $liga; ?>" <?php if ($view == 'aktuell_runde') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=aktuell_runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>">
		<span>Aktuelle Runde</span></a>
		</li>
		<?php } ?>
		<?php $itemidn = 22;
		if ($link->runden_modus == 1 OR $link->runden_modus == 2 OR $link->runden_modus == 3) { ?>
		<li>
		<a href="index.php?option=com_clm&amp;view=paarungsliste&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemidn <>'') { echo "&Itemid=".$itemidn; } ?>">
		<span>Paarungsliste</span></a>
		</li>
		<?php } ?>
	<?php for ($y=0; $y < $link->runden; $y++) { ?>
		<li>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=1<?php if ($itemidn <>'') { echo "&Itemid=".$itemidn; } ?>">
		<span><?php if ($runden[$y]->published =="0") { ?><s><?php } echo $runden[$y]->name; ?><?php if ($runden[$y]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php } $cnt = $y;
	if ($link->durchgang > 1) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=2<?php if ($itemidn <>'') { echo "&Itemid=".$itemidn; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span><?php if ($runden[$y+$cnt]->published =="0") { ?><s><?php } echo $runden[$y+$cnt]->name; ?><?php if ($runden[$y+$cnt]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php }} ?>
    
        <?php if ( $par_dwzliga == 1 ) { ?>
		<li <?php if ($view == 'dwz_liga') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=dwz_liga&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemidn <>'') { echo "&Itemid=".$itemidn; } ?>" <?php if ($view == 'dwz_liga') { ?> class="active_link" <?php } ?>>
		<span>DWZ Mannschaften</span></a>
		</li>
		<?php } ?>

        <?php if ( $par_statistik == 1 ) { ?>
		<li <?php if ($view == 'statistik') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'statistik') { ?> class="active_link" <?php } ?>>
		<span>Ligastatistiken</span></a>
		</li>
		<?php } ?>
		
		<?php 
		// Konfigurationsparameter auslesen
		if ($pdf_melde == 1) {
		?>
		<li <?php if ($view == 'rangliste') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=rangliste&amp;format=clm_pdf&amp;layout=heft&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'rangliste') { ?> class="active_link" <?php } ?>>
		<span>Ligaheft drucken</span></a>
		</li>
		<?php } ?>
		
	</ul>
	<?php } ?>
	</li>
<!-- Unterlink angeklickt -->
<?php if ($liga == $link->id AND $view != $view21 ){ ?>
	<li class="parent active">
	<ul>
		<?php if ( $par_mt_type == 0 ) { ?>
		<li class="first_link liga<?php echo $liga; ?>" <?php if ($view == 'aktuell_runde') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=aktuell_runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>">
		<span>Aktuelle Runde</span></a>
		</li>
		<?php } ?>
		
		<li <?php if ($view == 'paarungsliste') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=paarungsliste&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'paarungsliste') { ?> class="active_link" <?php } ?>>
		<span>Paarungsliste</span></a>
		</li>
	<?php for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde' AND $dg == 1) { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=1<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span><?php if ($runden[$y]->published =="0") { ?><s><?php } echo $runden[$y]->name; ?><?php if ($runden[$y]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php } $cnt = $y;
	if ($link->durchgang > 1) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde' AND $dg == 2) { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&amp;dg=2<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span><?php if ($runden[$y+$cnt]->published =="0") { ?><s><?php } echo $runden[$y+$cnt]->name; ?><?php if ($runden[$y+$cnt]->published =="0") { ?></s><?php } ?></span></a>
		</li>
	<?php }} ?>
    
        <?php if ( $par_dwzliga == 1 ) { ?>
		<li <?php if ($view == 'dwz_liga') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=dwz_liga&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'dwz_liga') { ?> class="active_link" <?php } ?>>
		<span>DWZ Mannschaften</span></a>
		</li>
        <?php } ?>

        <?php if ( $par_statistik == 1 ) { ?>
		<li <?php if ($view == 'statistik') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'statistik') { ?> class="active_link" <?php } ?>>
		<span>Ligastatistiken</span></a>
		</li>
        <?php } ?>
		
        
		<?php 
		// Konfigurationsparameter auslesen
		if ($pdf_melde == 1) {
		?>
		<li <?php if ($view == 'rangliste') { ?> class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=rangliste&amp;format=clm_pdf&amp;layout=heft&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>" <?php if ($view == 'rangliste') { ?> class="active_link" <?php } ?>>
		<span>Ligaheft drucken</span></a>
		</li>
		<?php } ?>
		
	</ul>
	</li>
<?php							}
			} ?>
            
</ul>