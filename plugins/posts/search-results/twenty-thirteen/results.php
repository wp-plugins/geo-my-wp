<?php 
/**
 * Default Wordpress loop results page
 * @version 1.0
 * @author Eyal Fitoussi
 */
?>
<!--  Main results wrapper - wraps the paginations, map and results -->
<div class="gmw-results-wrapper gmw-results-wrapper-<?php echo $gmw['ID']; ?> gmw-pt-results-wrapper">
	
	<?php do_action( 'gmw_search_results_start' , $gmw, $post ); ?>
	
	<!-- results count -->
	<div class="gmw-results-count">
		<span><?php gmw_pt_within( $gmw, $sm=__( 'Showing', 'GMW' ), $om=__( 'out of', 'GMW' ), $rm=__( 'results', 'GMW' ) ,$wm=__( 'within', 'GMW' ), $fm=__( 'from','GMW' ), $nm=__( 'your location', 'GMW' ) ); ?></span>
	</div>
	
	<?php do_action( 'gmw_before_top_pagination' , $gmw, $post ); ?>
	
	<div class="gmw-pt-pagination-wrapper gmw-pt-top-pagination-wrapper">
		<!--  paginations -->
		<?php gmw_pt_per_page_dropdown( $gmw, '' ); ?><?php gmw_pt_paginations( $gmw ); ?>
	</div> 
		
	<!-- Map -->
	<?php gmw_results_map( $gmw ); ?>
	
	<div class="clear"></div>
	
	<?php do_action( 'gmw_search_results_before_loop' , $gmw, $post ); ?>
	
	<!--  Results wrapper -->
	<div class="gmw-posts-wrapper">
		
		<!--  this is where wp_query loop begins -->
		<?php while ( $gmw_query->have_posts() ) : $gmw_query->the_post(); ?>
			
			<!--  single results wrapper  -->
			<article id="post-<?php the_ID(); ?>" <?php post_class('wppl-single-result'); ?>>
				
				<?php do_action( 'gmw_posts_loop_post_start' , $gmw, $post ); ?>
				
				<header class="entry-header">
					<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
					<div class="entry-thumbnail">
						<?php the_post_thumbnail(); ?>
					</div>
					<?php endif; ?>
			
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?><span><?php echo gmw_pt_by_radius( $gmw, $post ); ?></span></a>
					</h1>
			
					<div class="entry-meta">
			
						<?php twentythirteen_entry_meta(); ?>
						<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
						<div>
							<?php echo $post->formatted_address; ?>
						</div>
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->
			
				
				<div class="entry-summary">
				
					<?php gmw_pt_excerpt( $gmw, $post ); ?>
					
					<div class="clear"></div>
					
					<?php gmw_pt_taxonomies( $gmw, $post ); ?>
    					
    				<?php gmw_pt_additional_info( $gmw, $post, $tag='div' ); ?>

				</div><!-- .entry-summary -->
				
				<footer class="entry-meta">
					
					<!-- Get directions -->	 	
    				<?php gmw_pt_directions( $gmw, $post, $title=__('Get Directions','GMW') ) ?>
    			
					<!--  Driving Distance -->
    				<?php gmw_pt_driving_distance( $gmw, $post, $class='wppl-driving-distance', $title=__( 'Driving: ', 'GMW' ) ); ?>
				</footer><!-- .entry-meta -->
				
				<?php do_action( 'gmw_posts_loop_post_end' , $gmw, $post ); ?>
			
			</article><!-- #post -->
		
		<?php endwhile;	 ?>
	</div>
	
	<?php do_action( 'gmw_search_results_after_loop' , $gmw, $post ); ?>
	
	<div class="gmw-pt-pagination-wrapper gmw-pt-bottom-pagination-wrapper">
		<!--  paginations -->
		<?php gmw_pt_per_page_dropdown( $gmw, '' ); ?><?php gmw_pt_paginations( $gmw ); ?>
	</div> 
	
</div> <!-- output wrapper -->

