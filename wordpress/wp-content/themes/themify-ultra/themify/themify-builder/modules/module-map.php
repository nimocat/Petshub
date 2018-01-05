<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Map
 * Description: Display Map
 */
class TB_Map_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Map', 'themify'),
			'slug' => 'map'
		));
	}

	public function get_options() {
		$zoom_opt = array();
		for ( $i=1; $i < 17 ; $i++ ) {
                    $zoom_opt[] = $i;
		}
		return array(
			array(
				'id' => 'mod_title_map',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large'
			),
			array(
				'id' => 'map_display_type',
				'type' => 'radio',
				'label' => __('Type', 'themify'),
				'options' => array(
					'dynamic' => __( 'Dynamic', 'themify' ),
					'static' => __( 'Static image', 'themify' ),
				),
				'default' => 'dynamic',
				'option_js' => true
			),
			array(
				'id' => 'address_map',
				'type' => 'textarea',
				'value' => '',
				'class' => 'fullwidth',
				'label' => __('Address', 'themify')
			),
			array(
				'id' => 'latlong_map',
				'type' => 'text',
				'value' => '',
				'class' => 'large',
				'label' => __('Lat/Long', 'themify'),
				'help' => '<br/>' . __('Use Lat/Long instead of address (Leave address field empty to use this). Exp: 43.6453137,-79.3918391', 'themify')
			),
			array(
				'id' => 'zoom_map',
				'type' => 'select',
				'label' => __('Zoom', 'themify'),
				'default' => 8,
				'options' => $zoom_opt
			),
			array(
				'id' => 'w_map',
				'type' => 'text',
				'class' => 'xsmall',
				'label' => __('Width', 'themify'),
				'unit' => array(
					'id' => 'unit_w',
					'type' => 'select',
					'options' => array(
						array( 'id' => 'pixel_unit_w', 'value' => 'px'),
						array( 'id' => 'percent_unit_w', 'value' => '%')
					)
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic'
			),
			array(
				'id' => 'w_map_static',
				'type' => 'text',
				'class' => 'xsmall',
				'label' => __('Width', 'themify'),
				'value' => 500,
				'after' => 'px',
				'wrap_with_class' => 'tb-group-element tb-group-element-static'
			),
			array(
				'id' => 'h_map',
				'type' => 'text',
				'label' => __('Height', 'themify'),
				'class' => 'xsmall',
				'unit' => array(
					'id' => 'unit_h',
					'type' => 'select',
					'options' => array(
						array( 'id' => 'pixel_unit_h', 'value' => 'px')
					)
				),
				'value' => 300
			),
			array(
				'id' => 'multi_map_border',
				'type' => 'multi',
				'label' => __('Border', 'themify'),
				'fields' => array(
					array(
						'id' => 'b_style_map',
						'type' => 'select',
						'label' => '',
						'options' => array(
							'solid' => __( 'Solid', 'themify' ),
							'dashed' => __( 'Dashed', 'themify' ),
							'dotted' => __( 'Dotted', 'themify' ),
							'double' => __( 'Double', 'themify' ),
							'' => __( 'None', 'themify' )
						)
					),
					array(
						'id' => 'b_width_map',
						'type' => 'text',
						'label' => '',
						'class' => 'medium',
						'after' => 'px'
					),
					array(
						'id' => 'b_color_map',
						'type' => 'text',
						'colorpicker' => true,
						'class' => 'large',
						'label' => ''
					),
				)
			),
			array(
				'id' => 'type_map',
				'type' => 'select',
				'label' => __('Type', 'themify'),
				'options' => array(
					'ROADMAP' => __( 'Road Map', 'themify' ),
					'SATELLITE' => __( 'Satellite', 'themify' ),
					'HYBRID' => __( 'Hybrid', 'themify' ),
					'TERRAIN' => __( 'Terrain', 'themify' )
				)
			),
			array(
				'id' => 'scrollwheel_map',
				'type' => 'select',
				'label' => __( 'Scrollwheel', 'themify' ),
				'options' => array(
					'disable' => __( 'Disable', 'themify' ),
					'enable' => __( 'Enable', 'themify' ),
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic'
			),
			array(
				'id' => 'draggable_map',
				'type' => 'select',
				'label' => __( 'Draggable', 'themify' ),
				'options' => array(
					'enable' => __( 'Enable', 'themify' ),
					'disable' => __( 'Disable', 'themify' )
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic'
			),
			array(
				'id' => 'draggable_disable_mobile_map',
				'type' => 'select',
				'label' => __( 'Disable draggable on mobile', 'themify' ),
				'options' => array(
					'yes' => __( 'Yes', 'themify' ),
					'no' => __( 'No', 'themify' )
				),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic'
			),
			array(
				'id' => 'info_window_map',
				'type' => 'textarea',
				'value' => '',
				'class' => 'fullwidth',
				'label' => __('Info window', 'themify'),
				'help' => __('Additional info that will be shown when clicking on map marker', 'themify'),
				'wrap_with_class' => 'tb-group-element tb-group-element-dynamic'
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_map',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'themify') )
			)
		);
	}

	public function get_default_settings() {
		return array(
			'address_map' => 'Toronto',
			'b_style_map' => 'solid',
			'w_map' => '100',
			'unit_w' => '%'
		);
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }

	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-map', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-map'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-map'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-map')
		);

		return array(
			array(
				'type' => 'tabs',
				'id' => 'module-styling',
				'tabs' => array(
					'general' => array(
                                            'label' => __( 'General', 'themify' ),
                                            'fields' => $general
					),
					'module-title' => array(
                                            'label' => __( 'Module Title', 'themify' ),
                                            'fields' => self::module_title_custom_style( $this->slug )
					)
				)
			)
		);

	}

	/**
	 * Render plain content
	 */
	public function get_plain_content( $module ) {
		$mod_settings = wp_parse_args( $module['mod_settings'], array(
			'mod_title_map' => '',
			'zoom_map' => 15
		) );
		if (!empty($mod_settings['address_map'])) {
			$mod_settings['address_map'] = preg_replace('/\s+/', ' ', trim($mod_settings['address_map']));
		}
		$text = sprintf( '<h3>%s</h3>', $mod_settings['mod_title_map'] );
		$text .= sprintf(
			'<iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=%s&amp;t=m&amp;z=%d&amp;output=embed&amp;iwloc=near"></iframe>',
			urlencode( $mod_settings['address_map'] ),
			absint( $mod_settings['zoom_map'] )
		);
		return $text;
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Map_Module' );
