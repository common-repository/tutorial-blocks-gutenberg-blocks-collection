<?php
/**
 * Plugin Name: Tutorial Blocks Collection
 * Plugin URI: https://wordpress.org/plugins/tutorial-blocks-gutenberg-blocks-collection/
 * Description: This Plug-in is add on on Gutenberg. it provides usefull blocks for developers blog, such as Code Bocks, Output Blocks, Note
 * Block, Definition Blocks, Paragrapf Blocks, etc.
 * Version: 1.0.0
 * Author: Vicky Agravat
 * Author URI: https://profiles.wordpress.org/vickyagravat
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: tutorial-gutenberg
 * @package TUTORIAL GUTENBERG
 */

defined('ABSPATH') || die;

if (! defined('TUTORIAL_GUTENBERG_BLOCKS_PLUGIN')) {
    define('TUTORIAL_GUTENBERG_BLOCKS_PLUGIN', __FILE__);
}
include( 'inc/class-tutorial-gutenberg-blocks.php' );
Tutorial_Gutenberg_Blocks::instance();
