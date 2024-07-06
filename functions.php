<?php
// Thanks to https://gist.github.com/hmowais
// PLEASE CHECK THIS CODE IN DEV MODE FIRST ಠ⁠_⁠ಠ

// Update OFF
// Remove WP Updates
function remove_core_updates(){
global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
//add_filter('pre_site_transient_update_core','remove_core_updates');
add_filter('pre_site_transient_update_plugins','remove_core_updates');
add_filter('pre_site_transient_update_themes','remove_core_updates');

/* cleaup*/

remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
remove_action( 'wp_head', 'index_rel_link' ); // index link
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version

// disbale embed
function disable_embeds_code_init() {
	// Remove the REST API endpoint.
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );
	// Turn off oEmbed auto discovery.
	add_filter( 'embed_oembed_discover', '__return_false' );
	// Don't filter oEmbed results.
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
	// Remove oEmbed discovery links.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );
	remove_action( 'wp_head', 'wlwmanifest_link');
	// Remove all embeds rewrite rules.
	add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
	// Remove filter of the oEmbed result before any HTTP requests are made.
	remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
   }
   add_action( 'init', 'disable_embeds_code_init', 9999 );
   function disable_embeds_tiny_mce_plugin($plugins) {
	   return array_diff($plugins, array('wpembed'));
   }
   function disable_embeds_rewrites($rules) {
	   foreach($rules as $rule => $rewrite) {
		   if(false !== strpos($rewrite, 'embed=true')) {
			   unset($rules[$rule]);
		   }
	   }
	   return $rules;
   }
   add_action( 'after_setup_theme', 'prefix_remove_unnecessary_tags' );

   function prefix_remove_unnecessary_tags(){
   
	   // REMOVE WP EMOJI
	   remove_action('wp_head', 'print_emoji_detection_script', 7);
	   remove_action('wp_print_styles', 'print_emoji_styles');
   
	   remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	   remove_action( 'admin_print_styles', 'print_emoji_styles' );
   
   
	   // remove all tags from header
	   remove_action( 'wp_head', 'rsd_link' );
	   remove_action( 'wp_head', 'wp_generator' );
	   remove_action( 'wp_head', 'feed_links', 2 );
	   remove_action( 'wp_head', 'index_rel_link' );
	   remove_action( 'wp_head', 'wlwmanifest_link' );
	   remove_action( 'wp_head', 'feed_links_extra', 3 );
	   remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	   remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	   remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
	   remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
	   remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	   remove_action( 'wp_head',      'rest_output_link_wp_head'              );
	   remove_action( 'wp_head',      'wp_oembed_add_discovery_links'         );
	   remove_action( 'template_redirect', 'rest_output_link_header', 11 );
   
	   // language
	   add_filter('multilingualpress.hreflang_type', '__return_false');
   }

   remove_action( 'wp_head', 'wp_resource_hints', 2 );

   add_filter( 'show_recent_comments_widget_style', function() { return false; });


   add_filter( 'wp_default_scripts', $af = static function( &$scripts) {
    if(!is_admin()) {
        $scripts->remove( 'jquery');
        $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.12.4' );
    }    
}, PHP_INT_MAX );
unset( $af );

// add_action('after_setup_theme', 'remove_admin_bar');
/* Disable WordPress Admin Bar for all users */
function wpc_show_admin_bar() {
	return true;
  }
  add_filter('show_admin_bar' , 'wpc_show_admin_bar');

  add_filter('use_block_editor_for_post', '__return_false', 10);

// add_filter( 'wp_get_attachment_image_attributes', 'gs_change_attachment_image_markup' );
function gs_change_attachment_image_markup($attributes){
if (isset($attributes['src'])) {
	if(!is_admin()){
	$attributes['loading'] = "lazy";
	$attributes['data-imgurl'] = $attributes['src'];
	$attributes['src'] = get_template_directory_uri().'/img/load.svg';
	}
}
	//$attributes['class'] .= ' lazyload ';
return $attributes;
}
add_filter('nav_menu_item_id', '__return_false');
add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
function my_css_attributes_filter($var) {
  return is_array($var) ? array_intersect($var, array('current-menu-item')) : '';
}
//start add .html in URL in pages
//  add_action('init', 'html_page_permalink', -1);

register_activation_hook(__FILE__, 'cvf_active');
register_deactivation_hook(__FILE__, 'cvf_deactive');
function html_page_permalink() {
       global $wp_rewrite;
        if ( !strpos($wp_rewrite->get_page_permastruct(), '.html')){
        $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
    }
 }
add_filter('user_trailingslashit', 'no_page_slash',66,2);
function no_page_slash($string, $type){
      global $wp_rewrite;
	  
    if ($wp_rewrite->using_permalinks() && $wp_rewrite->use_trailing_slashes==true && $type == 'page' && $type == 'single'){
        return untrailingslashit($string);
    } else {
        return $string;
    }
 }
function cvf_active() {
    global $wp_rewrite;
    
    if ( !strpos($wp_rewrite->get_page_permastruct(), '.html')){
        $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
    }
    $wp_rewrite->flush_rules();
 }
function cvf_deactive() {
    global $wp_rewrite;
     $wp_rewrite->page_structure = str_replace(".html","",$wp_rewrite->page_structure);
    $wp_rewrite->flush_rules();
 }
//end add .html in URL in pages


//Disable WP Admin Bar Removal
show_admin_bar( false );
if ( ! current_user_can( 'manage_options' ) ) {
    show_admin_bar( false );
}
add_filter('show_admin_bar', '__return_false');



//Hide Front-End Admin Bar Including 32 px Spacing

add_action('get_header', 'remove_admin_login_header');
function remove_admin_login_header() {
    remove_action('wp_head', '_admin_bar_bump_cb');
}

function remove_core_updates(){
	global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
	}
	add_filter('pre_site_transient_update_core','remove_core_updates');
	add_filter('pre_site_transient_update_plugins','remove_core_updates');
	add_filter('pre_site_transient_update_themes','remove_core_updates');




	// removed span and adding bootstrap classes
function wpcf7_form_elements( $elements ) { 

	// var_dump($elements);
	// $elements = preg_replace('/<(span).*?class="\s*(?:.*\s)?wpcf7-form-control-wrap(?:\s[^"]+)?\s*"[^\>]*>(.*)<\/\1>/i', '\2', $elements);

     $elements = str_replace('<br />', '', $elements);
    $elements = str_replace('<p>', '<div class="forms_inner wpcf7-form-control-wrap">', $elements);
     $elements = str_replace('</p>', '</div>', $elements);
    //  $elements = str_replace('wpcf7-form-control', 'wpcf7-form-control-wrap form-control', $elements);
    //  $elements = str_replace('wpcf7-submit', 'wpcf7-submit btn btn-outline-primary', $elements);
     
    // make filter magic happen here... 
    return $elements; 
}; 
         
// add the filter 
add_filter( 'wpcf7_form_elements', 'wpcf7_form_elements', 10, 1 ); 


function add_defer_attribute($tag, $handle, $src) {
	if('googlemaps' !== $handle) {//Here we check if our handle is googlemaps
		 return $tag; //We return the entire <script> tag as is without modifications.
	 }
	 return $tag;
	 return "<script src='".$src."'></script>";//Usually the value in $tag variable looks similar to this script tag but without the async and defer
 
   if('jquery-core-js' !== $handle ) {//Here we check if our handle is googlemaps
		 return $tag; //We return the entire <script> tag as is without modifications.
		 return "<script src='".$src."'></script>";
	 }
	return "<script src='".$src."'></script>";
	
 
 
	  }
//  add_filter('script_loader_tag', 'add_attribute_to_script_tag', 10, 2);

function deregister_unused_scripts()
{
    wp_dequeue_script('wp-polyfill-js');
    wp_dequeue_script('wp-polyfill-js');
    
}

add_action('wp_print_scripts', 'deregister_unused_scripts', 100);
add_action('wp_enqueue_scripts', 'deregister_unused_scripts', 100);


function add_rel_preload($html, $handle, $href, $media) {
    
    if (!is_admin())
     $html = <<<EOT
<link rel='stylesheet' href='$href' media="print" onload="this.media='all'" as='style' id='$handle' crossorigin />
<noscript><link rel="preload" href="$href" as="style" 
onload="this.rel='stylesheet'" crossorigin></noscript>
EOT;
    return $html;
}
add_filter( 'style_loader_tag', 'add_rel_preload',10, 999 );

//** * Enable preview / thumbnail for webp image files.*/
function webp_is_displayable($result, $path) {
    if ($result === false) {
        $displayable_image_types = array( IMAGETYPE_WEBP );
        $info = @getimagesize( $path );

        if (empty($info)) {
            $result = false;
        } elseif (!in_array($info[2], $displayable_image_types)) {
            $result = false;
        } else {
            $result = true;
        }
    }

    return $result;
}
add_filter('file_is_displayable_image', 'webp_is_displayable', 10, 2);

add_action(
    'after_setup_theme',
    function() {
        add_theme_support( 'html5', [ 'script', 'style' ] );
    }
);

class WP_HTML_Compression
{
    // Settings
    protected $compress_css = true;
    protected $compress_js = true;
    protected $info_comment = true;
    protected $remove_comments = true;

    // Variables
    protected $html;
    public function __construct($html)
    {
   	 if (!empty($html))
   	 {
   		 $this->parseHTML($html);
   	 }
    }
    public function __toString()
    {
   	 return $this->html;
    }
    protected function bottomComment($raw, $compressed)
    {
   	 $raw = strlen($raw);
   	 $compressed = strlen($compressed);
   	 
   	 $savings = ($raw-$compressed) / $raw * 100;
   	 
   	 $savings = round($savings, 2);
   	 
   	// return '<!--HTML compressed, size saved '.$savings.'%. From '.$raw.' bytes, now '.$compressed.' bytes-->';
    }
    protected function minifyHTML($html)
    {
   	 $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
   	 preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
   	 $overriding = false;
   	 $raw_tag = false;
   	 // Variable reused for output
   	 $html = '';
   	 foreach ($matches as $token)
   	 {
   		 $tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;
   		 
   		 $content = $token[0];
   		 
   		 if (is_null($tag))
   		 {
   			 if ( !empty($token['script']) )
   			 {
   				 $strip = $this->compress_js;
   			 }
   			 else if ( !empty($token['style']) )
   			 {
   				 $strip = $this->compress_css;
   			 }
   			 else if ($content == '<!--wp-html-compression no compression-->')
   			 {
   				 $overriding = !$overriding;
   				 
   				 // Don't print the comment
   				 continue;
   			 }
   			 else if ($this->remove_comments)
   			 {
   				 if (!$overriding && $raw_tag != 'textarea')
   				 {
   					 // Remove any HTML comments, except MSIE conditional comments
   					 $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);
   				 }
   			 }
   		 }
   		 else
   		 {
   			 if ($tag == 'pre' || $tag == 'textarea')
   			 {
   				 $raw_tag = $tag;
   			 }
   			 else if ($tag == '/pre' || $tag == '/textarea')
   			 {
   				 $raw_tag = false;
   			 }
   			 else
   			 {
   				 if ($raw_tag || $overriding)
   				 {
   					 $strip = false;
   				 }
   				 else
   				 {
   					 $strip = true;
   					 
   					 // Remove any empty attributes, except:
   					 // action, alt, content, src
   					 $content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);
   					 
   					 // Remove any space before the end of self-closing XHTML tags
   					 // JavaScript excluded
   					 $content = str_replace(' />', '/>', $content);
   				 }
   			 }
   		 }
   		 
   		 if ($strip)
   		 {
   			 $content = $this->removeWhiteSpace($content);
   		 }
   		 
   		 $html .= $content;
   	 }
   	 
   	 return $html;
    }
   	 
    public function parseHTML($html)
    {
   	 $this->html = $this->minifyHTML($html);
   	 
   	 if ($this->info_comment)
   	 {
   		 $this->html .= "\n" . $this->bottomComment($html, $this->html);
   	 }
    }
    
    protected function removeWhiteSpace($str)
    {
   	 $str = str_replace("\t", ' ', $str);
   	 $str = str_replace("\n",  '', $str);
   	 $str = str_replace("\r",  '', $str);
   	 
   	 while (stristr($str, '  '))
   	 {
   		 $str = str_replace('  ', ' ', $str);
   	 }
   	 
   	 return $str;
    }
}

function wp_html_compression_finish($html)
{
    return new WP_HTML_Compression($html);
}

function wp_html_compression_start()
{
    ob_start('wp_html_compression_finish');
}
add_action('get_header', 'wp_html_compression_start');



function rjs_lwp_contactform_css_js() {
    global $post;
    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'contact-form-7') ) {
        wp_enqueue_script('contact-form-7');
         wp_enqueue_style('contact-form-7');

    }else{
        wp_dequeue_script( 'contact-form-7' );
        wp_dequeue_style( 'contact-form-7' );
    }
}
add_action( 'wp_enqueue_scripts', 'rjs_lwp_contactform_css_js');

function get_excerpt($limit, $source = null){

    $excerpt = $source == "content" ? get_the_content() : get_the_excerpt();
    $excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, $limit);
    $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    $excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
	$excerpt = $excerpt.'...';
    return $excerpt;
}
function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['webp'] = 'image/webp';
	return $mimes;
  }
  add_filter('upload_mimes', 'cc_mime_types');


//   add_action( 'wp_enqueue_scripts', 'combine_all_scripts', 9999 );
  function combine_all_scripts() 
  {
	  global $wp_scripts;
	  
	  /*
		  Reorder the handles based on dependency
	  */
	  $wp_scripts -> all_deps($wp_scripts -> queue);    
	  
	  // New file location eg: wp-content/themes/maintheme/js/combined-script.js
	  $combined_file_location = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'combined-script.js';
	  
	  $combined_script    = '';
	  
	  // Loop through javascript files and save to $merged_script variable
	  foreach( $wp_scripts -> to_do as $handle) 
	  {
		  /*
			  Clean up url, for example wp-content/themes/maintheme/main.js?v=1.2.4
			  become wp-content/themes/maintheme/main.js
		  */
		  $src = strtok( $wp_scripts -> registered[ $handle ] -> src, '?' );
		  
		  /**
			  Combine javascript files.
		  */
		  // If src is url http / https        
		  if ( strpos ( $src, 'http' ) !== false )
		  {
			  // Get our site url, for example: http://lbfl.co.uk/
			  $site_url = site_url();
		  
			  if ( strpos ( $src, $site_url ) !== false )
				  $js_file_path = str_replace ( $site_url, '', $src );
			  else
				  $js_file_path = $src;
			  
			  /*
				  To be able to use file_get_contents function we need to remove the trailing forward slash
			  */
			  $js_file_path = ltrim ($js_file_path, '/' );
		  }
		  else 
		  {            
			  $js_file_path = ltrim ( $src, '/' );
		  }
		  
		  // Check to see if the file exists then combine
		  if  ( file_exists ( $js_file_path ) ) 
		  {
			  // Check for wp_localize_script
			  $localize = '';
			  if ( @key_exists( 'data', $wp_scripts -> registered[ $handle ] -> extra)) {
				  $localize = $obj -> extra['data'] . ';';
			  }
			  $combined_script .=  $localize . file_get_contents ( $js_file_path ) . ';';
		  }
	  }
	  
	  // write the combined script into current theme directory
	  file_put_contents ( $combined_file_location , $combined_script );
	  
	  // Load the URL of combined file
	  wp_enqueue_script( 'combined-script',  get_stylesheet_directory_uri() . '/combined-script.js' );
	  
	  // Deregister handles
	  foreach( $wp_scripts->to_do as $handle ) 
	  {
		  wp_deregister_script($handle);
	  }
  }

function custom_script() { ?>
	<script>
setTimeout(() => {
        $('[data-imgurl]').each(function() { 
            var $this = $(this),
            ele = $this.attr('src'),
            attData = $this.data('imgurl');
    
            if(ele !== undefined){
                $this.attr('src', attData);
            }else{
                $this.css({
                    "background-image": 'url('+ attData + ')'
                });
            }
            $this.removeAttr('data-imgurl','');
        });
},2000);
</script>
<?php }
add_action('wp_footer','custom_script');


//defer css
function add_rel_preload($html, $handle, $href, $media) {
    
    if (is_admin())
        return $html;

     $html = <<<EOT
<link defer="defer" rel='stylesheet' href='$href' media="print" onload="this.media='all'" id='$handle' crossorigin="anonymous"/>
<noscript><link defer rel="preload" href="$href" crossorigin="anonymous"></noscript>
EOT;
    return $html;
}
add_filter( 'style_loader_tag', 'add_rel_preload', 10, 4 );


add_action('wp_head', 'hook_css');

//wphead cleanup
remove_action('wp_head', 'rsd_link'); // remove really simple discovery link
remove_action('wp_head', 'wp_generator'); // remove wordpress version

remove_action('wp_head', 'feed_links', 2); // remove rss feed links (make sure you add them in yourself if youre using feedblitz or an rss service)
remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links

remove_action('wp_head', 'index_rel_link'); // remove link to index page
remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml (needed to support windows live writer)

remove_action('wp_head', 'start_post_rel_link', 10, 0); // remove random post link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // remove parent post link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // remove the next and previous post links
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
      
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
      
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0); // Remove shortlink

/*
* Remove JSON API links in header html
*/
function remove_json_api () {

    // Remove the REST API lines from the HTML Header
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

    // Remove the REST API endpoint.
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );

    // Turn off oEmbed auto discovery.
    add_filter( 'embed_oembed_discover', '__return_false' );

    // Don't filter oEmbed results.
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

    // Remove oEmbed discovery links.
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );

   // Remove all embeds rewrite rules.
   add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );

}
add_action( 'after_setup_theme', 'remove_json_api' );

/*
	Snippet completely disable the REST API and shows {"code":"rest_disabled","message":"The REST API is disabled on this site."} 
	when visiting http://yoursite.com/wp-json/
*/
function disable_json_api () {

  // Filters for WP-API version 1.x
  add_filter('json_enabled', '__return_false');
  add_filter('json_jsonp_enabled', '__return_false');

  // Filters for WP-API version 2.x
  add_filter('rest_enabled', '__return_false');
  add_filter('rest_jsonp_enabled', '__return_false');

}
add_action( 'after_setup_theme', 'disable_json_api' );


function remove_cssjs_ver( $src ) {
    if( strpos( $src, '?ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 1000 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 1000 );

// Defer scripts
function mind_defer_scripts( $tag, $handle, $src ) {
  $defer = array(
    'custom-animate',
    'owl-carousel',
    'swiper-jquery',
    'jquery-migrate',
    'florida-custom',
    // 'custom_script',
    'scrolldepth',
    'tp-tools',
    'revmin',
    'jquery',
    // 'doubletab',
    'hoverIntent',
    'megamenu',
    'jquery-lazyloadxt',
    'jquery-lazyloadxt-srcset',
    'jquery-lazyloadxt-extend',
    // 'wpb_composer_front_js',
    // 'vc_accordion_script'
    'vc_carousel_js',
    'vc_tabs_script',
    'vc_tta_autoplay_script',
    'vc_transition_bootstrap_js',
    'mediaelement',
    'contact-form-7',
    'icegram_main_js',
    'dynamic-js',
    'wpcf7-redirect-script'
    // 'mediaelement-core'
  );
  if ( in_array( $handle, $defer ) ) {
     return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
  }
    
    return $tag;
} 
add_filter( 'script_loader_tag', 'mind_defer_scripts', 10, 3 );

// function PREFIX_remove_scripts() {
 
//     wp_dequeue_style( 'dashicons' );
//     // wp_dequeue_style( 'layerslider' );
//     wp_dequeue_style( 'ls-user' );
//     wp_dequeue_style( 'wp-block-library' );
//     wp_dequeue_style( 'ls-google-fonts' );
//     // wp_dequeue_style( 'layerslider-front' );
//     wp_dequeue_style( 'contact-form-7' );
//     wp_dequeue_style( 'rs-plugin-settings' );
//     wp_dequeue_style( 'gfont-style' );
//     wp_dequeue_style( 'flexslider' );
//     wp_dequeue_style( 'swiper-style' );
//     wp_dequeue_style( 'owl-theme-default' );
//     // wp_dequeue_style( 'js_composer_front' );
//     wp_dequeue_style( 'megamenu' );
//     wp_dequeue_style( 'admin-bar' );
//     wp_dequeue_style( 'yoast-seo-adminbar' );


//     wp_dequeue_script( 'dynamic-js' );
//     wp_dequeue_script( 'jquery' );
//     wp_dequeue_script( 'tp-tools' );
//     wp_dequeue_script( 'revmin' );

//     wp_dequeue_script( 'custom-animate' );
//     wp_dequeue_script( 'owl-carousel' );
//     wp_dequeue_script( 'swiper-jquery' );
//     wp_dequeue_script( 'jquery-migrate' );
//     // wp_dequeue_script( 'florida-custom' );
//     wp_dequeue_script( 'custom_script' );
//     wp_dequeue_script( 'scrolldepth' );
//     // wp_dequeue_script( 'doubletab' );
//     wp_dequeue_script( 'hoverIntent' );
//     wp_dequeue_script( 'megamenu' );
//     wp_dequeue_script( 'wpb_composer_front_js' );
//     wp_dequeue_script( 'vc_accordion_script' );
//     wp_dequeue_script( 'vc_carousel_js' );
//     wp_dequeue_script( 'vc_tabs_script' );
//     wp_dequeue_script( 'vc_tta_autoplay_script' );
//     wp_dequeue_script( 'vc_transition_bootstrap_js' );
//     wp_dequeue_script( 'contact-form-7' );
//     wp_dequeue_script( 'mediaelement-core' );
//     wp_dequeue_script( 'mediaelement' );
//     wp_dequeue_script( 'wpcf7-redirect-script' );
//     wp_dequeue_script( 'jquery-lazyloadxt' );
//     wp_dequeue_script( 'jquery-lazyloadxt-srcset' );
//     wp_dequeue_script( 'icegram_main_js' );

//     wp_dequeue_script( 'jquery-lazyloadxt-extend' );

//     wp_dequeue_script( 'another-jquery' );

//     // Now register your styles and scripts here
// }
// add_action( 'wp_enqueue_scripts', 'PREFIX_remove_scripts', 20 );

// function prefix_add_footer() {

//     wp_enqueue_style( 'dashicons');
//     // wp_enqueue_style( 'layerslider');
//     wp_enqueue_style( 'ls-user');
//     wp_enqueue_style( 'wp-block-library');
//     wp_enqueue_style( 'ls-google-fonts');
//     // wp_enqueue_style( 'layerslider-front');
//     wp_enqueue_style( 'contact-form-7');
//     wp_enqueue_style( 'rs-plugin-settings');
//     wp_enqueue_style( 'gfont-style');
//     wp_enqueue_style( 'flexslider');
//     wp_enqueue_style( 'swiper-style');
//     wp_enqueue_style( 'owl-theme-default');
//     // wp_enqueue_style( 'js_composer_front');
//     wp_enqueue_style( 'megamenu');
//     wp_enqueue_style( 'admin-bar');
//     wp_enqueue_style( 'yoast-seo-adminbar');


//     wp_enqueue_script( 'dynamic-js');
//     wp_enqueue_script( 'jquery' );
//     // wp_enqueue_script( 'doubletab' );
//     wp_enqueue_script( 'tp-tools' );
//     wp_enqueue_script( 'revmin' );

//     wp_enqueue_script( 'custom-animate' );
//     wp_enqueue_script( 'owl-carousel' );
//     wp_enqueue_script( 'swiper-jquery' );
//     wp_enqueue_script( 'jquery-migrate' );
//     // wp_enqueue_script( 'florida-custom' );
//     wp_enqueue_script( 'custom_script' );
//     wp_enqueue_script( 'scrolldepth' );
    
    
//     wp_enqueue_script( 'hoverIntent' );
//     wp_enqueue_script( 'megamenu' );
//     wp_enqueue_script( 'wpb_composer_front_js' );
//     wp_enqueue_script( 'vc_accordion_script' );
//     wp_enqueue_script( 'vc_carousel_js' );
//     wp_enqueue_script( 'vc_tabs_script' );
//     wp_enqueue_script( 'vc_tta_autoplay_script' );
//     wp_enqueue_script( 'vc_transition_bootstrap_js' );
//     wp_enqueue_script( 'contact-form-7' );
//     wp_enqueue_script( 'mediaelement-core' );
//     wp_enqueue_script( 'mediaelement' );
//     wp_enqueue_script( 'wpcf7-redirect-script' );
//     wp_enqueue_script( 'jquery-lazyloadxt' );
//     wp_enqueue_script( 'jquery-lazyloadxt-srcset' );
//     wp_enqueue_script( 'icegram_main_js' );
//     // wp_enqueue_script( 'jquery-lazyloadxt-extend' );
//     // wp_enqueue_script( 'another-jquery' );
// };
// add_action( 'get_footer', 'prefix_add_footer' );

// html compressor
class WP_HTML_Compression
{
    // Settings
    protected $compress_css = true;
    protected $compress_js = false;
    protected $info_comment = true;
    protected $remove_comments = true;

    // Variables
    protected $html;
    public function __construct($html)
    {
   	 if (!empty($html))
   	 {
   		 $this->parseHTML($html);
   	 }
    }
    public function __toString()
    {
   	 return $this->html;
    }
    protected function bottomComment($raw, $compressed)
    {
   	 $raw = strlen($raw);
   	 $compressed = strlen($compressed);
   	 
   	 $savings = ($raw-$compressed) / $raw * 100;
   	 
   	 $savings = round($savings, 2);
   	 
   	 return '<!--HTML compressed, size saved '.$savings.'%. From '.$raw.' bytes, now '.$compressed.' bytes-->';
    }
    protected function minifyHTML($html)
    {
   	 $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
   	 preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
   	 $overriding = false;
   	 $raw_tag = false;
   	 // Variable reused for output
   	 $html = '';
   	 foreach ($matches as $token)
   	 {
   		 $tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;
   		 
   		 $content = $token[0];
   		 
   		 if (is_null($tag))
   		 {
   			 if ( !empty($token['script']) )
   			 {
   				 $strip = $this->compress_js;
   			 }
   			 else if ( !empty($token['style']) )
   			 {
   				 $strip = $this->compress_css;
   			 }
   			 else if ($content == '<!--wp-html-compression no compression-->')
   			 {
   				 $overriding = !$overriding;
   				 
   				 // Don't print the comment
   				 continue;
   			 }
   			 else if ($this->remove_comments)
   			 {
   				 if (!$overriding && $raw_tag != 'textarea')
   				 {
   					 // Remove any HTML comments, except MSIE conditional comments
   					 $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);
   				 }
   			 }
   		 }
   		 else
   		 {
   			 if ($tag == 'pre' || $tag == 'textarea')
   			 {
   				 $raw_tag = $tag;
   			 }
   			 else if ($tag == '/pre' || $tag == '/textarea')
   			 {
   				 $raw_tag = false;
   			 }
   			 else
   			 {
   				 if ($raw_tag || $overriding)
   				 {
   					 $strip = false;
   				 }
   				 else
   				 {
   					 $strip = true;
   					 
   					 // Remove any empty attributes, except:
   					 // action, alt, content, src
   					 $content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);
   					 
   					 // Remove any space before the end of self-closing XHTML tags
   					 // JavaScript excluded
   					 $content = str_replace(' />', '/>', $content);
   				 }
   			 }
   		 }
   		 
   		 if ($strip)
   		 {
   			 $content = $this->removeWhiteSpace($content);
   		 }
   		 
   		 $html .= $content;
   	 }
   	 
   	 return $html;
    }
   	 
    public function parseHTML($html)
    {
   	 $this->html = $this->minifyHTML($html);
   	 
   	 if ($this->info_comment)
   	 {
   		 $this->html .= "\n" . $this->bottomComment($html, $this->html);
   	 }
    }
    
    protected function removeWhiteSpace($str)
    {
   	 $str = str_replace("\t", ' ', $str);
   	 $str = str_replace("\n",  '', $str);
   	 $str = str_replace("\r",  '', $str);
   	 
   	 while (stristr($str, '  '))
   	 {
   		 $str = str_replace('  ', ' ', $str);
   	 }
   	 
   	 return $str;
    }
}

function wp_html_compression_finish($html)
{
    return new WP_HTML_Compression($html);
}

function wp_html_compression_start()
{
    ob_start('wp_html_compression_finish');
}
add_action('get_header', 'wp_html_compression_start');

// remove_action('wp_head', 'wp_print_scripts');
// remove_action('wp_head', 'wp_print_head_scripts', 9);
// remove_action('wp_head', 'wp_enqueue_scripts', 1);
// add_action('wp_footer', 'wp_print_scripts', 5);
// add_action('wp_footer', 'wp_enqueue_scripts', 5);
// add_action('wp_footer', 'wp_print_head_scripts', 5);
