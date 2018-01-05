<div class="ap-tabs-board" id="board-captcha-settings" style="display: none;">
	<h2><?php _e( 'Captcha Settings', 'accesspress-anonymous-post' ); ?></h2>
	<div class="ap-tab-wrapper">
		<div class="ap-option-wrapper">
			<div class="ap-option-field">
				<div class="ap-checkbox-form"><label class="ap-label-wrapper"><input type="checkbox" name="captcha_settings" value="1" <?php if ( $ap_settings['captcha_settings'] == '1' ) { ?>checked="checked"<?php } ?> class="ap-captcha-selector"/><span><?php _e( 'Show Captcha', 'accesspress-anonymous-post' ) ?></span></label></div>
			</div>
		</div>
		<div class="ap-option-wrapper">
			<label><?php _e( 'Captcha Type', 'accesspress-anonymous-post' ); ?></label>
			<div class="ap-option-field">
				<?php $captcha_type = isset( $ap_settings['captcha_type'] ) ? $ap_settings['captcha_type'] : 'math'; ?>
				<label><input type="radio" value="math" name="captcha_type" <?php checked( $captcha_type, 'math' ); ?> class="ap-captcha-type"/><?php _e( 'Mathematical Captcha', 'accesspress-anonymous-post' ); ?></label>
				<label><input type="radio" value="google" name="captcha_type"  <?php checked( $captcha_type, 'google' ); ?>  class="ap-captcha-type"/><?php _e( 'Google Captcha', 'accesspress-anonymous-post' ); ?></label>
			</div>
		</div>
		<div class="ap-option-wrapper">
			<label><?php _e( 'Captcha Label', 'accesspress-anonymous-post' ) ?></label>
			<div class="ap-option-field">
				<input type="text" name="math_captcha_label" value="<?php echo esc_attr( $ap_settings['math_captcha_label'] ); ?>"/>
			</div>
		</div>
		<div class="ap-option-wrapper">
			<label><?php _e( 'Captcha Error Message', 'accesspress-anonymous-post' ) ?></label>
			<div class="ap-option-field">
				<input type="text" name="math_captcha_error_message" value="<?php echo esc_attr( $ap_settings['math_captcha_error_message'] ); ?>"/>
			</div>
		</div>

		<div class="captcha-fields ap-google-captcha-fields"  <?php if ( $captcha_type == 'math' ) { ?>style="display:none;"<?php } ?>>
			<div class="ap-option-wrapper">
				<label><?php _e( 'Site Key', 'accesspress-anonymous-post' ); ?></label>
				<div class="ap-option-field">
					<input type="text" name="google_captcha_sitekey" value="<?php echo isset( $ap_settings['google_captcha_sitekey'] ) ? esc_attr( $ap_settings['google_captcha_sitekey'] ) : ''; ?>"/>
				</div>
			</div>
			<div class="ap-option-wrapper">
				<label><?php _e( 'Secret Key', 'accesspress-anonymous-post' ); ?></label>
				<div class="ap-option-field">
					<input type="text" name="google_captcha_secretkey" value="<?php echo isset( $ap_settings['google_captcha_secretkey'] ) ? esc_attr( $ap_settings['google_captcha_secretkey'] ) : ''; ?>"/>
				</div>
			</div>
			<div class="ap-captcha-note">
				<?php _e('Google Captcha will only show up in form if you have filled the valid google captcha keys.Please visit <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a> to get your site and secret key.','accesspress-anonymous-post');?>
			</div>
		</div>
	</div>
</div>