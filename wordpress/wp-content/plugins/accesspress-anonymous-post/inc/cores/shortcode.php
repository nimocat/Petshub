<?php 
/**
 * Setting all the backend option values in the form
 * */

$ap_form ='<div class="ap-form-wrapper">';
if($ap_settings['login_check']==0)//if login is not require to submit the post
{
  $ap_form .= $this->prepare_form_html();//includes the form html     
}
else
{
    if(is_user_logged_in())//if user is logged in 
    {
        $ap_form .= $this->prepare_form_html(); //includes the form html
    }
    else 
    {
        $current_page = urlencode($this->curPageURL());
        if($ap_settings['login_message']=='')
        {
            $login_message = __('Please login to submit the post.','accesspress-anonymous-post');
        }
        else
        {
            $login_message = $ap_settings['login_message'];
        }
        if($ap_settings['login_link_text']=='')
        {
            $login_link_text = __('Login','accesspress-anonymous-post');
        }
        else
        {
            $login_link_text = esc_attr($ap_settings['login_link_text']);
        }
        $login_link_url = isset($ap_settings['login_link_url'])?esc_url($ap_settings['login_link_url']):site_url().'/wp-login.php?redirect_to='.$current_page;
        $ap_form .= '<div class="ap-login-text">'.$login_message.'</div><a class="ap-main-submit-button" href="'.$login_link_url.'">'.$login_link_text.'</a>';
    }
    
}
$ap_form .='</div><!--ap-form-->';
