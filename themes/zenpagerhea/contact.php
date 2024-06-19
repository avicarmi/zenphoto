<?php
// force UTF-8 Ø

if (!defined('WEBPATH'))
	die();
if (extensionEnabled('contact_form')) {
	?>
	<!DOCTYPE html>
	<html<?php printLangAttribute(); ?>>
		<head>
			<meta charset="<?php echo LOCAL_CHARSET; ?>">
			<?php zp_apply_filter('theme_head'); ?>
			<?php printHeadTitle(); ?>
			<link rel="stylesheet" href="<?php echo $_zp_themeroot; ?>/style.css" type="text/css" />
		</head>
		<body>
			<?php zp_apply_filter('theme_body_open'); ?>

			<div id="main">

				<div id="header">
					<a id="rheacarmi" href="/">Rhea Carmi</a>	 				
						<?php
						if (getOption('Allow_search')) {
							printSearchForm("", "search", "", gettext("Search"));
						}
						?>
				</div>

				<div id="content">

					<div id="breadcrumb">
						<h2  style="height:22px;"><?php printGalleryIndexURL(' » ', 'Home'); echo gettext("Contact us");?></h2>
					</div>

					<div id="content-left">
						<h2><?php echo gettext('Contact us') ?></h2>
						<?php contactForm::printContactForm(); ?>

					</div><!-- content left-->


					<div id="sidebar">
						<?php include("sidebar.php"); ?>
					</div><!-- sidebar -->



					<div id="footer">
						<?php include("footer.php"); ?>
					</div>

				</div><!-- content -->

			</div><!-- main -->
			<?php
			zp_apply_filter('theme_body_close');
			?>
		</body>
	</html>
	<?php
} else {
	include(SERVERPATH . '/' . ZENFOLDER . '/404.php');
}
	?>