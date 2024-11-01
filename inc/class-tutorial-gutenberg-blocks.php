<?php

defined('ABSPATH') || die;
/**
 * @package Tutorial_Gutenberg_Blocks
 *
 */
class Tutorial_Gutenberg_Blocks {
    /**
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access private
     * @static @var boolean instance
     *
     */
    private static $instance = null;
    /**
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access private
     * @var array notice
     *
     */
    private $notice = [];
    /**
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access private
     * @var string plugin_version
     *
     */
    private $plugin_version;
    /**
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access private
     * @var const CODEMIRROR_VERSION
     *
     */
    const CODEMIRROR_VERSION = '5.40.2';
    /**
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access private
     * @var const GUTENBERG_REQUIRE_VERSION
     *
     */
    const GUTENBERG_REQUIRE_VERSION = '4.5.0';

    /**
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access public
     * @method __construct
     *
     */
    public function __construct()
    {
        add_action('init', array($this, 'init') );

        // add_action('init', array($this, 'gutenberg_block_init') );

        add_action('plugins_loaded', array($this, 'gutenberg_version_check') );

        // enqueue styles and scripts
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));

        // add custom block category
        add_action('block_categories', array($this, 'block_categories'), 10, 2);

        // filters block types
        // add_action('allowed_block_types', array($this, 'allowed_block_types'), 10, 2);
    }

    /**
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access public
     * @static @method instance
     *
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
        self::$instance = new self( );
        }
        return self::$instance;
    }
    /**
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access public
     * @method init
     *
     */
    public function init() {
        $plugin_data = get_file_data(TUTORIAL_GUTENBERG_BLOCKS_PLUGIN, array('Version' => 'Version'), false);
        $plugin_version = $plugin_data['Version'];
    }

    /**
     * Tutorial Gutenberg Bocks only works if Gutenberg is installed and activated.
     * or Wordpress version 5 haveing built in Gutenberg Editor.
     *
     * @since Tutorial Gutenberg Bocks 1.0.0
     * @access public
     * @method gutenberg_version_check
     *
     */
    public function gutenberg_version_check() {
        // if register_block_type is exist, it means site is run on Wordpress 5 with built-in Gutenberg, then just return, no further check is needed.
        if (function_exists('register_block_type')) {
            return;
        }
        else if (defined('GUTENBERG_VERSION')) {

            if (version_compare(GUTENBERG_VERSION, self::GUTENBERG_REQUIRE_VERSION, 'lt')) {

                $this->notice['message'] = sprintf(
                    __('Tutorial Gutenberg Bocks requires Gutenberg Editor Versiion  %s. Your running %s, Please Update Gutenberg!', 'tutorial-gutenberg-blocks'),
                    self::GUTENBERG_REQUIRE_VERSION,
                    GUTENBERG_VERSION
                );
                add_action( 'admin_notices', array($this, 'show_upgrade_notice') );
            }
        } else {
            $gutenbergInstallUrl = wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => 'install-plugin',
                        'plugin' => 'gutenberg'
                    ),
                    admin_url('update.php')
                ),
                'install-plugin_gutenberg'
            );

            $link = '<a href="'. esc_attr($gutenbergInstallUrl) .'">'. esc_html__('Install Gutenberg Now!', 'tutorial-gutenberg-blocks') .'</a>';
            $this->notice['message'] = sprintf(
                __('Tutorial Gutenberg Bocks requires Gutenberg Editor. Please Activate it or: %s', 'tutorial-gutenberg-blocks'),
                $link
            );

            add_action( 'admin_notices', array($this, 'show_upgrade_notice') );
        }
    }

    /**
     * Adds a notice for updating or Instal Gutenberg.
     *
     * Prints an update nag after an activating plugin
     * @access public
     * @since Tutorial Gutenberg Bocks 1.0.0
     *
     */
    public function show_upgrade_notice() {
        printf( '<div class="error"><p>%s</p></div>', $this->notice['message']);
    }
    /**
     * Enquque admin styles and scripts.
     *
     * @access public
     * @since Tutorial Gutenberg Bocks 1.0.0
     *
     */
    public function admin_enqueue_scripts() {

        wp_enqueue_script( 'codemirror', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/lib/codemirror.js', array(), self::CODEMIRROR_VERSION, true );

        wp_enqueue_style( 'codemirror', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/lib/codemirror.css', array(), self::CODEMIRROR_VERSION );

        wp_enqueue_script( 'codemirror-loademode', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/addon/mode/loadmode.js', array(), '1.0.0', true );

        wp_add_inline_script(
            'codemirror-loademode',
            'var GB_CODEMIRROR_URL = "'. plugins_url("/vendor/codemirror/", TUTORIAL_GUTENBERG_BLOCKS_PLUGIN).'";',
            'before'
        );

        // wp_enqueue_script( 'codemirror-meta', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/mode/meta.js', array(), '1.0.0', true );

        wp_enqueue_script(
            'tutorial-gutenberg-blocks-cditor-init', // Handle.
            plugins_url( '/assets/js/code-editor-init.js',  TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ), // Block.build.js: We register the block here. Built with Webpack.
            array( 'codemirror', 'codemirror-loademode' ),  // Dependencies, defined above.
            filemtime( plugin_dir_path( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'assets/js/code-editor-init.js' ), // Version: filemtime — Gets file modification time.
            true // Enqueue the script in the footer.
        );

        wp_enqueue_script(
            'tutorial-gutenberg-blocks-blocks', // Handle.
            plugins_url( '/assets/blocks/blocks.build.js',  TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ), // Block.build.js: We register the block here. Built with Webpack.
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components',  'wp-compose', 'underscore' ),  // Dependencies, defined above.
            filemtime( plugin_dir_path( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'assets/blocks/blocks.build.js' ), // Version: filemtime — Gets file modification time.
            true // Enqueue the script in the footer.
        );

        // Styles. only use for editor
        wp_enqueue_style(
            'tutorial-gutenberg-blocks-blocks-editor', // Handle.
            plugins_url( 'assets/blocks/blocks.editor.build.css',  TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ), // Block editor CSS.
            array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
            filemtime( plugin_dir_path( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'assets/blocks/blocks.editor.build.css' ) // Version: filemtime — Gets file modification time.
        );

        wp_enqueue_style(
            'tutorial-gutenberg-blocks-blocks', // Handle.
            plugins_url( 'assets/blocks/blocks.style.build.css',  TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ), // Block editor CSS.
            array(), // Dependency to include the CSS after it.
            filemtime( plugin_dir_path( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'assets/blocks/blocks.style.build.css' ) // Version: filemtime — Gets file modification time.
        );
    }
    /**
     * Enquque Frontend styles and scripts.
     *
     * @access public
     * @since Tutorial Gutenberg Bocks 1.0.0
     *
     */
    public function wp_enqueue_scripts() {

        wp_enqueue_script( 'codemirror', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/lib/codemirror.js', array(), self::CODEMIRROR_VERSION, true );

        wp_enqueue_style( 'codemirror', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/lib/codemirror.css', array(), self::CODEMIRROR_VERSION );

        wp_enqueue_script( 'codemirror-runmode', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/addon/runmode/runmode.js', array(), '1.0', true );

        wp_enqueue_script( 'codemirror-loademode', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/addon/mode/loadmode.js', array(), '1.0.0', true );

        wp_add_inline_script(
            'codemirror-loademode',
            'var GB_CODEMIRROR_URL = "'. plugins_url("/vendor/codemirror/", TUTORIAL_GUTENBERG_BLOCKS_PLUGIN).'";',
            'before'
        );

        // wp_enqueue_script( 'codemirror-meta', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'vendor/codemirror/mode/meta.js', array(), '1.0.0', true );

        wp_enqueue_script(
            'tutorial-gutenberg-blocks-editor-init', // Handle.
            plugins_url( '/assets/js/code-editor-init.js',  TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ), // Block.build.js: We register the block here. Built with Webpack.
            array( 'codemirror', 'codemirror-loademode' ),  // Dependencies, defined above.
            filemtime( plugin_dir_path( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'assets/js/code-editor-init.js' ), // Version: filemtime — Gets file modification time.
            true // Enqueue the script in the footer.
        );

        wp_add_inline_script(
            'tutorial-gutenberg-blocks-editor-init',
            'wpgbcm.frontEndInitialization();',
            'after'
        );

        // wp_enqueue_script( 'codemirror-custom', plugin_dir_url( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'assets/js/custom.js', array('codemirror-runmode'), '1.0.0', true);


        // Styles. only use for frontend
        wp_enqueue_style(
            'tutorial-gutenberg-blocks-blocks', // Handle.
            plugins_url( 'assets/blocks/blocks.style.build.css',  TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ), // Block editor CSS.
            array(), // Dependency to include the CSS after it.
            filemtime( plugin_dir_path( TUTORIAL_GUTENBERG_BLOCKS_PLUGIN ) . 'assets/blocks/blocks.style.build.css' ) // Version: filemtime — Gets file modification time.
        );
    }

    /**
     * Add custom block category
     * @since Tutorial Gutenberg Bocks 1.0.0
     *
     * @param array $categories Gutenberg Block Categories.
     * @param object $post.
     * @return null
     */
    public function block_categories( $categories, $post ) {
        if ( $post->post_type !== 'post' ) {
            return $categories;
        }
        return array_merge(
            $categories,
            array(
                array(
                    'slug'  => 'tutorial-blocks',
                    'title' => __( 'Tutorial Blocks', 'tutorial-gutenberg-blocks' ),
                    // 'icon'  => 'wordpress'
                ),
            )
        );
    }
    /*
    public function allowed_block_types( $allowed_block_types, $post ) {
        global $registered_block_types;
        if ( $post->post_type !== 'post' ) {
            return $allowed_block_types;
        }
        return array( 'core/paragraph' );
    }

    public function gutenberg_block_init() {
        register_meta( 'post', 'author', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ) );
    }
    */
}
