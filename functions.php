// Aplazar JavaScripts
// Aplaza la carga de jQuery usando la propiedad HTML5 defer
if (!(is_admin() )) {
function defer_parsing_of_js ( $url ) {
if ( FALSE === strpos( $url, '.js' ) ) return $url;
if ( strpos( $url, 'jquery.js' ) ) return $url;
// return "$url' defer ";
return "$url' defer onload='";
}
add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
}

if(!is_admin()) {
// Mover todo el JS de la cabecera (header) al pié (footer)
remove_action('wp_head', 'wp_print_scripts');
remove_action('wp_head', 'wp_print_head_scripts', 9);
remove_action('wp_head', 'wp_enqueue_scripts', 1);
add_action('wp_footer', 'wp_print_scripts', 5);
add_action('wp_footer', 'wp_enqueue_scripts', 5);
add_action('wp_footer', 'wp_print_head_scripts', 5);
}

// Deregister los dashicons si no se muestra la barra de admin
add_action( 'wp_print_styles', function() {
if (!is_admin_bar_showing()) wp_deregister_style( 'dashicons' );
}, 100);

//Desactivar Heartbeat API
add_action( 'init', 'stop_heartbeat', 1 );
function stop_heartbeat() {
wp_deregister_script('heartbeat');
}

// disable json api and remove link from header
if (in_array('json_api',$settings)) {
// remove json_api
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
remove_action( 'rest_api_init', 'wp_oembed_register_route' );
add_filter( 'embed_oembed_discover', '__return_false' );
remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
// disable json_api
add_filter('json_enabled', '__return_false');
add_filter('json_jsonp_enabled', '__return_false');
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');
}

// remove emoji styles and script from header
if (in_array('emojicons',$settings)) {// disabled options are called at the end of machete_admin.php//remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
//remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
}

add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );
function child_manage_woocommerce_styles() {
remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
if ( function_exists( 'is_woocommerce' ) ) {
if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
wp_dequeue_style( 'woocommerce_frontend_styles' );
wp_dequeue_style( 'woocommerce_fancybox_styles' );
wp_dequeue_style( 'woocommerce_chosen_styles' );
wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
wp_dequeue_script( 'wc_price_slider' );
wp_dequeue_script( 'wc-single-product' );
wp_dequeue_script( 'wc-add-to-cart' );
wp_dequeue_script( 'wc-cart-fragments' );
wp_dequeue_script( 'wc-checkout' );
wp_dequeue_script( 'wc-add-to-cart-variation' );
wp_dequeue_script( 'wc-single-product' );
wp_dequeue_script( 'wc-cart' );
wp_dequeue_script( 'wc-chosen' );
wp_dequeue_script( 'woocommerce' );
wp_dequeue_script( 'prettyPhoto' );
wp_dequeue_script( 'prettyPhoto-init' );
wp_dequeue_script( 'jquery-blockui' );
wp_dequeue_script( 'jquery-placeholder' );
wp_dequeue_script( 'fancybox' );
wp_dequeue_script( 'jqueryui' );
}
}
}

//Desactivar devicepx de JetPack
function remove_devicepx() {
wp_dequeue_script( 'devicepx' );
}
add_action( 'wp_enqueue_scripts', 'remove_devicepx' );

//Quitar control de versiones de scripts de WP
function _remove_script_version( $src ){
$parts = explode( '?', $src );
return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

//Quitar las query strings from statics resources
function _remove_script_version( $src ){
$parts = explode( '?ver', $src );
return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

<?php
/* Carga eficaz de estilos del tema padre en vez de @import */
function child_theme_styles() {
wp_dequeue_style( 'parent-theme-style' );
wp_enqueue_style( 'child-theme-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'child_theme_styles' );
?>

//ELIMINAR TRANSIENTS
add_action( 'wp_scheduled_delete', 'delete_expired_db_transients' );function delete_expired_db_transients() {global $wpdb, $_wp_using_ext_object_cache;if( $_wp_using_ext_object_cache )
return;$time = isset ( $_SERVER['REQUEST_TIME'] ) ? (int)$_SERVER['REQUEST_TIME'] : time() ;
$expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout%' AND option_value < {$time};" );foreach( $expired as $transient ) {
$key = str_replace('_transient_timeout_', '', $transient);
delete_transient($key);
}
}
