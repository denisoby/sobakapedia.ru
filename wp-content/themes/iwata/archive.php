<?php get_header(); ?>

<div class="content section">
	
	<div class="section-inner">
	
		<div class="page-title">
				
			<h4><?php if ( is_day() ) : ?>
				<?php printf( __( 'Date: %s', 'iwata' ), '' . get_the_date() . '' ); ?>
			<?php elseif ( is_month() ) : ?>
				<?php printf( __( 'Month: %s', 'iwata' ), '' . get_the_date( _x( 'F Y', 'F = Month, Y = Year', 'iwata' ) ) ); ?>
			<?php elseif ( is_year() ) : ?>
				<?php printf( __( 'Year: %s', 'iwata' ), '' . get_the_date( _x( 'Y', 'Y = Year', 'iwata' ) ) ); ?>
			<?php elseif ( is_category() ) : ?>
				<?php printf( __( 'Category: %s', 'iwata' ), '' . single_cat_title( '', false ) . '' ); ?>
			<?php elseif ( is_tag() ) : ?>
				<?php printf( __( 'Tag: %s', 'iwata' ), '' . single_tag_title( '', false ) . '' ); ?>
			<?php elseif ( is_author() ) : ?>
				<?php $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author)); ?>
				<?php printf( __( 'Author: %s', 'iwata' ), $curauth->display_name ); ?>
			<?php else : ?>
				<?php _e( 'Archive', 'iwata' ); ?>
			<?php endif; ?></h4>
			
			<?php
				$tag_description = tag_description();
				if ( ! empty( $tag_description ) )
					echo apply_filters( 'tag_archive_meta', '<div class="page-title-meta">' . $tag_description . '</div>' );
			?>
			
		</div> <!-- /page-title -->
		
		<?php if ( have_posts() ) : ?>
		
			<?php rewind_posts(); ?>
				
			<div class="posts" id="posts">
				
				<?php while ( have_posts() ) : the_post(); ?>
							
					<?php get_template_part( 'content', get_post_format() ); ?>
					
				<?php endwhile; ?>
								
			</div> <!-- /posts -->
			
			<?php iwata_archive_navigation(); ?>
					
		<?php endif; ?>
	
	</div> <!-- /section-inner -->

</div> <!-- /content -->

<?php get_footer(); ?>