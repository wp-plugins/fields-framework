=== Fields Framework ===
Contributors: naifamoodi
Donate link: http://www.rhyzz.com/donate.html
Tags: fields-framework, field-framework, custom-fields, custom-field, fields, field
Requires at least: 3.3
Tested up to: 3.5.2
Stable tag: 0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A framework which can be used by developers to add fields to various sections of the administration panel.

== Description ==

[WordPress Fields Framework Documentation](http://www.rhyzz.com/fields-framework.html "WordPress Fields Framework Documentation")

A framework which can be used by developers to add fields to various sections of the administration panel.

This includes the ability of adding fields to:

* Posts, Pages, Attachments and [Custom Post Types](http://codex.wordpress.org/Post_Types)
* Categories, Tags and [Custom Taxonomies](http://codex.wordpress.org/Taxonomies)
* Custom Administration Menus and Sub Menus
* User Profiles

Sections for Posts and Pages can also be loaded conditionally depending on whether a Page uses a certain [Page Template](http://codex.wordpress.org/Page_Templates) or if a Post uses a certain [Post Format](http://codex.wordpress.org/Post_Formats)

= Fields Supported =

Currently the following types of fields are supported:

* **Group** - A group of fields
* Text Field
* Hidden Field
* Media Field - Let's you upload an attachment or enter a custom URL. You can use this field for uploading of any type of file
* Textarea
* Checkbox
* Radio
* Select
* Select_Posts - A select box which can contain posts of type post, page, attachments or any other custom post type
* Select_Terms - A select box which can contain terms from taxonomies like tags, categories or some custom taxonomy
* Editor - A WYSIWYG editor
* DateTime
* ColorPicker

Most fields including the Group field can be made repeatable.

= Support =

Please show your support towards the development of this plugin by giving it a rating!

And if you feel the need for an additional type of field then please use the [support form](http://wordpress.org/support/plugin/fields-framework) and leave a message.

== Installation ==

1. Upload the folder `fields-framework` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create fields using the functions provided and place them inside your theme's functions.php file or inside your plugin.

== Frequently Asked Questions ==

= Where is the documentation for this plugin located? =

[WordPress Fields Framework Documentation](http://www.rhyzz.com/fields-framework.html "WordPress Fields Framework Documentation")

== Screenshots ==

1. Administration Menu
2. Administration Sub Menu
3. Posts Page
4. Category Page
5. User Profile Page

== Changelog ==

= 0.6 =
* Added ColorPicker Field
* Removed URL and Email fields because a regular Text Field seems to serve just fine for any such field

= 0.51 =
* Added new DateTime field
* Fixed few bugs pertaining to a variable not set warning
* Added new Select Field for displaying a list of Posts or Terms

= 0.4 =
* Added support for displaying of fields conditionally depending on post format or page template
* Error are now thrown as Exceptions instead of using trigger_error. This will help developers pin out the exact location which caused an error

= 0.3 =
* Added new Radio field

= 0.2 =
* Added 3 functions to be used on the frontend to retrieve a particular field or all fields from a certain section. Refer to the documentation for more information
* Took care of some refactoring of code

= 0.1 =
* Initial release

== Upgrade Notice ==

None