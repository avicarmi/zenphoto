<?php
// force UTF-8 Ø

if (!defined('WEBPATH'))
	die();
?>
<!DOCTYPE html>
<html<?php printLangAttribute(); ?>>
	<head>
		<meta charset="<?php echo LOCAL_CHARSET; ?>">
		<?php zp_apply_filter('theme_head'); ?>
		<?php printHeadTitle(); ?>
		<link rel="stylesheet" href="<?php echo $_zp_themeroot; ?>/style.css" type="text/css" />
		<?php if (class_exists('RSS')) printRSSHeaderLink('Album', getAlbumTitle()); ?>
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
					<h2><?php printGalleryIndexURL(' » ', 'Home'); printParentBreadcrumb("", " » ", " » "); ?><strong><?php printAlbumTitle(); ; printCurrentPageAppendix(); ?></strong></h2>
				</div>

				<div id="content-left">
					<div><?php printAlbumDesc(); ?></div>


					<?php printPageListWithNav("« " . gettext("prev"), gettext("next") . " »"); ?>
					<div id="albums">
						<?php while (next_album()): ?>
							<div class="album">
								<div class="thumb">
									<a href="<?php echo html_encode(getAlbumURL()); ?>" title="<?php echo gettext('View album:'); ?> <?php printBareAlbumTitle(); ?>"><?php printCustomAlbumThumbMaxSpace(getBareAlbumTitle(), 200, 200); ?></a>
								</div>
								<div class="albumdesc">
									<h3><a href="<?php echo html_encode(getAlbumURL()); ?>" title="<?php echo gettext('View album:'); ?> <?php printBareAlbumTitle(); ?>"><?php printAlbumTitle(); ?></a></h3>
								</div>
								<p style="clear: both; "></p>
							</div>
						<?php endwhile; ?>
					</div>

					<div id="images">
						<?php while (next_image()): ?>
							<div class="image">
								<div class="imagethumb"><a href="<?php echo html_encode(getImageURL()); ?>" title="click to view image: <?php printBareImageTitle(); ?>"><?php printCustomSizedImageThumbMaxSpace(getBareImageTitle(), 200, 200); ?></a></div>
							</div>
						<?php endwhile; ?>

					</div>
					<p style="clear: both; "><br /></p>
					<?php
      printPageListWithNav("« " . gettext("prev"), gettext("next") . " »");
      printTags('links', gettext('<strong>Tags:</strong>') . ' ', 'taglist', ', ');
     ?>
					<br style="clear:both;" /><br />
					<?php
					
					if(class_exists('ScriptlessSocialSharing')) {
						ScriptlessSocialSharing::printButtons();
					}	
					
					if (function_exists('printSlideShowLink')) {
						echo '<span id="slideshowlink">';
						printSlideShowLink();
						echo '</span>';
					}
					?>
					<br style="clear:both;" />
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