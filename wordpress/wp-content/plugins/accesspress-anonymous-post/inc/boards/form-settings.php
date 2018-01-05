<div class="ap-tabs-board" id="board-form-settings" style="display: none;">
  <div class="ap-form-config">
    <h2><?php _e('Form field configurations','accesspress-anonymous-post');?></h2>
    <div class="ap-tab-wrapper">
      <div class="ap-option-wrapper">
        <div class="ap-form-configuration-wrapper">
         <!--Post Title-->
         <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Post Title','accesspress-anonymous-post');?></label>
          </div><!--ap-fioelds-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_fields[]" value="post_title" checked="checked" onclick="return false;"/><span><?php _e('Show on form','accesspress-anonymous-post');?></span></label>
            </div>
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="post_title" checked="checked" onclick="return false;"/><span><?php _e('Required','accesspress-anonymous-post');?></span></label>
               
            </div>
            <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[post_title]" placeholder="<?php _e('Post Title Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['post_title'])?esc_attr($ap_settings['form_required_message']['post_title']):'';?>" class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
         </div><!--ap-each-config-wrapper-->
        <!--Post Title Ends--> 

        <!--Post content-->
        <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Post Content','accesspress-anonymous-post');?></label>
          </div><!--ap-fioelds-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_fields[]" value="post_content" checked="checked" onclick="return false;"/><span><?php _e('Show on form','accesspress-anonymous-post');?></span></label>
            </div>
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="post_content" checked="checked" onclick="return false;"/><span><?php _e('Required','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[post_content]" placeholder="<?php _e('Post Content Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['post_content'])?esc_attr($ap_settings['form_required_message']['post_content']):'';?>"  class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
        </div><!--ap-each-config-warpper-->
        <!--Post content ends-->

        <!--Post Excerpt-->
        <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Post Excerpt','accesspress-anonymous-post');?></label>
          </div><!--ap-fioelds-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_fields[]" value="post_excerpt" <?php if(in_array('post_excerpt',$ap_settings['form_included_fields'])){?>checked="checked"<?php }?>/><span><?php _e('Show on form','accesspress-anonymous-post');?></span></label>
            </div>
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="post_excerpt" <?php if(in_array('post_excerpt',$form_required_fields)){?>checked="checked"<?php }?>/><span><?php _e('Required','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[post_excerpt]" placeholder="<?php _e('Excerpt Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['post_excerpt'])?esc_attr($ap_settings['form_required_message']['post_excerpt']):'';?>"   class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
        </div><!--ap-each-config-wrapper-->
        <!--Post Excerpt Ends-->

        <!--Post Image-->
        <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Post Image','accesspress-anonymous-post');?></label>
          </div><!--ap-fioelds-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_fields[]" value="post_image" <?php if(in_array('post_image',$ap_settings['form_included_fields'])){?>checked="checked"<?php }?>/><span><?php _e('Show in form','accesspress-anonymous-post')?></span></label>
            </div>
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="post_image" <?php if(in_array('post_image',$form_required_fields)){?>checked="checked"<?php }?>/><span><?php _e('Required','accesspress-anonymous-post')?></span></label>
            </div>
             <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[post_image]" placeholder="<?php _e('Post Image Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['post_image'])?esc_attr($ap_settings['form_required_message']['post_image']):'';?>"   class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
        </div><!--ap-each-config-wrapper-->
        <!--Post Image Ends-->

        <!--Author Name-->
        <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Author Name','accesspress-anonymous-post');?></label>
          </div><!--ap-fioelds-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_fields[]" value="author_name" <?php if(in_array('author_name',$ap_settings['form_included_fields'])){?>checked="checked"<?php }?>/><span><?php _e('Show on form','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="author_name" <?php if(in_array('author_name',$form_required_fields)){?>checked="checked"<?php }?>/><span><?php _e('Required','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[author_name]" placeholder="<?php _e('Author Name Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['author_name'])?esc_attr($ap_settings['form_required_message']['author_name']):'';?>"  class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
        </div><!--ap-each-config-wrapper-->
        <!--Author Name ends-->

        <!--Author URL-->
        <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Author URL','accesspress-anonymous-post');?></label>
          </div><!--ap-fields-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_fields[]" value="author_url" <?php if(in_array('author_url',$ap_settings['form_included_fields'])){?>checked="checked"<?php }?>/><span><?php _e('Show on form','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="author_url" <?php if(in_array('author_url',$form_required_fields)){?>checked="checked"<?php }?>/><span><?php _e('Required','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[author_url]" placeholder="<?php _e('Author URL Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['author_url'])?esc_attr($ap_settings['form_required_message']['author_url']):'';?>"  class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
        </div><!--ap-each-config-wrapper-->
        <!--Author URL ends-->

        <!--Author Email-->
        <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Author Email','accesspress-anonymous-post');?></label>
          </div><!--ap-fields-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_fields[]" value="author_email" <?php if(in_array('author_email',$ap_settings['form_included_fields'])){?>checked="checked"<?php }?>/><span><?php _e('Show on form','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="author_email" <?php if(in_array('author_email',$form_required_fields)){?>checked="checked"<?php }?>/><span><?php _e('Required','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[author_email]" placeholder="<?php _e('Author Email Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['author_email'])?esc_attr($ap_settings['form_required_message']['author_email']):'';?>"  class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
        </div><!--ap-each-config-wrapper-->
        <!--Author Email ends-->

        <!--Taxanomies-->

        <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Post Category','accesspress-anonymous-post');?></label>
          </div><!--ap-fioelds-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_taxonomy[]" value="category"  <?php if(in_array('category', $ap_settings['form_included_taxonomy'])){?>checked="checked"<?php }?> /><span><?php _e('Show on form','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="category" <?php if(in_array('category',$form_required_fields)){?>checked="checked"<?php }?>/><span><?php _e('Required','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[category]" placeholder="<?php _e('Post Category Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['category'])?esc_attr($ap_settings['form_required_message']['category']):'';?>"  class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
        </div><!--ap-each-config-wrapper-->

        <div class="ap-each-config-wrapper">
          <div class="ap-fields-label">
            <label><?php _e('Post Tags','accesspress-anonymous-post');?></label>
          </div><!--ap-fioelds-label-->
          <div class="ap-fields-configurations">
            <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_included_taxonomy[]" value="post_tag"  <?php if(in_array('post_tag', $ap_settings['form_included_taxonomy'])){?>checked="checked"<?php }?> /><span><?php _e('Show on form','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
              <label><input type="checkbox" name="form_required_fields[]" value="post_tag" <?php if(in_array('post_tag',$form_required_fields)){?>checked="checked"<?php }?>/><span><?php _e('Required','accesspress-anonymous-post');?></span></label>
            </div>
             <div class="ap-included-single-wrap">
                 <input type="text" name="form_required_message[post_tag]" placeholder="<?php _e('Post Tags Required Message','accesspress-anonymous-post');?>" value="<?php echo isset($ap_settings['form_required_message']['post_tag'])?esc_attr($ap_settings['form_required_message']['post_tag']):'';?>"  class="ap-required-message"/>
            </div>
          </div><!--ap-fields-configurations-->
        </div><!--ap-each-config-wrapper-->

        <!--Taxanomies ends-->

        <div class="ap-option-note ap-options-width"><?php _e('Post Title and Post Content are mandatory.','accesspress-anonymous-post');?></div>
      </div><!--ap-form-configuration-wrapper-->
    </div><!--ap-option-wrapper-->
  </div>
  <div class="line"></div>
  <div class="seperator"></div>
  <div class="ap-tab-wrapper">
    <div class="ap-option-wrapper">
      <label><?php _e('Post Categories','accesspress-anonymous-post');?></label>
      <div class="ap-option-field">
        <select name="post_category">
          <?php 
        /**
         * Get all the terms of the post category
         * */
        $categories = get_terms('category',array('hide_empty'=>0,'order'=>'ASC','orderby'=>'id'));
        if(!empty($categories))
        {
          foreach($categories as $category)
          {
            ?>
            <option value="<?php echo $category->term_id;?>" <?php if($ap_settings['post_category']==$category->term_id){?>selected="selected"<?php }?>><?php echo $category->name;?></option>
            <?php 
          }
        }
        ?>
      </select>
      <div class="ap-option-note ap-option-width"><?php _e('Choose any  only if you don\'t include to show the category selecting options in the form','accesspress-anonymous-post');?></div>
    </div>
  </div>
  <div class="ap-option-wrapper">
    <label><?php _e('Post Content Editor Type:','accesspress-anonymous-post');?></label>
    <div class="ap-option-field">
      <select name="editor_type">
        <option value="simple" <?php if($ap_settings['editor_type']=='simple'){?>selected="selected"<?php }?>>Simple Text Box</option>
        <option value="rich" <?php if($ap_settings['editor_type']=='rich'){?>selected="selected"<?php }?>>Rich Text Editor</option>
        <option value="visual" <?php if($ap_settings['editor_type']=='visual'){?>selected="selected"<?php }?>>Visual Editor</option>
        <option value="html" <?php if($ap_settings['editor_type']=='html'){?>selected="selected"<?php }?>>HTML Editor</option>
      </select>
    </div>
  </div>
  <div class="ap-option-wrapper">
    <label><?php _e('Editor Size','accesspress-anonymous-post');?></label>
    <div class="ap-option-field">
      <input type="text" name="editor_size" value="<?php echo (isset($ap_settings['editor_size']))?$ap_settings['editor_size']:'';?>"/>
      <div class="ap-option-note ap-option-width"><?php _e('Please enter the size of editor in number of rows.Default number of rows is 10','accesspress-anonymous-post');?></div>
    </div>
  </div>
</div>
</div><!--Form Configurations-->
<div class="line"></div>
<!--Form Labels-->
<div class="ap-form-labels">
  <h2><?php _e('Form Labels','accesspress-anonymous-post');?></h2>
  <div class="ap-tab-wrapper">
          <!--Post Title--->
          <div class="ap-option-wrapper">
            <label><?php _e('Post Title','accesspress-anonymous-post')?></label>
            <div class="ap-option-field">
                <input type="text" name="post_title_label" value="<?php echo esc_attr($ap_settings['post_title_label']);?>"/>
            </div>
          </div>
          <!--Post Title Ends-->
          
          <!--Post content-->
          <div class="ap-option-wrapper">
            <label><?php _e('Post Content','accesspress-anonymous-post')?></label>
            <div class="ap-option-field">
              <input type="text" name="post_content_label" value="<?php echo  esc_attr($ap_settings['post_content_label']);?>"/>
            </div>
          </div>
          <!--Post content ends-->
          
          <!--Post Excerpt -->
          <div class="ap-option-wrapper">
            <label><?php _e('Post Excerpt','accesspress-anonymous-post')?></label>
            <div class="ap-option-field">
              <input type="text" name="post_excerpt_label" value="<?php echo  esc_attr($ap_settings['post_excerpt_label']);?>"/>
            </div>
          </div>
          <!--Post Excerpt ends-->
          
          <!--Post Image -->
          <div class="ap-option-wrapper">
            <label><?php _e('Post Image','accesspress-anonymous-post')?></label>
            <div class="ap-option-field">
              <input type="text" name="post_image_label" value="<?php echo  esc_attr($ap_settings['post_image_label']);?>"/>
            </div>
          </div>
          <!--Post Image ends-->
          
          <!--Post Taxonomy-->
          <div class="ap-option-wrapper">
            <label><?php _e('Post Category','accesspress-anonymous-post');?></label>
            <div class="ap-option-field">
              <input type="text" name="category_label" value="<?php echo  esc_attr($ap_settings['category_label'])?>"/>
            </div>
          </div>
          
          <div class="ap-option-wrapper">
            <label><?php _e('Post Tags','accesspress-anonymous-post');?></label>
            <div class="ap-option-field">
              <input type="text" name="post_tag_label" value="<?php echo  esc_attr($ap_settings['post_tag_label'])?>"/>
            </div>
          </div>
          <!--Post Taxonomy ends-->

          <!--Author Name-->
          <div class="ap-option-wrapper">
            <label><?php _e('Author Name','accesspress-anonymous-post');?></label>
            <div class="ap-option-field">
              <input type="text" name="author_name_label" value="<?php echo  esc_attr($ap_settings['author_name_label']);?>"/>
            </div>
          </div>
          <!--Author Name ends-->
          
          <!--Author URL-->
          <div class="ap-option-wrapper">
            <label><?php _e('Author URL','accesspress-anonymous-post');?></label>
            <div class="ap-option-field">
              <input type="text" name="author_url_label" value="<?php echo  esc_attr($ap_settings['author_url_label']);?>"/>
            </div>
          </div>
          <!--Author URL ends-->
          
          <!--Author Email-->
          <div class="ap-option-wrapper">
            <label><?php _e('Author Email','accesspress-anonymous-post');?></label>
            <div class="ap-option-field">
              <input type="text" name="author_email_label" value="<?php echo  esc_attr($ap_settings['author_email_label']);?>"/>
            </div>
          </div>
          <!--Author Email Ends-->
          
          <!--Submit Button-->
          <div class="ap-option-wrapper">
            <label><?php _e('Submit Button','accesspress-anonymous-post');?></label>
            <div class="ap-option-field">
              <input type="text" name="post_submit_label" value="<?php echo esc_attr($ap_settings['post_submit_label']);?>"/>
            </div>
          </div>
          <!--Submit Button-->

          <div class="ap-option-note ap-option-width"><?php _e('These field will only show up in frontend if you have checked these fields in Form Field Configurations secton above.','accesspress-anonymous-post');?></div>    
        </div>
      </div><!--ap-form-label-->
      <!--Form Labels-->

    </div>