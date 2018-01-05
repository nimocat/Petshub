<div class="ap-tabs-board" id="board-general-settings">
  <h2><?php _e('General Settings','accesspress-anonymous-post');?></h2>
  <div class="ap-tab-wrapper">
    <div class="ap-option-wrapper">
      <label><?php _e('Form Title','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <input type="text" name="form_title" value="<?php echo esc_attr($ap_settings['form_title']);?>"/>
      </div>
    </div>
    <div class="ap-option-wrapper">
      <label><?php _e('Post Publish Status:','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <select name="publish_status">
          <option value="publish" <?php if($ap_settings['publish_status']=='publish'){?>selected="selected"<?php }?>><?php _e('Publish','accesspress-anonymous-post');?></option>
          <option value="pending" <?php if($ap_settings['publish_status']=='pending'){?>selected="selected"<?php }?>><?php _e('Pending','accesspress-anonymous-post');?></option>
          <option value="draft" <?php if($ap_settings['publish_status']=='draft'){?>selected="selected"<?php }?>><?php _e('Draft','accesspress-anonymous-post');?></option>
          <option value="private" <?php if($ap_settings['publish_status']=='private'){?>selected="selected"<?php }?>><?php _e('Private','accesspress-anonymous-post');?></option>
        </select>
      </div>
    </div>
    <div class="ap-option-wrapper">
            <label><?php _e('Post Format:', 'anonymous-post-pro'); ?></label>
            <div class="ap-option-field">
                <select name="post_format">
                    <?php $ap_post_format = isset($ap_settings['post_format'])?$ap_settings['post_format']:'none';            ?>
                    <option value="none" <?php selected($ap_post_format,'none'); ?>><?php _e('Standard', 'anonymous-post-pro'); ?></option>
                    <?php
                    if (current_theme_supports('post-formats')) {
                        $post_formats = get_theme_support('post-formats');
                        //$this->print_array($post_formats);
                        if (is_array($post_formats[0])) {
                            foreach ($post_formats[0] as $post_format) {
                                ?>
                                <option value="<?php echo $post_format; ?>" <?php selected($ap_post_format,$post_format);?>><?php echo $post_format; ?></option>
                                <?php
                            }
                            // Array( supported_format_1, supported_format_2 ... )
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
    <div class="ap-option-wrapper">
      <label class="ap-check-login"><?php _e('Admin Notification:','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <div class="ap-option-checkbox-field">
          <div class="ap-checkbox-form"><input type="checkbox" name="admin_notification" value="1" <?php if($ap_settings['admin_notification']=='1'){?>checked="checked"<?php }?>/></div>
          <div class="ap-option-note"><?php _e('Check if you want admin to recieve notification email after submitting of a new post.','accesspress-anonymous-post');?></div>
        </div>
      </div>
    </div>
    <div class="ap-option-wrapper">
      <label class="ap-check-login"><?php _e('Allow Media Uploads:','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <div class="ap-option-checkbox-field">
          <div class="ap-checkbox-form"><input type="checkbox" name="media_upload" value="1" <?php if($ap_settings['media_upload']=='1'){?>checked="checked"<?php }?>/></div>
          <div class="ap-option-note"><?php _e('Check if you want logged in users to upload allowed media files','accesspress-anonymous-post');?></div>
        </div>
      </div>
    </div>
    <div class="ap-option-wrapper">
      <label class="ap-check-login"><?php _e('Check Login','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <div class="ap-option-checkbox-field">
          <div class="ap-checkbox-form"><input type="checkbox" name="login_check" value="1" <?php if($ap_settings['login_check']==1){?>checked="checked"<?php }?>/></div>
          <div class="ap-option-note"><?php _e('Check if you want admin login to submit a new post.','accesspress-anonymous-post');?></div>
        </div>
      </div>
    </div>
    <div class="ap-option-wrapper">
      <label><?php _e('Login Message','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <textarea name="login_message" rows="10" cols="41"><?php echo $this->output_converting_br($ap_settings['login_message']);?></textarea>
        <div class="ap-option-note  ap-option-width"><?php _e('Message to be displayed if the user is not logged in and you have checked admin login option to submit the post.','accesspress-anonymous-post');?></div>
      </div>
    </div>
    <div class="ap-option-wrapper">
      <label><?php _e('Login Link Text','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <input type="text" name="login_link_text" value="<?php echo $ap_settings['login_link_text'];?>"/>
        <div class="ap-option-note  ap-option-width"><?php _e('Text to be shown in login link  if the user is not logged in and you have checked admin login option to submit the post.','accesspress-anonymous-post');?></div>
      </div>
    </div>
    <div class="ap-option-wrapper">
      <label><?php _e('Assign Author','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <select name="post_author">
          <?php 
          $users = get_users();
          foreach($users as $user)
          {
            ?>
            <option value="<?php echo $user->ID;?>" <?php if($ap_settings['post_author']==$user->ID){?>selected="selected"<?php }?>><?php echo $user->data->user_nicename;?></option>
            <?php 
          }
          
          ?>
        </select>
      </div>
    </div>
    <div class="ap-option-wrapper">
      <label><?php _e('Login Link URL','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <input type="text" name="login_link_url" value="<?php echo isset($ap_settings['login_link_url'])?esc_url($ap_settings['login_link_url']):'';?>"/>
        <div class="ap-option-note  ap-option-width"><?php _e('Link for the login button. Default link is WordPress Login Page','accesspress-anonymous-post');?></div>
      </div>
    </div>
    <div class="ap-option-wrapper">
      <label class="ap-check-login"><?php _e('Auto Fill Logged in Author Details:','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <div class="ap-option-checkbox-field">
          <div class="ap-checkbox-form"><input type="checkbox" name="auto_author_details" value="1" <?php if(isset($ap_settings['auto_author_details']) && $ap_settings['auto_author_details']=='1'){?>checked="checked"<?php }?>/></div>
          <div class="ap-option-note"><?php _e('Check if you want to auto fill logged in author details','accesspress-anonymous-post');?></div>
        </div>
      </div>
    </div><!--ap-option-wrapper-->
    <div class="ap-option-wrapper">
      <label class="ap-check-login"><?php _e('Plugin Styles:','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <div class="ap-option-checkbox-field">
          <div class="ap-checkbox-form"><input type="checkbox" name="plugin_styles" value="1" <?php if($ap_settings['plugin_styles']=='1'){?>checked="checked"<?php }?>/></div>
          <div class="ap-option-note"><?php _e('Check if you want to use the plugin\'s basic styles in frontend form','accesspress-anonymous-post');?></div>
        </div>
      </div>
    </div><!--ap-option-wrapper-->
    <div class="ap-option-wrapper">
      <label><?php _e('Redirect URL','accesspress-anonymous-post')?></label>
      <div class="ap-option-field">
        <input type="text" name="redirect_url" value="<?php echo esc_url($ap_settings['redirect_url']);?>"/>
        <div class="ap-option-note ap-option-width"><?php _e('URL to be redirected after successful post submission. If kept blank, it will be redirected to same page.','accesspress-anonymous-post');?></div>
      </div>

    </div>
    <div class="ap-option-wrapper">
      <label><?php _e('Post Submission Message','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <textarea name="post_submission_message" rows="10" cols="41"><?php echo $this->output_converting_br($ap_settings['post_submission_message']);?></textarea>
        <div class="ap-option-note  ap-option-width"><?php _e('Message displayed after successful post submission.','accesspress-anonymous-post');?></div>
      </div>
    </div>
  </div>
</div>