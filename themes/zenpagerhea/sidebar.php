<?php
// force UTF-8 Ø

if (function_exists('printCustomMenu') && getOption('zenpage_custommenu')) {
	?>
	<div class="menu">
		<?php printCustomMenu('zenpage', 'list', '', "menu-active", "submenu", "menu-active", 2); ?>
	</div>
	<?php
} else {
	if (ZP_PAGES_ENABLED) { ?>
		<div class="menu">
			<?php printPageMenu("list", "", "menu-active", "submenu", "menu-active"); ?>
		</div>
	<?php }
	
	if (function_exists("printAlbumMenu")) { ?>
		<div class="menu">
		<?php printAlbumMenu("list", false, "", "menu-active", "submenu", "menu-active", ''); ?>
		</div>
	<?php }

} // custom menu check end
?>

<?php
if (getOption("zenpage_contactpage") && extensionEnabled('contact_form')) {
	?>
	<div class="menu">
		<ul>
			<li>
				<?php
				if ($_zp_gallery_page != 'contact.php') {
					printCustomPageURL(gettext('Contact us'), 'contact', '', '');
				} else {
					echo gettext("Contact us");
				}
				?></li>
		</ul>
	</div>
	<?php
}
?>
<?php callUserFunction('printLanguageSelector'); ?>