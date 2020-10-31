<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class TwentyTwenty extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ));
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ));
		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = new Timber\Menu();
		$context['site']  = $this;
		$context['enable_header_search'] = get_theme_mod( 'enable_header_search', true );

		if ( has_nav_menu( 'mobile' ) ) {
			$mobile_menu_location = 'mobile';
		} elseif ( has_nav_menu( 'primary' ) ) {
			$mobile_menu_location = 'primary';
		} elseif ( has_nav_menu( 'expanded' ) ) {
			$mobile_menu_location = 'expanded';
		}

		$context['mobile_menu_location'] = $mobile_menu_location;

		$context['has_footer_menu'] = has_nav_menu( 'footer' );
		$context['has_social_menu'] = has_nav_menu( 'social' );

		$context['has_sidebar_1'] = is_active_sidebar( 'sidebar-1' );
		if ($context['has_sidebar_1']) {
			$context['sidebar_1'] = Timber::get_widgets('sidebar-1');
		}

		$context['has_sidebar_2'] = is_active_sidebar( 'sidebar-2' );
		if ($context['has_sidebar_2']) {
			$context['sidebar_2'] = Timber::get_widgets('sidebar-2');
		}

		$footer_top_classes = '';

		$footer_top_classes .= $has_footer_menu ? ' has-footer-menu' : '';
		$footer_top_classes .= $has_social_menu ? ' has-social-menu' : '';

		$context['footer_top_classes'] = $footer_top_classes;


		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Custom background color.
		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'f5efe0',
			)
		);

		// Set content-width.
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 580;
		}

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// Set post thumbnail size.
		set_post_thumbnail_size( 1200, 9999 );

		// Add custom image size used in Cover Template.
		add_image_size( 'twentytwenty-fullscreen', 1980, 9999 );

		// Custom logo.
		$logo_width  = 120;
		$logo_height = 90;

		// If the retina setting is active, double the recommended width and height.
		if ( get_theme_mod( 'retina_logo', false ) ) {
			$logo_width  = floor( $logo_width * 2 );
			$logo_height = floor( $logo_height * 2 );
		}

		add_theme_support(
			'custom-logo',
			array(
				'height'      => $logo_height,
				'width'       => $logo_width,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Twenty Twenty, use a find and replace
		 * to change 'twentytwenty' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'twentytwenty', get_template_directory() . '/languages' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for responsive embeds.
		add_theme_support( 'responsive-embeds' );

		/*
		 * Adds starter content to highlight the theme on fresh sites.
		 * This is done conditionally to avoid loading the starter content on every
		 * page load, as it is a one-off operation only needed once in the customizer.
		 */
		if ( is_customize_preview() ) {
			require get_template_directory() . '/inc/starter-content.php';
			add_theme_support( 'starter-content', twentytwenty_get_starter_content() );
		}

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		 * Adds `async` and `defer` support for scripts registered or enqueued
		 * by the theme.
		 */
		$loader = new TwentyTwenty_Script_Loader();
		add_filter( 'script_loader_tag', array( $loader, 'filter_script_loader_tag' ), 10, 2 );
	}

	/**
	 * Enqueue styles
	 *
	 * @since  1.0.0
	 */
	public function styles() {
		$theme_version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array(), $theme_version );

		/**
		 * Styles
		 */
		wp_enqueue_style( 'twentytwenty-style', get_template_directory_uri() . '/style.css', array(), $theme_version );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since  1.0.0
	 */
	public function scripts() {
		$theme_version = wp_get_theme()->get( 'Version' );

	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );

		$themeFunctions = [
			'twentytwenty_the_theme_svg',
		];

		foreach ($themeFunctions as $themeFunction) {
			$twig->addFunction(
				new Timber\Twig_Function( $themeFunction, $themeFunction )
			);
		}

		return $twig;
	}

}

new TwentyTwenty();
