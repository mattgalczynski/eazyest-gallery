=== Eazyest Gallery ===
Contributors: macbrink
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=22A3Y8ZUGR6PE
Tags: media,photo,album,picture,lazyest,image,gallery,easy,exif,subfolders,widget,ftp,upload,schortcode,comment
Tested up to: 3.5.1
Requires at least: 3.5
Stable tag: 0.1.0-beta-5
License: GPLv3

Eazyest Gallery extends WordPress media featuring folders, subfolders, comments, slideshows, ftp-upload, and many more features.

== Description ==
Eazyest Gallery extends WordPress media by adding folders and subfolders.

Eazyest Gallery 0.1.0-beta is almost finished software, but not quite. 
Please use this version at your own risks, or for test purposes only. If you run into bugs, please add your findings to the [support forum](http://wordpress.org/support/plugin/eazyest-gallery).

Eazyest Gallery is the successor to [Lazyest Gallery](http://wordpress.org/extend/plugins/lazyest-gallery/)

= Eazyest Gallery features =
* __Fully integrated in Admin and Media management__ 
	
	The plugin stores your all your folder information in the WordPress database as custom post types. This will allow you to easily find, retrieve, edit, and show your folders. You can add post tags. The folders will display in tag archives. All images link to the folders as normal WordPress attachments. You can access all images in the WordPress media manager. You can even build WordPress default galleries from Eazyest Gallery images. The plugin uses the WordPress Image Editor and Media Manager. If uploading and re-sizing works in WordPress, it will work in Eazyest Gallery. The plugin includes templates for the WordPress default themes TwentyTen, TwentyEleven and TwentyTwelve. You may copy and adjust these templates to your (child) theme.

* __Unlimited number of images in unlimited number of nested folders__
	
	Just like WordPress pages, you can add child and parent folders. Eazyest Gallery builds a directory structure on your server to match your folder hierarchy. The WordPress Menu Editor shows all folders. You can easily add folders to your site's menu.

* __Comment on folders and images__
	
	Comments on folders and images are only limited by your discussion settings. If you allow visitors to comment on posts, they will be able to comment on folders and on individual images. You can switch commenting on and off per folder.

* __Widgets__
	
	You can show your images anywhere on your site by using the widgets.
	* You have a widget to list all your folders,
	* a widget to show randomly chosen images,
	* a widget to show your latest added folders,
	* a widget to show your latest added images,
	* a widget to show a continuously running slideshow of randomly chosen thumbnails.

* __Shortcodes__
	
	Eazyest Gallery adds three shortcodes to show your gallery in posts or pages.
	`[eazyest_gallery]` to show the gallery root page, `[eazyest_folder]` to show your folder contents and `[eazyest_slideshow]`		 to show images from a folder as slideshow.
	Don't worry if you update from Lazyest Gallery, Eazyest Gallery supports all `[lg_gallery]`, `[lg_folder]`, `[lg_slideshow]`, and `[lg_image]` shortcodes.

* __Automatic indexing of (ftp) uploaded folders and images__
	
	You don't have to use the WordPress media manager to add folders or to upload images. The plugin indexes folders and images as soon as you open the the Eazyest Gallery menu. It will sanitize folder names and image file names to create 'clean' permalinks for folders and images, and will use the unsanitized version as folder or image title. *Please be aware that WordPress should have write access to your FTP uploaded folders.*

* __Upgrade/Import tool for Lazyest Gallery__
	
	I won't develop new features for Lazyest Gallery, in favor of Eazyest Gallery. The plugin includes an updater to import all your Lazyest Gallery content and comments to the new custom post-type folder structure.

* __Many actions and filters to interact with Eazyest Gallery__
	
	The plugin offers theme and plugin builders a myriad of action hooks and filters to interact with the inner workings.

== Installation ==

1. Install eazyest-gallery using the WordPress plugin installer
2. Confirm your Gallery folder in Settings -> Eazyest Gallery

== Frequently Asked Questions ==

= My FTP uploaded folders do not show up in Eazyest Gallery =

Eazyest Gallery will index your new folders when you open the __All Folders__ menu in WordPress Admin. If they do not show, please check if WordPress (PHP) has [write permissions](http://codex.wordpress.org/Changing_File_Permissions) to your new folders.

= When I click an attachment picture, my full size image does not show in lightbox =

The attachment view behavior depends on the code in the attachment template. Eazyest Gallery searches for a template called `eazyest-image.php`. Please copy a template from a theme in `eazyest-gallery/themes` as an example to build a template for your theme.

== Screenshots ==

1. Eazyest Gallery menu, below WordPress Media menu
2. Manually sorting folders in the Gallery Admin screen
3. The gallery folder edit screen
4. Upload images with the WordPress Media uploader
5. A Gallery folder in Twenty Twelve with random image widget
6. Camera slideshow by [Manuel Masia](http://www.pixedelic.com/plugins/camera/) included

== Upgrade Notice ==

= 0.1.0-beta-5 =
* More bugs fixed (see changelog)  

== Changelog ==

= 0.1.0-beta-5 =
* Bug Fix: Error in auto-index message

= 0.1.0-beta-4 =
* Bug fix: Double set of thumbs in non-Twenty themes
* Bug Fix: Out-of-execution-time error in upgrade and auto-index
* Bug Fix: Gallery folder dropdown did noit show all folders from web root
* Bug Fix: Could not find resized images
* Changed: More information during upgrade/import/auto-index processes
* Changed: Auto-index script is stoppable
* Changed: Aspect ratio for slideshow
* Changed: Do not show -Insert from URL- in media view


= 0.1.0-beta-3 =
* Bug Fix: Zombie folders came back as published after they were permanently deleted
* Bug Fix: Thumbnails did not show if you selected 'medium' or 'large' for Thumbnail click
* Bug Fix: Camera slideshow did not work in non-Twenty themes
* Bug Fix: File tree dropdown did not unfold on some browsers
* Changed: No link in breadcrumb trail for trashed parent folders
* Changed: Admin Searching indicator is now on top of the folders list

= 0.1.0-beta-2 =
* Bug Fix: Sort order did not apply to manually sorted folders
* Bug Fix: Responsive display folder columns = 0
* Bug Fix: Incorrect import and sanitize of Lazyest gallery folders
* Added: Thumbnail navigation or AJAX "More thumbnails" for Folder display
* Changed: Maximum number of icons/thumbnails is now full rows times columns

= 0.1.0-beta-1 =
* Bug Fix: Split-up of imported folders with many images
* Added: About page
* Added: post_status 'hidden'
* Added: Include folders in post tag archives
* Added Sliddshow button for folders in frontend
* Added: Exif on attachment page
* Added: Support for header images from eazyest gallery images
* Changed: Lazyest Gallery cache slides/ thumbs/ will not be deleted
* Changed: Menu icons
* Changed: You cannot change root gallery folder after you have inserted a folder

= 0.1.0-alpha-5 =
* Changed: Image and subfolder list tables visible when empty
* Added: Widgets
* Added: Display Exif data on attachment page
* Added: Slideshow
* Added: Support for Header images from Eazyest Gallery images
* Changed: Use iptc/exif created timestamp for attachment post_date

= 0.1.0-alpha-4 =
* Changed: All resized images now stored in subdirectory _cache
* Changed: Folder path now saved in postmeta as key '_gallery_path' instead of 'gallery_path'

== Copyright ==

* Copyright (c) 2004        Nicholas Bruun Jespersen (lazy-gallery)
* Copyright (c) 2005 - 2006 Valerio Chiodino (lazyest-gallery)
* Copyright (c) 2008 - 2013 Marcel Brinkkemper (lazyest-gallery and eazyest-gallery)
* TableDnD plug-in for JQuery, Copyright (c) Denis Howlett
* JQuery File Tree, Copyright (c) 2008, A Beautiful Site, LLC
* Camera slideshow v1.3.3, Copyright (c) 2012 by Manuel Masia - www.pixedelic.com
* Jigsoar icons, Handcrafted by Benjamin Humphrey for Jigsoar - www.jigsoaricons.com

== License ==

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).