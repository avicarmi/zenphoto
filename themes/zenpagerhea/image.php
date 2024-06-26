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
		<?php if (zp_has_filter('theme_head', 'colorbox::css')) { ?>
			<script>
				$(document).ready(function () {
					$("a.thickbox").colorbox({
						maxWidth: "98%",
						maxHeight: "98%",
						photo: true,
						close: '<?php echo gettext("close"); ?>',
						onComplete: function () {
							$(window).resize(resizeColorBoxImage);
						}
					});
				});
			</script>
		<?php } ?>
		<?php if (class_exists('RSS')) printRSSHeaderLink('Album', getAlbumTitle()); ?>
	</head>
	<body>
		<?php zp_apply_filter('theme_body_open'); ?>

		<div id="main">
			<div id="header">
				<a  id="rheacarmi" href="/">Rhea Carmi</a>
			</div>

			<div id="content">

				<div id="breadcrumb">
					<h2><?php
						printGalleryIndexURL(' » ','Home');
						printParentBreadcrumb("", " » ", " » ");
						printAlbumBreadcrumb("  ", " » ");
						?>
						<strong><?php printImageTitle(); ?></strong> (<?php echo imageNumber() . "/" . getNumImages(); ?>)
					</h2>
				</div>
				<div id="content-left">

					<!-- The Image -->
					<?php
					//
					if (function_exists('printThumbNav')) {
						printThumbNav(3, 6, 50, 50, 50, 50, FALSE);
					} else {
						callUserFunction('printPagedThumbsNav', 6, FALSE, gettext('« prev thumbs'), gettext('next thumbs »'), 40, 40);
					}
					?>

					<div class="imgnav">
						<?php if (hasPrevImage()) { ?>
							<div class="imgprevious"><a href="<?php echo html_encode(getPrevImageURL()); ?>" title="<?php echo gettext("Previous Image"); ?>">« <?php echo gettext("prev"); ?></a></div>
						<?php } if (hasNextImage()) { ?>
							<div class="imgnext"><a href="<?php echo html_encode(getNextImageURL()); ?>" title="<?php echo gettext("Next Image"); ?>"><?php echo gettext("next"); ?> »</a></div>
						<?php } ?>
					</div>

					<div id="image">
						<?php
						if (getOption("Use_thickbox") && !$_zp_current_image->isVideo()) {
							$boxclass = " class=\"thickbox\"";
						} else {
							$boxclass = "";
						}
						if ($_zp_current_image->isPhoto()) {
							$tburl = getFullImageURL();
						} else {
							$tburl = NULL;
						}
						if (!empty($tburl)) {
							?>
							<a href="<?php echo html_encode(pathurlencode($tburl)); ?>"<?php echo $boxclass; ?> target="_blank" title="<?php printBareImageTitle(); ?>">
								<?php
							}
							printCustomSizedImageMaxSpace(getBareImageTitle(), 950, 950);
							?>
							<?php
							if (!empty($tburl)) {
								?>
							</a>
							<?php
						}
						?>
					</div>
					<div id="narrow">
						<div id="imagedesc"><?php printImageDesc(); ?></div>
						<?php
						if (getTags()) {
							echo gettext('<strong>Tags:</strong>');
						} printTags('links', '', 'taglist', ', ');
						?>
						
						<div class="imgnav">
							<?php if (hasPrevImage()) { ?>
								<div class="imgprevious"><a href="<?php echo html_encode(getPrevImageURL()); ?>" title="<?php echo gettext("Previous Image"); ?>">« <?php echo gettext("prev"); ?></a></div>
							<?php } if (hasNextImage()) { ?>
								<div class="imgnext"><a href="<?php echo html_encode(getNextImageURL()); ?>" title="<?php echo gettext("Next Image"); ?>"><?php echo gettext("next"); ?> »</a></div>
							<?php } ?>
						</div>
						
						<br style="clear:both;" />
						<br />
						<br />
						<?php
						if (function_exists('printSlideShowLink')) {
							echo '<span id="slideshowlink">';
							printSlideShowLink();
							echo '</span>';
						}
						?>

<?php if (!empty($tburl)) {	?>
<div style="float:right;font-size:13px;line-height: normal;">
<a href="<?php echo html_encode(pathurlencode($tburl)); ?>" target="_blank" title="<?php printBareImageTitle(); ?>">Click on image for<br />full size raw image.</a>
</div>
<?php }	?>

						<?php
						if (getImageMetaData()) {
							printImageMetadata();
						}
						?>

						<br style="clear:both" />
						<?php
						if (class_exists('ScriptlessSocialSharing')) {
							ScriptlessSocialSharing::printButtons();
						}
						?>
					</div>

				</div><!-- content-left -->

				<div id="sidebar">
<?php include("sidebar.php"); ?>
				</div>

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
