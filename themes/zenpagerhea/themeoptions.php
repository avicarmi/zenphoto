<?php

// force UTF-8 Ø

/* Plug-in for theme option handling
 * The Admin Options page tests for the presence of this file in a theme folder
 * If it is present it is linked to with a require_once call.
 * If it is not present, no theme options are displayed.
 *
 */

class ThemeOptions {

	function __construct() {
		$me = basename(dirname(__FILE__));
		setThemeOptionDefault('zenpage_zp_index_news', false);
		setThemeOptionDefault('Allow_search', true);
		setThemeOptionDefault('Use_thickbox', true);
		setThemeOptionDefault('zenpage_homepage', 'none');
		setThemeOptionDefault('zenpage_contactpage', true);
		setThemeOptionDefault('zenpage_custommenu', false);
		setThemeOptionDefault('albums_per_page', 16);
		setThemeOptionDefault('albums_per_row', 4);
		setThemeOptionDefault('images_per_page', 16);
		setThemeOptionDefault('images_per_row', 4);
		setThemeOption('image_size', 950, NULL, 'zenpagerhea');
		setThemeOption('image_use_side', 'longest', NULL, 'zenpagerhea');
		setThemeOption('thumb_size', 200, NULL, 'zenpagerhea');
		setThemeOption('thumb_use_side', 'longest', NULL, 'zenpagerhea');
		setThemeOptionDefault('thumb_crop_width', 200);
		setThemeOptionDefault('thumb_crop_height', 200);
		setThemeOptionDefault('thumb_crop', 0);
		setThemeOptionDefault('thumb_transition', 0);
		setOptionDefault('colorbox_' . $me . '_album', 1);
		setOptionDefault('colorbox_' . $me . '_image', 1);
		setOptionDefault('colorbox_' . $me . '_search', 1);
		if (extensionEnabled('zenpage')) {
			setThemeOption('custom_index_page', '', NULL, 'zenpagerhea', false);
		} else {
			setThemeOption('custom_index_page', '', NULL, 'zenpagerhea', false);
		}
		if (class_exists('cacheManager')) {
			cacheManager::deleteCacheSizes($me);
			$img_wmk = getOption('fullimage_watermark') ? getOption('fullimage_watermark') : null;
			$thumb_wmk = getOption('Image_watermark') ? getOption('Image_watermark') : null;
			$img_effect = getOption('image_gray') ? 'gray' : null;
			cacheManager::addCacheSize($me, NULL, 950, 950, NULL, NULL, NULL, NULL, NULL, $img_wmk, $img_effect, true);
			cacheManager::addCacheSize($me, NULL, 200, 200, 200, 200, NULL, NULL, NULL, $thumb_wmk, $img_effect, false);
			cacheManager::addDefaultThumbSize();
			if (extensionEnabled('bxslider_thumb_nav')) {
				cacheManager::addCacheSize($me, NULL, 50, 50, 50, 50, NULL, NULL, NULL, $thumb_wmk, $img_effect, false);
			}
			if (extensionEnabled('paged_thumbs_nav')) {
				cacheManager::addCacheSize($me, NULL, 40, 40, 40, 40, NULL, NULL, NULL, $thumb_wmk, $img_effect, false);
			}
		}
		if (function_exists('createMenuIfNotExists')) {
			$menuitems = array(
					array(
							'type' => 'menulabel',
							'title' => gettext('News Articles'),
							'link' => '',
							'show' => 1,
							'nesting' => 0),
					array(
							'type' => 'menufunction',
							'title' => gettext('All news'),
							'link' => 'printAllNewsCategories("All news",TRUE,"","menu-active",false,false,false,"list",false,getOption("menu_manager_truncate_string"));',
							'show' => 1,
							'include_li' => 0,
							'nesting' => 1),
					array(
							'type' => 'menulabel',
							'title' => gettext('Gallery'),
							'link' => '',
							'show' => 1,
							'nesting' => 0),
					array(
							'type' => 'custompage',
							'title' => gettext('Gallery index'),
							'link' => 'gallery',
							'show' => 1,
							'nesting' => 1),
					array(
							'type' => 'menufunction',
							'title' => gettext('All Albums'),
							'link' => 'printAlbumMenuList("list",NULL,"","menu-active","submenu","menu-active","",false,false,false,false,getOption("menu_manager_truncate_string"));',
							'show' => 1,
							'include_li' => 0,
							'nesting' => 1),
					array(
							'type' => 'menulabel',
							'title' => gettext('Pages'),
							'link' => '',
							'show' => 1,
							'nesting' => 0),
					array(
							'type' => 'menufunction',
							'title' => gettext('All pages'),
							'link' => 'printPageMenu("list","","menu-active","submenu","menu-active","",0,false,getOption("menu_manager_truncate_string"));',
							'show' => 1,
							'include_li' => 0,
							'nesting' => 1),
			);
			createMenuIfNotExists($menuitems, 'zenpage');
		}
	}

	function getOptionsSupported() {
		global $_zp_db;
		$unpublishedpages = $_zp_db->queryFullArray("SELECT title,titlelink FROM " . $_zp_db->prefix('pages') . " WHERE `show` != 1 ORDER by `sort_order`");
		$list = array();
		foreach ($unpublishedpages as $page) {
			$list[get_language_string($page['title'])] = $page['titlelink'];
		}
		return array(gettext('Allow search') => array(
						'key' => 'Allow_search',
						'type' => OPTION_TYPE_CHECKBOX,
						'desc' => gettext('Check to enable search form.')),
				gettext('Use Colorbox') => array(
						'key' => 'Use_thickbox',
						'type' => OPTION_TYPE_CHECKBOX,
						'desc' => gettext('Check to display of the full size image with Colorbox.')),
				gettext('News on index page') => array(
						'key' => 'zenpage_zp_index_news',
						'type' => OPTION_TYPE_CHECKBOX,
						'desc' => gettext("Enable this if you want to show the news section’s first page on the <code>index.php</code> page.")),
				gettext('Homepage') => array(
						'key' => 'zenpage_homepage',
						'type' => OPTION_TYPE_SELECTOR,
						'selections' => $list,
						'null_selection' => gettext('none'),
						'desc' => gettext("Choose here any <em>un-published Zenpage page</em> (listed by <em>titlelink</em>) to act as your site’s homepage instead the normal gallery index.") . "<p class='notebox'>" . gettext("<strong>Note:</strong> This of course overrides the <em>News on index page</em> option and your theme must be setup for this feature! Visit the theming tutorial for details.") . "</p>"),
				gettext('Use standard contact page') => array(
						'key' => 'zenpage_contactpage',
						'type' => OPTION_TYPE_CHECKBOX,
						'desc' => gettext('Disable this if you do not want to use the separate contact page with the contact form. You can also use the codeblock of a page for this. See the contact_form plugin documentation for more info.')),
				gettext('Use custom menu') => array(
						'key' => 'zenpage_custommenu',
						'type' => OPTION_TYPE_CHECKBOX,
						'desc' => gettext('Check this if you want to use the <em>menu_manager</em> plugin if enabled to build a custom menu instead of the separate standard ones. A standard menu named "zenpage" is created and used automatically.'))
		);
	}

	function getOptionsDisabled() {
		return array('custom_index_page', 'image_size', 'thumb_size');
	}

	function handleOption($option, $currentValue) {
		
	}

}

?>