=== DW Question Answer - Embed Question ===
Contributors: designwall
Tags: question, answer, embed question
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: 1.0.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Your WordPress site still has a full-featured Question & Answer section like Stack Overflow, Quora or Yahoo Answers. DWQA Embed 
Question plugin will be auto-integrated with the DW Question & Answer plugin right after installing successfully.
With one click , the DWQA Embed Question lets you share a favourite question via Social channels more quickly than ever.
Plus, by pasting directly the question link, using the iFrame style of the question or even shortcode, you can easily embed the question from any Question site using the DW Question & Answer plugin into the other sites.

== Description ==

DWQA Embed Question is a free, simple and small WordPress Plugin (add-on) for the DW Question & Answer plugin. It allows
users to share any great question on Social media channels such as Faceboook, Google Plus, Twitter, Linkedin, Tumblr. 
You can also embed any interesting question from DW Question & Answer site into your blog post, page content and even the Text widget. 

Feature
* Share a question on the Social Media sites.
* Embed a question from DW Question & Answer site into your blog post, page.
* Share your question post through embed code

== Installation ==

1. Upload `dwqa-embed-question` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Paste your question link inside your blog's post.

*Note:To be compatible with DW Question & Answer 1.4+, open file in this path : dw-question-answer/templates/content-single-question.php
 >> Line 42 ( before : <?php comments_template(); ?> ) add this code
 >> <?php do_action( 'dwqa-question-content-footer' ); ?>

== Screenshots ==

1. Embed Question inside Blog's Posts
2. Embed Question inside Question/Answer content
3. Get Embed Code
4. Embed question text widget

== Changelog ==
= 1.0.2 =
* Fix: Style compatible with DW Question & Answer 1.4+



= 1.0.1 =
* Fix: Compatible with DW Question & Answer 1.3.8

= 1.0.0 =
* Initial Release


