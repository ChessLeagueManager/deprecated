<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

$sid	= JRequest::getInt( 'saison');
$liga	= JRequest::getInt( 'liga');
$runde	= JRequest::getInt( 'runde');
$view	= JRequest::getVar( 'view' );
$dg	= JRequest::getInt( 'dg' );
$itemid	= JRequest::getInt( 'Itemid' );

//URL-Test: falls nicht belegt --> mod_clm oder mod_clm_archiv
//			falls belegt --> mod_clm_ext
$url		= JRequest::getVar('url');

?>
<ul class="menu">
<?php 
    foreach($saison as $saison){ ?>
	<li id="current" class="first_link">
	    <a href="index.php?option=com_clm&amp;view=info&amp;saison=<?php echo $saison->id;?>&amp;Itemid=<?php echo $itemid;?>"
	    <?php if ($liga == $link->id AND $view == 'rangliste') {echo ' class="active_link"';} ?>>
	    <span><?php echo "<b>".$saison->name."</b>"; ?></span>
	    </a>
	</li>
<?php    
//} else {
if($sid == $saison->id AND !isset($url)) { ?>
<ul><?php
foreach ($link as $link) {
// Haupttlinks des Menüs
?>
	<!--<ul>-->
	<li id="current" class="first_link">
	<a href="index.php?option=com_clm&amp;view=rangliste&amp;saison=<?php echo $link->sid;?>&amp;liga=<?php echo $link->id;?>&amp;Itemid=<?php echo $itemid;?>"
	<?php if ($liga == $link->id AND $view == 'rangliste') {echo ' class="active_link"';} ?>>
	<span><?php echo "&nbsp;&nbsp;".$link->name; ?></span>
	</a>
<?php 
// Unterlinks falls Link angeklickt
if ($liga == $link->id AND $view == 'rangliste' AND !isset($url) ) { ?>
	<ul>
		<li class="first_link liga<?php echo $liga; ?>">
		<a href="index.php?option=com_clm&amp;view=paarungsliste&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;Itemid=<?php echo $itemid;?>">
		<span>Paarungsliste</span></a>
		</li>
	<?php for ($y=0; $y < $link->runden; $y++) { ?>
		<li>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&dg=1&amp;Itemid=<?php echo $itemid;?>">
		<span>Runde <?php echo $y+1; if ($link->durchgang >1) {echo " (Hinrunde)";}?></span></a>
		</li>
	<?php } 
	if ($link->durchgang > 1) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&dg=2&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span>Runde <?php echo $y+1; ?> (Rückrunde)</span></a>
		</li>
	<?php }} ?>
		<li <?php if ($view == 'dwz_liga') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=dwz_liga&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'dwz_liga') { ?> class="active_link" <?php } ?>>
		<span>DWZ Mannschaften</span></a>
		</li>

		<li <?php if ($view == 'statistik') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'statistik') { ?> class="active_link" <?php } ?>>
		<span>Ligastatistiken</span></a>
		</li>
	</ul>
	<?php } ?>
	</li>
<!-- Unterlink angeklickt -->
<?php 
	if ($liga == $link->id AND $view != 'rangliste' AND !isset($url) ) { ?>
	<li class="parent active">
	<ul>
		<li <?php if ($view == 'paarungsliste') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=paarungsliste&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'paarungsliste') { ?> class="active_link" <?php } ?>>
		<span>Paarungsliste</span></a>
		</li>
	<?php for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde' AND $dg == 1) { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&dg=1&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span>Runde <?php echo $y+1; if ($link->durchgang >1) {echo " (Hinrunde)";}?></span></a>
		</li>
	<?php }
	if ($link->durchgang > 1) {
	for ($y=0; $y < $link->runden; $y++) { ?>
		<li <?php if ($view == 'runde' AND $dg == 2) { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=runde&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;runde=<?php echo $y+1; ?>&dg=2&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'runde' AND $runde == ($y+1)) { ?> class="active_link" <?php } ?>>
		<span>Runde <?php echo $y+1; ?> (Rückrunde)</span></a>
		</li>
	<?php }} ?>
		<li <?php if ($view == 'dwz_liga') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=dwz_liga&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'dwz_liga') { ?> class="active_link" <?php } ?>>
		<span>DWZ Mannschaften</span></a>
		</li>

		<li <?php if ($view == 'statistik') { ?> id="current" class="active" <?php } ?>>
		<a href="index.php?option=com_clm&amp;view=statistik&amp;saison=<?php echo $link->sid; ?>&amp;liga=<?php echo $liga; ?>&amp;Itemid=<?php echo $itemid;?>" <?php if ($view == 'statistik') { ?> class="active_link" <?php } ?>>
		<span>Ligastatistiken</span></a>
		</li>
	</ul>
	</li>
<?php							}
		
	   } ?></ul><?php
			}
			      } ?>
</ul>