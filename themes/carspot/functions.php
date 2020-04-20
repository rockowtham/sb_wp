<?php

/**
 * carspot functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package carspot
 */
add_action('after_setup_theme', 'carspot_setup');
if (!function_exists('carspot_setup')) :

    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function carspot_setup() {

        /* ------------------------------------------------ */
        /* Theme Settings */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/theme_settings.php';
        /* ------------------------------------------------ */
        /* Theme Utilities */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/utilities.php';
        /* ------------------------------------------------ */
        /* Theme Utilities */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/unilities_new.php';
        /* ------------------------------------------------ */
        /* TGM */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'tgm/tgm-init.php';
        /* ------------------------------------------------ */
        /* Theme Options */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/options-init.php';
        /* ------------------------------------------------ */
        /* Theme Nav */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/nav.php';
        /* ------------------------------------------------ */
        /* Search Widgets */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/ads-widgets.php';
        /* ------------------------------------------------ */
        /* Reviews Widgets */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/reviews-widgets.php';
        /* ------------------------------------------------ */
        /* Theme Shortcodes */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/theme_shortcodes/shortcodes.php';
        /* custom changing functions */
        /* ------------------------------------------------ */
        require trailingslashit(get_template_directory()) . 'inc/custom-functions.php';
    }

endif;
/* ------------------------------------------------ */
/* Enqueue scripts and styles. */
/* ------------------------------------------------ */
add_action('wp_enqueue_scripts', 'carspot_scripts');

function carspot_scripts() {
    global $carspot_theme;

    /* Register scripts. */
    wp_enqueue_script('bootstrap', trailingslashit(get_template_directory_uri()) . 'js/bootstrap.min.js', false, false, true);
    wp_enqueue_script('toastr', trailingslashit(get_template_directory_uri()) . 'js/toastr.min.js', false, false, true);
    $is_live = true;
    if (isset($carspot_theme['sb_comming_soon_mode']) && $carspot_theme['sb_comming_soon_mode']) {
        $is_live = false;
        if (is_super_admin(get_current_user_id())) {
            if (!$is_live) {
                $is_live = true;
            }
        }
    }
    if ($is_live) {
        wp_enqueue_script('carspot-dt', trailingslashit(get_template_directory_uri()) . 'js/datepicker.min.js', false, false, true);
        wp_enqueue_script('animate-number', trailingslashit(get_template_directory_uri()) . 'js/animateNumber.min.js', false, false, true);
        wp_enqueue_script('easing', trailingslashit(get_template_directory_uri()) . 'js/easing.js', false, false, true);
        wp_enqueue_script('isotope', trailingslashit(get_template_directory_uri()) . 'js/isotope.min.js', false, false, true);
        wp_enqueue_script('carousel', trailingslashit(get_template_directory_uri()) . 'js/carousel.min.js', false, false, true);
        wp_enqueue_script('dropzone', trailingslashit(get_template_directory_uri()) . 'js/dropzone.js', false, false, true);
        wp_enqueue_script('carspot-megamenu', trailingslashit(get_template_directory_uri()) . 'js/carspot-menu.js', false, false, true);
        wp_enqueue_script('form-dropzone', trailingslashit(get_template_directory_uri()) . 'js/form-dropzone.js', false, false, true);
        wp_enqueue_script('icheck', trailingslashit(get_template_directory_uri()) . 'js/icheck.min.js', false, false, true);
        wp_enqueue_script('modernizr', trailingslashit(get_template_directory_uri()) . 'js/modernizr.js', false, false, true);
        wp_enqueue_script('jquery-appear', trailingslashit(get_template_directory_uri()) . 'js/jquery.appear.min.js', false, false, true);
        wp_enqueue_script('jquery-countTo', trailingslashit(get_template_directory_uri()) . 'js/jquery.countTo.js', false, false, true);
        wp_enqueue_script('jquery-inview', trailingslashit(get_template_directory_uri()) . 'js/jquery.inview.min.js', false, false, true);
        wp_enqueue_script('nouislider-all', trailingslashit(get_template_directory_uri()) . 'js/nouislider.all.min.js', false, false, true);
        wp_enqueue_script('perfect-scrollbar', trailingslashit(get_template_directory_uri()) . 'js/perfect-scrollbar.min.js', false, false, true);
        wp_enqueue_script('select-2', trailingslashit(get_template_directory_uri()) . 'js/select2.min.js', false, false, true);
        wp_enqueue_script('slide', trailingslashit(get_template_directory_uri()) . 'js/slide.js', false, false, true);
        wp_enqueue_script('color-switcher', trailingslashit(get_template_directory_uri()) . 'js/color-switcher.js', false, false, true);
        wp_enqueue_script('parsley', trailingslashit(get_template_directory_uri()) . 'js/parsley.min.js', false, false, true);
        wp_enqueue_script('recaptcha', '//www.google.com/recaptcha/api.js', false, false, true);
        wp_enqueue_script('hello', trailingslashit(get_template_directory_uri()) . 'js/hello.js', false, false, true);
        wp_enqueue_script('jquery-te', trailingslashit(get_template_directory_uri()) . 'js/jquery-te.min.js', false, false, true);
        wp_enqueue_script('tagsinput', trailingslashit(get_template_directory_uri()) . 'js/jquery.tagsinput.min.js', false, false, true);
        wp_enqueue_script('bootstrap-confirmation', trailingslashit(get_template_directory_uri()) . 'js/bootstrap-confirmation.min.js', false, false, true);
        wp_enqueue_script('fancybox2', trailingslashit(get_template_directory_uri()) . 'js/jquery.fancybox.min.js', false, false, true);
        wp_enqueue_script('auto-complete', trailingslashit(get_template_directory_uri()) . 'js/jquery.autocomplete.min.js', array('jquery'), false, true);
        wp_enqueue_script('tooltop', trailingslashit(get_template_directory_uri()) . 'js/protip.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-confirm', trailingslashit(get_template_directory_uri()) . 'js/jquery-confirm.min.js', array('jquery'), false, true);
        if (is_page_template('page-profile.php')) {
            wp_enqueue_script('slim-scroll', trailingslashit(get_template_directory_uri()) . 'js/jquery.slimscroll.min.js', array('jquery'), false, true);
        }

        if (isset($carspot_theme['sb_search_page']) && $carspot_theme['sb_search_page'] != "" && is_page()) {
            if (get_page_template_slug($carspot_theme['sb_search_page']) == get_page_template_slug(get_the_ID())) {
                wp_enqueue_script('carspot-search', trailingslashit(get_template_directory_uri()) . 'js/search.js', false, false, true);
            }
        }


        $mapType = carspot_mapType();
        if ($mapType == 'leafletjs_map') {

            /* Open Street Map In The API */
            if (!is_rtl()) {
                wp_enqueue_style('leaflet', trailingslashit(get_template_directory_uri()) . 'assets/leaflet/leaflet.css');
            } else {
                wp_enqueue_style('leaflet', trailingslashit(get_template_directory_uri()) . 'assets/leaflet/leaflet-rtl.css');
            }
            wp_enqueue_style('leaflet-search', trailingslashit(get_template_directory_uri()) . 'assets/leaflet/leaflet-search.min.css');
            wp_register_script('leaflet', trailingslashit(get_template_directory_uri()) . 'assets/leaflet/leaflet.js', false, false, false);
            wp_register_script('leaflet-markercluster', trailingslashit(get_template_directory_uri()) . 'assets/leaflet/leaflet.markercluster.js', false, false, false);

            wp_register_script('leaflet-search', trailingslashit(get_template_directory_uri()) . 'assets/leaflet/leaflet-search.min.js', false, false, false);

            wp_enqueue_script('leaflet');
            wp_enqueue_script('leaflet-markercluster');
            wp_enqueue_script('leaflet-search');
        } else if ($mapType == 'no_map') {
            /* No Mapp In The Theme */
        } else {

            if (isset($carspot_theme['gmap_api_key']) && $carspot_theme['gmap_api_key'] != "") {
                $map_lang = 'en';
                if (isset($carspot_theme['carspot_gmap_lang']) && $carspot_theme['carspot_gmap_lang'] != "") {
                    $map_lang = $carspot_theme['carspot_gmap_lang'];
                }
				if(is_page_template('page-search.php'))
				{
					 wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?v=3&key=' . $carspot_theme['gmap_api_key'] . '&libraries=places', false, false, true);
				}
				else if(is_page_template('page-profile.php') || is_singular() || is_author())
				{
				   wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?key=' . $carspot_theme['gmap_api_key'] . '&language=' . $map_lang, false, false, true);
				}
            }
        }
		
		 

        /* Load the custom scripts. */
        wp_enqueue_script('carspot-maxcdn1', trailingslashit(get_template_directory_uri()) . 'js/html5shiv.min.js', array(), '3.7.2', false);
        wp_script_add_data('carspot-maxcdn1', 'conditional', 'lt IE 9');
        wp_enqueue_script('carspot-maxcdn2', trailingslashit(get_template_directory_uri()) . 'js/respond.min.js', array(), '1.4.2', false);
        wp_script_add_data('carspot-maxcdn2', 'conditional', 'lt IE 9');
        if (is_singular()) {
            wp_enqueue_script("comment-reply", '', true);
        }
        if (is_singular('ad_post')) {
            wp_enqueue_script('google-map');
        }
        wp_enqueue_script("jquery-ui-sortable");
        wp_enqueue_script('imagesloaded');
        wp_enqueue_script('wow', trailingslashit(get_template_directory_uri()) . 'js/wow.js', false, false, true);
        if (isset($carspot_theme['sb_video_icon']) && $carspot_theme['sb_video_icon']) {
            wp_enqueue_style('popup-video-iframe-style', trailingslashit(get_template_directory_uri()) . 'css/video_player.css');
            wp_enqueue_script('popup-video-iframe', trailingslashit(get_template_directory_uri()) . 'js/video_player.js', false, false, true);
        }

        wp_enqueue_script('carspot-custom', trailingslashit(get_template_directory_uri()) . 'js/custom.js', array('jquery'), false, true);
    } else {
        wp_enqueue_script('coundown-timer', trailingslashit(get_template_directory_uri()) . 'js/coundown-timer.js', false, false, true);
        wp_enqueue_script('carspot-comming-soon', trailingslashit(get_template_directory_uri()) . 'js/coming-soon.js', false, false, true);
    }
    /* Load the stylesheets. */
    wp_enqueue_style('carspot-style', get_stylesheet_uri());

//Google Fonts Included
    function carspot_theme_slug_fonts_url() {
        $fonts_url = '';
        $source_sans = _x('on', 'Source Sans Pro: on or off', 'carspot');
        $poppins = _x('on', 'Poppins font: on or off', 'carspot');
        if ('off' !== $source_sans || 'off' !== $poppins) {
            $font_families = array();
            if ('off' !== $source_sans) {
                $font_families[] = 'Source+Sans+Pro:400,400italic,600,600italic,700,700italic,900italic,900,300,300italic';
            }
            if ('off' !== $poppins) {
                $font_families[] = 'Poppins:400,500,600';
            }
            $query_args = array(
                'family' => urlencode(implode('%7C', $font_families)),
                'subset' => urlencode('latin,latin-ext'),
            );
            $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
        }
        return urldecode($fonts_url);
    }

    wp_enqueue_style('bootstrap', trailingslashit(get_template_directory_uri()) . 'css/bootstrap.css');
	wp_enqueue_style('carspot-theme-tooltip', trailingslashit(get_template_directory_uri()) . 'css/user-dashboard/protip.min.css');

    if (is_rtl()) {
        wp_enqueue_style('slider', trailingslashit(get_template_directory_uri()) . 'css/carspot-menu-rtl.css');
        wp_enqueue_style('bootstrap-rtl', trailingslashit(get_template_directory_uri()) . 'css/bootstrap-rtl.css');
    }
    if (is_author() || is_page_template('page-dealer-reviews.php')) {
        wp_enqueue_style('carspot-star-rating', trailingslashit(get_template_directory_uri()) . 'css/user-dashboard/star-rating.css');
        wp_enqueue_script('carspot-star-rating-js', trailingslashit(get_template_directory_uri()) . 'js/star-rating.js', false, false, true);
    }

    if (is_page_template('page-profile.php')) {
        wp_enqueue_style('carspot-theme-dashboard', trailingslashit(get_template_directory_uri()) . 'css/user-dashboard/style.css');
        
        /* wp_enqueue_script( 'google-map-callback', '//maps.googleapis.com/maps/api/js?key=' . $carspot_theme['gmap_api_key'] . '&libraries=places&callback=' . 'carspot_location' , false, false, true ); */
        wp_enqueue_script('carspot-profile', trailingslashit(get_template_directory_uri()) . 'js/profile.js', false, false, true);
    } else {
        wp_enqueue_style('carspot-theme', trailingslashit(get_template_directory_uri()) . 'css/style.css');
    }

    wp_enqueue_style('carspot-jquery-confirm', trailingslashit(get_template_directory_uri()) . 'css/user-dashboard/jquery-confirm.css');
    wp_enqueue_style('carspot-datepicker', trailingslashit(get_template_directory_uri()) . 'css/datepicker.min.css');
    wp_enqueue_style('carspot-theme-slug-fonts', carspot_theme_slug_fonts_url(), array(), null);
    wp_enqueue_style('et-line-fonts', trailingslashit(get_template_directory_uri()) . 'css/et-line-fonts.css');
    wp_enqueue_style('font-awesome', trailingslashit(get_template_directory_uri()) . 'css/font-awesome.css');
    wp_enqueue_style('line-awesome', trailingslashit(get_template_directory_uri()) . 'css/line-awesome.min.css');
    wp_enqueue_style('animate', trailingslashit(get_template_directory_uri()) . 'css/animate.min.css');
    wp_enqueue_style('flaticon', trailingslashit(get_template_directory_uri()) . 'css/flaticon.css');
    wp_enqueue_style('flaticon2', trailingslashit(get_template_directory_uri()) . 'css/flaticon2.css');
    wp_enqueue_style('custom-icons', trailingslashit(get_template_directory_uri()) . 'css/custom_icons.css');
    wp_enqueue_style('carspot-select2', trailingslashit(get_template_directory_uri()) . 'css/select2.min.css');
    wp_enqueue_style('nouislider', trailingslashit(get_template_directory_uri()) . 'css/nouislider.min.css');
    wp_enqueue_style('owl-carousel', trailingslashit(get_template_directory_uri()) . 'css/owl.carousel.css');
    wp_enqueue_style('owl-theme', trailingslashit(get_template_directory_uri()) . 'css/owl.theme.css');
    wp_enqueue_style('carspot-custom', trailingslashit(get_template_directory_uri()) . 'css/custom.css');
    wp_enqueue_style('toastr', trailingslashit(get_template_directory_uri()) . 'css/toastr.min.css');
    wp_enqueue_style('carspot-woo', trailingslashit(get_template_directory_uri()) . 'css/woocommerce.css');
    wp_enqueue_style('minimal', trailingslashit(get_template_directory_uri()) . 'skins/minimal/minimal.css');
    wp_enqueue_style('fancybox2', trailingslashit(get_template_directory_uri()) . 'css/jquery.fancybox.min.css');
    if (is_rtl()) {
        wp_enqueue_style('slider-rtl-single', trailingslashit(get_template_directory_uri()) . 'css/rtl-single-slider.css');
        wp_enqueue_style('carspot-theme-rtl', trailingslashit(get_template_directory_uri()) . 'css/style-rtl.css');
        wp_enqueue_style('carspot-dashboard-rtl', trailingslashit(get_template_directory_uri()) . 'css/user-dashboard/style-rtl.css');
    } else {
        wp_enqueue_style('slider', trailingslashit(get_template_directory_uri()) . 'css/slider.css');
        wp_enqueue_style('carspot-menu', trailingslashit(get_template_directory_uri()) . 'css/carspot-menu.css');
    }
    wp_enqueue_style('responsive-media', trailingslashit(get_template_directory_uri()) . 'css/responsive-media.css');
    $css_color = 'defualt';
    if (isset($carspot_theme['theme_color']) && $carspot_theme['theme_color'] != "") {
        $css_color = $carspot_theme['theme_color'];
    }
    wp_enqueue_style('defualt-color', trailingslashit(get_template_directory_uri()) . 'css/colors/' . $css_color . '.css', array(), null);
}

add_action('admin_enqueue_scripts', 'carspot_load_admin_js');

function carspot_load_admin_js() {
    wp_enqueue_script('carspot-admin', trailingslashit(get_template_directory_uri()) . 'js/admin.js', false, false, true);
}

add_action('get_header', 'carspot_remove_admin_login_header');

function carspot_remove_admin_login_header() {
    remove_action('wp_head', '_admin_bar_bump_cb');
}