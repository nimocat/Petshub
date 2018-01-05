<?php
if (!defined('ABSPATH'))
    exit;

/**
 * Template Fancy Heading
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):
    $fields_default = array(
        'heading' => '',
        'heading_tag' => 'h1',
        'sub_heading' => '',
        'text_alignment' => 'themify-text-center',
        'animation_effect' => '',
        'css_class' => '',
    );

    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $animation_effect, $fields_args['css_class']
                    ), $mod_name, $module_ID, $fields_args)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module fancy heading -->
	<div <?php echo self::get_element_attributes($container_props); ?>>
		<?php if( $fields_args['heading_tag'] === 'h1' ) : ?>
			<h1 class="fancy-heading <?php echo $fields_args['text_alignment']; ?>">
				<span class="maketable">
					<span class="addBorder"></span>
					<span class="fork-icon"></span>
					<span class="addBorder"></span>
				</span>
				<em class="sub-head"><?php echo $fields_args['sub_heading']; ?></em>
				<span class="main-head"><?php echo $fields_args['heading']; ?></span>
				<span class="bottomBorder"></span>
			</h1>
		<?php else: ?>
			<<?php echo $fields_args['heading_tag']; ?> class="fancy-heading <?php echo $fields_args['text_alignment']; ?>">
			<span class="maketable">
				<span class="addBorder"></span>
				<em class="sub-head"><?php echo $fields_args['sub_heading']; ?></em>
				<span class="addBorder"></span>
			</span>
			<span class="main-head"><?php echo $fields_args['heading']; ?></span>
			<span class="maketable">
				<span class="addBorder"></span>
				<span class="fork-icon"></span>
				<span class="addBorder"></span>
			</span>
			</<?php echo $fields_args['heading_tag']; ?>>
		<?php endif; ?>
		
    </div>
    <!-- /module fancy heading -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>