<?php
get_header();
$var               = variables();
$set               = $var['setting_home'];
$assets            = $var['assets'];
$url               = $var['url'];
$url_home          = $var['url_home'];
$id                = get_the_ID();
$default_image     = get_the_post_thumbnail_url() ?: $assets . 'img/head_img6.jpg';
$news_item_image   = carbon_get_post_meta( $id, 'news_item_image' );
$news_item_banner  = carbon_get_post_meta( $id, 'news_item_banner' );
$news_item_gallery = carbon_get_post_meta( $id, 'news_item_gallery' );
$img               = $news_item_banner ? _u( $news_item_banner, 1 ) : $default_image;
?>


	<div class="bg_frame lozad " data-background-image="<?php echo $img; ?>">
		<div class="bg_frame_bottom"></div>
	</div>

	<section class="section-head first_screen single-news-section">
		<div class="section_bg">
			<div class="screen_content">
				<div class="head_title"><?php _l( 'attention to details' ); ?></div>
			</div>
		</div>
		<div class="screen_content">
			<div class="light_frame">
				<div class="single-news-container">
					<?php if ( $news_item_image ): ?>
						<div class="single-news__banner">
							<img src="<?php _u( $news_item_image ) ?>" alt="">
						</div>
					<?php endif; ?>
                    <div class="articles-item__date">
						<?php echo get_the_date( 'd/m/Y' ); ?>
                    </div>
					<div class="single-news__title">
						<h1>
							<?php echo get_the_title() ?>
						</h1>
					</div>
					<div class="single-news__text">
						<div class="simple_text">
							<?php the_post();
							the_content(); ?>
						</div>
					</div>
					<?php if ( $news_item_gallery ): ?>
						<div class="single-news__gallery">
							<?php foreach ( $news_item_gallery as $image ): ?>
								<div>
									<div class="single-news__image">
										<img src="<?php _u( $image ) ?>" alt="">
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>




<?php get_footer(); ?>