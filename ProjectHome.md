# Description #
Eazyest Gallery extends WordPress Media by adding folders and subfolders.

Eazyest Gallery is the successor to [Lazyest Gallery](http://wordpress.org/extend/plugins/lazyest-gallery/). Lazyest Gallery users please read [how to upgrade to Eazyest Gallery](http://brimosoft.nl/2013/02/27/how-to-move-from-lazyest-gallery-to-eazyest-gallery/)

## Eazyest Gallery features ##
  1. **Fully integrated in Admin and Media management**: The plugin stores your all your folder information in the WordPress database as custom post types. This will allow you to easily find, retrieve, edit, and show your folders. You can add post tags. The folders will display in tag archives. All images link to the folders as normal WordPress attachments. You can access all images in the WordPress media manager. You can even build WordPress default galleries from Eazyest Gallery images. The plugin uses the WordPress Image Editor and Media Manager. If uploading and re-sizing works in WordPress, it will work in Eazyest Gallery. The plugin includes templates for the WordPress default themes TwentyTen, TwentyEleven and TwentyTwelve. You may copy and adjust these templates to your (child) theme.
  1. **Unlimited number of images in unlimited number of nested folders**: Just like WordPress pages, you can add child and parent folders. Eazyest Gallery builds a directory structure on your server to match your folder hierarchy. The WordPress Menu Editor shows all folders. You can easily add folders to your site's menu.
  1. **Comment on folders and images**: Comments on folders and images are only limited by your discussion settings. If you allow visitors to comment on posts, they will be able to comment on folders and on individual images. You can switch commenting on and off per folder.
  1. **Widgets**: You can show your images anywhere on your site by using the widgets.
    * You have a widget to list all your folders,
    * a widget to show randomly chosen images,
    * a widget to show your latest added folders,
    * a widget to show your latest added images,
    * a widget to show a continuously running slideshow of randomly chosen thumbnails.
  1. **Shortcodes**: Eazyest Gallery adds three shortcodes to show your gallery in posts or pages. `[eazyest_gallery]` to show the gallery root page, `[eazyest_folder]` to show your folder contents and `[eazyest_slideshow]` to show images from a folder as slideshow. Don't worry if you update from Lazyest Gallery, Eazyest Gallery supports all `[lg_gallery]`,  `[lg_folder]`, `[lg_slideshow]`, and `[lg_image]` shortcodes.
  1. **Automatic indexing of (ftp) uploaded folders and images**: You don't have to use the WordPress media manager to add folders or to upload images. The plugin indexes folders and images as soon as you open the the Eazyest Gallery menu. It will sanitize folder names and image file names to create 'clean' permalinks for folders and images, and will use the unsanitized version as folder or image title. **Please be aware that WordPress should have write access to your FTP uploaded folders**.
  1. **Upgrade/Import tool for Lazyest Gallery**: I won't develop new features for Lazyest Gallery, in favor of Eazyest Gallery. The plugin includes an updater to import all your Lazyest Gallery content and comments to the new custom post-type folder structure.
  1. **Many actions and filters to interact with Eazyest Gallery**: The plugin offers theme and plugin builders a myriad of action hooks and filters to interact with the inner workings.

## Installation ##

  1. Install eazyest-gallery using the WordPress plugin installer
  1. Confirm your Gallery folder in Settings -> Eazyest Gallery

## Frequently Asked Questions ##

### My FTP uploaded folders do not show up in Eazyest Gallery ###

Eazyest Gallery will index your new folders when you open the **All Folders** menu in WordPress Admin. If they do not show, please check if WordPress (PHP) has [write permissions](http://codex.wordpress.org/Changing_File_Permissions) to your new folders.

### When I click an attachment picture, my full size image does not show in lightbox ###

The attachment view behavior depends on the code in the attachment template. Eazyest Gallery searches for a template called `eazyest-image.php`. Please copy a template from a theme in `eazyest-gallery/themes` as an example to build a template for your theme.

## Copyright ##

  * Copyright (c) 2004        Nicholas Bruun Jespersen (lazy-gallery)
  * Copyright (c) 2005 - 2006 Valerio Chiodino (lazyest-gallery)
  * Copyright (c) 2008 - 2013 Marcel Brinkkemper (lazyest-gallery and eazyest-gallery)
  * TableDnD plug-in for JQuery, Copyright (c) Denis Howlett
  * JQuery File Tree, Copyright (c) 2008, A Beautiful Site, LLC
  * Camera slideshow v1.3.3, Copyright (c) 2012 by Manuel Masia - www.pixedelic.com
  * Jigsoar icons, Handcrafted by Benjamin Humphrey for Jigsoar - www.jigsoaricons.com
