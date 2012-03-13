<?php
/**
 *
 * Displays a "plugin usage" document based on the plugin's doc comment block.
 *
 * Supports the following PHPDoc markup tags:
 * 	<i> for emphasis
 *  <b> for strong
 *  <code> for mono-spaced text
 *  <hr> for horizontal rule
 *  <ul><li>, <ol><li> for lists
 *  <pre>
 *  <br> for line breaks
 *  <var> for variables (treated the same as <code>)
 *
 * @package admin
 */
define('OFFSET_PATH', 2);
require_once(dirname(__FILE__).'/admin-globals.php');

admin_securityChecks(NULL, currentRelativeURL());

$markup = array(
						'&lt;i&gt;'=>'<em>',
						'&lt;/i&gt;'=>'</em>',
						'&lt;b&gt;'=>'<strong>',
						'&lt;/b&gt;'=>'</strong>',
						'&lt;code&gt;'=>'<span class="inlinecode">',
						'&lt;/code&gt;'=>'</span>',
						'&lt;hr&gt;'=>'<hr />',
						'&lt;ul&gt;'=>'<ul>',
						'&lt;/ul&gt;'=>'</ul>',
						'&lt;ol&gt;'=>'<ol>',
						'&lt;/ol&gt;'=>'</ol>',
						'&lt;li&gt;'=>'<li>',
						'&lt;/li&gt;'=>'</li>',
						'&lt;pre&gt;'=>'<pre>',
						'&lt;/pre&gt;'=>'</pre>',
						'&lt;br&gt;'=>'<br />',
						'&lt;var&gt;'=>'<span class="inlinecode">',
						'&lt;/var&gt;'=>'</span>'
);

$extension = sanitize($_GET['extension']);
$thirdparty = isset($_GET['thirdparty']);
if ($thirdparty) {
	$path = SERVERPATH.'/'.USER_PLUGIN_FOLDER.'/'.$extension.'.php';
} else {
	$path = SERVERPATH.'/'.ZENFOLDER.'/'.PLUGIN_FOLDER.'/'.$extension.'.php';
}

$plugin_description = '';
$plugin_notice = '';
$plugin_author = '';
$plugin_version = '';
$plugin_is_filter = '';

require_once($path);
$buttonlist = zp_apply_filter('admin_utilities_buttons', array());

$pluginStream = file_get_contents($path);

if ($thirdparty) {
	$whose = gettext('third party plugin');
	$path = stripSuffix($path).'/logo.png';
	if (file_exists($path)) {
		$ico = str_replace(SERVERPATH, WEBPATH, $path);
	} else {
		$ico = 'images/place_holder_icon.png';
	}
} else {
	$whose = 'Zenphoto official plugin';
	$ico = 'images/zp_gold.png';
}
$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="stylesheet" href="<?php echo WEBPATH.'/'.ZENFOLDER; ?>/admin.css" type="text/css" />
	<meta http-equiv="content-type" content="text/html; charset=<?php echo LOCAL_CHARSET; ?>" />
	<title><?php echo sprintf(gettext('%1$s %2$s: %3$s'),html_encode($_zp_gallery->getTitle()),gettext('admin'),html_encode($extension)); ?></title>
</head>
<body>
	<div id="main">
		<?php echo gettext('Plugin usage information'); ?>
		<div id="content">
			<h1><img class="zp_logoicon" src="<?php echo $ico; ?>" alt="<?php echo gettext('logo'); ?>" title="<?php echo $whose; ?>" /><?php echo html_encode($extension); ?></h1>
			<div class="border">
				<?php echo $plugin_description; ?>
			</div>
				<?php
				if ($thirdparty) {
					?>
					<h3><?php printf( gettext('Version: %s'), $plugin_version); ?></h3>
					<?php
					}
				?>
				<h3><?php printf(gettext('Author: %s'), html_encode($plugin_author)); ?></h3>
			<div>
			<?php
			if ($plugin_notice) {
				?>
				<p class="notebox">
					<?php echo $plugin_notice; ?>
				</p>
				<?php
			}
			$i = strpos($pluginStream, '/*');
			$j = strpos($pluginStream, '*/');
			if ($i !== false && $j !== false) {
				$commentBlock = substr($pluginStream, $i+2, $j-$i-2);
				$lines = explode('*', $commentBlock);
				$doc = '';
				$par = false;
				$empty = false;

				foreach ($lines as $line) {
					$line = trim($line);
					if (empty($line)) {
						if (!$empty) {
							if ($par) {
								$doc .=  '</p>';
							}
							$doc .= '<p>';
							$empty = $par = true;
						}
					} else {
						if (strpos($line, '@') === 0) {
							preg_match('/@(.*?)\s/',$line,$matches);
							$subpackage = false;
							if (!empty($matches)) {
								switch ($matches[1]) {
									case 'author':
										$plugin_author = trim(substr($line, 8));
										break;
									case 'subpackage':
										$subpackage = trim(substr($line, 11)).'/';
										break;
								}
							}
						} else {
							$line = strtr(html_encode($line),$markup);
							if(preg_match($reg_exUrl, $line, $url)) {
								$line = preg_replace($reg_exUrl, '<a href="'.$url[0].'">'.$url[0].'</a> ', $line);
							}
							$doc .= $line.' ';
							$empty = false;
						}
					}
				}
				if ($par) {
					$doc .=  '</p>';
					echo $doc;
					$doc = '';
				}
			echo $doc;
			}
			if ($thirdparty) {
				if ($str = isolate('$plugin_URL', $pluginStream)) {
					if (false !== eval($str)) {
						printf(gettext('See also the <a href="%1$s">%2$s</a>'),$plugin_URL, $extension);
					}
				}
			} else {
				$plugin_URL = 'http://www.zenphoto.org/documentation/plugins/'.$subpackage.'_'.PLUGIN_FOLDER.'---'.$extension.'.php.html';
				printf(gettext('See also the Zenphoto online documentation: <a href="%1$s">%2$s</a>'),$plugin_URL, $extension);
			}
			if (!empty($buttonlist)) {
				$buttonlist = sortMultiArray($buttonlist, array('category','button_text'), false);
				?>
				<div class="box" id="overview-utility">
				<h2 class="h2_bordered">
				<?php echo gettext("Added to overview utilitiy functions"); ?>
				</h2>
					<?php
					$category = '';
					foreach ($buttonlist as $button) {
						$button_category = $button['category'];
						$button_icon = $button['icon'];
						if ($category != $button_category) {
							if ($category) {
								?>
								</fieldset>
								<?php
							}
							$category = $button_category;
							?>
							<fieldset class="utility_buttons_field"><legend><?php echo $category; ?></legend>
							<?php
						}
						?>
						<form name="<?php echo $button['formname']; ?>"	action="<?php echo $button['action']; ?>" class="overview_utility_buttons">
							<?php if (isset($button['XSRFTag']) && $button['XSRFTag']) XSRFToken($button['XSRFTag']); ?>
							<?php echo $button['hidden']; ?>
							<div class="buttons">
								<button class="tooltip" type="submit"	title="<?php echo $button['title']; ?>" <?php if (!$button['enable']) echo 'disabled="disabled"'; ?>>
								<?php
								if(!empty($button_icon)) {
									?>
									<img src="<?php echo $button_icon; ?>" alt="<?php echo $button['alt']; ?>" />
									<?php
								}
								echo html_encode($button['button_text']);
								?>
								</button>
							</div><!--buttons -->
						</form>
						<?php
					}
					if ($category) {
						?>
						</fieldset>
						<?php
					}
					?>
				</div><!-- overview-utility -->
				<br clear="all">
				<?php
			}
			?>
			</div>
		</div>
	</div>
</body>
