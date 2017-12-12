# mkinsight-theme

**mkinsight-theme** is a [WordPress](https://wordpress.org) theme developed for [MK:Insight](http://mkinsight.org) project


![MK:Insight home page](screenshot.png)

## How to use it

As any other themes for WordPress, mkinsiht-theme can be used by uploading it 
into ``wp-contents/themes`` directory, enabling it from WordPress admin backend.

For further information please refer to [WordPress guide about themes](https://codex.wordpress.org/Using_Themes)


### WordPress dependencies

Currently MK:Insight is running under WordPress version 4.7.8

Following the list of required plugins:

- [amr shortcode any widget](https://wordpress.org/plugins/amr-shortcode-any-widget/)
- [Bop Search Box Item Type For Nav Menus](https://wordpress.org/plugins/bop-search-box-item-type-for-nav-menus/)
- [Co-Authors Plus](https://wordpress.org/plugins/co-authors-plus/)
- [*Contact Form DB](http://wordpress.org/extend/plugins/contact-form-7-to-database-extension/)
- [UK Cookie Consent](https://wordpress.org/plugins/uk-cookie-consent/)
- [Disable Comments](https://wordpress.org/plugins/disable-comments/)
- [*Disable Real MIME Check](https://wordpress.org/plugins/disable-real-mime-check/)
- [Idea Factory](https://wordpress.org/plugins/idea-factory/)
- [iframe](https://wordpress.org/plugins/iframe/)
- [List category posts](https://wordpress.org/plugins/list-category-posts/)
- [Ultimate Social Media PLUS](https://wordpress.org/plugins/ultimate-social-media-plus/)
- [Very Simple Contact Form](https://wordpress.org/plugins/very-simple-contact-form/)
- [WP Attachments](https://wordpress.org/plugins/wp-attachments/)
- [WP Statistics](https://wordpress.org/plugins/wp-statistics/)

*Deprecated / No longer available plugins.

## Development

1. Download [WordPress downloads](https://wordpress.org/downloads)
2. Copy / Clone ``mkinstight-theme within`` within ``./wp-content/themes`` folder

For further information about the general mechanics of themes in WordPress, please refer to the official [theme handbook](https://developer.wordpress.org/themes/getting-started/)

## Dependencies

- [mkio2](https://github.com/mdaquin/mkio2) data visualisation and data exploration library for [MK:Datahub](https://datahub.mksmart.org)
- [Bootstrap](https://getbootstrap.com)
- [JQuery mobile](https://jquerymobile.com)
- [JQuery easing](http://gsgd.co.uk/sandbox/jquery/easing/)
- [modernizr](https://modernizr.com)
- [inline tweet](https://ireade.github.io/inlinetweetjs/)

## Theme Structure
```
|-- mkinsight-theme
    |-- 404.php
    |-- archive.php
    |-- attachment.php
    |-- author.php
    |-- category-default.php
    |-- category.php
    |-- chart-generator-template.php
    |-- comments.php
    |-- entry-content.php
    |-- entry-footer.php
    |-- entry-meta.php
    |-- entry-summary.php
    |-- entry.php
    |-- footer.php
    |-- functions.php
    |-- header.php
    |-- index.php
    |-- nav-below-single.php
    |-- nav-below.php
    |-- page.php
    |-- readme.txt
    |-- resources-page-template.php
    |-- screenshot.png
    |-- search.php
    |-- sidebar.php
    |-- single.php
    |-- style.css
    |-- tag.php
    |-- assets
    |   |-- css
    |   |   |-- bootstrap.css
    |   |   |-- hoverex-all.css
    |   |   |-- ionicons.min.css
    |   |   |-- style.css
    |   |-- fonts
    |   |-- img
    |   |-- includes
    |   |   |-- get-tweets.php
    |   |   |-- shortcode-wpautop-control.php
    |   |   |-- twitter.php
    |   |   |-- twitteroauth
    |   |       |-- OAuth.php
    |   |       |-- twitteroauth.php
    |   |-- js
    |       |-- bootstrap.min.js
    |       |-- ie10-viewport-bug-workaround.js
    |       |-- inline-tweet.min.js
    |       |-- jquery.easing.min.js
    |       |-- jquery.mobile.custom.min.js
    |       |-- modernizr-2.8.3.min.js
    |-- mkio2
```