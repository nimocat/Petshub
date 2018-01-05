<?php
/**
 * Template for generic post display.
 * @package themify
 * @since 1.0.0
 */
?>
<?php if(!is_single()){ global $more; $more = 0; } //enable more link ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php themify_post_before(); // hook ?>
<article id="post-<?php the_id(); ?>" <?php post_class( 'post clearfix' ); ?>>
	<?php themify_post_start(); // hook ?>

    <?php if($themify->unlink_title!='yes' || $themify->unlink_image!='yes'):?>
        <a href="<?php echo themify_get_featured_image_link(); ?>" data-post-permalink="yes" style="display: none;"></a>
    <?php endif;?>

	<?php if( 'below' != $themify->media_position ) get_template_part( 'includes/post-media', 'loop'); ?>

	<div class="post-content">
		<div class="post-content-inner">

			<?php if ( $themify->hide_date != 'yes' ): ?>
				<div class="post-date-wrap<?php echo $themify->inline_date?' post-date-inline':''?>">
					<time class="post-date entry-date updated" datetime="<?php echo esc_attr( the_time( 'o-m-d' ) ); ?>">
                                            <?php if($themify->inline_date):?>
                                                <span class="year"><?php the_time( get_option( 'date_format' ) ); ?></span>
                                            <?php else:?>
                                                <?php $format = themify_date_format();?>
                                                <?php foreach($format as $val):?>
                                                    <span class="<?php echo $val[1]?>"><?php the_time( $val[0] ); ?></span>
                                                <?php endforeach;?>
                                            <?php endif;?>
					</time>
				</div>
			<?php endif; //post date ?>

			<?php if($themify->hide_title != 'yes'): ?>
				<?php themify_post_title(); ?>
			<?php endif; //post title ?>

			<?php if ( $themify->hide_meta != 'yes' ) : ?>
				<p class="post-meta entry-meta">
					<?php if ( $themify->hide_meta_author != 'yes' ): ?>
						<span class="post-author"><?php echo themify_get_author_link() ?></span>
					<?php endif; ?>

					<?php if ( $themify->hide_meta_category != 'yes' ): ?>
						<?php the_terms( get_the_id(), 'category', ' <span class="post-category">', ', ', '</span>' ); ?>
					<?php endif; // meta category ?>

					<?php if ( $themify->hide_meta_tag != 'yes' ): ?>
						<?php the_terms( get_the_id(), 'post_tag', ' <span class="post-tag">', ', ', '</span>' ); ?>
					<?php endif; // meta tag ?>

					<?php if ( ! themify_get( 'setting-comments_posts' ) && comments_open() && $themify->hide_meta_comment != 'yes' ) : ?>
						<span class="post-comment"><?php comments_popup_link( '0', '1', '%' ); ?></span>
					<?php endif; // meta comments ?>
				</p>
				<!-- /post-meta -->
			<?php endif; //post meta ?>

			<?php if( 'below' == $themify->media_position ) get_template_part( 'includes/post-media', 'loop'); ?>

			<div class="entry-content">

				<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>

					<?php the_excerpt(); ?>

					<?php if( themify_check('setting-excerpt_more') ) : ?>

						<p><a href="<?php echo esc_url( get_permalink() ); ?>" class="more-link"><?php echo themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify') ?></a></p>

					<?php endif; ?>

				<?php elseif($themify->display_content == 'none'): ?>

				<?php else: ?>

					<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>

				<?php endif; //display content ?>

			</div><!-- /.entry-content -->

			<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

		</div>
		<!-- /.post-content-inner -->
	</div>
	<!-- /.post-content -->
	<?php themify_post_end(); // hook ?>

</article>
<!-- /.post -->
<?php themify_post_after(); // hook ?>
