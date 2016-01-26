<?php

if ( current_user_can('contributor') && !current_user_can('upload_files') )
	add_action('admin_init', 'allow_contributor_uploads');

function allow_contributor_uploads() {
	$contributor = get_role('contributor');
	$contributor->add_cap('upload_files');
}

add_action('admin_init', 'remove_contributor_delete_posts');

function remove_contributor_delete_posts() {
	$role = get_role( 'contributor' );
	$role->remove_cap( 'delete_posts' );
}

// remove image attributes and [CAPTION]
function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height|alt|class)="[^"]*?"\s/', "", $html );
   return $html;
}

add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );

// remove deafault quicktags
function remove_quicktags( $qtInit ) {
    $qtInit['buttons'] = 'link';
    return $qtInit;
}
add_filter('quicktags_settings', 'remove_quicktags');

// add more buttons to the html editor
function appthemes_add_quicktags() {
    if (wp_script_is('quicktags')){
?>
    <script type="text/javascript">
	QTags.addButton( 'eg_header_2', 'h2', '<h2>', '</h2>', 'none', 'Header 2 tag', 1 );
	QTags.addButton( 'eg_header_3', 'h3', '<h3>', '</h3>', 'none', 'Header 3 tag', 2 );
	QTags.addButton( 'eg_strong', 'Жирный', '<strong>', '</strong>', 'none', 'Strong tag', 3 );
	QTags.addButton( 'eg_em', 'Курсив', '<em>', '</em>', 'none', 'EM tag', 4 );
	QTags.addButton( 'eg_ul_template', 'Список НЕнумерованный','<ul>\n<li><p>Text</p></li>\n<li><p>Text</p></li>\n<li><p>Text</p></li>\n<li><p>Text</p></li>\n<li><p>Text</p></li>\n</ul>\n', '', 'none', 'UL tag', 5 );
	QTags.addButton( 'eg_ol_template', 'Список нумерованный','<ol>\n<li><p>Text</p></li>\n<li><p>Text</p></li>\n<li><p>Text</p></li>\n<li><p>Text</p></li>\n<li><p>Text</p></li>\n</ol>\n', '', 'none', 'OL tag', 6 );
	QTags.addButton( 'eg_p_simple', 'p', '<p>', '</p>', 'none', 'P simple tag', 7 );
	QTags.addButton( 'eg_ul_simple', 'ul', '<ul>\n', '\n</ul>', 'none', 'UL simple tag', 8 );
	QTags.addButton( 'eg_ol_simple', 'ol', '<ol>\n', '\n</ol>', 'none', 'OL simple tag', 9 );
	QTags.addButton( 'eg_li_simple', 'li', '<li><p>', '</p></li>', 'none', 'LI simple tag', 10 );
	QTags.addButton( 'eg_video_youtube', 'Видео', '[embed]', '[/embed]', 'none', 'Youtube video tag', 11 );
    </script>
<?php
    }
}
add_action( 'admin_print_footer_scripts', 'appthemes_add_quicktags' );

// Theme setup
add_action( 'after_setup_theme', 'iwata_setup' );

function iwata_setup() {
	
	// Automatic feed
	add_theme_support( 'automatic-feed-links' );
	
	// Post thumbnails
	add_theme_support( 'post-thumbnails' ); 
	add_image_size( 'post-image', 688, 9999 );
	
	//Post Formats
	add_theme_support( 'post-formats', array('aside','image','quote') );
		
	// Jetpack infinite scroll
	add_theme_support( 'infinite-scroll', array(
	    'container' => 'posts'
	) );
	
	// Custom header
	$args = array(
		'width'         => 1440,
		'height'        => 198,
		'uploads'       => true,
		'header-text'  	=> false
		
	);
	add_theme_support( 'custom-header', $args );
	
	// Custom background
	add_theme_support( "custom-background", array('default-color' => 'f6f6f6') ); 
	
	// Add nav menu
	register_nav_menu( 'primary', __('Primary Menu','iwata') );
	
	// Title Tag
	add_theme_support( "title-tag" );
	
	// Make the theme translation ready
	load_theme_textdomain('iwata', get_template_directory() . '/languages');
	
	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable($locale_file) )
	  require_once($locale_file);
	
}

// Register and enqueue Javascript files
function iwata_load_javascript_files() {

	if ( !is_admin() ) {
		wp_enqueue_script( 'iwata_doubletap', get_template_directory_uri().'/js/doubletaptogo.js', array('jquery'), '', true );
		wp_enqueue_script( 'iwata_global', get_template_directory_uri().'/js/global.js', array('jquery'), '', true );
		
		if ( is_singular() && get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
		
	}
}

add_action( 'wp_enqueue_scripts', 'iwata_load_javascript_files' );


// Register and enqueue styles
function iwata_load_style() {
	if ( !is_admin() ) {
	    wp_enqueue_style('iwata_googleFonts', '//fonts.googleapis.com/css?family=Fira+Sans:400,500,700,400italic,500italic,700italic' );
	    wp_enqueue_style('iwata_fontawesome', get_template_directory_uri() . '/fa/css/font-awesome.css' );
		wp_enqueue_style('iwata_style', get_stylesheet_uri() );		
	}
}
add_action('wp_print_styles', 'iwata_load_style');


// Add editor styles
function iwata_add_editor_styles() {
    add_editor_style( 'iwata-editor-styles.css' );
    $font_url = '//fonts.googleapis.com/css?family=Fira+Sans:400,500,700,400italic,500italic,700italic';
    add_editor_style( str_replace( ',', '%2C', $font_url ) );
}
add_action( 'init', 'iwata_add_editor_styles' );


// Set content-width
if ( ! isset( $content_width ) ) $content_width = 640;


// Check whether the browser supports javascript
function iwata_html_js_class () {
    echo '<script>document.documentElement.className = document.documentElement.className.replace("no-js","js");</script>'. "\n";
}
add_action( 'wp_head', 'iwata_html_js_class', 1 );


// Add classes to next_posts_link and previous_posts_link
function iwata_posts_link_attributes_1() { return 'class="archive-nav-older"'; }
function iwata_posts_link_attributes_2() { return 'class="archive-nav-newer"'; }
add_filter('next_posts_link_attributes', 'iwata_posts_link_attributes_1');
add_filter('previous_posts_link_attributes', 'iwata_posts_link_attributes_2');


// Custom more-link text
function iwata_custom_more_link( $more_link, $more_link_text ) {
	return str_replace( $more_link_text, __('Read more', 'iwata'), $more_link );
}
add_filter( 'the_content_more_link', 'iwata_custom_more_link', 10, 2 );


// Style the admin area
function iwata_admin_style() { 
   echo '
<style type="text/css">

	#postimagediv #set-post-thumbnail img {
		max-width: 100%;
		height: auto;
	}

</style>';
}
add_action('admin_head', 'iwata_admin_style');


// Set excerpt length
function iwata_custom_excerpt_length( $length ) {
	return 33;
}
add_filter( 'excerpt_length', 'iwata_custom_excerpt_length', 999 );


// Set excerpt ender
function iwata_new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'iwata_new_excerpt_more');


// Add post class to pages
function iwata_post_class_to_pages( $classes ) {
	global $post;
	if ( is_search() ) {
		$classes[] = 'post';
	}
	return $classes; 
}
add_filter( 'post_class', 'iwata_post_class_to_pages' );


// Iwata post meta function
function iwata_post_meta() { ?>

	<?php if ( !is_page() || comments_open() || current_user_can('edit_posts') ) : ?>
	
		<div class="post-meta">
				
			<?php if ( is_sticky() ) : ?>
				<p class="post-sticky is-sticky"><span class="fa fw fa-thumb-tack"></span><?php echo __('Sticky','iwata') . '<span> ' . __('Post','iwata') . '</span>'; ?></p>
			<?php endif; ?>
			
			<?php if ( !is_page() ) : ?>
				<p class="post-date"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><span class="fa fw fa-calendar"></span><?php the_time(get_option('date_format')); ?></a></p>
			<?php endif; ?>
			
			<?php if ( comments_open() ) : ?>
				<p class="post-comments">
					<?php comments_popup_link( '<span class="fa fw fa-comment"></span>' . __('Add Comment','iwata'), '<span class="fa fw fa-comment"></span>1 ' . __('Comment', 'iwata'), '<span class="fa fw fa-comment"></span>% ' . __('Comments', 'iwata') ); ?>
				</p>
			<?php endif; ?>
			
			<?php edit_post_link( '<span class="fa fw fa-cog"></span>' . __('Edit','iwata'), '<p class="post-edit">', '</p>'); ?>
			
		</div> <!-- /post-meta -->
		
	<?php endif;
}


// Iwata archive navigation function
function iwata_archive_navigation() {
	
	global $wp_query;
	
	if ( $wp_query->max_num_pages > 1 ) : ?>
				
		<div class="archive-nav">
			
			<?php echo get_next_posts_link( __('Older','iwata') . '<span> ' . __('Posts','iwata') . '</span> &raquo;' ); ?>
			
			<?php global $paged; ?>
			
			<div class="page-number"><?php printf( __('Page %s of %s','iwata'), $paged, $wp_query->max_num_pages ); ?></div>
						
			<?php echo get_previous_posts_link( '&laquo; ' . __('Newer','iwata') . '<span> ' .__('Posts','iwata') . '</span>' ); ?>
			
			<div class="clear"></div>
				
		</div> <!-- /archive-nav-->
						
	<?php endif;
}


// Change the Jetpack infinite scroll "Older posts" text
function iwata_custom_jetpack_infinite_more() { 
	if ( is_home() || is_archive() ) { ?>
	    <script type="text/javascript">
		    //<![CDATA[
		    infiniteScroll.settings.text = "<?php _e('Load More','iwata'); ?>";
		    //]]>
	    </script> 
	    <?php
	}
}
add_action( 'wp_footer', 'iwata_custom_jetpack_infinite_more', 3 );


// Iwata comment function
if ( ! function_exists( 'iwata_comment' ) ) :
function iwata_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
	
		<?php __( 'Pingback:', 'iwata' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'iwata' ), '<span class="edit-link">', '</span>' ); ?>
		
	</li>
	<?php
			break;
		default :
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	
		<div id="comment-<?php comment_ID(); ?>" class="comment">
		
			<div class="comment-header">
										
				<h4><?php echo get_comment_author_link(); ?></h4>
				
				<div class="comment-meta">
<a class="comment-date-link" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>" title="<?php echo get_comment_date() . ' at ' . get_comment_time(); ?>"><?php echo get_comment_date() . '<span> ' . __('at','iwata') . ' ' . get_comment_time() . '</span>'; ?></a>
					<?php if ( $comment->user_id === $post->post_author ) : ?>
					
						<div class="post-author-text"><span>&bull;</span><?php _e('Post Author','iwata'); ?></div>
					
					<?php endif; ?>
					
				</div>
			
			</div> <!-- /comment-header -->

			<div class="comment-content post-content">
			
				<?php if ( '0' == $comment->comment_approved ) : ?>
				
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'iwata' ); ?></p>
					
				<?php endif; ?>
			
				<?php comment_text(); ?>
				
			</div><!-- /comment-content -->
			
			<div class="comment-actions">
				
				<?php 
					comment_reply_link( array_merge( $args, 
					array( 
						'reply_text' 	=>  	'<span class="fa fw fa-reply"></span>' . __( 'Reply', 'iwata' ), 
						'depth'			=> 		$depth, 
						'max_depth' 	=> 		$args['max_depth'],
						'before'		=>		'<p class="comment-reply">',
						'after'			=>		'</p>'
						) 
					) ); 
				?>
				
				<?php edit_comment_link( '<span class="fa fw fa-cog"></span>' . __( 'Edit', 'iwata' ), '<p class="comment-edit">', '</p>' ); ?>
												
			</div> <!-- /comment-actions -->
						
		</div>
				
	<?php
		break;
	endswitch;
}
endif;


// Iwata theme options
class iwata_Customize {

   public static function iwata_register ( $wp_customize ) {
   
      //1. Define a new section (if desired) to the Theme Customizer
      $wp_customize->add_section( 'iwata_options', 
         array(
            'title' => __( 'Options for Iwata', 'iwata' ), //Visible title of section
            'priority' => 35, //Determines what order this appears in
            'capability' => 'edit_theme_options', //Capability needed to tweak
            'description' => __('Allows you to customize theme settings for Iwata.', 'iwata'), //Descriptive tooltip
         ) 
      );
      
      //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'accent_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#00A0D7', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
      		'sanitize_callback' => 'sanitize_hex_color'
         ) 
      );
      
      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'iwata_accent_color', //Set a unique ID for the control
         array(
            'label' => __( 'Accent Color', 'iwata' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'accent_color', //Which setting to load and manipulate (serialized is okay)
            'priority' => 10, //Determines the order this control appears in for the specified section
         ) 
      ) );
      
      //4. We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
      $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
      $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
   }

   public static function iwata_header_output() {
      ?>
      
	      <!-- Customizer CSS --> 
	      
	      <style type="text/css">
	           <?php self::iwata_generate_css('body a', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('body a:hover', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.bg-accent', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.main-menu ul a:hover', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-title a:hover', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-content a', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-content a:hover', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-content blockquote:before', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-content a.more-link,', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-content input[type="submit"]', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-content input[type="reset"]', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-content input[type="button"]', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.post-content fieldset legend', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.comment-form input[type="submit"]', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('#infinite-handle span', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.page-links a:hover', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.bypostauthor .avatar', 'border-color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.comment-actions a', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.comment-actions a:hover', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.comment-header h4 a:hover', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('#cancel-comment-reply-link', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.comments-nav a:hover', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.bypostauthor > .comment .avatar-container', 'background', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.to-the-top:hover', 'color', 'accent_color'); ?>
	           <?php self::iwata_generate_css('.nav-toggle.active .bar', 'background', 'accent_color'); ?>
	      </style> 
	      
	      <!--/Customizer CSS-->
	      
      <?php
   }
   
   public static function iwata_live_preview() {
      wp_enqueue_script( 
           'iwata-themecustomizer', // Give the script a unique ID
           get_template_directory_uri() . '/js/theme-customizer.js', // Define the path to the JS file
           array(  'jquery', 'customize-preview' ), // Define dependencies
           '', // Define a version (optional) 
           true // Specify whether to put in footer (leave this true)
      );
   }

   public static function iwata_generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true ) {
      $return = '';
      $mod = get_theme_mod($mod_name);
      if ( ! empty( $mod ) ) {
         $return = sprintf('%s { %s:%s; }',
            $selector,
            $style,
            $prefix.$mod.$postfix
         );
         if ( $echo ) {
            echo $return;
         }
      }
      return $return;
    }
}

// Setup the Theme Customizer settings and controls...
add_action( 'customize_register' , array( 'iwata_Customize' , 'iwata_register' ) );

// Output custom CSS to live site
add_action( 'wp_head' , array( 'iwata_Customize' , 'iwata_header_output' ) );

// Enqueue live preview javascript in Theme Customizer admin screen
add_action( 'customize_preview_init' , array( 'iwata_Customize' , 'iwata_live_preview' ) );

?>