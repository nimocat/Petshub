<?php 
/**
 * POST PARAMETERS
 * 
 * 
 *  Array
(
    [action] => ap_settings_action
    [taxonomy_reference] => category,post_tag
    [form_title] => Anonymous Post
    [publish_status] => draft
    [admin_notification] => 1
    [post_author] => 1
    [plugin_styles] => 1
    [redirect_url] => 
    [post_submission_message] => 
    [form_included_fields] => Array
        (
            [0] => post_title
            [1] => post_content
            [2] => post_excerpt
            [3] => post_image
            [4] => author_name
            [5] => author_url
            [6] => author_email
        )

    [form_included_taxonomy] => Array
        (
            [0] => category
            [1] => post_tag
        )

    [post_category] => 1
    [editor_type] => rich
    [post_title_label] => Post Title
    [post_content_label] => Post Content
    [post_excerpt_label] => Post Excerpt
    [post_image_label] => Post Image
    [category_label] => Post Category
    [post_tag_label] => Post Tags
    [author_name_label] => Author Name
    [author_url_label] => Author URL
    [author_email_label] => Author Email
    [post_submit_label] => Submit Post
    [captcha_settings] => human
    [math_captcha_label] => Human Check
    [math_captcha_error_message] => Please enter the correct sum.
    [ap_settings_submit] => Save all changes
)
 * */
 


/**
 * Changes all the posted fields into its respective variables from $_POST
 * */
 
 //$this->print_array($_POST);die();
 //for stripping unnecessary slashes
 $_POST = array_map( 'stripslashes_deep', $_POST );
 foreach($_POST as $key=>$val)
        {
            
            if(is_array($val))
            {
                 $new_array = array();
                if($key!='form_required_message')
                {
                   
                    foreach($val as $value)
                    {
                        $new_array[] = sanitize_text_field($value);//sanitizing each and every field that is being recieved as array    
                          
                    }
                    
                }
                else
                {
                    foreach($val as $k=>$v)
                    {
                        $new_array[$k] = sanitize_text_field($v);
                    }
                    
                }
                $val = $new_array;
                
            }
            else
            {
                if($key=='redirect_url')
                {
                    $val = esc_url($val);
                    
                }
                else if($key=='post_submission_message' || $key=='login_message')
                {
                    $val = $this->sanitize_escaping_linebreaks($val);//for changing the line breaks into <br /> tags
                }
                else
                {
                    $val = sanitize_text_field($val);
                }
                
            }
            
            $$key = $val;
        }
 //$this->print_array($form_required_message);die();
$ap_settings = array();//array for saving all the plugin's settings in single array
$ap_settings['publish_status'] = $publish_status;
$ap_settings['post_format'] = $post_format;
if(isset($admin_notification))
{
    $ap_settings['admin_notification'] = 1;
}
else
{
    $ap_settings['admin_notification'] = 0;
}
$ap_settings['post_category'] = $post_category;
if(isset($login_check))
{
    $ap_settings['login_check'] = 1;
}
else
{
    $ap_settings['login_check'] = 0;
}
$ap_settings['login_message'] = $login_message;
$ap_settings['login_link_text'] = $login_link_text;
$ap_settings['login_link_url'] = $login_link_url;
$ap_settings['post_author'] = $post_author;
$ap_settings['post_submission_message'] = $post_submission_message;
$ap_settings['form_title'] = $form_title;
$ap_settings['form_included_fields'] = (isset($form_included_fields) && !empty($form_included_fields))?$form_included_fields:array('post_title','post_content');
$ap_settings['form_required_fields'] = (isset($form_required_fields) && !empty($form_required_fields))?$form_required_fields:array('post_title','post_content');
$ap_settings['form_required_message'] = $form_required_message;
$ap_settings['taxonomy_reference'] = $taxonomy_reference;
$ap_settings['form_included_taxonomy'] = isset($form_included_taxonomy)?$form_included_taxonomy:array();
$ap_settings['media_upload'] = isset($media_upload)?1:0;
$ap_settings['post_category'] = $post_category;
$ap_settings['post_title_label'] = $post_title_label;
$ap_settings['post_content_label'] = $post_content_label;
$ap_settings['post_excerpt_label'] = $post_excerpt_label;
$ap_settings['post_image_label'] = $post_image_label;
$ap_settings['author_name_label'] = $author_name_label;
$ap_settings['author_url_label'] = $author_url_label;
$ap_settings['author_email_label'] = $author_email_label;
$ap_settings['post_submit_label'] = $post_submit_label;
$ap_settings['editor_type'] = $editor_type;
$ap_settings['editor_size'] = $editor_size;
$taxonomies_array = explode(',',$taxonomy_reference);
foreach($taxonomies_array as $tax)
{
    $label_name = $tax.'_label';
    $ap_settings[$label_name] = $$label_name;
}
$ap_settings['captcha_settings'] = (isset($captcha_settings))?1:0;
$ap_settings['plugin_styles'] = (isset($plugin_styles))?1:0;
$ap_settings['auto_author_details'] = (isset($auto_author_details))?1:0;
$ap_settings['redirect_url'] = $redirect_url;
$ap_settings['captcha_type'] = $captcha_type;
$ap_settings['math_captcha_error_message'] = $math_captcha_error_message;
$ap_settings['math_captcha_label'] = $math_captcha_label;
$ap_settings['google_captcha_sitekey'] = $google_captcha_sitekey;
$ap_settings['google_captcha_secretkey'] = $google_captcha_secretkey;
/**
 * Filters options array saved in database
 * 
 * */
$update_option_check = update_option('ap_settings',apply_filters('ap_settings',$ap_settings));

wp_redirect(admin_url().'admin.php?page=anonymous-post&message=1');
exit;



