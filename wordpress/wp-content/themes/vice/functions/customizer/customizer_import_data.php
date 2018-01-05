<?php
class vice_customize_import_dummy_data {

	private static $instance;

	public static function init( ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof vice_customize_import_dummy_data ) ) {
			self::$instance = new vice_customize_import_dummy_data;
			self::$instance->vice_setup_actions();
		}

	}

	/**
	 * Setup the class props based on the config array.
	 */
	

	/**
	 * Setup the actions used for this class.
	 */
	public function vice_setup_actions() {

		// Register the section
		add_action( 'customize_register', array( $this, 'vice_customize_register' ) );

		// Enqueue scripts
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'vice_import_customize_scripts' ), 0 );

	}

	public function vice_import_customize_scripts() {

	wp_enqueue_script( 'vice-import-customizer-js', get_stylesheet_directory_uri() . '/js/vice-import-customizer.js', array( 'customize-controls' ) );
	
	
	}

	public function vice_customize_register( $wp_customize ) {

		require_once get_stylesheet_directory() . '/functions/custom_control/class-dummy-import-control.php';
		
		$wp_customize->register_section_type( 'vice_dummy_import' );

		$wp_customize->add_section(
			new vice_dummy_import(
				$wp_customize,
				'vice_import_section',
				array(
					'priority' => 0,
				)
			)
		);

	}
}

$import_customizer = array(

		'import_data' => array(
			'recommended' => true,
			
		),
);
vice_customize_import_dummy_data::init( apply_filters( 'vice_import_customizer', $import_customizer ) );
