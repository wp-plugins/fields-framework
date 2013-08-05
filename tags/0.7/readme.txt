=== Fields Framework ===
Contributors: naifamoodi
Donate link: http://www.rhyzz.com/donate.html
Tags: fields-framework, field-framework, custom-fields, custom-field, fields, field
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A framework which can be used by developers to add fields to various areas of the administration panel.

== Description ==

[WordPress Fields Framework Documentation](http://www.rhyzz.com/fields-framework.html "WordPress Fields Framework Documentation")

A framework which can be used by developers to add fields to various areas of the administration panel.

This includes the ability of adding fields to:

* Posts, Pages, Attachments and [Custom Post Types](http://codex.wordpress.org/Post_Types)
* Categories, Tags and [Custom Taxonomies](http://codex.wordpress.org/Taxonomies)
* Custom Administration Menus and Sub Menus
* User Profiles

Sections for Posts and Pages can also be displayed conditionally depending on whether a Page uses a certain [Page Template](http://codex.wordpress.org/Page_Templates) or whether a Post uses a certain [Post Format](http://codex.wordpress.org/Post_Formats)

* The plugin can also be placed inside a theme. This is useful if you would like to bundle this plugin with a theme

= Fields Supported =

Currently the following types of fields are supported:

* **Group** - A group of fields. You can also create a *group within a group*, i.e. a nested group! Groups can be nested to an infinite level, at least in theory.
* Text Field
* Hidden Field
* Media Field - Let's you upload a file or enter a custom URL pointing to a file. You can use this field for uploading any type of file to the Media library.
* Textarea
* Checkbox - You can set whether a checkbox can accept multiple values or not
* Radio
* Select - A drop down from which a single or multiple items can be selected depending on whether you have allowed selection of multiple values
* Select_Posts - A drop down which can contain items belonging to any post type viz. Posts, Page, Attachments or of any other Custom Post Type
* Select_Terms - A drop down which can contain terms from any taxonomy which includes Tags, Categories or any other Custom Taxonomy
* Editor - A WYSIWYG editor. This is the same one that's used on the post edit screen by default for editing the content of the post
* DateTime
* ColorPicker

Most fields including the Group field can be made repeatable.

= Support =

If you feel the need for an additional field type then please use the [support forum](http://wordpress.org/support/plugin/fields-framework) and leave your suggestion. This will be looked into ASAP.

Also please show your support for this plugin by giving it a rating!

== Installation ==

1. Upload the folder `fields-framework` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create fields using the functions provided and place them inside your theme's functions.php file or inside a plugin.

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

= 0.7 =

* Improved default values that are returned for the fields.
* Refactored various parts of the plugin
* Updated online documentation

= 0.6.1 =
* Fixed minor bugs

= 0.6 =
* Added a new ColorPicker Field
* Removed URL and Email fields because a regular Text Field seems to serve well for these fields

= 0.51 =
* Added a new DateTime field
* Fixed variable not set warnings
* Added a new Drop Down Field which displays a list of posts or terms

= 0.4 =
* Added the ability of displaying fields conditionally depending on their post format(s) or page template(s)
* Error are now thrown as Exceptions. This will help developers pinpoint the exact location which generated the error

= 0.3 =
* Added a new Radio field

= 0.2 =
* Added three functions that can be used on the frontend to retrieve a particular field or all fields from a certain section. Refer to the documentation for more information on this.
* Refactored parts of the plugin

= 0.1 =
* Initial release

== Upgrade Notice ==

None