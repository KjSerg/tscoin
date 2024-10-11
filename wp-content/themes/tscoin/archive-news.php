<?php
global $wp_query;
get_header();
$var             = variables();
$set             = $var['setting_home'];
$assets          = $var['assets'];
$url             = $var['url'];
$url_home        = $var['url_home'];
$id              = get_the_ID();
$isLighthouse    = isLighthouse();
$size            = isLighthouse() ? 'thumbnail' : 'full';
$default_image   = carbon_get_theme_option( 'news_image' );
$news_appearance = carbon_get_theme_option( 'news_appearance' );
$default_image   = $default_image ? _u( $default_image, 1 ) : $assets . 'img/head_img4.jpg';
$seo_text        = '';
$paged           = get_query_var( 'paged' ) ?: 1;
if ( function_exists( 'pll_current_language' ) ) {
	$language = pll_current_language();
	$seo_text = carbon_get_theme_option( "seo_text_$language" );
} else {
	$seo_text = carbon_get_theme_option( "seo_text" );
}
?>

    <div class="bg_frame lozad" data-background-image="<?php echo $default_image; ?>">
        <div class="bg_frame_bottom"></div>
    </div>
    <section class="section-head first_screen dark_bg animated_block">
        <div class="section_bg">
            <div class="screen_content">
                <div class="head_title"><?php _l( 'attention to details' ); ?></div>
            </div>
        </div>
        <div class="screen_content">
            <div class="main_title large ttu animation_up delay1"><?php _l( 'News' ); ?></div>
			<?php if ( $seo_text ): ?>
                <div class="simple_text larger_text animation_up delay2">
					<?php _t( $seo_text ); ?>
                </div>
			<?php endif; ?>
        </div>
    </section>
    <section class="section-articles remove_pt">
        <div class="screen_content">
            <div class="light_frame">
				<?php if ( $news_appearance == 'old' ): ?>
                    <div class="article_blocks">
						<?php if ( have_posts() ): while ( have_posts() ): the_post();
							$_id    = get_the_ID();
							$_title = get_the_title();
							?>
                            <div class="article_block">
                                <div class="article_block_img">
                                    <img class="lozad"
                                         data-src="<?php echo get_the_post_thumbnail_url() ?: $assets . 'img/article1.jpg'; ?>"
                                         alt="<?php echo $_title; ?>" width="267" height="267"/>
                                </div>
                                <div class="article_block_body">
                                    <div class="article_block_head">
                                        <div class="date">
											<?php echo get_the_date( 'd/m/Y' ); ?>
                                        </div>
                                        <div class="article_block_title">
											<?php echo $_title; ?>
                                        </div>
                                    </div>
                                    <div class="article_block_middle simple_text">
										<?php echo get_content_by_id( $_id ); ?>
                                    </div>
                                </div>
                            </div>
						<?php endwhile; else: ?>
                            <div class="main_title centered">
								<?php _l( 'Not found' ) ?>
                            </div>
						<?php endif; ?>
                    </div>
				<?php else: ?>
                    <div class="articles-list">
	                    <?php if ( have_posts() ): while ( have_posts() ): the_post();
		                    $_id    = get_the_ID();
		                    $_title = get_the_title();
                            the_article();
		                    endwhile; else: ?>
                            <div class="main_title centered">
			                    <?php _l( 'Not found' ) ?>
                            </div>
	                    <?php endif; ?>
                    </div>
				<?php endif; ?>
                <div class="pagination_block dark_bg">
                    <div class="pagination_count">
						<?php echo _l( 'Page', 1 ) . ' ' . $paged . ' ' . _l( 'of', 1 ) . ' ' . $wp_query->max_num_pages; ?>
                    </div>
					<?php if ( function_exists( 'wp_pagenavi' ) ) {
						$nav = str_replace(
							array(
								'<a',
								'</a>',
								'<span',
								'</span>',
								'wp-pagenavi',
								'current',
								'class="page',
								'nextpostslink',
								'previouspostslink',
							),
							array(
								'<li><a',
								'</a></li>',
								'<li><span',
								'</span></li>',
								'wp-pagenavi page-numbers pagination_list',
								'current page-numbers',
								'class="page page-numbers',
								'nextpostslink page-numbers',
								'previouspostslink page-numbers',
							),
							wp_pagenavi( array( 'echo' => 0 ) ) );
						echo $nav;
					} ?>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>