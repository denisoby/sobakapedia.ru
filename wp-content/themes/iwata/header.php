<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>
		
		<meta http-equiv="Content-type" content="text/html;charset=<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
																		
		<title><?php wp_title('|', true, 'right'); ?></title>
		 
		<?php wp_head(); ?>
		<link rel="stylesheet" type="text/css" media="all" href="/wp-content/themes/iwata/custom.css">
	
	</head>
	
	<body <?php body_class(); ?>>
		
		<?php if (get_header_image() != '') : ?>
	
			<div class="header section bg-image" style="background-image: url(<?php header_image(); ?>);">
			
		<?php else : ?>
		
			<div class="header section">
		
		<?php endif; ?>
			
			<div class="cover bg-accent"></div>
		
			<div class="section-inner">
						
				<?php if ( get_bloginfo( 'title' ) ) : ?>
			
					<h2 class="blog-title">
						<a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?> &mdash; <?php echo esc_attr( get_bloginfo( 'description' ) ); ?>" rel="home"><?php echo esc_attr( get_bloginfo( 'title' ) ); ?></a>
					</h2>
					
				<?php endif; ?>
				
				<a class="search-toggle" title="<?php _e('Click to view the search field','iwata'); ?>" href="#">
					<span class="fa fw fa-search"></span>
				</a>
				
				<a class="nav-toggle hidden" title="<?php _e('Click to view the navigation','iwata'); ?>" href="#">
					<div class="bars">
						<div class="bar"></div>
						<div class="bar"></div>
						<div class="bar"></div>
					</div>
				</a> <!-- /nav-toggle -->
				
				<ul class="main-menu">
					
					<?php if ( has_nav_menu( 'primary' ) ) {
																		
						wp_nav_menu( array( 
						
							'container' => '', 
							'items_wrap' => '%3$s',
							'theme_location' => 'primary'
														
						) ); } else {
					
						wp_list_pages( array(
						
							'container' => '',
							'title_li' => ''
						
						));
						
					} ?>
					
				 </ul>
				
				 <div class="clear"></div>
			
			</div> <!-- /section-inner -->
							
		</div> <!-- /header -->
		
		<form method="get" class="header-search section hidden" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<div class="section-inner">
				<input class="search-field" type="search" placeholder="<?php _e('Type and press enter', 'iwata'); ?>" name="s" /> 
			</div> <!-- /section-inner -->
		</form> <!-- /header-search -->
		
		<ul class="mobile-menu hidden">			
			
			<?php if ( has_nav_menu( 'primary' ) ) {
																
				wp_nav_menu( array( 
				
					'container' => '', 
					'items_wrap' => '%3$s',
					'theme_location' => 'primary'
												
				) ); 
				
			} else {
			
				wp_list_pages( array(
				
					'container' => '',
					'title_li' => ''
				
				));
				
			} ?>
			
		</ul> <!-- /mobile-menu -->
		
		<form method="get" class="mobile-search section hidden" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input class="search-field" type="search" placeholder="<?php _e('Type and press enter', 'iwata'); ?>" name="s" /> 
			<a class="search-button" onclick="document.getElementById('search-form').submit(); return false;"><span class="fa fw fa-search"></span></a>
		</form> <!-- /mobile-search -->