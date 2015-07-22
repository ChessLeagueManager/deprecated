<?php
/**
*	name					default.php
*	description			Template default
*
*	start					30.11.2010
*	last edit			06.07.2011
*	done					Erweiterung um config-Panels
*
*	author				Helge Frowein
*	(c)					2010-2011
*/
defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php?option=com_clm" method="post" name="adminForm" autocomplete="off">
	
	<?php
	$pane =& JPane::getInstance('tabs'); 
	echo $pane->startPane( 'pane' );
		
		echo $pane->startPanel( JText::_('PARAM_HEADER_BASICS'), 'panel_basics' );
		?>
			<table class="noshow">
				<tr>
					<td width="50%">
						<fieldset class="adminform">
							<table class="admintable" cellspacing="1">
							<tbody>
								<tr>
									<td valign="top">
											<?php echo $this->params->render( 'params', 'basics' ); ?>
									</td>
								</tr>
							</tbody>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>		
		<?php
		echo $pane->endPanel();
		
		echo $pane->startPanel( JText::_('PARAM_HEADER_PLAYERLIST'), 'panel_list' );
		?>
			<table class="noshow">
				<tr>
					<td width="50%">
						<fieldset class="adminform">
							<table class="admintable" cellspacing="1">
							<tbody>
								<tr>
									<td valign="top">
											<?php echo $this->params->render( 'params', 'list' ); ?>
									</td>
								</tr>
							</tbody>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>		
		<?php
		echo $pane->endPanel();
		
		echo $pane->startPanel( JText::_('PARAM_HEADER_PLAYERINFO'), 'panel_player' );
		?>
			<table class="noshow">
				<tr>
					<td width="50%">
						<fieldset class="adminform">
							<table class="admintable" cellspacing="1">
							<tbody>
								<tr>
									<td valign="top">
											<?php echo $this->params->render( 'params', 'player' ); ?>
									</td>
								</tr>
							</tbody>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>		
		<?php
		echo $pane->endPanel();
		
		echo $pane->startPanel( JText::_('PARAM_HEADER_STATS'), 'panel_stats' );
		?>
			<table class="noshow">
				<tr>
					<td width="50%">
						<fieldset class="adminform">
							<table class="admintable" cellspacing="1">
							<tbody>
								<tr>
									<td valign="top">
											<?php echo $this->params->render( 'params', 'stats' ); ?>
									</td>
								</tr>
							</tbody>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>		
		<?php
		echo $pane->endPanel();
		
	echo $pane->endPane();
	?>

	<div class="clr"></div>
	<input type="hidden" name="option" value="com_dwzliste" />
	<input type="hidden" name="view" value="dwzliste" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<table class="admintable" width="100%">
	<tr>
		<td align="center">
			<?php echo JText::_('DWZLISTE_DESCRIPTION'); ?>
		</td>
	</tr>
	
	<tr>
		<td align="center">
			<?php 
			// install-version	
			$Dir = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_dwzliste'; 
			$data = JApplicationHelper::parseXMLInstallFile($Dir.DS.'dwzliste.xml');
			$install = explode("v", $data['version']);
			$verInstall = $install[0];
			// print
			echo JText::_('VERSION').": ".$verInstall;
			echo '<br />';
			
			// aktuelle Versionsnummern auslesen !
			if (ini_get('allow_url_fopen') == 0) {
				echo JText::_('NO_FOPEN');
			} elseif (!$fp = fopen("http://www.chessleaguemanager.de/download/com_dwzliste_J15.csv","r")) {
				echo JText::_( 'NO_SERVER' );
			} else {
				$develop = fgetcsv($fp, 500, "|");
				$verDevelop = $develop[0];
				if ($verDevelop > $verInstall) {
					echo JText::_('VERSION_UPGRADE').': '.$verDevelop;
				} else {
					echo JText::_('VERSION_CURRENT');
				}
			}
			
			
			
			?>
		</td>
	</tr>

	<tr>
		<td align="center">
			Programmierung durch <a href="mailto:helge-frowein@online.de">Helge Frowein</a> nach einer Vorlage von <a href="mailto:thomas.schwietert@quakenbruecker-schachfreunde.de">Thomas Schwietert</a>
		</td>
	</tr>
	<tr>
		<td align="center">
			Projekt-Entwicklung auf <a href="http://sourceforge.net/projects/dwzliste/" target="_blank">http://sourceforge.net/projects/dwzliste/</a> und <a href="http://www.chessleaguemanager.de" target="_blank">http://www.chessleaguemanager.de</a>
		</td>
	</tr>
</table>

