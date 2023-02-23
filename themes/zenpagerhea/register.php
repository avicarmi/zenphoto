<?php
// force UTF-8 Ø

if (!defined('WEBPATH'))
	die();
if (function_exists('printRegistrationForm')) {
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
						<h2><?php printGalleryIndexURL(' » ', 'Home'); ?></h2>
					</div>

					<div id="content-left">
						<h1><?php echo gettext('User Registration') ?></h1>
						<?php printRegistrationForm(); ?>
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