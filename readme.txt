=== Plugin Name ===
Contributors: SimonaIlie
Donate link: 
Tags: comments extra fields, change comment form
Requires at least: 3.2.1
Tested up to: 3.3.1
Stable tag: 1.7
 
Add extra fields to default Wordpress comment form

== Description ==

After activation the plugin adds a new option in Admin Settings called Comment Extra Fields. The user can select what kind of fields to add to comments form: they can choose between seven predefined field types: text, textarea, radio buttons, checkboxes, file (upload on front implemented with swfupload), drop down and a custom type called db select. For this type the user can write the name of a table from database, the name of a column for id and one name of a column for value (e.g. populate a drop down with Country names from database).
Also for a field the user must (are required fields for first version of the plugin) fill in the label, the id and order of the field. On admin side the type of fields are listed and the user can Edit / Delete each of them.
On front side the fields are added to not logged in user comment form (made some css to integrate it with twentyeleven theme style, but this is not the focus on this first version of this plugin).
After user fills in the comment the extra fields can be seen in admin area in Comments section -> Edit.
When deleting a comment, the extra fields values for the comment are also deleted.
When deleting an extra field the already filled in values are kept, so that they can be used when a field with the same id is defined.

To use the users input for extra fields in your theme there are two functions:
 
cef_get_all_extra_field_values($comment_id);

cef_get_extra_field_value($comment_id, $field_id);


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `comment-extra-fields.zip` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In admin area a new option appears under Settings: Comment Extra Fields
1. In Comment Extra Fields section you can manage (add/edit/delete) your specific extra fields which will be used on comment form on front side.


== Frequently Asked Questions ==


== Screenshots ==

1. List of already defined extra fields

2. Add/edit extra field form with tooltip

3. Db-select field

== Changelog ==

= 1.0 =
Basic features to start workin with comments extra fields

= 1.1 =

* Fixed edit bug

* Added icons for tooltips

* Added HTML Code field with more hints to use for more flexible comments form format

* Added function to get user input values for extra fields

= 1.2 =
 
* test-tag created mostly for SVN wordpress use

= 1.3 =

* fix base plugin path bug

= 1.4 =

* fix "unexpected output" warning during activation

* added bulk fields deletion

* added option to show field on logged in user form, guest or both

= 1.5 =

* replaced tooltips javascript with a less intrusive mechanism

* added new field Where to show comment fields on different custom post types

* fixed some minor bugs as reported in support forum

= 1.6 =

* added new field Who to show comment fields for all users or only guests or only logged in users.

= 1.7 =

* for fields defined with custom HTML code show in Admin comment edit page the value filled in by users on front site.

== Upgrade Notice ==