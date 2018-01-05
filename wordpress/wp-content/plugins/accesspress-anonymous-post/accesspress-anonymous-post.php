<?php 
defined('ABSPATH') or die("No script kiddies please!");
/**
 * Plugin Name:AccessPress Anonymous Post
 * Plugin URI: http://accesspressthemes.com/wordpress-plugins/accesspress-anonymous-post/
 * Description: A plugin that provides the ability to publish post from frontend with or without login anonymously using a simple html5 form from anywhere of the site with the help of shortcode and various backend settings.
 * Version:2.7.3
 * Author:AccessPress Themes
 * Author URI:http://accesspressthemes.com/
 * Text Domain: accesspress-anonymous-post
 * Domain Path: /languages/
 * Network: false
 * License:GPLv2 or later
 **/
 
 
  
 if(!class_exists('AP_Class'))
 {
    class AP_Class
    {
   	 var $ap_settings;

	/**
	 * Initializes the plugin functions 
	 */
        function __construct()
        {
            
            $this->ap_settings = get_option('ap_settings');
            $this->define_constants();
            register_activation_hook( __FILE__, array( $this, 'load_default_settings' ) );//loads default settings for the plugin while activating the plugin
            add_action('init',array($this,'plugin_text_domain'));//loads text domain for translation ready
            add_action('init',array($this,'session_init')); //starts session if not started
            add_action('template_redirect',array($this,'submit_form'));//captures all the form values before printing any other html
            add_action('admin_post_ap_settings_action',array($this,'ap_settings_action'));//settings action 
            add_action('admin_menu',array($this,'add_ap_menu'));//adds plugin menu in wp-admin
            add_action('admin_enqueue_scripts',array($this,'register_admin_assets'));//register plugin scripts and css in wp-admin
            add_shortcode('ap-form',array($this,'ap_form'));//adds the plugin shortcode
            add_shortcode('ap-form-message',array($this,'ap_form_message'));//add the shortcode to display the post submission message in redirected page.
            add_action('wp_enqueue_scripts',array($this,'register_frontend_assets'));//registers scripts and styles for front end
            add_action('pre_get_posts',array($this,'restrict_media_library'));//restricts user to see only uploaded by logged in user
            add_action('admin_post_ap_restore_default',array($this,'ap_restore_default'));//restores default settings
           
            
        }
        
         /**
          *
          * Declartion of necessary constants for plugin
          * 
          */
        function define_constants(){
           
                defined('AP_IMAGE_DIR') or define('AP_IMAGE_DIR',plugin_dir_url( __FILE__ ).'images');
             
                defined('AP_JS_DIR') or define('AP_JS_DIR',plugin_dir_url( __FILE__ ).'js');
             
                defined('AP_CSS_DIR') or define('AP_CSS_DIR',plugin_dir_url( __FILE__ ).'css');
             
                defined('AP_VERSION') or define('AP_VERSION','2.7.3');
             
        }
        
        //load the text domain for language translation
        function plugin_text_domain()
        {
            load_plugin_textdomain('accesspress-anonymous-post', false, basename( dirname( __FILE__ ) ) . '/languages/' );
               
        }
        
        //grabes the posted form data and save post accordingly
        function submit_form()
        {
           
            if(isset($_POST['ap_form_nonce']) && wp_verify_nonce($_POST['ap_form_nonce'],'ap_form_nonce'))
            {
                 include_once('inc/cores/save-post.php');
            }
        }
        
        //registers all the necessary css and js for wp-admin
        function register_admin_assets()
        {
            //including the plugin's css and js only in plugin's settings page
            if(isset($_GET['page']) && $_GET['page']=='anonymous-post')
            {
                wp_enqueue_script('ap-admin-script',AP_JS_DIR.'/admin-script.js',array('jquery'),AP_VERSION);
                wp_enqueue_style('ap-admin-style',AP_CSS_DIR.'/admin-style.css',false,AP_VERSION);
                
            }
        }
        
        //registers css and js for frontend
        function register_frontend_assets()
        {
            $ap_settings = $this->ap_settings;
            //including plugin only if admin has selected the option to show
            if($ap_settings['plugin_styles']==1)
            {
              wp_enqueue_style('ap-front-styles',AP_CSS_DIR.'/frontend-style.css',false,AP_VERSION);    
            }
            wp_enqueue_script('ap-frontend-js',AP_JS_DIR.'/frontend.js',array('jquery'),AP_VERSION,true);
            wp_localize_script('ap-frontend-js','ap_form_required_message',__('This field is required','accesspress-anonymous-post'));
            wp_localize_script('ap-frontend-js','ap_captcha_error_message',__('Sum is not correct.','accesspress-anonymous-post'));
            
        }
        
        //Adds admin menu 
        function add_ap_menu()
        {
            add_menu_page(__('AccessPress Anonymoust Post Settings','accesspress-anonymous-post'),__('AccessPress Anonymous Post','accesspress-anonymous-post'),'manage_options','anonymous-post',array($this,'ap_settings'),AP_IMAGE_DIR.'/ap-icon.png');
        }
        
        //returns the ID of the first user
        function get_first_user_id()
        {
            $users = get_users();
            foreach($users as $user)
            {
               return $user->ID;
               exit;    
            }
        
        }
        
        //starts the session with the call of init hook
        
        function session_init()
        {
            if(!session_id() && !headers_sent())
            {
                session_start();
            }
        }
        
        
        //Load default settings during plugin activation
        function load_default_settings()
        {
            $ap_settings = array();//array for saving all the plugin's settings in single array
            $ap_settings['form_title'] = __('Anonymous Post','accesspress-anonymous-post');
            $ap_settings['publish_status'] = 'draft';
            $ap_settings['admin_notification'] = 1;
            $ap_settings['login_check'] = 0;
            $ap_settings['login_message'] = __('Please login to submit the post.','accesspress-anonymous-post');
            $ap_settings['login_link_text'] = '';
            $ap_settings['post_author'] = $this->get_first_user_id();
            $ap_settings['plugin_styles'] = 1;
            $ap_settings['post_submission_message'] = '';
            $ap_settings['form_included_fields'] = array('post_title','post_content');
            $ap_settings['form_required_fields'] = array('post_title','post_content');
            $ap_settings['taxonomy_reference'] = 'category,post_tag';
            $ap_settings['editor_type'] = 'rich';
            $ap_settings['media_upload'] = 0;
            $ap_settings['form_included_taxonomy'] = array();
            $ap_settings['post_category'] = '';
            $ap_settings['post_title_label'] = '';
            $ap_settings['post_excerpt_label'] = '';
            $ap_settings['post_content_label'] = '';
            $ap_settings['post_image_label'] = '';
            $ap_settings['author_name_label'] = '';
            $ap_settings['author_url_label'] = '';
            $ap_settings['author_email_label'] = '';
            $ap_settings['post_submit_label'] = '';
            $ap_settings['category_label'] = '';
            $ap_settings['post_tag_label'] = '';
            $ap_settings['captcha_settings'] = '1';
            $ap_settings['math_captcha_label'] = '';
            $ap_settings['editor_type'] = 'rich';
            $ap_settings['redirect_url'] = '';
            $ap_settings['admin_email_list'] = array();
            $ap_settings['math_captcha_error_message'] = '';
            if(!get_option('ap_settings'))
            {
                update_option('ap_settings',$ap_settings);//update as default option while activating for the first time.    
            }
            
        }
        
        //plugin backend settings page
        function ap_settings()
        {
          include_once('inc/settings.php');  
        }
        
        
        //prints array in pre format
        function print_array($array)
        {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }
        
       
        
        //Sanitizes field values for saving in db
        function filter_field($field)
        {
            
            return sanitize_text_field($field);
        }
        
        //Sanitizes field by converting line breaks to <br /> tags
        function sanitize_escaping_linebreaks($text)
        {
            $text = implode( "<br \>", array_map( 'sanitize_text_field', explode( "\n", $text)));
            return $text;
        }
        
        //outputs by converting <Br/> tags into line breaks
        function output_converting_br($text)
        {
            $text = implode( "\n", array_map( 'sanitize_text_field', explode( "<br \>", $text)));
            return $text;
        }
        
        //Saves all the settings
        function ap_settings_action()
        {
            if(isset($_POST['ap_settings_action'],$_POST['ap_settings_submit']))
            {
                include_once('inc/cores/save-settings.php');
            }
        }
       
       //Shortcode for the form
       function ap_form()
       {
        $ap_settings = $this->ap_settings;
        include('inc/cores/shortcode.php');
        return $ap_form;
       }
       
       //Prepares the form html for the shortcode
       function prepare_form_html()
       {
        include('inc/cores/front-form.php');
        return $form;
       }
       
       //returns the html generated by wp_editor hook
       function get_wp_editor_html($editor_type)
       {
            $ap_settings = $this->ap_settings;
            
                    switch($editor_type){
            	case 'rich':
            		$teeny = false;
            		$show_quicktags = true;
            		break;
            	case 'visual':
            		$teeny = false;
            		$show_quicktags = false;
            		break;
            	case 'html':
            		$teeny = true;
            		$show_quicktags = true;
            		add_filter ( 'user_can_richedit' , create_function ( '' , 'return false;' ) , 50 );
            		break;
            }
            $media_upload = ($ap_settings['media_upload']==1)?true:false;
            $total_rows = isset($ap_settings['editor_size'])?$ap_settings['editor_size']:10;
           	$settings = array(
    			'media_buttons'	=> $media_upload,
    			'teeny'			=> $teeny,
    			'wpautop'		=> true,
    			'quicktags'		=> $show_quicktags,
                'editor_class'=>'ap-form-content-editor',
                'textarea_rows'=>$total_rows
    		);
           
            ob_start();
            wp_editor('','ap_form_content_editor',$settings);
            $wp_editor = ob_get_contents();
            ob_end_clean();
            return $wp_editor;
       }
       
       //returns nonce field html as variable
       function get_nonce_field_html()
       {
        ob_start();
        wp_nonce_field( 'ap_form_nonce', 'ap_form_nonce' );
        $nonce_field = ob_get_contents();
        ob_end_clean();
        return $nonce_field;
       }
       
       //send admin notification if enabled from backend
       function send_admin_notification($post_id,$post_title)
       {
          	$blogname = get_option('blogname');
		    $email = get_option('admin_email');
            $headers = "MIME-Version: 1.0\r\n" . "From: ".$blogname." "."<".$email.">\n" . "Content-Type: text/HTML; charset=\"" . get_option('blog_charset') . "\"\r\n";
            $message = __('Hello there,','accesspress-anonymous-post').'<br/><br/>'. 
                        __('A new post has been submitted via AccessPress Anonymous Post plugin in ','accesspress-anonymous-post').$blogname.' site.'.__(' Please find details below:','accesspress-anonymous-post').'<br/><br/>'.
                        
                        'Post title: '.$post_title.'<br/><br/>';
            $post_author_name = get_post_meta($post_id,'ap_author_name',true);
            $post_author_email = get_post_meta($post_id,'ap_author_email',true);
            $post_author_url = get_post_meta($post_id,'ap_author_url',true);
            if($post_author_name!=''){
                $message .= 'Post Author Name: '.$post_author_name.'<br/><br/>';
            }
            if($post_author_email!=''){
                $message .= 'Post Author Email: '.$post_author_email.'<br/><br/>';
            }
            if($post_author_url!=''){
                $message .= 'Post Author URL: '.$post_author_url.'<br/><br/>';
            }
      
                        
            $message .= '____<br/><br/>
                        '.__('To take action (approve/reject)- please go here:','accesspress-anonymous-post').'<br/>'
                        .admin_url().'post.php?post='.$post_id.'&action=edit <br/><br/>
                        
                        '.__('Thank You','accesspress-anonymous-post');
            $subject = __('New Post Submission - via AccessPress Anonymous Post','accesspress-anonymous-post');
            
            /**
             * Filters admin email message
             * 
             * @param string $message
             * @param int $post_id
             * 
             * @since 2.6.7
             * */
            wp_mail($email,$subject,apply_filters('ap_admin_message',$message,$post_id),$headers);
                        
                        
       }
       
       //returns the current page url
       function curPageURL() {
                 $pageURL = 'http';
                 if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')
                 {
                    $pageURL .= "s";
                 }
                 $pageURL .= "://";
                 if ($_SERVER["SERVER_PORT"] != "80") {
                  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
                 } else {
                  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                 }
                 return $pageURL;
            }
            
       //shortcode for showing the message in any redirected page after successful post submission
       function ap_form_message($atts)
       {
        
        if(isset($_SESSION['ap_form_success_msg']))
        {
            
         $msg = $_SESSION['ap_form_success_msg'];
         unset($_SESSION['ap_form_success_msg']);
         return $msg;    
        }else
            {
            if(isset($atts['redirect'])){
                $redirect_url = esc_url($atts['redirect']);
            echo "<script>";
            echo "window.location = '$redirect_url';";
            echo "</script>";
            //exit;
            }    
        }
        
        
         
       }
       
       //returns only logged in user related media items
       function restrict_media_library( $wp_query_obj ) {
        if(is_user_logged_in()){
            global $current_user, $pagenow;
            if(isset($current_user->caps)){
             $caps = $current_user->caps;
             if(is_array($caps)){
                        reset($caps);
                         $user_role = key($caps);
                         if($user_role!='administrator'){
                         if( !is_a( $current_user, 'WP_User') )
                        return;
                        if( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' )
                        return;
                        if( !current_user_can('manage_media_library') )
                        $wp_query_obj->set('author', $current_user->ID );
                        return;
                        }
                    }
                }   
            }
             
            }
          
            
      //restores default settings explicitly
      function ap_restore_default()
      {
        $nonce = $_REQUEST['_wpnonce'];
        if(!empty($_GET) && wp_verify_nonce( $nonce, 'aps-restore-default-nonce' ))
        {
            $ap_settings = array();//array for saving all the plugin's settings in single array
            $ap_settings['form_title'] = 'Anonymous Post';
            $ap_settings['publish_status'] = 'draft';
            $ap_settings['admin_notification'] = 1;
            $ap_settings['login_check'] = 0;
            $ap_settings['login_message'] = __('Please login to submit the post.','accesspress-anonymous-post');
            $ap_settings['login_link_text'] = '';
            $ap_settings['post_author'] = $this->get_first_user_id();
            $ap_settings['plugin_styles'] = 1;
            $ap_settings['post_submission_message'] = '';
            $ap_settings['form_included_fields'] = array('post_title','post_content');
            $ap_settings['form_required_fields'] = array('post_title','post_content');
            $ap_settings['taxonomy_reference'] = 'category,post_tag';
            $ap_settings['editor_type'] = 'rich';
            $ap_settings['media_upload'] = 0;
            $ap_settings['form_included_taxonomy'] = array();
            $ap_settings['post_category'] = '';
            $ap_settings['post_title_label'] = '';
            $ap_settings['post_excerpt_label'] = '';
            $ap_settings['post_content_label'] = '';
            $ap_settings['post_image_label'] = '';
            $ap_settings['author_name_label'] = '';
            $ap_settings['author_url_label'] = '';
            $ap_settings['author_email_label'] = '';
            $ap_settings['post_submit_label'] = '';
            $ap_settings['category_label'] = '';
            $ap_settings['post_tag_label'] = '';
            $ap_settings['captcha_settings'] = '1';
            $ap_settings['math_captcha_label'] = '';
            $ap_settings['editor_type'] = 'rich';
            $ap_settings['redirect_url'] = '';
            $ap_settings['admin_email_list'] = array();
            $ap_settings['math_captcha_error_message'] = '';
            $restore = update_option('ap_settings',$ap_settings);
          //  $_SESSION['ap_message'] = __('Default Settings Restored Successfully.','accesspress-anonymous-post');
            wp_redirect(admin_url().'admin.php?page=anonymous-post&message=2');
            exit;
                
            
        }
        else
        {
            die('No script kiddies please!');
        }
      }
      
      
       
    }//class termination
    
 $ap_obj = new AP_Class();
 }//class exist check close
 