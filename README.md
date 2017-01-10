=== Code From Url ===

Contributors: mattc78, Jared Barneck (Rhyous), SnifferNandez
Tags: github, snippet, code
Requires at least: 2.6
Tested up to: 4.7

Code from URL automatically pulls a source file from a URL and displays it in a blog post using the SyntaxHighlighter Wordpress plugin.

== Description ==

GitHub Code Viewer automatically pulls a file from and put in a [code] wordpress' shortcode. To use you can put in your post something like 
[CodeFromUrl="https://raw.githubusercontent.com/SnifferNandez/GitHub-Code-Viewer-for-WordPress/master/github.php" lang="php" opt="highlight='71'"]
It have a bug (to fix) that I expose in http://sniffer.comparte.tips/codigo-de-github-a-wordpress/

== Installation ==

1. Install and activate the https://wordpress.org/plugins/syntaxhighlighter/ plugin
2. Upload `github.php` to the `/wp-content/plugins/` directory (or create a zip file and upload vía wp dashboard)
3. Activate the plugin through the 'Plugins' menu in WordPress
4. To display a file in your blog take the link from GitHub and using a URL, a lang and a opt variable (strict order):

[CodeFromUrl="https://raw.githubusercontent.com/SnifferNandez/GitHub-Code-Viewer-for-WordPress/master/github.php" lang="php" opt=" highlight='1-3,6,9' padlinenumbers='false' toolbar='true' title='GitHub Code viewer for Wordpress'"]
