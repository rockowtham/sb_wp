<?php

/* ------------------------------------------------ */
/* Post Ad  */
/* ------------------------------------------------ */

if (!function_exists('ad_post_short')) {

    function ad_post_short() {
        vc_map(array(
            "name" => esc_html__("Ad Post", 'carspot'),
            "base" => "ad_post_short_base",
            "category" => esc_html__("Theme Shortcodes", 'carspot'),
            "params" => array(
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Ad Post Form Type", 'carspot'),
                    "param_name" => "ad_post_form_type",
                    "admin_label" => true,
                    "value" => array(
                        esc_html__('Select Post Form', 'carspot') => '',
                        esc_html__('Default Form', 'carspot') => 'no',
                        esc_html__('Categories Based Form', 'carspot') => 'yes',
                    ),
                    'edit_field_class' => 'vc_col-sm-12 vc_column',
                    "std" => 'no',
                    "description" => esc_html__("Select the ad post form type default or with dynamic categories based. Extra fields will only works with default form.", 'carspot'),
                ),
                carspot_generate_type(esc_html__('Extra Fields Section Title', 'carspot'), 'textfield', 'extra_section_title'),
                carspot_generate_type(esc_html__('Tip Section Title', 'carspot'), 'textfield', 'tip_section_title'),
                carspot_generate_type(esc_html__('Tips Description', 'carspot'), 'textarea', 'tips_description'),
                // Making add more loop for fields
                array
                    (
                    'group' => esc_html__('Extra Fileds', 'carspot'),
                    'type' => 'param_group',
                    'heading' => esc_html__('Add field', 'carspot'),
                    'param_name' => 'fields',
                    'value' => '',
                    'params' => array
                        (
                        carspot_generate_type(esc_html__('Title', 'carspot'), 'textfield', 'title'),
                        carspot_generate_type(esc_html__('Slug', 'carspot'), 'textfield', 'slug', esc_html__('This should be unique and if you change it the pervious data of this field will be lost', 'carspot')),
                        carspot_generate_type(esc_html__('Type', 'carspot'), 'dropdown', 'type', '', "", array("Please select" => "", "Textfield" => "text", "Select/List" => "select")),
                        carspot_generate_type(esc_html__('Values for Select/List', 'carspot'), 'textarea', 'option_values', esc_html__('Like: value1,value2,value3', 'carspot'), '', '', '', 'vc_col-sm-12 vc_column', array('element' => 'type', 'value' => 'select')),
                    )
                ),
                // Making add more loop for tips
                array
                    (
                    'group' => esc_html__('Saftey Tips', 'carspot'),
                    'type' => 'param_group',
                    'heading' => esc_html__('Add Tip', 'carspot'),
                    'param_name' => 'tips',
                    'value' => '',
                    'params' => array
                        (
                        carspot_generate_type(esc_html__('Tip', 'carspot'), 'textarea', 'description'),
                    )
                ),
            ),
        ));
    }

}

add_action('vc_before_init', 'ad_post_short');



if (!function_exists('ad_post_short_base_func')) {

    function ad_post_short_base_func($atts, $content = '') {
        global $carspot_theme;
        extract(shortcode_atts(array(
            'section_title' => '',
            'section_description' => '',
            'tip_section_title' => '',
            'extra_section_title' => '',
            'tips_description' => '',
            'fields' => '',
            'tips' => '',
            'ad_post_form_type' => 'no',
                        ), $atts));


        /* if sell your car switch off */
        $sell_car_switch = isset($carspot_theme['ad_in_menu']) ? $carspot_theme['ad_in_menu'] : true;
        $sell_your_car_flag = false;
        if (isset($sell_car_switch) && $sell_car_switch) {
            $sell_your_car_flag = true;
        } else if (current_user_can('administrator')) {
            $sell_your_car_flag = true;
        }
        if ($sell_your_car_flag) {
            $input_ad_post_form_type = ( $ad_post_form_type == "yes" ) ? 1 : 0 ;
            update_option('_carspot_current_ad_post_template', $ad_post_form_type);
            // Making tips
            $rows = vc_param_group_parse_atts($atts['tips']);
            $tips = '';
            if (count((array) $rows) > 0) {
                foreach ($rows as $row) {
                    if (isset($row['description'])) {
                        $tips .= '<li>' . $row['description'] . '</li>';
                    }
                }
            }

            $profile = new carspot_profile();
            global $woocommerce;
            $size_arr = explode('-', $carspot_theme['sb_upload_size']);
            $display_size = $size_arr[1];
            $actual_size = $size_arr[0];
            carspot_user_not_logged_in();
            $cart_total = '';

            //check payment type now only for category based
            if (isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'category_based') {
                if (isset($carspot_theme['show_cart_total']) && $carspot_theme['show_cart_total'] == 1) {
                    $cart_title = '';
                    $animation = '';
                    if (isset($carspot_theme['cart_float_text']) && $carspot_theme['cart_float_text'] != "") {
                        $cart_title = $carspot_theme['cart_float_text'];
                    }
                    if (isset($carspot_theme['cart_float_animation']) && $carspot_theme['cart_float_animation'] != "") {
                        $animation = $carspot_theme['cart_float_animation'];
                    }
                    $cart_total = '
				<a id="quick-cart-pay" class="wow ' . $animation . ' animated" data-wow-delay="300ms" data-wow-iteration="infinite" data-wow-duration="2s" href="javascript:void(0)">
						<span>
						  <strong class="quick-cart-text">' . $cart_title . '<br></strong>
						  <span id="sb-quick-cart-price">' . $woocommerce->cart->get_cart_total() . '</span>
						</span>
				</a>';
                }
            }
            $description = '';
            $title = '';
            $price = '';
            $avg_city = '';
            $avg_hwy = '';
            $poster_name = '';
            $poster_ph = '';
            $ad_mapLocation = '';
            $ad_location = '';
            $ad_condition = '';
            $is_update = '';
            $level = '';

            $cats_html = '';
            $sub_cats_html = '';
            $sub_sub_cats_html = '';
            $sub_sub_sub_cats_html = '';

            $type_selected = '';
            $ad_type = '';
            $ad_warranty = '';
            $ad_year = '';
            $ad_body_type = '';
            $ad_transmission = '';
            $ad_engine_capacity = '';
            $ad_engine_type = '';
            $ad_assemble = '';
            $ad_color = '';
            $ad_insurance = '';
            $adfeatures = '';
            $ad_mileage = '';
            $tags = '';
            $id = '';
            $ad_yvideo = '';
            $ad_map_lat = '';
            $ad_map_long = '';
            $ad_bidding = '';
            $levelz = '';
            $country_html = '';
            $country_states = '';
            $country_cities = '';
            $country_towns = '';
            $ad_price_type = '';
            $is_feature_ad = 0;
            $is_bump_ad = 0;
            $heading1 = '';
            if (isset($carspot_theme['cat_level_1']) && $carspot_theme['cat_level_1'] != "") {
                $heading1 = $carspot_theme['cat_level_1'];
            }
            $heading2 = '';
            if (isset($carspot_theme['cat_level_2']) && $carspot_theme['cat_level_2'] != "") {
                $heading2 = $carspot_theme['cat_level_2'];
            }
            $heading3 = '';
            if (isset($carspot_theme['cat_level_3']) && $carspot_theme['cat_level_3'] != "") {
                $heading3 = $carspot_theme['cat_level_3'];
            }
            $heading4 = '';
            if (isset($carspot_theme['cat_level_4']) && $carspot_theme['cat_level_4'] != "") {
                $heading4 = $carspot_theme['cat_level_4'];
            }
            if (isset($_GET['id']) && $_GET['id'] != "") {

                $id = $_GET['id'];
                $my_url = carspot_get_current_url();
                if (strpos($my_url, 'carspot.scriptsbundle.com') !== false && !is_super_admin(get_current_user_id())) {
                    echo carspot_redirect(home_url('/'));
                    exit;
                }
                if (get_post_field('post_author', $id) != get_current_user_id() && !is_super_admin(get_current_user_id())) {
                    echo carspot_redirect(home_url('/'));
                    exit;
                } else {
                    $post = get_post($id);
                    $description = $post->post_content;
                    $title = $post->post_title;
                    $price = get_post_meta($id, '_carspot_ad_price', true);
                    $avg_city = get_post_meta($id, '_carspot_ad_avg_city', true);
                    $avg_hwy = get_post_meta($id, '_carspot_ad_avg_hwy', true);
                    $poster_name = get_post_meta($id, '_carspot_poster_name', true);
                    $poster_ph = get_post_meta($id, '_carspot_poster_contact', true);
                    $ad_location = get_post_meta($id, '_carspot_ad_location', true);
                    $ad_condition = get_post_meta($id, '_carspot_ad_condition', true);
                    $ad_type = get_post_meta($id, '_carspot_ad_type', true);
                    $ad_warranty = get_post_meta($id, '_carspot_ad_warranty', true);
                    $ad_year = get_post_meta($id, '_carspot_ad_years', true);
                    $ad_body_type = get_post_meta($id, '_carspot_ad_body_types', true);
                    $ad_transmission = get_post_meta($id, '_carspot_ad_transmissions', true);
                    $ad_engine_capacity = get_post_meta($id, '_carspot_ad_engine_capacities', true);
                    $ad_engine_type = get_post_meta($id, '_carspot_ad_engine_types', true);
                    $ad_assemble = get_post_meta($id, '_carspot_ad_assembles', true);
                    $ad_color = get_post_meta($id, '_carspot_ad_colors', true);
                    $ad_insurance = get_post_meta($id, '_carspot_ad_insurance', true);
                    $adfeatures = get_post_meta($id, '_carspot_ad_features', true);
                    $ad_mileage = get_post_meta($id, '_carspot_ad_mileage', true);
                    $ad_yvideo = get_post_meta($id, '_carspot_ad_yvideo', true);
                    $ad_map_lat = get_post_meta($id, '_carspot_ad_map_lat', true);
                    $ad_map_long = get_post_meta($id, '_carspot_ad_map_long', true);
                    $ad_bidding = get_post_meta($id, '_carspot_ad_bidding', true);
                    $ad_price_type = get_post_meta($id, '_carspot_ad_price_type', true);
                    $is_feature_ad = get_post_meta($id, '_carspot_is_feature', true);
                    $is_bump_ad = get_post_meta($id, '_carspot_bump_ads', true);

                    $ad_mapLocation = get_post_meta($id, '_carspot_ad_map_location', true);
                    $tags_array = wp_get_object_terms($id, 'ad_tags', array('fields' => 'names'));
                    $tags = implode(',', $tags_array);

                    $is_update = $id;
                    $cats = carspot_get_ad_cats($id, 'ID');

                    $level = count($cats);
                    /* Make cats selected on update ad */
                    $ad_cats = carspot_get_cats('ad_cats', 0);
                    $cats_html = '';
                    foreach ($ad_cats as $ad_cat) {
                        $selected = '';
                        if ($level > 0 && $ad_cat->term_id == $cats[0]['id']) {
                            $selected = 'selected="selected"';
                        }
                        $cats_html .= '<option value="' . $ad_cat->term_id . '" ' . $selected . '>' . $ad_cat->name . '</option>';
                    }

                    if ($level >= 2) {
                        $ad_sub_cats = carspot_get_cats('ad_cats', $cats[0]['id']);
                        $sub_cats_html = '';
                        foreach ($ad_sub_cats as $ad_cat) {
                            $selected = '';
                            if ($level > 0 && $ad_cat->term_id == $cats[1]['id']) {
                                $selected = 'selected="selected"';
                            }
                            $sub_cats_html .= '<option value="' . $ad_cat->term_id . '" ' . $selected . '>' . $ad_cat->name . '</option>';
                        }
                    }

                    if ($level >= 3) {
                        $ad_sub_sub_cats = carspot_get_cats('ad_cats', $cats[1]['id']);
                        $sub_sub_cats_html = '';
                        foreach ($ad_sub_sub_cats as $ad_cat) {
                            $selected = '';
                            if ($level > 0 && $ad_cat->term_id == $cats[2]['id']) {
                                $selected = 'selected="selected"';
                            }
                            $sub_sub_cats_html .= '<option value="' . $ad_cat->term_id . '" ' . $selected . '>' . $ad_cat->name . '</option>';
                        }
                    }


                    if ($level >= 4) {
                        $ad_sub_sub_sub_cats = carspot_get_cats('ad_cats', $cats[2]['id']);
                        $sub_sub_sub_cats_html = '';
                        foreach ($ad_sub_sub_sub_cats as $ad_cat) {
                            $selected = '';
                            if ($level > 0 && $ad_cat->term_id == $cats[3]['id']) {
                                $selected = 'selected="selected"';
                            }
                            $sub_sub_sub_cats_html .= '<option value="' . $ad_cat->term_id . '" ' . $selected . '>' . $ad_cat->name . '</option>';
                        }
                    }

                    /* Countries */
                    $countries = carspot_get_ad_country($id, 'ID');
                    $levelz = count($countries);
                    /* Make cats selected on update ad */
                    $ad_countries = carspot_get_cats('ad_country', 0);

                    $country_html = '';
                    foreach ($ad_countries as $ad_country) {
                        $selected = '';
                        if ($levelz > 0 && $ad_country->term_id == $countries[0]['id']) {
                            $selected = 'selected="selected"';
                        }
                        $country_html .= '<option value="' . $ad_country->term_id . '" ' . $selected . '>' . $ad_country->name . '</option>';
                    }

                    if ($levelz >= 2) {
                        $ad_states = carspot_get_cats('ad_country', $countries[0]['id']);
                        $country_states = '';
                        foreach ($ad_states as $ad_state) {
                            $selected = '';
                            if ($levelz > 0 && $ad_state->term_id == $countries[1]['id']) {
                                $selected = 'selected="selected"';
                            }
                            $country_states .= '<option value="' . $ad_state->term_id . '" ' . $selected . '>' . $ad_state->name . '</option>';
                        }
                    }

                    if ($levelz >= 3) {
                        $ad_country_cities = carspot_get_cats('ad_country', $countries[1]['id']);
                        $country_cities = '';
                        foreach ($ad_country_cities as $ad_city) {
                            $selected = '';
                            if ($levelz > 0 && $ad_city->term_id == $countries[2]['id']) {
                                $selected = 'selected="selected"';
                            }
                            $country_cities .= '<option value="' . $ad_city->term_id . '" ' . $selected . '>' . $ad_city->name . '</option>';
                        }
                    }

                    if ($levelz >= 4) {
                        $ad_country_town = carspot_get_cats('ad_country', $countries[2]['id']);
                        $country_towns = '';
                        foreach ($ad_country_town as $ad_town) {
                            $selected = '';
                            if ($levelz > 0 && $ad_town->term_id == $countries[3]['id']) {
                                $selected = 'selected="selected"';
                            }
                            $country_towns .= '<option value="' . $ad_town->term_id . '" ' . $selected . '>' . $ad_town->name . '</option>';
                        }
                    }
                }
            } 
			else {
                carspot_post_ad_process();
                //only for Package based pricing
                if (isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'package_based' && class_exists('WooCommerce')) {
                    if (is_super_admin(get_current_user_id())) {
                        
                    } else {
                        if (get_user_meta(get_current_user_id(), '_carspot_expire_ads', true) != '-1') {
                            if (get_user_meta(get_current_user_id(), '_carspot_expire_ads', true) < date('Y-m-d')) {
                                wp_redirect(get_the_permalink($carspot_theme['sb_packages_page']));
                            }
                        }

                        if (!$carspot_theme['admin_allow_unlimited_ads']) {
                            carspot_check_validity();
                        }
                        if (!is_super_admin(get_current_user_id())) {
                            carspot_check_validity();
                        }
                    }
                }

                $poster_name = $profile->user_info->display_name;
                $poster_ph = $profile->user_info->_sb_contact;
                $ad_location = get_user_meta($profile->user_info->ID, '_sb_address', true);

                $ad_cats = carspot_get_cats('ad_cats', 0);
                $cats_html = '';
                foreach ($ad_cats as $ad_cat) {
                    $cats_html .= '<option value="' . $ad_cat->term_id . '">' . $ad_cat->name . '</option>';
                }

                //Countries
                $ad_country = carspot_get_cats('ad_country', 0);
                $country_html = '';
                foreach ($ad_country as $ad_count) {
                    $country_html .= '<option value="' . $ad_count->term_id . '">' . $ad_count->name . '</option>';
                }
            }
            $bump_ad_html = '';
            $simple_feature_html = '';
            if (isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'package_based' && class_exists('WooCommerce')) {
                if (isset($carspot_theme['sb_allow_featured_ads']) && $carspot_theme['sb_allow_featured_ads'] && $is_feature_ad == 0 && ( get_user_meta(get_current_user_id(), '_carspot_expire_ads', true) == '-1' || get_user_meta(get_current_user_id(), '_carspot_expire_ads', true) >= date('Y-m-d') )) {
                    if (get_user_meta(get_current_user_id(), '_carspot_featured_ads', true) == '-1' || get_user_meta(get_current_user_id(), '_carspot_featured_ads', true) > 0) {
                        $simple_feature_html = '<div class="alert alert-info alert-featured">
			<span> ' . __('Do you want to make this ad <strong> featured </strong>!', 'carspot') . ' <span>
				<div class="skin-minimal">
				   <ul class="list">
					  <li>
						 <input name="sb_make_it_feature" id="sb_make_it_feature"   type="checkbox">
					  </li>
				   </ul>
				</div>
		   </div>';
                    } else {
                        $simple_feature_html = '<div role="alert" class="alert alert-info alert-dismissible">
				<button aria-label="Close" data-dismiss="alert" class="close" type="button"></button>
				' . __('This is a FREE ad. You may upgrade it now! See our PACKAGES', 'carspot') . ' 
				<a href="' . get_the_permalink($carspot_theme['sb_packages_page']) . '" class="sb_anchor" target="_blank">
				' . __('Packages. ', 'carspot') . '
                </a></div>';
                    }
                } else {
                    $simple_feature_html = '<div role="alert" class="alert alert-info alert-dismissible">
			<button aria-label="Close" data-dismiss="alert" class="close" type="button"></button>
			' . __('If you want to make it <strong>Featured</strong> then please have a look on', 'carspot') . ' 
			<a href="' . get_the_permalink($carspot_theme['sb_packages_page']) . '" class="sb_anchor" target="_blank">
			' . __('Packages. ', 'carspot') . '
			</a></div>';
                }

                if ($is_feature_ad == 1) {
                    $simple_feature_html = '<div role="alert" class="alert alert-info alert-dismissible">
				<button aria-label="Close" data-dismiss="alert" class="close" type="button"></button>
				' . __('This ad is already FEATURED', 'carspot') . '</div>';
                }

                //bump up

                $bump_ad_html = '';
                if (isset($is_update) && $is_update != "") {
                    if ($is_bump_ad == 0 && ( get_user_meta(get_current_user_id(), '_carspot_expire_ads', true) == '-1' || get_user_meta(get_current_user_id(), '_carspot_expire_ads', true) >= date('Y-m-d') )) {
                        if (get_user_meta(get_current_user_id(), '_carspot_bump_ads', true) == '-1' || get_user_meta(get_current_user_id(), '_carspot_bump_ads', true) > 0) {
                            $bump_ad_html = ' <div class="alert alert-warning alert-bumped">
	 <span>' . esc_html__('Bump it up on the top of the list!', 'carspot') . ' <span>
                                                                            <div class="skin-minimal">
					   <ul class="list">
						  <li>
							 <input type="checkbox" name="sb_bump_up" id="sb_bump_up">
						  </li>
					   </ul>
					</div>
				</div>';
                        }
                    }
                }
            }
            $loc_lvl_1 = esc_html__('Select Your Country', 'carspot');
            $loc_lvl_2 = esc_html__('Select Your State', 'carspot');
            $loc_lvl_3 = esc_html__('Select Your City', 'carspot');
            $loc_lvl_4 = esc_html__('Select Your Town', 'carspot');
            if ($carspot_theme['sb_location_titles'] != "") {
                $titles_array = explode("|", $carspot_theme['sb_location_titles']);
                if (count((array) $titles_array) > 0) {
                    if (isset($titles_array[0]))
                        $loc_lvl_1 = $titles_array[0];
                    if (isset($titles_array[1]))
                        $loc_lvl_2 = $titles_array[1];
                    if (isset($titles_array[2]))
                        $loc_lvl_3 = $titles_array[2];
                    if (isset($titles_array[3]))
                        $loc_lvl_4 = $titles_array[3];
                }
            }


            $dataFields = '';
            $averageFields = '';
            $customDynamicAdType = '';
            $customDynamicFields = '';
            //if admin set category-based form then on front-end also show cateogry-based form
            //if ($carspot_theme['carspot_form_type'] == 'no') {
             if ($ad_post_form_type == 'no' ) {
                $priceStaticHTML = '';
                if (isset($carspot_theme['allow_price_type'])) {
                    if ($carspot_theme['allow_price_type']) {
                        $price_fixed = '';
                        $price_negotiable = '';
                        $price_on_call = '';
                        $free = '';
                        $no_price = '';
                        if ($ad_price_type == 'Fixed') {
                            $price_fixed = 'selected=selected';
                        } else if ($ad_price_type == 'Negotiable') {
                            $price_negotiable = 'selected=selected';
                        } else if ($ad_price_type == 'on_call') {
                            $price_on_call = 'selected=selected';
                        } else if ($ad_price_type == 'free') {
                            $free = 'selected=selected';
                        } else if ($ad_price_type == 'no_price') {
                            $no_price = 'selected=selected';
                        }
                        $priceStaticHTML .= '<div class="col-md-6 col-lg-6 col-xs-12 col-sm-6" >
				<label class="control-label">' . esc_html__('Price Type', 'carspot') . '</label>
				<select class="form-control" id="ad_price_type" name="ad_price_type">
					<option value="">' . esc_html__('None', 'carspot') . '</option>
					<option value="Fixed" ' . $price_fixed . '>' . esc_html__('Fixed', 'carspot') . '</option>
					<option value="Negotiable" ' . $price_negotiable . '>' . esc_html__('Negotiable', 'carspot') . '</option>
					<option value="on_call" ' . $price_on_call . '>' . esc_html__('Price on call', 'carspot') . '</option>
					<option value="free" ' . $free . '>' . esc_html__('Free', 'carspot') . '</option>
					<option value="no_price" ' . $no_price . '>' . esc_html__('No price', 'carspot') . '</option>
			</select></div>';
                    }
                }
                $dataFields = '<div class="row">
			  <!-- Price  -->
			  <div class="col-md-6 col-lg-6 col-xs-12 col-sm-6">
				 <label class="control-label">' . esc_html__('Price', 'carspot') . '<small>' . $carspot_theme['sb_currency'] . " " . esc_html__('only', 'carspot') . '</small></label>
				 <input class="form-control" type="text" id="ad_price" name="ad_price" data-parsley-required="true" data-parsley-type="integer" data-parsley-error-message="' . esc_html__('Can\'t be empty and only integers allowed.', 'carspot') . '" value="' . $price . '">
			  </div>
			  ' . $priceStaticHTML . '
	   </div>
 	' . carspot_get_term_form($ad_cat->term_id, $id, "static") . '
			
		   <div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Photos for your ad', 'carspot') . ' <small>' . esc_html__('Only allowed jpg, png and jpeg and max file will not more than', 'carspot') . " " . $display_size . '</small></label>
				 <div id="dropzone" class="dropzone"></div>
			  </div>
		   </div>
		   <div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Youtube Video Link', 'carspot') . '</label>
				 <input class="form-control" type="text" name="ad_yvideo" value="' . $ad_yvideo . '">
			  </div>
		   </div>
		   <div class="row">
				<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
					 <label class="control-label">' . esc_html__('Tags', 'carspot') . ' <small>' . esc_html__('Comma(,) sepataed', 'carspot') . '</small></label>
					 <input class="form-control" name="tags" id="tags" value="' . $tags . '" >
					 
				</div>
			</div>';
            } else {
                $customDynamicFields = '<div id="dynamic-fields"> ' . carspot_returnHTML($id) . ' </div>';
            }
            $extra_fields_html = '';
            // Making fields
            $rows = vc_param_group_parse_atts($fields);
            if (count((array) $rows) > 0) {
                $total_fileds = 1;
                $extra_fields_html .= '<div class="select-package">
			   <div class="no-padding col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <h3 class="margin-bottom-10">' . $extra_section_title . '</h3>
				 <hr />
			  </div>
			</div>';
                foreach ($rows as $row) {
                    if (isset($row['title']) && isset($row['type']) && isset($row['slug'])) {
                        $extra_fields_html .= '<div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <label class="control-label">' . $row['title'] . '</label>';
                        if ($row['type'] == 'text') {
                            $extra_fields_html .= '<input class="form-control" value="' . get_post_meta($id, '_sb_extra_' . $row['slug'], true) . '" type="text" name="sb_extra_' . $total_fileds . '" id="sb_extra_' . $total_fileds . '" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'carspot') . '"></div></div>';
                        }
                        if ($row['type'] == 'select' && isset($row['option_values'])) {
                            $extra_fields_html .= '<select class="category form-control" id="sb_extra_' . $total_fileds . '" name="sb_extra_' . $total_fileds . '">';
                            $options = explode(',', $row['option_values']);
                            foreach ($options as $key => $value) {
                                $is_select = '';
                                if ($value == get_post_meta($id, '_sb_extra_' . $row['slug'], true)) {
                                    $is_select = 'selected';
                                }
                                $extra_fields_html .= '<option value="' . $value . '" ' . $is_select . '>' . $value . '</option>';
                            }
                            $extra_fields_html .= '</select></div></div>';
                        }
                        $extra_fields_html .= '<input type="hidden" name="title_' . $total_fileds . '" value="' . $row['slug'] . '">';
                        $total_fileds++;
                    }
                }
                $total_fileds = $total_fileds - 1;
                $extra_fields_html .= '<input type="hidden" name="sb_total_extra" value="' . $total_fileds . '">';
            }


            /* Only need on this page so inluded here don't want to increase page size for optimizaion by adding extra scripts in all the web */
            wp_enqueue_style('jquery-tagsinput', trailingslashit(get_template_directory_uri()) . 'css/jquery.tagsinput.min.css');
            wp_enqueue_style('jquery-te', trailingslashit(get_template_directory_uri()) . 'css/jquery-te.css');
            wp_enqueue_style('dropzone', trailingslashit(get_template_directory_uri()) . 'css/dropzone.css');
            carspot_load_search_countries(1);
            $mapType = carspot_mapType();

            if ($mapType == 'google_map') {
                wp_enqueue_script('google-map-callback', '//maps.googleapis.com/maps/api/js?key=' . $carspot_theme['gmap_api_key'] . '&libraries=places&callback=' . 'carspot_location', false, false, true);
            }

            $update_notice = '';
            if (isset($id) && $id != "") {
                $update_notice = '<div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				<div role="alert" class="alert alert-info alert-dismissible">
					<button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">&#10005;</span></button>
					' . $carspot_theme['sb_ad_update_notice'] . '
				</div>
			</div>
			</div>';
            }

            $lat_long_html = '';
            $lat_lon_script = '';
            $pin_lat = $ad_map_lat;
            $pin_long = $ad_map_long;
            if ($ad_map_lat == "" && $ad_map_long == "" && isset($carspot_theme['sb_default_lat']) && $carspot_theme['sb_default_lat'] && isset($carspot_theme['sb_default_long']) && $carspot_theme['sb_default_long']) {
                $pin_lat = $carspot_theme['sb_default_lat'];
                $pin_long = $carspot_theme['sb_default_long'];
            }
            $for_g_map = '<div class="row">
		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
			  <div id="dvMap" style="width: 700px; height: 300px"></div>
			  <em><small>' . esc_html__('Drag pin for your pin-point location.', 'carspot') . '</small></em>
			  </div></div>';
            if ($mapType == 'leafletjs_map') {
                $lat_lon_script = '<script type="text/javascript">	
var mymap = L.map(\'dvMap\').setView([' . $pin_lat . ', ' . $pin_long . '], 13);
	L.tileLayer(\'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}{r}.png\', {
		maxZoom: 18,
		attribution: \'\'
	}).addTo(mymap);
	var markerz = L.marker([' . $pin_lat . ', ' . $pin_long . '],{draggable: true}).addTo(mymap);
	var searchControl 	=	new L.Control.Search({
		url: \'//nominatim.openstreetmap.org/search?format=json&q={s}\',
		jsonpParam: \'json_callback\',
		propertyName: \'display_name\',
		propertyLoc: [\'lat\',\'lon\'],
		marker: markerz,
		autoCollapse: true,
		autoType: true,
		minLength: 2,
	});
	searchControl.on(\'search:locationfound\', function(obj) {
		
		var lt	=	obj.latlng + \'\';
		var res = lt.split( "LatLng(" );
		res = res[1].split( ")" );
		res = res[0].split( "," );
		document.getElementById(\'ad_map_lat\').value = res[0];
		document.getElementById(\'ad_map_long\').value = res[1];
	});
	mymap.addControl( searchControl );
	
	markerz.on(\'dragend\', function (e) {
	  document.getElementById(\'ad_map_lat\').value = markerz.getLatLng().lat;
	  document.getElementById(\'ad_map_long\').value = markerz.getLatLng().lng;
	});
	

	
</script>';
            } else if ($mapType == 'google_map') {
                if (isset($carspot_theme['allow_lat_lon']) && !$carspot_theme['allow_lat_lon']) {
                    
                } else {
                    $lat_lon_script = '<script type="text/javascript">
    var markers = [
        {
            "title": "",
            "lat": "' . $pin_lat . '",
            "lng": "' . $pin_long . '",
        },
    ];
    window.onload = function () {
        	my_g_map(markers);
        }
		function my_g_map(markers1)
		{
	
			var mapOptions = {
            center: new google.maps.LatLng(markers1[0].lat, markers1[0].lng),
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var infoWindow = new google.maps.InfoWindow();
        var latlngbounds = new google.maps.LatLngBounds();
        var geocoder = geocoder = new google.maps.Geocoder();
        var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
            var data = markers1[0]
            var myLatlng = new google.maps.LatLng(data.lat, data.lng);
            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title: data.title,
                draggable: true,
                animation: google.maps.Animation.DROP
            });
            (function (marker, data) {
                google.maps.event.addListener(marker, "click", function (e) {
                    infoWindow.setContent(data.description);
                    infoWindow.open(map, marker);
                });
                google.maps.event.addListener(marker, "dragend", function (e) {
					document.getElementById("sb_loading").style.display	= "block";
                    var lat, lng, address;
                    geocoder.geocode({ "latLng": marker.getPosition() }, function (results, status) {
						
                        if (status == google.maps.GeocoderStatus.OK) {
                            lat = marker.getPosition().lat();
                            lng = marker.getPosition().lng();
                            address = results[0].formatted_address;
							document.getElementById("ad_map_lat").value = lat;
							document.getElementById("ad_map_long").value = lng;
							document.getElementById("sb_user_address").value = address;
							document.getElementById("sb_loading").style.display	= "none";
                            //alert("Latitude: " + lat + "\nLongitude: " + lng + "\nAddress: " + address);
                        }
                    });
                });
            })(marker, data);
            latlngbounds.extend(marker.position);
		}
        /*var bounds = new google.maps.LatLngBounds();
        map.setCenter(latlngbounds.getCenter());
        map.fitBounds(latlngbounds);*/
</script>';
                }
            }
            $lat_long_html = $for_g_map . '<div class="row latlong-adpost">
			  <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Latitude', 'carspot') . '</label>
				 <input class="form-control" type="text" name="ad_map_lat" id="ad_map_lat" value="' . $ad_map_lat . '">
			  </div>
			  <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Longitude', 'carspot') . '</label>
				 <input class="form-control" name="ad_map_long" id="ad_map_long" value="' . $ad_map_long . '" type="text">
			  </div>
		   </div>';
            // Check phone is required or not
            $phone_html = '<input class="form-control" name="sb_contact_number" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'carspot') . '" value="' . $poster_ph . '" type="text">';
            if (isset($carspot_theme['sb_user_phone_required']) && !$carspot_theme['sb_user_phone_required']) {
                $phone_html = '<input class="form-control" name="sb_contact_number" value="' . $poster_ph . '" type="text">';
            }


            $bidable = '';
			
			if (isset($carspot_theme['sb_enable_comments_offer']) && $carspot_theme['sb_enable_comments_offer']) {
				if (isset($carspot_theme['sb_enable_comments_offer_user']) && $carspot_theme['sb_enable_comments_offer_user']) {
					$bidable .= '<div class="select-package">
				   <div class="no-padding col-md-12 col-lg-12 col-xs-12 col-sm-12">
					 <h3 class="margin-bottom-10">' . $carspot_theme['sb_enable_comments_offer_user_title'] . '</h3>
					 <hr />
				  </div>
				</div>';
					$bid_on = '';
					$bid_off = '';
					if ($ad_bidding == 1) {
						$bid_on = 'selected=selected';
					} else {
						$bid_off = 'selected=selected';
					}
	
					$bidding_options = '<option value="1" ' . $bid_on . '>' . esc_html__('ON', 'carspot') . '</option>';
					$bidding_options .= '<option value="0" ' . $bid_off . '>' . esc_html__('OFF', 'carspot') . '</option>';
					$bidable .= '<div class="row"><div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <select class="form-control" name="ad_bidding" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'carspot') . '">
						' . $bidding_options . '
				</select>
			  </div></div>';
				}
			}

            /* ------------------------- */
            $extraFeatures = '';
            if (isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'category_based') {
                $extraFeatures = carspot_getPostAd_adons('ad_post');
            }
            /* - ------------  ------------ */

            /* For No Map  */
            $map_html = '';
            if ($mapType != 'no_map') {


                $map_html = '<div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				  <label class="control-label">' . esc_html__('Address', 'carspot') . '</label>
				 <input class ="form-control" name="sb_user_address" placeholder="' . esc_html__('Search Location', 'carspot') . '"  id="sb_user_address" value="' . $ad_mapLocation . '" />
			  </div>
		   </div>' . $lat_long_html . $lat_lon_script . '';
            }

            $custom_location = '';
            if (isset($carspot_theme['enable_custom_locationz']) && $carspot_theme['enable_custom_locationz'] == true) {
                global $carspot_theme;
                $custom_location = ' <div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_attr($loc_lvl_1) . '</label>
				 <select class="country form-control" id="ad_country" name="ad_country" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'carspot') . '">
					<option value="">Select Option</option>
					' . $country_html . '
				 </select>
				 <input type="hidden" name="ad_country_id" id="ad_country_id" value="" />
			  </div>
		   </div>
		   <div class="row" id="ad_country_sub_div">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" >
			  <label class="control-label">' . esc_attr($loc_lvl_2) . '</label>
				<select class="category form-control" id="ad_country_states" name="ad_country_states">
					' . $country_states . '
				</select>
			  </div>
			</div>
			 <div class="row" id="ad_country_sub_sub_div" >
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
			  <label class="control-label">' . esc_attr($loc_lvl_3) . '</label>
				<select class="category form-control" id="ad_country_cities" name="ad_country_cities">
					' . $country_cities . '
				</select>
			  </div>
			</div>
			 <div class="row" id="ad_country_sub_sub_sub_div">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
			  <label class="control-label">' . esc_attr($loc_lvl_4) . '</label>
				<select class="category form-control" id="ad_country_towns" name="ad_country_towns">
					' . $country_towns . '
				</select>
			  </div>
			</div>';
            }
            $top_padding = 'no-top';
            if (isset($carspot_theme['sb_header']) && $carspot_theme['sb_header'] == 'transparent' || $carspot_theme['sb_header'] == 'transparent2') {
                $top_padding = '';
            }


            return ' 
  <section class="section-padding ' . carspot_returnEcho($top_padding) . ' gray">

<div class="container">
' . $update_notice . '
<!-- Row -->
<div class="row">
  <div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
	 <!-- end post-padding -->
	 <div class="post-ad-form postdetails">
		<form  class="submit-form" id="ad_post_form">
		   <div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Title', 'carspot') . '</label>
				 <input class="form-control" placeholder="' . esc_html__('Enter title', 'carspot') . '" type="text" name="ad_title" id="ad_title" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'carspot') . '" value="' . $title . '">
				 <input type="hidden" id="is_update" name="is_update" value="' . $is_update . '" />
				 <input type="hidden" id="is_level" name="is_level" value="' . $level . '" />
				 <input type="hidden" id="country_level" name="country_level" value="' . $levelz . '" />
			  </div>
		   </div>
		   <div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <label class="control-label">' . $heading1 . ' <small>' . esc_html__('Select suitable category for your ad', 'carspot') . '</small></label>
				 <select class="category form-control" id="ad_cat" name="ad_cat" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'carspot') . '">
					<option value="">' . esc_html__("Select Option", "carspot") . '</option>
					' . $cats_html . '
				 </select>
				 <input type="hidden" name="ad_cat_id" id="ad_cat_id" value="" />
			  </div>
		   </div>
		   <div class="row" id="ad_cat_sub_div">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" >
			  <label class="control-label">' . $heading2 . '</label>
				<select class="category form-control" id="ad_cat_sub" name="ad_cat_sub">
					' . $sub_cats_html . '
				</select>

			  </div>
			</div>
		   <div class="row" id="ad_cat_sub_sub_div" >
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
			  <label class="control-label">' . $heading3 . '</label>
				<select class="category form-control" id="ad_cat_sub_sub" name="ad_cat_sub_sub">
					' . $sub_sub_cats_html . '
				</select>
			  </div>
			</div>
		   <div class="row" id="ad_cat_sub_sub_sub_div">
			  <!-- Category  -->
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
			  <label class="control-label">' . $heading4 . '</label>
				<select class="category form-control" id="ad_cat_sub_sub_sub" name="ad_cat_sub_sub_sub">
					' . $sub_sub_sub_cats_html . '
				</select>
			  </div>
			</div>
				' . $dataFields . '
				' . $customDynamicFields . '
		   <div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12  col-sm-12">
				 <label class="control-label">' . esc_html__('Ad Description', 'carspot') . ' <small>' . esc_html__('Enter long description for your project', 'carspot') . '</small></label>
				 <textarea rows="12" class="form-control" name="ad_description" id="ad_description">' . esc_textarea($description) . '</textarea>
			  </div>
		   </div>
				' . $extra_fields_html . '
				' . $bidable . '			
			<div class="select-package">
			   <div class="no-padding col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <h3 class="margin-bottom-10">' . esc_html__('User Information', 'carspot') . '</h3>
				 <hr />
			  </div>
			</div>
			
			' . $custom_location . '
			
		   <div class="row">
			  <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Your Name', 'carspot') . '</label>
				 <input class="form-control" type="text" name="sb_user_name" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'carspot') . '" value="' . $poster_name . '">
			  </div>
			  <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Mobile Number', 'carspot') . '</label>
				 <input class="form-control" name="sb_contact_number" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'carspot') . '" value="' . $poster_ph . '" type="text">
			  </div>
		   </div>
		   
		   ' . $map_html . '
			' . $extraFeatures . '
		   <!-- end row -->
			   <div class="ad_post_alerts">
			   ' . $simple_feature_html . '
				' . $bump_ad_html . '
				</div>
		   <button class="btn btn-theme pull-right" id="ad_submit">' . esc_html__('Post My Ad', 'carspot') . '</button>
		   <input type="hidden" id="ad_post_nonce" value="'.wp_create_nonce('carspot_ad_post_secure').'"  />
		</form>
	 </div>
	 <!-- end post-ad-form-->
  </div>
  <div class="col-md-4 col-xs-12 col-sm-12">
	 <div class="blog-sidebar">
		<div class="widget">
		   <div class="widget-heading">
			  <h4 class="panel-title"><a>' . $tip_section_title . '</a></h4>
		   </div>
		   <div class="widget-content">
			  <p class="lead">' . $tips_description . '</p>
			  <ol>
				 ' . $tips . '
			  </ol>
		   </div>
		</div>
	 </div>
  </div>
</div>
</div>

<input type="hidden" id="dictDefaultMessage" value="' . esc_html__('Drop files here to upload', 'carspot') . '" />
<input type="hidden" id="dictFallbackMessage" value="' . esc_html__('Your browser does not support drag\'n\'drop file uploads.', 'carspot') . '" />
<input type="hidden" id="dictFallbackText" value="' . esc_html__('Please use the fallback form below to upload your files like in the olden days.', 'carspot') . '" />
<input type="hidden" id="dictFileTooBig" value="' . esc_html__('File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.', 'carspot') . '" />
<input type="hidden" id="dictInvalidFileType" value="' . esc_html__('You can\'t upload files of this type.', 'carspot') . '" />
<input type="hidden" id="dictResponseError" value="' . esc_html__('Server responded with {{statusCode}} code.', 'carspot') . '" />
<input type="hidden" id="dictCancelUpload" value="' . esc_html__('Cancel upload', 'carspot') . '" />
<input type="hidden" id="dictCancelUploadConfirmation" value="' . esc_html__('Are you sure you want to cancel this upload?', 'carspot') . '" />
<input type="hidden" id="dictRemoveFile" value="' . esc_html__('Remove file', 'carspot') . '" />
<input type="hidden" id="dictMaxFilesExceeded" value="' . esc_html__('You can not upload any more files.', 'carspot') . '" />
<input type="hidden" id="required_images" value="' . esc_html__('Images are required.', 'carspot') . '" />

<input type="hidden" id="input_ad_post_form_type" value="' . $input_ad_post_form_type . '" />



<!-- Main Container End -->
</section>
' . $cart_total . '';
        } else {
            wp_redirect(home_url());
            exit;
        }
    }

}

if (function_exists('carspot_add_code')) {
    carspot_add_code('ad_post_short_base', 'ad_post_short_base_func');
}