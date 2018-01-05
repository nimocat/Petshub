<?php
class vice_dummy_import extends WP_Customize_Section {
	
	public $type = 'vice_import_section';
	protected function render_template() {
	?>
			<li>
				<h3 class="accordion-section-title">
					<?php _e('Activate Homepage','vice'); ?>
				</h3>
				<div class="updated notice notice-success notice-alt is-dismissible">
				<p><?php echo sprintf( esc_html__('Welcome! Thank you for choosing the Vice theme. Use our homepage to make the most out of it. For instructions, follow the button.', 'vice' ), '<a href="' . esc_url( admin_url( 'themes.php?page=appointment-info' ) ) . '">', '</a>' ); ?></p>
				<p><a href="<?php echo esc_url( admin_url( 'themes.php?page=appointment-info#actions_required' ) ); ?>" class="button button-blue-secondary"><?php _e( 'Getting started', 'vice' ); ?></a></p>
				</div>
			</li>
	<?php }
}