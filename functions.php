<?php
/**
 * Dark-mode related functions & hooks.
 *
 * @package wordpress/twentytwentyone-dark-mode
 */

 /**
 * Editor custom color variables.
 *
 * @since 1.0.0
 *
 * @return void
 */
function tt1_dark_mode_editor_custom_color_variables() {
	$background_color            = get_theme_mod( 'background_color', 'D1E4DD' );
	$should_respect_color_scheme = get_theme_mod( 'respect_user_color_preference', true ); // @phpstan-ignore-line. Passing true instead of default value of false to get_theme_mod.
	if ( $should_respect_color_scheme && Twenty_Twenty_One_Custom_Colors::get_relative_luminance_from_hex( $background_color ) > 127 ) {
		// Add dark mode variable overrides.
		wp_add_inline_style( 'twenty-twenty-one-custom-color-overrides', '@media (prefers-color-scheme: dark) { :root .editor-styles-wrapper { --global--color-background: var(--global--color-dark-gray); --global--color-primary: var(--global--color-light-gray); --global--color-secondary: var(--global--color-light-gray); } }' );
	}

	wp_enqueue_script(
		'twentytwentyone-editor-dark-mode-support',
		plugins_url( 'assets/js/editor-dark-mode-support.js', __FILE__ ),
		array(),
		'1.0.0',
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'tt1_dark_mode_editor_custom_color_variables' );

/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 *
 * @return void
 */
function tt1_dark_mode_scripts() {
	wp_enqueue_style(
		'tt1-dark-mode',
		plugins_url( 'assets/css/style.css', __FILE__ ),
		array( 'twenty-twenty-one-style' ),
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'tt1_dark_mode_scripts' );

/**
 * Enqueue scripts for the customizer.
 *
 * @since 1.0.0
 *
 * @return void
 */
function tt1_dark_mode_customize_controls_enqueue_scripts() {

	wp_enqueue_script(
		'twentytwentyone-customize-controls',
		plugins_url( 'assets/js/customize.js', __FILE__ ),
		array( 'customize-base', 'customize-controls', 'underscore', 'jquery', 'twentytwentyone-customize-helpers' ),
		'1.0.0',
		true
	);

	wp_localize_script(
		'twentytwentyone-customize-controls',
		'backgroundColorNotice',
		array(
			'message' => esc_html__( 'You currently have dark mode enabled on your device. Changing the color picker will allow you to preview light mode.', 'twentytwentyone-dark-mode' ),
		)
	);
}
add_action( 'customize_controls_enqueue_scripts', 'tt1_dark_mode_customize_controls_enqueue_scripts' );

/**
 * Register customizer options.
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 *
 * @return void
 */
function tt1_dark_mode_register_customizer_controls( $wp_customize ) {

	$wp_customize->add_setting(
		'respect_user_color_preference',
		array(
			'capability'        => 'edit_theme_options',
			'default'           => true,
			'sanitize_callback' => function( $value ) {
				return (bool) $value;
			},
		)
	);

	$wp_customize->add_control(
		'respect_user_color_preference',
		array(
			'type'            => 'checkbox',
			'section'         => 'colors',
			'label'           => esc_html__( 'Respect visitor\'s device dark mode settings', 'twentytwentyone-dark-mode' ),
			'description'     => __( 'Dark mode is a device setting. If a visitor to your site requests it, your site will be shown with a dark background and light text.', 'twentytwentyone-dark-mode' ),
			'active_callback' => function( $value ) {
				return 127 < Twenty_Twenty_One_Custom_Colors::get_relative_luminance_from_hex( get_theme_mod( 'background_color', 'D1E4DD' ) );
			},
		)
	);
}
add_action( 'customize_register', 'tt1_dark_mode_register_customizer_controls' );

/**
 * Calculate classes for the main <html> element.
 *
 * @since 1.0.0
 *
 * @param string $classes The classes for <html> element.
 *
 * @return string
 */
function tt1_dark_mode_the_html_classes( $classes ) {
	$background_color            = get_theme_mod( 'background_color', 'D1E4DD' );
	$should_respect_color_scheme = get_theme_mod( 'respect_user_color_preference', true );
	if ( $should_respect_color_scheme && 127 <= Twenty_Twenty_One_Custom_Colors::get_relative_luminance_from_hex( $background_color ) ) {
		return ( $classes ) ? ' respect-color-scheme-preference' : 'respect-color-scheme-preference';
	}
	return $classes;
}
add_filter( 'twentytwentyone_html_classes', 'tt1_dark_mode_the_html_classes' );

/**
 * Adds a class to the <body> element in the editor to accommodate dark-mode.
 *
 * @since 1.0.0
 *
 * @param string $classes The admin body-classes.
 *
 * @return string
 */
function tt1_dark_mode_admin_body_classes( $classes ) {
	global $current_screen;
	if ( empty( $current_screen ) ) {
		set_current_screen();
	}

	if ( $current_screen->is_block_editor() ) {
		$should_respect_color_scheme = get_theme_mod( 'respect_user_color_preference', true ); // @phpstan-ignore-line. Passing true instead of default value of false to get_theme_mod.
		$background_color            = get_theme_mod( 'background_color', 'D1E4DD' );

		if ( $should_respect_color_scheme && Twenty_Twenty_One_Custom_Colors::get_relative_luminance_from_hex( $background_color ) > 127 ) {
			$classes .= ' twentytwentyone-supports-dark-theme';
		}
	}

	return $classes;
}
add_filter( 'admin_body_class', 'tt1_dark_mode_admin_body_classes' );

/**
 * Add night/day switch.
 *
 * Inspired from https://codepen.io/aaroniker/pen/KGpXZo (MIT-licensed)
 *
 * @since 1.0.0
 *
 * @return void
 */
function tt1_dark_mode_night_switch() {
	if (
		! get_theme_mod( 'respect_user_color_preference', true ) ||
		127 > Twenty_Twenty_One_Custom_Colors::get_relative_luminance_from_hex( get_theme_mod( 'background_color', 'D1E4DD' ) )
	) {
		return;
	}
	?>
	<div id="night-day-toggle">
		<input type="checkbox" id="night-day-toggle-input"/>
		<label for="night-day-toggle-input">
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle color scheme', 'twentytwentyone-dark-mode' ); ?></span>
		</label>
	<script>
		<?php include 'assets/js/toggler.js'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude ?>
	</script>
	<?php
}
add_action( 'wp_footer', 'tt1_dark_mode_night_switch' );
