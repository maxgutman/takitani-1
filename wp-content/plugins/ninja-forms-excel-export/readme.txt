=== Ninja Forms - Spreadsheet Export ===
Contributors: haet
Tags: ninjaforms, excel, form, export, spreadsheet
Requires at least: 3.3
Tested up to: 4.2.4
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Export selected fields from your Ninja Forms entries to Excel spreadsheet file format.

== Description ==

= Advanced data export to Excel Open XML (XLSX) spreadsheet file format =

Exporting form submissions with multiline fields normally ends up with merged columns and manual corrections. Another possible problems are visitors typing your csv seperator into one of your input fields, causing a column shift in your CSV file.

Ninja Forms Spreadsheet Export has two features to bypass these problems


  1. export your data to the reliable Excel Open XML (XLSX) spreadsheet file format 
  2. choose the fields you want to appear in your exported Excel file.

== Installation ==
1. Upload folder `ninja-forms-spreadsheet` to the `/wp-content/plugins/` directory

    or upload .zip file in the \'Plugins\' menu 

    or search in Plugin repository (\'Plugins\'->\'Add New\')
2. Activate the plugin through the \'Plugins\' menu in WordPress

== Changelog ==

= 1.3 (21 September 2015) =

* Fixed compatibility with Multipart forms



= 1.2 (1 September 2015) =

* Preserve leading zero for numbers entered as text
* Added ID column


= 1.1 (10 August 2015) =

* Added batch processing to export thousands of submissions. Tested up to 25000 entries on a shared hosting with limited memory and limited execution time.


= 1.0.1 (8 July 2015) =

* Fixed a bug with empty fields


= 1.0 (7 July 2015) =

* Initial Release