<?php
/**
 * A plugin to add a cookie notify dialog to comply with the EU cookie law and Google's requirement for Google Ads and more
 * https://www.cookiechoices.org
 *
 * Adapted of https://cookieconsent.osano.com/
 * 
 * Note that to actually use the opt-in and out-out complicance modes your theme may require customisation. 
 * as the plugin does not clear or block scripts by itself. It is not possible to savely delete third party cookies.
 * 
 * It also does not block cookies Zenphoto sets itself as these are not privacy related and require to work properly. 
 * Learn more about Zenphotp's cookies on: https://zenphoto.org/news/cookies/
 * 
 * But you can use this plugin to only executed scripts on consent by:
 * 
 * a) Add the JS calls to block or allow the scripts option so they cannot set or use their cookies unless allowed to run
 * b) Use the method `cookieconsemt::checkConsent()` to manually wrap JS script calls 
 * 
 * @author Malte Müller (acrylian), Fred Sondaar (fretzl), Vincent Bourganel (vincent3569)
 * @license GPL v3 or later
 * @package plugins
 * @subpackage cookieconsent
 */
$plugin_is_filter = 5 | THEME_PLUGIN;
$plugin_description = gettext("A plugin to add a cookie notify dialog to comply with the EU cookie law and Google's request regarding usages of Google Adwords, Analytics and more. Based on <a href='https://cookieconsent.osano.com/'>https://cookieconsent.osano.com/</a>");
$plugin_author = "Malte Müller (acrylian), Fred Sondaar (fretzl), Vincent Bourganel (vincent3569)";
$plugin_notice = gettext('Note: This plugin cannot block or delete cookies by itself without proper configuration that also may require customisations to your site.');
$option_interface = 'cookieConsent';
$plugin_category = gettext('Misc');

if (!zp_loggedin() && !isset($_COOKIE['cookieconsent_status'])) {
	zp_register_filter('theme_head', 'cookieConsent::getCSS');
	zp_register_filter('theme_head', 'cookieConsent::getJS');
	zp_register_filter('theme_head', 'cookieconsent::getConsentedJS');
}

class cookieConsent {

	function __construct() {
		setOptionDefault('zpcookieconsent_expirydays', 365);
		setOptionDefault('zpcookieconsent_theme', 'block');
		setOptionDefault('zpcookieconsent_position', 'bottom');
		setOptionDefault('zpcookieconsent_colorpopup', '#000');
		setOptionDefault('zpcookieconsent_colorbutton', '#f1d600');
		setOptionDefault('zpcookieconsent_compliancetype', 'info');
		setOptionDefault('zpcookieconsent_consentrevokable', 0);
	}

	function getOptionsSupported() {
		$options = array(
				gettext('Button: Agree') => array(
						'key' => 'zpcookieconsent_buttonagree',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 1,
						'multilingual' => 1,
						'desc' => gettext('Text used for the dismiss button in info complicance. Leave empty to use the default text.')),
				gettext('Button: Allow cookies') => array(
						'key' => 'zpcookieconsent_buttonallow',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 1,
						'multilingual' => 1,
						'desc' => gettext('Text used for the button to allow cookies in opt-in and opt-out complicance. Leave empty to use the default text.')),
				gettext('Button: Decline cookies') => array(
						'key' => 'zpcookieconsent_buttondecline',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 1,
						'multilingual' => 1,
						'desc' => gettext('Text used for the button to decline cookies in opt-in and opt-out complicance. Leave empty to use the default text.')),
				gettext('Button: Learn more') => array(
						'key' => 'zpcookieconsent_buttonlearnmore',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 2,
						'multilingual' => 1,
						'desc' => gettext('Text used for the learn more info button. Leave empty to use the default text.')),
				gettext('Button: Learn more - URL') => array(
						'key' => 'zpcookieconsent_buttonlearnmorelink',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 3,
						'desc' => gettext('URL to your cookie policy / privacy info page.')),
				gettext('Message') => array(
						'key' => 'zpcookieconsent_message',
						'type' => OPTION_TYPE_TEXTAREA,
						'order' => 4,
						'multilingual' => 1,
						'desc' => gettext('The message shown by the plugin. Leave empty to use the default text.')),
				gettext('Domain') => array(
						'key' => 'zpcookieconsent_domain',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 5,
						'desc' => gettext('The domain for the consent cookie that Cookie Consent uses, to remember that users have consented to cookies. Useful if your website uses multiple subdomains, e.g. if your script is hosted at <code>www.example.com</code> you might override this to <code>example.com</code>, thereby allowing the same consent cookie to be read by subdomains like <code>foo.example.com</code>.')),
				gettext('Expire') => array(
						'key' => 'zpcookieconsent_expirydays',
						'type' => OPTION_TYPE_TEXTBOX,
						'order' => 6,
						'desc' => gettext('The number of days Cookie Consent should store the user’s consent information for. Use -1 for no expiry.')),
				gettext('Theme') => array(
						'key' => 'zpcookieconsent_theme',
						'type' => OPTION_TYPE_SELECTOR,
						'order' => 7,
						'selections' => array(
								'block' => 'block',
								'edgeless' => 'edgeless',
								'classic' => 'classic',
								gettext('custom') => 'custom'
						),
						'desc' => gettext('These are the included default themes. The chosen theme is added to the popup container as a CSS class in the form of .cc-style-THEME_NAME. Users can create their own themes.')),
				gettext('Position') => array(
						'key' => 'zpcookieconsent_position',
						'type' => OPTION_TYPE_SELECTOR,
						'order' => 7,
						'selections' => array(
								gettext('Top') => 'top',
								gettext('Top left') => 'top-left',
								gettext('Top right') => 'top-right',
								gettext('Bottom') => 'bottom',
								gettext('Bottom left') => 'bottom-left',
								gettext('Bottom right') => 'bottom-right',
						),
						'desc' => gettext('Choose the position of the popup. Top and Bottom = banner, Top left/right, Bottom left/right = floating')),
				gettext('Dismiss on Scroll') => array(
						'key' => 'zpcookieconsent_dismissonscroll',
						'type' => OPTION_TYPE_CHECKBOX,
						'order' => 9,
						'desc' => gettext('Check to dismiss when users scroll a page [other than <em>Learn more</em> page].')),
				gettext('Color - Popup') => array(
						'key' => 'zpcookieconsent_colorpopup',
						'type' => OPTION_TYPE_COLOR_PICKER,
						'order' => 10,
						'desc' => gettext('Choose the color of the popup background.')),
				gettext('Color - Button') => array(
						'key' => 'zpcookieconsent_colorbutton',
						'type' => OPTION_TYPE_COLOR_PICKER,
						'order' => 11,
						'desc' => gettext('Choose the color of the button.')),
				gettext('Compliance type') => array(
						'key' => 'zpcookieconsent_compliancetype',
						'type' => OPTION_TYPE_SELECTOR,
						'order' => 12,
						'selections' => array(
								gettext('Info: Cookies are always allowed') => 'info',
								gettext('Opt-in: Cookies are allowed after consent') => 'opt-in',
								gettext('Opt-out: Cookies are allowed unless declined') => 'opt-out'
						),
						'desc' => gettext('Choose the compliance type required for your juristiction. Note that your site may require modification to properly apply this to your cookies. Also see the scripts option below.')),
				gettext('Consent revokable') => array(
						'key' => 'zpcookieconsent_consentrevokable',
						'type' => OPTION_TYPE_CHECKBOX,
						'order' => 13,
						'desc' => gettext('Check to allow revoking the consent as required in some jurisdictions.')),
				gettext('Scripts to allow or block') => array(
						'key' => 'zpcookieconsent_scripts',
						'type' => OPTION_TYPE_TEXTAREA,
						'order' => 14,
						'multilingual' => false,
						'desc' => gettext('Add privacy related JS scripts (ad trackers statistics etc.) here to allow or block opt-in/opt-out complicances (without the script wrapper). As we cannot safely delete cookies set by third party scripts, we block their execution so they can neither set nor fetch their cookies. You can also use the methode <code>cookieconsent::checkConsent()</code> on your theme.'))
		);
		return $options;
	}
	
	/**
	 * Gets the CSS for the cookieconsent script
	 */
	static function getCSS() {
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo FULLWEBPATH . '/' . ZENFOLDER . '/' . PLUGIN_FOLDER; ?>/cookieconsent/cookieconsent.min.css" />
		<?php
	}

	/**
	 * Gets the JS definition of the cookieconsent script based on the options
	 */
	static function getJS() {
		$message = gettext('This website uses cookies. By continuing to browse the site, you agree to our use of cookies.');
		if (getOption('zpcookieconsent_message')) {
			$message = get_language_string(getOption('zpcookieconsent_message'));
		}
		$dismiss = gettext('Agree');
		if (getOption('zpcookieconsent_buttonagree')) {
			$dismiss = get_language_string(getOption('zpcookieconsent_buttonagree'));
		}
		$allow = gettext('Allow cookies');
		if (getOption('zpcookieconsent_buttonallow')) {
			$allow = get_language_string(getOption('zpcookieconsent_buttonallow'));
		}
		$decline = gettext('Decline');
		if (getOption('zpcookieconsent_buttondecline')) {
			$decline = get_language_string(getOption('zpcookieconsent_buttondecline'));
		}
		$dataprivacy_info = getDataUsageNotice();
		if (getOption('zpcookieconsent_buttonlearnmore')) {
			$learnmore = get_language_string(getOption('zpcookieconsent_buttonlearnmore'));
		} else {
			$learnmore = $dataprivacy_info['linktext'];
		}
		if (getOption('zpcookieconsent_buttonlearnmorelink')) {
			$link = getOption('zpcookieconsent_buttonlearnmorelink');
		} else {
			$link = $dataprivacy_info['url'];
		}
		$theme = 'block';
		if (getOption('zpcookieconsent_theme')) {
			$theme = getOption('zpcookieconsent_theme');
			//fix old option
			if (!in_array($theme, array('block', 'edgeless', 'classic', 'custom'))) {
				$theme = 'block';
				setOption('zpcookieconsent_theme', $theme, true);
			}
		}
		$domain = '';
		if (getOption('zpcookieconsent_domain')) {
			$domain = getOption('zpcookieconsent_domain');
		}
		$position = getOption('zpcookieconsent_position');
		$cookie_expiry = getOption('zpcookieconsent_expirydays');
		$dismiss_on_scroll = "false";
		if (getOption('zpcookieconsent_dismissonscroll') && strpos(sanitize($_SERVER['REQUEST_URI']), $link) === false) { // false in Cookie Policy Page
			$dismiss_on_scroll = 100;
		}
		$color_popup = getOption('zpcookieconsent_colorpopup');
		$color_button = getOption('zpcookieconsent_colorbutton');
		$complicance_type = getOption('zpcookieconsent_compliancetype');
		$consentrevokable = getOption('zpcookieconsent_consentrevokable');
		if ($consentrevokable) {
			$consentrevokable = 'true';
		} else {
			$consentrevokable = 'false';
		}
		?>
		<script src="<?php echo FULLWEBPATH . '/' . ZENFOLDER . '/' . PLUGIN_FOLDER; ?>/cookieconsent/cookieconsent.min.js"></script>
		<script>
			window.addEventListener("load", function () {
				window.cookieconsent.initialise({
					palette: {
						popup: {
							background: "<?php echo $color_popup; ?>"
						},
						button: {
							background: "<?php echo $color_button; ?>"
						}
					},
					position: "<?php echo js_encode($position); ?>",
					theme: "<?php echo js_encode($theme); ?>",
					dismissOnScroll: <?php echo js_encode($dismiss_on_scroll); ?>,
					type: '<?php echo $complicance_type; ?>',
					revokable: <?php echo $consentrevokable; ?>,
					cookie: {
						domain: "<?php echo js_encode($domain); ?>",
						expiryDays: <?php echo js_encode($cookie_expiry); ?>
					},
					content: {
						message: "<?php echo js_encode($message); ?>",
						allow: '<?php echo js_encode($allow); ?>',
						deny: '<?php echo js_encode($decline); ?>',
						dismiss: "<?php echo js_encode($dismiss); ?>",
						link: "<?php echo js_encode($learnmore); ?>",
						href: "<?php echo html_encode($link); ?>"
					},
					onInitialise: function (status) {
						// We don't do anythhing here
						var type = this.options.type;
						var didConsent = this.hasConsented();
						if (type == 'opt-in' && didConsent) {
							// enable cookie
						}
						if (type == 'opt-out' && !didConsent) {
							// disable cookies
						} 
					},
					onStatusChange: function (status, chosenBefore) {
						// We don't do anythhing here
						this.element.parentNode.removeChild(this.element);
						var type = this.options.type;
						var didConsent = this.hasConsented();
						if (type == 'opt-in' && didConsent) {
							// enable cookies
						}
						if (type == 'opt-out' && !didConsent) {
							// disable cookies
						} 
					},
					onRevokeChoice: function () {
						// We don't do anythhing here
						var type = this.options.type;
						if (type == 'opt-in') {
							// disable cookies
						}
						if (type == 'opt-out') {
							// enable cookies
							
						}
					}
				});
			});
		</script>
		<?php
	}
	
	
	/**
	 * Checks if consent has been given depending on the compliance mode and if the cookieconsent_status cookie is set
	 * 
	 * - info: All just informational so always true
	 * - opt-in: Returns true only if the consent cookie is set to "allow"
	 * - opt-out: Returns true by default unless declined or if the consent cookie is set to "allow"
	 * 
	 * @since ZenphotoCMS 1.5.8
	 * 
	 * @return boolean
	 */
	static function checkConsent() {
		$complicance = getOption('zpcookieconsent_compliancetype');
		$consent = zp_getCookie('cookieconsent_status');
		switch ($complicance) {
			case 'info':
				// just info but always allowed
				return true;
			case 'opt-in':
				// only allow by consent
				if ($consent && $consent == 'allow') {
					return true;
				} else {
					return false;
				}
			case 'opt-out':
				//Allows by default or by consent
				if (!$consent || $consent == 'allow') {
					return true;
				} else {
					return false;
				}
				break;
		}
		return false;
	}

	/**
	 * Prints the scropts added to the scripts option.
	 * These are then added to the theme_header filter automatically by the plugin
	 * 
	 * @since ZenphotoCMS 1.5.8
	 */
	static function getConsentedJS() {
		if (cookieconsent::checkConsent()) {
			echo '<script>' . getOption('zpcookieconsent_scripts') . '</script>';
		}
	}

}
