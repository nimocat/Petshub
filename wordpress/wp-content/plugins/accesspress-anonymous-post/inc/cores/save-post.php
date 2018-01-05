<?php

defined( 'ABSPATH' ) or die( "No script kiddies please!" );
global $error;
$ap_settings = $this->ap_settings;
$ap_form_post_title = sanitize_text_field( $_POST['ap_form_post_title'] );
$ap_form_content = wp_kses_post( $_POST['ap_form_content_editor'] );
$error = new stdClass();
$error_flag = 0;
$image_error_flag = 0;
//captcha server validation
//$this->print_array($_POST);die();
if ( $ap_settings['captcha_settings'] == '1' ) {
	$captcha_type = isset( $ap_settings['captcha_type'] ) ? $ap_settings['captcha_type'] : 'math';
	if ( $captcha_type == 'math' ) {
		$number1 = intval(sanitize_text_field( $_POST['ap_num1'] ));
		$number2 = intval(sanitize_text_field( $_POST['ap_num2'] ));
		$result = intval(sanitize_text_field( $_POST['ap_captcha_result'] ));
		$actual_sum = $number1 + $number2;
		if ( $actual_sum != $result ) {
			$error_flag = 1;
			$error_message = ($ap_settings['math_captcha_error_message'] != '') ? esc_attr( $ap_settings['math_captcha_error_message'] ) : __( 'The entered sum is not correct', 'accesspress-anonymous-post' );
			$error->captcha = $error_message;
		}
	} else {
		$captcha = filter_input( INPUT_POST, 'g-recaptcha-response' ); // get the captchaResponse parameter sent from our ajax

		/* Check if captcha is filled */
		if ( !$captcha ) {
			$error_flag = 1;
			$error_message = ($ap_settings['math_captcha_error_message'] != '') ? esc_attr( $ap_settings['math_captcha_error_message'] ) : __( 'Please verify that you are a human.', 'accesspress-anonymous-post' );
			$error->captcha = $error_message;
		} else {
			$secret_key = $ap_settings['google_captcha_secretkey'];
			$response = wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $captcha );
			$response = json_decode( $response['body'] );
			if ( $response->success == false ) {
				$error_flag = 1;
				$error_message = ($ap_settings['math_captcha_error_message'] != '') ? esc_attr( $ap_settings['math_captcha_error_message'] ) : __( 'Please verify that you are a human.', 'accesspress-anonymous-post' );
				$error->captcha = $error_message;
			}
		}
	}
}

//if captcha is disabled or captcha has been entered correctly
if ( $error_flag == 0 ) {
	if ( $ap_form_post_title == '' ) {//if post title is left blank
		$post_title_error = __( 'This field is required', 'accesspress-anonymous-post' );
		$error->title = $post_title_error;
		$error_flag = 1;
	}

	if ( $ap_form_content == '' ) {//if post content is left blank
		$post_title_error = __( 'This field is required', 'accesspress-anonymous-post' );
		$error->content = __( 'This field is required', 'accesspress-anonymous-post' );
		$error_flag = 1;
	}
	if ( in_array( 'post_image', $ap_settings['form_included_fields'] ) ) {//if post image is enabled in form
		if ( $_FILES['ap_form_post_image']['name'] != '' ) {//if user has uploaded the files
			$image_name = $_FILES['ap_form_post_image']['name'];
			$ext_array = explode( '.', $image_name );
			$ext = end( $ext_array );
			if ( !($ext == 'jpeg' || $ext == 'png' || $ext == 'jpg' || $ext == 'gif' || $ext == 'JPEG' || $ext == 'PNG' || $ext == 'JPG') ) {//if users upload invalid file type
				$error->image = __( 'Invalid File Type', 'accesspress-anonymous-post' );
				$error_flag = 1;
				$image_error_flag = 1;
			}
		}
	}
	if ( in_array( 'post_image', $ap_settings['form_included_fields'] ) && $_FILES['ap_form_post_image']['name'] != '' && $image_error_flag != 1 ) {
		if ( !function_exists( 'wp_handle_upload' ) )
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$uploadedfile = $_FILES['ap_form_post_image'];
		$upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
		if ( isset( $movefile['error'] ) ) {
			$error_flag = 1;
			$error->image = $movefile['error'];
		} else {
			if ( !function_exists( 'wp_crop_image' ) )
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			//include( ABSPATH . 'wp-admin/includes/image.php' );
			$wp_filetype = $movefile['type'];
			$filename = $movefile['file'];
			$wp_upload_dir = wp_upload_dir();
			$attachment = array(
				'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ),
				'post_mime_type' => $wp_filetype,
				'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $filename );
			//var_dump($attach_id);die();
			$attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );
		}
	}
	if ( $error_flag == 0 ) {

		//uploading image to media 


		$post_type = 'post';
		$publish_status = $ap_settings['publish_status'];
		if ( $ap_settings['login_check'] == 1 || is_user_logged_in() ) {
			$author = get_current_user_id();
		} else {
			$author = $ap_settings['post_author'];
		}

		$post_arguments = array( 'post_type' => 'post',
			'post_title' => $ap_form_post_title,
			'post_content' => $ap_form_content,
			'post_status' => $publish_status,
			'post_author' => $author
		);
		if ( isset( $_POST['ap_form_post_excerpt'] ) ) {
			$post_arguments['post_excerpt'] = sanitize_text_field( $_POST['ap_form_post_excerpt'] );
		}
		$post_id = wp_insert_post( $post_arguments );
		if ( $post_id && isset( $attach_id ) ) {
			add_post_meta( $post_id, '_thumbnail_id', $attach_id, true ); //adding featured image to post
		}

		if ( !empty( $ap_settings['form_included_taxonomy'] ) && $post_id ) {
			foreach ( $ap_settings['form_included_taxonomy'] as $taxonomy ) {
				if ( $taxonomy != 'post_tag' ) {
					$taxonomy_label = $taxonomy . '_taxonomy';
					$term_id = sanitize_text_field( $_POST[$taxonomy_label] );
					wp_set_post_terms( $post_id, array( $term_id ), $taxonomy );
				} else {
					if ( $_POST['post_tag_taxonomy'] != '' ) {
						$post_tags = sanitize_text_field( $_POST['post_tag_taxonomy'] );
						wp_set_post_tags( $post_id, $post_tags, false );
					}
				}
			}
		}
		if ( !in_array( 'category', $ap_settings['form_included_taxonomy'] ) ) {
			if ( $ap_settings['post_category'] != '' ) {
				$post_category = esc_attr( $ap_settings['post_category'] );
				wp_set_post_categories( $post_id, array( $post_category ) );
			}
		}

		if ( $post_id ) {
		  if(isset($ap_settings['post_format']) && $ap_settings['post_format']!='none'){
                set_post_format( $post_id , $ap_settings['post_format']);
            }
			//adding author name as post meta field
			if ( in_array( 'author_name', $ap_settings['form_included_fields'] ) && $_POST['ap_author_name'] != '' ) {
				$author_name = sanitize_text_field( $_POST['ap_author_name'] );
				add_post_meta( $post_id, 'ap_author_name', $author_name, false );
			}

			if ( in_array( 'author_url', $ap_settings['form_included_fields'] ) && $_POST['ap_author_url'] != '' ) {
				$author_url = esc_url( $_POST['ap_author_url'] );
				add_post_meta( $post_id, 'ap_author_url', $author_url, false );
			}
			if ( in_array( 'author_email', $ap_settings['form_included_fields'] ) && $_POST['ap_author_email'] != '' ) {
				$author_email = sanitize_text_field( $_POST['ap_author_email'] );
				add_post_meta( $post_id, 'ap_author_email', $author_email, false );
			}

			if ( $ap_settings['admin_notification'] ) {
				$this->send_admin_notification( $post_id, $ap_form_post_title );
			}
			$success = new stdClass();
			$success->msg = ($ap_settings['post_submission_message'] == '') ? __( 'Hi there, Thank you for submitting a post.', 'accesspress-anonymous-post' ) : $ap_settings['post_submission_message'];
           // setcookie('ap_form_success_msg',$success->msg);
			$_SESSION['ap_form_success_msg'] = $success->msg;
			wp_redirect( esc_url( $_POST['redirect_url'] ) );
			exit;
		}
	}//if close
}