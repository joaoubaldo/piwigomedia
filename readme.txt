=== PiwigoMedia ===
Contributors: johnnyfive
Donate link: http://joaoubaldo.com
Tags: gallery, piwigo, integration, media
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 1.9.0

This plugins allows media from a Piwigo site to be inserted into WordPress posts and pages.

== Description ==

PiwigoMedia WordPress plugin features integration between Piwigo sites and Wordpress, using the TinyMCE editor.

The main advantages of PiwigoMedia are:

*   Simplicity. Simple to use and configure
*   No duplicated media. Images are linked from Piwigo's site, nothing is imported into WordPress
*   Independency. WordPress and Piwigo don't have to be installed on the same server since PiwigoMedia uses Piwigo's webapi


== Installation ==

1. Unpack PiwigoMedia's zip file inside `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure PiwigoMedia. Go to 'Settings/PiwigoMedia' page in WordPress administration.
4. Access PiwigoMedia functionality using Wordpress' wysiwyg editor

== Frequently Asked Questions ==

= How to use shortcodes? =

pwg-image - display a single image:
[pwg-image site="http://..." id="IMAGE_ID"]

pwg-category - display any number of images from a category:
[pwg-category site="http://..." id="CATEGORY_ID" images="NUMBER_OF_IMAGES_TO_DISPLAY" page="PAGE_INDEX"]

pwg-gallery - display Piwigo categories as inline galleries:
[pwg-gallery site="http://..." id="CATEGORY_ID" images="NUMBER_OF_IMAGES_TO_DISPLAY" page="PAGE_INDEX" height="GALLERY_HEIGHT"]


= Which versions of Piwigo are supported? =

Since PiwigoMedia v1.9.0 only Piwigo v2.0 and upper were tested.


== Screenshots ==

1. PiwigoMedia's main window

== Changelog ==
= 1.9.0 =
* new: Major rewrite. PiwigoMedia now uses AngularJS and Bootstrap.
* update: UI completely changed. Cleaner and more responsive interface.
* updated: pot file was updated with new strings.
* updated: it is now possible to make a selection from multiple Piwigo sites.
* updated: shortcodes but pwm_gallery are working again.

= 1.1.4 =
* update: fixed a nasty bug in pagination/navigation.

= 1.1.3 =
* update: added Piwigo 2.6.1 compatibility.
* update: UI bugs fixed.
* update: more feedback messages when something goes wrong.

= 1.1.2 =
* update: Fixed full image urls. 
* update: Disabled PiwigoMedia's Widget as it was causing issues on some installations.
* update: pwg-gallery shortcode now has links to xxlarge image and image name is displayed in info box.

= 1.1.1 =
* new: pwg-gallery shortcode. This shortcode uses Galleria JS library (http://galleria.io/) to display Piwigo categories as inline galleries.
* update: curl requests are now configured to bypass peer verification. This solves the problem with self-signed certificates.

= 1.1.0 =
* new: WP shortcodes are now available: pwg-image and pwg-category.
* new: PiwigoMedia Widget. Display Piwigo images inside a widget. This is an EXPERIMENTAL feature and it's very basic at the moment.

= 1.0.1 =
* update: fix to pwm_curl_get(). Some users were experiencing problems with the query built inside this function.
* update: permission check now relies on the user being logged on.

= 1.0.0 =
* Major update: Most of the plugin's core has been rewritten to use jQuery.
* new: it is now possible to make image selections from multiple pages from the same category.
* update: string "Loading..." added to pot file.

= 0.9.9 =
* update: compatibility with Piwigo 2.4.3
* update: updated JS code to make it work with IE8 (but needs more testing).
* update: "Thumbnail" and "Insert:" were missing in POT.
* update: better security checks in popup.php.
* new: French (fr_FR) translation (Dimitri Robert).

= 0.9.8 =
* new: option to insert full images.
* update: Deutsch (de_DE), Swedish (sv_SE) and Italian (it_IT) translations added (thanks to their authors!).

= 0.9.7 =
* update: Turkish (tr) and Hungarian (hu_HU) translations are up-to-date (thanks to original authors).

= 0.9.6 =
* new: support for multiple Piwigo sites.
* new: new text added. translations need update (hu_HU, tr).
* update: improved UI.
* update: POT file updated.

= 0.9.2 =
* new: Hungarian (hu_HU) translation added (thanks to samli).
* new: Portuguese (pt_PT) translation added (thanks to me ;-p).

= 0.9.1 =
* new: Turkish translation added (thanks to Nakre).
* update: POT file updated.
* update: removed "Web service script" configuration (ws.php is always used).

= 0.9 =
* new: i18n support added.
* new: POT template for translators.
* update: reorganization of tinymce/.
* update: unused files cleaned.
* update: user capabilities checks added to popup.

= 0.8 =
* update: UI updated (a little more compact)
* update: code cleanup
* new: initial error handling.
* new: Piwigo Webservice URL setting has been split into two fields: Piwigo URL + Web service script

= 0.7 =
* Better navigation, using breadcrumb for Categories and pages numbers for Images
* Small improvements to javascript
* Little cleaner code
* New name (PiwigoMedia) and new icon for TinyMCE UI

= 0.6 =
* Reasonable stable version. This is the first import into WordPress SVN.

== Upgrade Notice ==

