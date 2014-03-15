=== Fields Framework ===
Contributors: naifamoodi
Donate link: http://www.rhyzz.com/donate.html
Tags: fields-framework, field-framework, custom-fields, custom-field, fields, field, advanced-custom-fields, magic-fields, more-fields, repeater, meta-box, metabox, cck, user-meta, repeater, repeater-fields, admin-fields, group-fields, field-groups, taxonomy-fields, taxonomy-field, widgets-fields, widget-fields, admin-menu-fields, post-fields, page-fields, custom-post-fields, custom-post-type-fields, category-fields, tag-fields, custom-taxonomy-fields, user-fields, profile-fields, field-sets, field-set, field-section, field-sections
Requires at least: 3.5
Tested up to: 3.8
Stable tag: 0.14.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A framework which can be used by developers to add fields to various areas of the administration panel either manually or using the Visual Builder.

== Description ==

*Update*: ***Now featuring a visual builder*** to help developers create sections and fields with ease!

Please show your support for this plugin by giving it [a rating](http://wordpress.org/support/view/plugin-reviews/fields-framework?rate=5#postform)!

[WordPress Fields Framework Documentation](http://www.rhyzz.com/fields-framework.html "WordPress Fields Framework Documentation")

This plugin can be used to add fields to:

* Custom Administration Menus and Sub Menus
* Posts, Pages, Attachments and [Custom Post Types](http://codex.wordpress.org/Post_Types)
* Categories, Tags and [Custom Taxonomies](http://codex.wordpress.org/Taxonomies)
* Custom [Widgets](http://codex.wordpress.org/WordPress_Widgets)
* User Profiles

Sections for Posts and Pages can also be displayed conditionally depending on whether a Page uses a certain [Page Template](http://codex.wordpress.org/Page_Templates) or whether a Post uses a certain [Post Format](http://codex.wordpress.org/Post_Formats)

* The plugin supports client side validation using JavaScript
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
* Select_Users - A drop down which can contain users registered on the site
* Editor - A WYSIWYG editor. This is the same one that's used on the post edit screen by default for editing the content of the post
* DateTime
* ColorPicker

All fields except the Editor field can be made repeatable.

= Additional Field Requests =

If you feel the need for an additional field type then please use the [support forum](http://wordpress.org/support/plugin/fields-framework) and leave your suggestion. This will be looked into ASAP.

== Installation ==

1. Upload the folder `fields-framework` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create fields using the functions provided and place them inside your theme's functions.php file or inside a plugin.

== Frequently Asked Questions ==

= Where is the documentation for this plugin located? =

[WordPress Fields Framework Documentation](http://www.rhyzz.com/fields-framework.html "WordPress Fields Framework Documentation")

== Screenshots ==

1. An Administration Menu
2. An Administration Sub Menu
3. Repeatable Field Groups and Nested Field Groups
4. A section assigned to a page with a page template named 'Showcase Page'
5. A Repeatable Field Group with two fields
6. A dump of values from a particular section
7. A custom post type named 'Slide' with a section associated to it
8. A custom post type named 'Service' with a section associated to it
9. A custom post type named 'Member' with a section associated to it
10. A custom post type named 'Testimonial' with a section associated to it
11. An Administration Sub Menu with a bunch of fields
12. A section assigned to posts using the audio post format
13. A section assigned to posts using the video post format
14. A section assigned to posts using the gallery post format
15. A section assigned to posts using the image post format
16. A section assigned to a page with a page template named 'About Page'
17. Two individual fields
18. A section assigned to a page with a page template named 'Contact Page'
19. DateTime field
20. ColorPicker field
21. A demo of all fields
22. An example of a Fields Widget with a Section selection drop down
23. A fields widget rendering fields of section associated to it.
24. Front end dump of a widget section
25. Builder - Sections
26. Builder - Fields
27. Builder - Fields By Sections

== Changelog ==

= 0.14.9 =
* Fixed bug related to fields that can accept multiple values

= 0.14.8 =
* Fixed bug where repeating doesn't work for minimal fields

= 0.14.7 =
* Fixed bug pertaining to enqueueing of administration scripts

= 0.14.6 =
* Fixed an undefined variable warning message related to the Editor field

= 0.14.5 =
* Fixed minor bug related to the builder

= 0.14.4 =
* Fixed another issue with builder related to saving of existing fields and sections

= 0.14.3 =
* Added counter for repeatable fields
* Fixed issue with builder pertaining to new fields and sections not saving

= 0.14.2 =
* File repeatable-fields.js missing from JS folder

= 0.14.1 =
* Fixed Builder UID validation bug

= 0.14 =
* Add new import and export feature for builder data
* Fixed bug related to editor field's name attribute
* Fixed bug related to multi-level group field's name attributes

= 0.13.1 =
* Minor bug fix

= 0.13 =
* Refactoring of code
* Duplicate uid no longer checked for sections & fields that skip registry
* Added new page_templates_not & post_formats_not boolean properties which if set to true will inverse the check
* Added 'Standard' to Post Formats list

= 0.12.2 =
* Bug fixes

= 0.12.1 =
* Fixed a JS ReferenceError

= 0.12 =
* Added Visual Builder

= 0.11.2 =
* Added support for client side validation using JavaScript

= 0.10.2 =
* Added new Widgets section which let's you add fields to custom Field Framework Widgets
* Passing -1 to DateTime's date_format or time_format arguments will disable display of Date or Time respectively.
* Manually assign post ID to media uploader if and only if a given post type doesn't have thumbnail support enabled
* Minor bug fixes

= 0.9.0.1 =
* Added a new Users field
* Fixed two issues related to the media uploader
* Further improvements to default values that are returned for the fields
* Added skip_save argument which if set to true and passed to sections, will skip saving of those sections
* DateTime and ColorPicker fields now appears on focus instead of click. They are not called on click because when delegated, they would need to be clicked twice to appear.
* Replaced ColorPicker widget to more advanced one
* Added new boolean section argument named hide_content_editor for Posts sections which if set to true will hide the content editor for those sections

= 0.8.2 =
* Minor bug fixes related to delegation of events
* Fixed an orphaned HTML right angle bracket
* Screenshots updated

= 0.8.1 =
* Field names no longer need to be the same as the field's unique id. Instead, they can be set manually by passing a variable with key 'name' to the arguments array.
* Registered sections will now be rendered even if they don't have any fields associated to them.
* Added actions ff_section_before and ff_section_after which get called respestively before and after a section is rendered. section_uid is passed as an argument to the Action.
* Added actions ff_field_before and ff_field_after which get called respestively before and after a field is rendered. field_uid is passed as an argument to the Action.
* Added new boolean field argument named minimal which if set renders the field using minimal HTML. Specially useful for groups which you would like to use as containers

= 0.7.1 =
* Minor bug fix

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

= 0.12.2 =
* Now requires WP 3.5 or greater

= 0.12 =
* Prepend Blank now set to true for all Fields of type Select and it's related types viz. Select_Posts, Select_Terms, and Select_Users

= 0.11.2 =
* Editor field setting variables will now need to be passed to a settings array instead of directly to the arguments array. So for example, the following:

'arguments' => array(
	'wpautop' => false
)

should now be:

'arguments' => array(
	'settings' => array(
		'wpautop' => false
	)
)