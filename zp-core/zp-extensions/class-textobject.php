<?php

/**
 *
 * Supports files of the following types:
 * <ul>
 * 	<li><var>.txt</var></var>
 * 	<li><var>.htm</var></li>
 * 	<li><var>.html</var></var>
 * </ul>
 * 		The contents of these files are "dumpped" into a <var><span>...</span></var> sized to a 24x36 ratioed box based on your
 * 		theme	"image size" option. This has a class of "textobject" so it can be styled.
 *
 * What this plugin really is for is to serve as a model of how a plugin can be made to handle file types
 * that zenphoto does not handle natively.
 *
 * Some key points to note:
 * <ul>
 * 	<li>The naming convention for these plugins is <i>class-<handler class>.php</i>.</li>
 * 	<li>The statement setting the <i>plugin_is_filter</i> variable must be near the front of the file. This is important
 * as it is the indicator to the Zenphoto plugin loader to load the script at the same point that other
 * object modules are loaded.</li>
 * <li>These objects are extension to the zenphoto "Image" class. This means they have all the properties of
 * an image plus whatever you add. Of course you will need to override some of the image class functions to
 * implement the functionality of your new class.</li>
 * <li>There is one VERY IMPORTANT method that you must provide which is not part of the "Image" base class. The
 * <i>getBody()</i> method. This method is called by <i>template-functions.php</i> in place of where it would normally put a URL
 * to the image to show. This method must do everything needed to cause your image object to be viewable by the
 * browser.</li>
 * </ul>
 *
 * So, briefly, the first three lines of code below are the standard plugin interface to Admin.
 * Then there are calls on <var>addPlginType(<file extension>, <Object Name>);</var> This function registers the plugin as the
 * handler for files with the specified extension. If the plugin can handle more than one file extension, make a call
 * to the registration function for each extension that it handles.
 * The rest is the object class for handling these files.
 *
 * The code of the object instantiation function is mostly required. Plugin <i>images</i> follow the lead of <var>class-video</var> in that
 * if there is a real image file with the same name save the suffix, it will be considered the thumb image of the object.
 * This image is fetched by the call on <var>checkObjectsThumb()</var>. There is also code in the <var>getThumb()</var> method to deal with
 * this property.
 *
 * Since text files have no natural height and width, we set them based on the image size option. This happens after the call
 * <var>PersistentObject()</var>. The rest of the code there sets up the default title.
 *
 * <var>getThumb()</var> is responsible for generating the thumbnail image for the object. As above, if there is a similar named real
 * image, it will be used. Otherwise [for this object implementation] we will use a thumbnail image provided with the plugin.
 * The particular form of the file name used when there is no thumb stand-in image allows Zenphoto to choose an image in the
 * plugin folder.
 *
 * @author Stephen Billard (sbillard)
 * @package zpcore\plugins\classtextobject
 *
 */
$plugin_is_filter = 990 | CLASS_PLUGIN;
$plugin_description = gettext('Provides a means for showing text type documents (.txt, .html, .htm).');
$plugin_author = "Stephen Billard (sbillard)";
$plugin_category = gettext('Media');
$plugin_deprecated = gettext('This plugin will be restructured and moved to core in later versions');

Gallery::addImageHandler('htm', 'TextObject');
Gallery::addImageHandler('html', 'TextObject');
Gallery::addImageHandler('txt', 'TextObject');
$option_interface = 'textObject_Options';

/**
 * Option class for textobjects objects
 * @deprecated 2.0
 */
class TextObject_Options {

	/**
	 * Standard option interface
	  * @deprecated 2.0
	 * @return array
	 */
	function getOptionsSupported() {
		return array(gettext('Watermark default images') => array('key'	 => 'textobject_watermark_default_images', 'type' => OPTION_TYPE_CHECKBOX,
										'desc' => gettext('Check to place watermark image on default thumbnail images.')));
	}

}

require_once(dirname(__FILE__) . '/class-textobject/class-textobject_core.php');
?>