<?php

/**
 * Add REST API support to an already registered post type.
 */
add_action('rest_api_init', 'carspotAPI_post_ad_get_hooks', 0);
function carspotAPI_post_ad_get_hooks()
{
	register_rest_route(
		'carspot/v1',
		'/post_ad/',
		array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'carspotAPI_post_ad_get',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
	register_rest_route(
		'carspot/v1',
		'/post_ad/is_update/',
		array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_post_ad_get',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
}

if (!function_exists('carspotAPI_shift_arr_key')) {
	function carspotAPI_shift_arr_key($key, $arr)
	{
		if (in_array($key, $arr)) {
			$value = $arr[$key];
			unset($arr[$key]);
			array_unshift($arr, $value);
		}
	}
}

if (!function_exists('carspotAPI_post_ad_get')) {
	function carspotAPI_post_ad_get($request)
	{

		global $carspotAPI;
		global $carspot_theme;
		$message = '';
		// if (!$carspot_theme['admin_allow_unlimited_ads']) {
		// 	$message = carspotAPI_check_ads_validity();
		// }
		// if (!is_super_admin(get_current_user_id())) {
		// 	$message = carspotAPI_check_ads_validity();
		// }
		if ($message != "") {
			return array('success' => false, 'data' => '', 'message' => $message);
		}
		$json_data 	= $request->get_json_params();
		$is_update	= (isset($json_data['is_update']) && $json_data['is_update'] != "") ? $json_data['is_update'] : '';
		$user 		= wp_get_current_user();
		$user_id 	= $user->data->ID;
		$pid		=	get_user_meta($user_id, 'ad_in_progress', true);
		if ($is_update != "") {
			$pid = (int)$is_update;
		} else if (get_post_status($pid) && $pid != "") {
			$pid = (int)$pid;
		} else {
			$my_post = array('post_status'   => 'private', 'post_author'   => $user_id, 'post_type' => 'ad_post');
			$id	=  wp_insert_post($my_post);
			if ($id) {
				update_user_meta($user_id, 'ad_in_progress', $id);
				update_post_meta($id, '_carspot_ad_status_', 'active');
			}
			$pid = (int)$id;
		}
		$display_name 				= $user->data->display_name;
		$sb_contact  				= get_user_meta($user_id, '_sb_contact', true);
		$sb_location 				= get_user_meta($user_id, '_sb_address', true);
		$customFields 				= get_option("_carspotAPI_customFields");
		$customFields 				= json_decode($customFields, true);
		$form_type 					= (isset($customFields['form_type']) && $customFields['form_type'] == 'yes') ? $customFields['form_type'] : 'no';
		$form_type 					= (isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true) ? 'yes' : 'no';
		$package_type 				= (isset($carspotAPI['carspot_package_type']) && $carspotAPI['carspot_package_type'] == 'category_based') ? $carspotAPI['carspot_package_type'] : 'package_based';
		$data['package_type'] 		= $package_type;
		$data['is_update'] 			= $is_update;
		$data['ad_id']     			=   (int)$pid;
		$data['title'] 				= ($is_update != "") ? __("Edit Add", "carspot-rest-api") : __("Add Post", "carspot-rest-api");
		$data['hide_price'] 		= array("ad_price", "ad_price_type");
		$ad_currency_count  		= wp_count_terms('ad_currency');
		$data['hide_currency'] 		= array("ad_price", "ad_currency");
		$data['title_field_name'] 	= 'ad_title';
		$adTitle = $ad_content = $ad_yvideo = $ad_tags = $map_lat = $map_long = $ad_price = $ad_condition = $ad_warranty = $ad_type = $ad_price_type = $ad_currency = $ad_bidding_time = $ad_cats_lvl = $images = $ad_price_typeVal = '';
		$ad_cats = array();
		$dynamicData = array();
		$ad_bidding = 0;

		if ($is_update != "") {
			$pid = (int)$pid;
			$adData = @get_post($pid);
			$adTitle = @$adData->post_title;
			$ad_content = trim(@$adData->post_content);
			$display_name 		= get_post_meta($pid, '_carspot_poster_name', true);
			$sb_contact 		= get_post_meta($pid, '_carspot_poster_contact', true);
			$sb_location 		= get_post_meta($pid, '_carspot_ad_location', true);
			$map_lat 			= get_post_meta($pid, '_carspot_ad_map_lat', true);
			$map_long 			= get_post_meta($pid, '_carspot_ad_map_long', true);
			$ad_type 			= get_post_meta($pid, '_carspot_ad_type', true);
			$ad_condition 		= get_post_meta($pid, '_carspot_ad_condition', true);
			$ad_warranty		= get_post_meta($pid, '_carspot_ad_warranty', true);
			$ad_price			= get_post_meta($pid, '_carspot_ad_price', true);
			$ad_price_typeVal  	= get_post_meta($pid, '_carspot_ad_price_type', true);
			$ad_yvideo			= get_post_meta($pid, '_carspot_ad_yvideo', true);
			$ad_phone_number   = get_post_meta($pid, '_carspot_ad_phone_number', true);
			$ad_user_address  = get_post_meta($pid, '_carspot_ad_user_address', true);
			$ad_country  = get_post_meta($pid, '_carspot_ad_country', true);
			$ad_colors  = get_post_meta($pid, '_carspot_ad_colors', true);
			$ad_insurance  = get_post_meta($pid, '_carspot_ad_insurance', true);
			$ad_make  = get_post_meta($pid, '_carspot_ad_make', true);
			$ad_model  = get_post_meta($pid, '_carspot_ad_model', true);
			$ad_version  = get_post_meta($pid, '_carspot_ad_version', true);
			$ad_4thlevel  = get_post_meta($pid, '_carspot_ad_4thlevel', true);
			$ad_bidding 		= get_post_meta($pid, '_carspot_ad_bidding', true);
			$ad_bidding_time	= get_post_meta($pid, '_carspot_ad_bidding_date', true);
			$ad_currency		= get_post_meta($pid, '_carspot_ad_currency', true);
			$tags_array 		= wp_get_object_terms($pid,  'ad_tags', array('fields' => 'names'));
			$ad_tags			= (isset($tags_array)) ? implode(',', $tags_array) : array();
			$ad_cats			= wp_get_object_terms($pid,  'ad_cats', array('fields' => 'ids'));
			$ad_term_id = '';
			if (count($ad_cats) > 0) {
				$term_id 	= end($ad_cats);
				$ad_term_id = $term_id;
				if ($term_id != "") { /*$dynamicData = carspotAPI_post_ad_fields( '', $term_id, $pid );*/
				}
			}
		}
		// echo "here";exit;
		if (isset($dynamicData) && count($dynamicData) > 0 && $dynamicData != "" && $form_type != 'no') {
			$data['fields'] = $dynamicData;
		}

		$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_title', '', '', __("Ad Title", "carspot-rest-api"), '', '', '1', true, $adTitle);
		$data['fields'][] = carspotAPI_getPostAdFields('select', 'ad_cats1', 'ad_cats', 0, __("Categories", "carspot-rest-api"), '', '', '1', true, '', $is_update);

		if ($form_type == 'no') {

			if (isset($carspotAPI['allow_price_type']) && $carspotAPI['allow_price_type'] == 1) {
				$ad_price_type  	= carspotAPI_adPrice_types($ad_price_typeVal);
				$data['fields'][] 	= carspotAPI_getPostAdFields('select', 'ad_price_type', $ad_price_type, 0, __("Price Type", "carspot-rest-api"), '', '', '2', false, $ad_price_typeVal, $is_update);
			}
			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_price', '', 0, __("Ad Price", "carspot-rest-api"), '', '', '2', true, $ad_price);

			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_mileage', 'ad_mileage', 0, __("Mileage", "carspot-rest-api"), '', '', '2', false, '', $is_update);
			// if(isset( $carspot_theme['allow_ad_economy'] ) && $carspot_theme['allow_ad_economy'] == 1 ){
			// $data['fields'][] = carspotAPI_getPostAdFields('textfield'	,'ad_avg_hwy', 'ad_avg_hwy', 0, __("Average in Highway", "carspot-rest-api"),'', '','2', false, '', $is_update);
			// $data['fields'][] = carspotAPI_getPostAdFields('textfield'	,'ad_avg_city', 'ad_avg_city', 0, __("Average in City", "carspot-rest-api"),'', '','2', false, '', $is_update);
			// }
			// if(isset( $carspot_theme['allow_tax_condition'] ) && $carspot_theme['allow_tax_condition'] == 1 ){
			//  $data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_condition', 'ad_condition', 0, __("Condition", "carspot-rest-api"),'','','2', true, '',$is_update);
			// }
			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_type', 'ad_type', 0, __("Ad Type", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			if (isset($carspot_theme['allow_tax_warranty']) && $carspot_theme['allow_tax_warranty'] == 1) {
				$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_warranty', 'ad_warranty', 0, __("Warranty", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			}

			if (isset($carspot_theme['allow_ad_years']) && $carspot_theme['allow_ad_years'] == 1) {
				$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_years', 'ad_years', 0, __("Year", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			}
			if (isset($carspot_theme['allow_ad_body_types']) && $carspot_theme['allow_ad_body_types'] == 1) {
				$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_body_types', 'ad_body_types', 0, __("Body Type", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			}

			if (isset($carspot_theme['allow_ad_transmissions']) && $carspot_theme['allow_ad_transmissions'] == 1) {
				$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_transmissions', 'ad_transmissions', 0, __("Transmission", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			}

			if (isset($carspot_theme['allow_ad_engine_capacities']) && $carspot_theme['allow_ad_engine_capacities'] == 1) {
				$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_engine_capacities', 'ad_engine_capacities', 0, __("Capacity", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			}

			if (isset($carspot_theme['allow_ad_engine_types']) && $carspot_theme['allow_ad_engine_types'] == 1) {
				$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_engine_types', 'ad_engine_types', 0, __("Engine Type", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			}

			if (isset($carspot_theme['allow_ad_assembles']) && $carspot_theme['allow_ad_assembles'] == 1) {
				$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_assembles', 'ad_assembles', 0, __("Assembly", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			}

			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_years', 'ad_years', 0, __("Year", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			// $data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_body_types', 'ad_body_types', 0, __("Body Type", "carspot-rest-api"),'','','2', true, '', $is_update);
			// $data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_transmissions', 'ad_transmissions', 0, __("Transmission", "carspot-rest-api"),'','','2', true, '', $is_update);
			// $data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_engine_capacities', 'ad_engine_capacities', 0, __("Capacity", "carspot-rest-api"),'','','2', true, '', $is_update);
			// $data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_engine_types', 'ad_engine_types', 0, __("Engine Type", "carspot-rest-api"),'','','2', true, '', $is_update);
			// $data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_assembles', 'ad_assembles', 0, __("Assembly", "carspot-rest-api"),'','','2', true, '', $is_update);
			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_colors', 'ad_colors', 0, __("Colour", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_insurance', 'ad_insurance', 0, __("Insurance", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_country', 'ad_country', 0, __("Country", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_make', 'ad_make', 0, __("Make", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_model', 'ad_model', 0, __("Country", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_version', 'ad_version', 0, __("Version", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			$data['fields'][] =  carspotAPI_getPostAdFields('select', 'ad_4thlevel', 'ad_4thlevel', 0, __("4thLevel", "carspot-rest-api"), '', '', '2', true, '', $is_update);
			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_phone_number', '', 0, __("Phone Number", "carspot-rest-api"), '', '', '2', false, '', $is_update);
			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_user_name', '', 0, __("Your Name", "carspot-rest-api"), '', '', '2', false, '', $is_update);
			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_user_address', '', 0, __("Address", "carspot-rest-api"), '', '', '2', false, '', $is_update);


			/*$data['fields'][] = carspotAPI_getPostAdFields('textfield'	,'ad_tags1', '', 0, __("Tags Comma(,) separated", "carspot-rest-api"),'', '','2', false, '', $is_update);*/
			/*$data['fields'][] = carspotAPI_getPostAdFields('textfield'	,'ad_tags1', '', 0, __("Tags Comma(,) separated", "carspot-rest-api"),'', '','2', false, '', $is_update);*/
			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_tags', '', 0, __("Tags Comma(,) separated", "carspot-rest-api"), '', '', '2', false, $ad_tags);
			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_tags', '', 0, __("Tags Comma(,) separated", "carspot-rest-api"), '', '', '2', false, $ad_tags);
			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_yvideo', '', 0, __("Youtube Video Link", "carspot-rest-api"), '', '', 2, false, $ad_yvideo);
			$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_yvideo', '', 0, __("Youtube Video Link", "carspot-rest-api"), '', '', 2, false, $ad_yvideo);
			if (isset($carspot_theme['allow_ad_features']) && $carspot_theme['allow_ad_features'] == 1) {
				$data['fields'][] = carspotAPI_getPostAdFields('checkbox', 'ad_features', 'ad_features', 0, __("Features", "carspot-rest-api"), '', '', '3', true, '', $is_update);
			}
		}
		//$data['fields'][] = carspotAPI_getPostAdFields('image'	 , 'ad_img', '', 0, __("Add Images", "carspot-rest-api"),'', '', 2);	
		$custom_fields = (isset($customFields['custom_fields']) && $form_type == 'no') ? $customFields['custom_fields'] : array();
		if (isset($custom_fields) && count($custom_fields) > 0) {
			foreach ($custom_fields as $fields) {
				$title = $fields['title'];
				$slug = '_sb_extra_' . $fields['slug'];

				if ($fields['type'] == 'text') {
					$data['fields'][] = carspotAPI_getPostAdFields('textfield', $slug, $slug, 0, $title, '', '', '1', false, '', $is_update);
				}
				if ($fields['type'] == 'select' && $fields['option_values'] != '') {
					$option_values = explode(",", $fields['option_values']);
					$data['fields'][] = carspotAPI_getPostAdFields('select',  $slug, $option_values, 0, $title, '', '', '1', false, '', $is_update);
				}
			}
		}
		$data['fields'][] = carspotAPI_getPostAdFields('textarea', 'ad_description', '', 0, __("Description", "carspot-rest-api"), '', '', '3', true, $ad_content);

		if (isset($carspot_theme['sb_enable_comments_offer_user']) && $carspot_theme['sb_enable_comments_offer_user']) {
			/*Enabled only if by admin*/
			$ad_biddingArr_on = array("key" => "1", "val" => __('On', 'carspot-rest-api'), "is_show" => true);
			$ad_biddingArr_off = array("key" => "0", "val" => __('Off', 'carspot-rest-api'), "is_show" => true);
			if ($ad_bidding == 1) {
				$ad_biddingArr[] = $ad_biddingArr_on;
				$ad_biddingArr[] = $ad_biddingArr_off;
			} else {
				$ad_biddingArr[] = $ad_biddingArr_off;
				$ad_biddingArr[] = $ad_biddingArr_on;
			}

			$data['fields'][] = carspotAPI_getPostAdFields('select', 'ad_bidding', $ad_biddingArr, 0, __("Bidding", "carspot-rest-api"), '', '', '2', false, '', $is_update);
			$bidding_timer_show = (isset($carspotAPI['bidding_timer']) && $carspotAPI['bidding_timer']) ? true : false;
			/*$top_bidder_limit_show = ( isset( $carspotAPI['top_bidder_limit'] ) && $carspotAPI['top_bidder_limit'] > 0) ? true : false;*/

			if ($bidding_timer_show) {
				$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_bidding_time', '', '', __("Bidding Time", "carspot-rest-api"), '', '', '3', false, $ad_bidding_time);
			}
		}
		$data['profile']['name'] = carspotAPI_getPostAdFields('textfield', 'name', '', 0, __("Name", "carspot-rest-api"), '', $display_name, '4', true, $display_name);
		$sb_change_ph       = (isset($carspotAPI['sb_change_ph']) && $carspotAPI['sb_change_ph'] == false) ? false : true;
		$is_verification_on = (isset($carspotAPI['sb_phone_verification']) && $carspotAPI['sb_phone_verification']) ? true : false;

		$data['profile']['is_phone_verification_on'] = $is_verification_on;
		$data['profile']['phone_editable'] 			 = $sb_change_ph;
		$data['profile']['phone'] 					 = carspotAPI_getPostAdFields('textfield', 'ad_phone', '', 0, __("Phone Number", "carspot-rest-api"), '', $sb_contact, '4', true, $sb_contact);


		/*Start Editing Here*/
		$data['profile']['ad_country_show'] = false;
		$is_show_location  = wp_count_terms('ad_country');
		if (isset($carspot_theme['enable_custom_locationz']) && $carspot_theme['enable_custom_locationz'] == 1) {
			if (isset($is_show_location) && (int)$is_show_location > 0) {
				$data['profile']['ad_country_show'] = true;
				$data['profile']['ad_country'] = carspotAPI_getPostAdFields('select', 'ad_country', 'ad_country', 0, __("Location", "carspot-rest-api"), '', '', '4', true, '', $is_update);
			}
			//ad_country
		}
		if (isset($json_data['is_update']) && $json_data['is_update'] != "") {
			/**/
		} else {
			$sb_location = '';
			$map_lat  = ($map_lat  != "") ? $map_lat  : $carspot_theme['sb_default_lat'];
			$map_long = ($map_long != "") ? $map_long : $carspot_theme['sb_default_long'];
		}
		$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_map_lat', '', 0, __("ad map lat", "carspot-rest-api"), '', '', '2', false, '', $map_lat);
		$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_map_long', '', 0, __("ad map long", "carspot-rest-api"), '', '', '2', false, '', $map_long);
		//
		$data['profile']['location'] = carspotAPI_getPostAdFields('glocation_textfield', 'ad_location', '', 0, __("Address", "carspot-rest-api"), '', $sb_location, '4', true, $sb_location);

		$map_on_off	=	(isset($carspot_theme['allow_lat_lon']) && $carspot_theme['allow_lat_lon'] == 1) ? true : false;
		$data['profile']['map']['on_off'] = $map_on_off;
		$data['profile']['map']['location_lat'] = carspotAPI_getPostAdFields('textfield', 'location_lat', '', 0, __("Latitude", "carspot-rest-api"), '', $map_lat, '4', true, $map_lat);
		$data['profile']['map']['location_long'] = carspotAPI_getPostAdFields('textfield', 'location_long', '', 0, __("Longitude", "carspot-rest-api"), '', $map_long, '4', true, $map_long);


		if ($pid != "") {
			$images = carspotAPI_get_ad_image($pid, -1, 'thumb', false);
		}
		$data['ad_images'] = $images;
		$maxLimit = (isset($carspotAPI['sb_upload_limit'])) ? $carspotAPI['sb_upload_limit'] : 5;
		$remaningImages = $maxLimit - count($images);
		if ($remaningImages <= 0) {
			$remaningImages = 0;
			$moer_message = __("you can not upload more images", "carspot-rest-api");
		} else {
			$moer_message = __("your can upload", "carspot-rest-api") . ' ' . $remaningImages . ' ' . __("more image", "carspot-rest-api");
		}
		$data['ad_images'] 			= $images;
		$data['images']['is_show'] 	= true;
		$data['images']['numbers'] 	= $remaningImages;
		$data['images']['message'] 	= $moer_message;
		$data['btn_submit'] 		= __("Post Ad", "carspot-rest-api");
		$data['ad_cat_id'] 			= (isset($ad_term_id) && $ad_term_id != "") ? $ad_term_id : '';
		$is_update_notice 			= '';
		if (isset($json_data['is_update']) && $json_data['is_update'] != "") {
			$is_update_notice = (isset($carspotAPI['sb_ad_update_notice']) && $carspotAPI['sb_ad_update_notice'] != "") ? $carspotAPI['sb_ad_update_notice'] : '';
		}
		$data["update_notice"] = $is_update_notice;
		/*Bump Ads starts here */
		$bump_ad_is_show	= false;
		$message_title = '';

		$data['profile']['pricing']['is_show']	= true;
		$data['profile']['pricing']['type']		= $package_type;
		//bump test line added below

		$is_show_bump_package = false;
		if (isset($carspot_theme['sb_allow_free_bump_up']) && $carspot_theme['sb_allow_free_bump_up'] == true) {
			$bump_ad_is_show = true;
			$message_title = __("Bump it up on the top of the list. Ads remaining: Unlimited", "carspot-rest-api");
		} else if (get_user_meta($user_id, '_carspot_expire_ads', true) == '-1' || get_user_meta($user_id, '_carspot_expire_ads', true) >= date('Y-m-d')) {
			$bump_count = get_user_meta($user_id, '_carspot_bump_ads', true);
			if ($bump_count > 0  || $bump_count == '-1') {
				if ($bump_count == '-1') {
					$bump_ad_is_show = true;
					$message_title = __("Bump it up on the top of the list. Ads remaining: Unlimited", "carspot-rest-api");
				} else {
					$bump_ad_is_show = true;
					$message_title = __("Bump it up on the top of the list. Ads remaining: ", "carspot-rest-api") . get_user_meta($user_id, '_carspot_bump_ads', true);
				}
			} else {

				$bump_notify = carspotAPI_adBump_notify($pid);
				$is_show_bump_package = true;
			}
		} else {

			$bump_notify = carspotAPI_adBump_notify($pid);
			$is_show_bump_package = true;
		}
		if ($is_update != "") {
			$bump_ad_is_show = true;
			if ($is_show_bump_package == true) {
				$bump_ad_is_show = false;
			}
		}
		if ($is_update == "") {
			$bump_ad_is_show = false;
		}

		//MyCodeHere
		$data['profile']['pricing']['package_based']['bump_ad_buy']	= $is_show_bump_package;
		if ($is_show_bump_package) {
			$data['profile']['pricing']['package_based']['bump_ad_notify']	= $bump_notify;
		}


		$data['profile']['pricing']['package_based']['bump_ad_is_show']	= $bump_ad_is_show;
		if ($bump_ad_is_show) {
			$data['profile']['pricing']['package_based']['bump_ad_text']	=  array(
				"title" => __("Confirmation", "carspot-rest-api"),
				"text" => __("Are you sure you want to bumup this ad.", "carspot-rest-api"),
				"btn_no" => __("Cancel", "carspot-rest-api"),
				"btn_ok" => __("Confirm", "carspot-rest-api"),
			);
			$is_update1 = '';
			$bump_ad_checkbox_arr	= array("main_title" => "", "field_type" => 'checkbox', "field_type_name" => 'ad_bump_ad', "field_val" => "", "field_name" => "", "title" => $message_title, "values" => "", "has_page_number" => "3", "is_required" => false, "has_cat_template" => false);
			$data['profile']['pricing']['package_based']['bump_ad'] = $bump_ad_checkbox_arr;
		}
		/*Bump Ads ends here */
		/*Featured Ads starts here */
		$featured_ad_is_show = false;
		$featured_ad_title = '';
		$is_feature_ad	= get_post_meta($pid, '_carspot_is_feature', true);
		$is_feature_ad  = ($is_feature_ad) ? $is_feature_ad : 0;
		//$is_feature_ad = 0;
		$is_show_package = false;
		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			$featured_ad_title = __("If you want to make it feature then please have a look on", "carspot-rest-api");
			if (isset($carspot_theme['sb_allow_featured_ads']) && $carspot_theme['sb_allow_featured_ads']) {
				$isFeature = get_post_meta($pid, '_carspot_is_feature', true);
				$isFeature = ($isFeature) ? $isFeature : 0;
				if ($isFeature != 1) {

					$sb_expire_ads   = get_user_meta($user_id, '_carspot_expire_ads', true);
					$sb_featured_ads = get_user_meta($user_id, '_carspot_featured_ads', true);
					if ($is_feature_ad == 0 && ($sb_expire_ads == '-1' || $sb_expire_ads >= date('Y-m-d'))) {

						if ($sb_featured_ads == '-1' || $sb_featured_ads > 0) {

							$featured_ad_title	= __('Featured ads remaining: Unlimited', 'carspot-rest-api');
							$featured_ad_is_show = true;
							if (get_user_meta($user_id, '_carspot_featured_ads', true) > 0) {
								$featured_ad_title	= __('Featured ads remaining: ', 'carspot-rest-api') . get_user_meta($user_id, '_carspot_featured_ads', true);
							}
							$feature_text	=	'';
							if (isset($carspot_theme['sb_feature_desc']) && $carspot_theme['sb_feature_desc'] != "") {
								$feature_text = $carspot_theme['sb_feature_desc'];
							}
						} else {

							$featured_notify = carspotAPI_adFeatured_notify($pid);

							$is_show_package = true;
						}
					} else {

						$featured_notify = carspotAPI_adFeatured_notify($pid);
						$is_show_package = true;
					}
				}
			}
		}

		////MyCodeHere
		//$is_show_package = $featured_ad_is_show = false;
		$data['profile']['pricing']['package_based']['featured_ad_buy']	= $is_show_package;
		if ($is_show_package) {
			$data['profile']['pricing']['package_based']['featured_ad_notify']	= $featured_notify;
		}

		$data['profile']['pricing']['package_based']['featured_ad_is_show']	= $featured_ad_is_show;
		if ($featured_ad_is_show) {
			$data['profile']['pricing']['package_based']['featured_ad_text']	= array(
				"title" => __("Confirmation", "carspot-rest-api"),
				"text" => __("Are you sure you want to make this ad featured.", "carspot-rest-api"),
				"btn_no" => __("Cancel", "carspot-rest-api"),
				"btn_ok" => __("Confirm", "carspot-rest-api"),
			);

			$featured_ad_checkbox_arr	= array("main_title" => "", "field_type" => 'checkbox', "field_type_name" => 'ad_featured_ad', "field_val" => "", "field_name" => "", "title" => $featured_ad_title, "values" => "", "has_page_number" => "1", "is_required" => false, "has_cat_template" => false);

			$data['profile']['pricing']['package_based']['featured_ad'] = $featured_ad_checkbox_arr;
		}

		$is_redirect = (isset($carspotAPI['carspot_package_type']) && $carspotAPI['carspot_package_type'] == 'category_based') ? true : false;
		$cat_template_on = (isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template']) ? true : false;
		$is_update_check = ($is_update != "") ? 1 : 0;
		$data['profile']['pricing']['category_based'] = carspotAPI_getPostAd_adons('both', $is_update_check);

		/*Featured Ads starts ends */
		$extra['image_text']     	= 	__("Select Images", "carspot-rest-api");
		$extra['user_info']      	= 	__("User Information", "carspot-rest-api");
		$extra['sort_image_msg'] 	= 	__("You can re-arange image by draging them.", "carspot-rest-api");
		$extra['dialog_send']    	= 	__("Submit", "carspot-rest-api");
		$extra['dialg_cancel']   	= 	__("Cancel", "carspot-rest-api");
		$extra['youtube']        	= 	__("Youtube video link", "carspot-rest-api");
		$extra['tags_txt']       	= 	__("Tags Comma(,) separated", "carspot-rest-api");
		$extra['price_type_title']  = 	__("Price Type", "carspot-rest-api");
		$extra['price_type']  		= 	__("Price", "carspot-rest-api");
		$extra['next_step']  		= 	__("Next Step", "carspot-rest-api");
		$extra['price_type_data']  	= 	carspotAPI_adPrice_types($ad_price_typeVal);
		$bidding_timer_show 		= 	($ad_bidding) ? true : false;
		$extra['is_show_bidtime']  	= 	$bidding_timer_show;
		/*$request_from = carspotAPI_getSpecific_headerVal('carspot-Request-From');*/
		$response = array('success' => true, 'data' => $data, 'message' => '', 'extra' => $extra);
		return $response;
	}
}

if (!function_exists('carspotAPI_getPostAd_adons')) {
	function carspotAPI_getPostAd_adons($show_on = 'both', $is_edit = 0)
	{
		global $carspot_theme;
		$extraFeatures = '';
		$args = array(
			'post_type' => 'product',
			'meta_key' => 'carspot_package_type',
			'post_status' => 'publish',
			'tax_query' => array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'carspot_category_pricing'),),
			'meta_query' => array('key' => 'carspot_package_type', 'value' => 'adons_based', 'compare' => '=',),
			'posts_per_page' => -1,
		);

		$lists_array =  $list_array = array();
		$lists = array();
		$is_show_pricing = false;

		$loop = new WP_Query($args);
		if ($loop->have_posts()) {
			global $woocommerce;
			while ($loop->have_posts()) {
				$loop->the_post();
				global $product;
				$id = get_post_meta(get_the_ID(), "carspot_package_ad_type", true);
				if ($id == 'bump' && $is_edit == 0) {
					continue;
				}
				$is_show_pricing = true;
				$list_array['adonid']  	= get_the_ID();
				$list_array['title']  	= get_the_title();
				$list_array['content']  = get_the_content();
				$list_array['price']  	= carspotAPI_convert_uniText(strip_tags(wc_price($product->get_price())));
				$list_array['type']  	= $id;
				$list_array['button']   = carspotAPI_adon_cart_button_text(get_the_ID());

				$lists_array[] = $list_array;
			}
			wp_reset_query();
		}

		$lists['is_show'] 		= $is_show_pricing;
		$lists['section_title'] = esc_html__("Adons", "carspot-rest-api");
		$lists['pricing'] 		= $lists_array;

		return $lists;
	}
}

/* Cart Button */
if (!function_exists('carspotAPI_adon_cart_button_text')) {
	function carspotAPI_adon_cart_button_text($pid)
	{
		wc()->frontend_includes();
		WC()->session = new WC_Session_Handler();
		WC()->session->init();
		WC()->customer = new WC_Customer(get_current_user_id(), true);
		WC()->cart = new WC_Cart();
		$btn_type  = 'add';
		$btn_txt   = esc_html__('Add To Cart', 'carspot-rest-api');
		foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
			$_product = $values['data'];
			if ($pid == $_product->id) {
				$btn_txt   = esc_html__('Remove', 'carspot-rest-api');
				$btn_type  = 'remove';
			}
		}
		return array("btn_type" => $btn_type, "btn_txt" => $btn_txt);
	}
}

if (!function_exists('carspotAPI_templating_func')) {
	function carspotAPI_templating_func()
	{
		global $carspotAPI;
		return (isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true) ? 'yes' : 'no';
	}
}

if (!function_exists('carspotAPI_ad_post_extra_fields')) {
	function carspotAPI_ad_post_extra_fields($ad_id = '')
	{
		return '';
		$extra_fields_html	=	'';
		// Making fields
		$arrays = array();
		$atts = WPBMap::getParam('ad_post_short_base', 'fields');
		return $atts['params'];
		//return $rows = vc_param_group_parse_atts( $atts['params'] );
		return $atts['params'];
		//if(isset($atts['fields'])){}
		if (true) {
			$rows = vc_param_group_parse_atts($atts);
			if (count($rows[0]) > 0 && count($rows) > 0) {
				$extra_section_title;
				foreach ($rows as $row) {
					$has_page_number  = 1;
					$is_required = true; //( isset( $row['is_req'] ) && $row['is_req'] == 1  ) ? true : false;
					if (isset($row['is_req']) && isset($row['type']) && isset($row['slug'])) {
						$name 		= esc_html($row['title']);
						$fieldName = 'sb_extra_' . $total_fileds;
						if ($row['type'] == 'text') {
							$fieldValue = ($ad_id != "") ? get_post_meta($ad_id, '_sb_extra_' . $row['slug'], true) : "";

							$arrays["$fieldName"] = 	array("main_title" => $name, "field_type" => 'textfield', "field_type_name" => $fieldName, "field_val" => "", "field_name" => "", "title" => $name, "values" => $fieldValue, "has_page_number" => $has_page_number, "is_required" => $is_required);
						}
						if ($row['type'] == 'select' && isset($row['option_values'])) {
							$termsArr[] = array("id" => "",  "name" => __("Select Option", "carspot-rest-api"), "has_sub" => false, "has_template" => false,);
							$options	=	@explode(',', $row['option_values']);
							foreach ($options as $key => $value) {
								$is_select	=	'';
								if ($ad_id != "") {
									$is_select($value == get_post_meta($ad_id, '_sb_extra_' . $row['slug'], true)) ? 'selected' : '';
								}
								$termsArr[] = array("id" => $value, "name" => $value, "has_sub" => false, "has_template" => false,);
							}
							$arrays["$fieldName"] = array("main_title" => $name, "field_type" => 'textfield', "field_type_name" => $fieldName, "field_val" => "", "field_name" => "", "title" => $name, "values" => $fieldValue, "has_page_number" => $has_page_number, "is_required" => $is_required);
						}
						$total_fileds++;
					}
				}
			}
		}
		return $arrays;
	}
}

add_action('rest_api_init', 'carspotAPI_post_ad_post_hooks', 0);
function carspotAPI_post_ad_post_hooks()
{
	register_rest_route(
		'carspot/v1',
		'/post_ad/',
		array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_post_ad_post',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
}

if (!function_exists('carspotAPI_post_ad_post')) {
	function carspotAPI_post_ad_post($request)
	{
		global $carspotAPI;
		global $carspot_theme;
		$user = wp_get_current_user();
		$user_id = $user->data->ID;

		$json_data 		 = $request->get_json_params();
		$ad_title 		 = isset($json_data['ad_title']) ? trim($json_data['ad_title']) : '';
		$ad_cats  		 = isset($json_data['ad_cats1']) ? $json_data['ad_cats1'] : '';
		$ad_country		 = isset($json_data['ad_country']) ? $json_data['ad_country'] : '';
		$ad_description  = isset($json_data['ad_description']) ? trim($json_data['ad_description']) : '';
		$ad_status 		 = ($carspot_theme['sb_ad_approval'] == 'manual') ? 'pending' : 'publish';
		$ad_categories  = isset($json_data['ad_categories']) ? $json_data['ad_categories'] : '';
		$ad_map_long  =  isset($json_data['ad_map_long']) ? $json_data['ad_map_long'] : '';
		$ad_map_lat  = isset($json_data['ad_map_lat']) ? $json_data['ad_map_lat'] : '';
		$ad_user_name = isset($json_data['ad_user_name']) ? $json_data['ad_user_name'] : '';

		$isUpdate	=	false;
		if ((isset($json_data['ad_id']) && $json_data['ad_id'] != "") && (isset($json_data['is_update']) && $json_data['is_update'] != "")) {
			$pid	=	$json_data['ad_id'];
			$isUpdate	=	true;
			$ad_status = ($carspot_theme['sb_update_approval'] == 'manual') ? 'pending' : 'publish';
			if (CARSPOT_API_ALLOW_EDITING == false) {
				$response = array('success' => false, 'data' => '', 'message' => __("Editing Not Alloded In Demo", "carspot-rest-api"));
				return $response;
			}
		} else {
			$pid	=	get_user_meta($user_id, 'ad_in_progress', true);
			if ($pid) {
			} else {
				$pid	=	$json_data['ad_id'];
			}
			print_r($ad_categories);
			echo $ad_categories_string = parse_json($ad_categories);exit;
			update_post_meta($pid, '_carspot_ad_categories', $ad_categories);
			update_post_meta($pid, '_carspot_ad_categories_string', $ad_categories_string);
			// echo $pid;exit;
			delete_user_meta($user_id, 'ad_in_progress');
			// if (!is_super_admin($user_id)) {
			// 	// $simple_ads_check	=	get_user_meta($user_id, '_sb_simple_ads', true);
			// 	$simple_ads_check = 1;
			// 	$expiry_check	    =   get_user_meta($user_id, '_carspot_expire_ads', true);

			// 	if ($simple_ads_check == -1) {
			// 	} else if ($simple_ads_check <= 0) {
			// 		$posted_msg = __("You have no more ads to post", "carspot-rest-api");
			// 		$response = array('success' => false, 'data' => '', 'message' => $posted_msg, 'extra' => '');
			// 		return $response;
			// 	}

			// 	if ($expiry_check != '-1') {
			// 		if ($expiry_check < date('Y-m-d')) {
			// 			$posted_msg = __("Your package has expired", "carspot-rest-api");
			// 			$response = array('success' => false, 'data' => '', 'message' => $posted_msg, 'extra' => '');
			// 			return $response;
			// 		}
			// 	}
			// }

			$simple_ads	=	get_user_meta($user_id, '_sb_simple_ads', true);
			if ($simple_ads > 0 && !is_super_admin($user_id)) {
				$simple_ads	=	$simple_ads - 1;
				update_user_meta($user_id, '_sb_simple_ads', $simple_ads);
			}
			update_post_meta($pid, '_carspot_ad_status_', 'active');
			update_post_meta($pid, '_carspot_is_feature', '0');
			carspotAPI_get_notify_on_ad_post($pid);
		}

		$ad_cats_id  = @explode("|", $ad_cats);

		$cats_arr 		=  carspotAPI_cat_ancestors($ad_cats_id[0], 'ad_cats', false);
		//print_r($cats_arr);
		$is_imageallow 	= carspotAPI_CustomFieldsVals($pid, $cats_arr);
		$media 			= get_attached_media('image', $pid);
		// print_r($media);exit;
		if ($is_imageallow == 1 && count($media) == 0) {
			//$response = array( 'success' => false, 'data' => '' , 'message' => __("Images are required", "carspot-rest-api") );
			//return $response;
		}
		global $wpdb;
		$qry = "UPDATE $wpdb->postmeta SET meta_value = '' WHERE post_id = '$pid' AND meta_key LIKE '_carspot_tpl_field_%'";
		@$wpdb->query($qry);

		$words		= @explode(',', $carspot_theme['bad_words_filter']);
		$replace	= $carspot_theme['bad_words_replace'];
		$desc		= carspotAPI_badwords_filter($words, $ad_description, $replace);
		$title		= carspotAPI_badwords_filter($words, $ad_title, $replace);
		$my_post 	= array('ID' => $pid, 'post_title' => $title, 'post_content'   => $desc, 'post_type' => 'ad_post', 'post_name' => $title, "");
		$pid 		= wp_update_post($my_post);

		global $wpdb;
		$wpdb->query("UPDATE $wpdb->posts SET post_status = '$ad_status' WHERE ID = '$pid' ");
		$catsArr	=  carspotAPI_cat_ancestors($ad_cats, 'ad_cats', true);
		wp_set_post_terms($pid, $catsArr, 'ad_cats');

		$is_show_location  = wp_count_terms('ad_country');
		if (isset($is_show_location) && $is_show_location > 0) {
			$ad_country_id  = @explode("|", $ad_country);
			if (isset($ad_country_id) && $ad_country_id != "") {
				$ad_countryArr	=  carspotAPI_cat_ancestors($ad_country_id[0], 'ad_country', true);
				wp_set_post_terms($pid, $ad_countryArr, 'ad_country');
			}
		}

		/* ads  */
		$ad_yvideo = '';

		$custom_fields = @$json_data['custom_fields'];
		$request_from = carspotAPI_getSpecific_headerVal('carspot-Request-From');
		if ($request_from == 'ios') {
			$custom_fields = json_decode(@$json_data['custom_fields'], true);
		}


		$custom_fields   = isset($custom_fields) ? $custom_fields : array();
		$in_loop = false;

		$in_loop = $ad_yvideo = $ad_price = $ad_price_types = $ad_currency =  $ad_warranty = $ad_type =  $ad_condition = $ad_tags = '';
		if (isset($custom_fields) && count($custom_fields) > 0) {
			if ($request_from == 'ios') {

				$ad_tags 				= isset($json_data['ad_tags']) ? trim($json_data['ad_tags']) : '';
				$ad_condition   		= isset($json_data['ad_condition']) ? $json_data['ad_condition'] : '';
				$ad_type  	   			= isset($json_data['ad_type']) ? $json_data['ad_type'] : '';
				$ad_warranty   			= isset($json_data['ad_warranty']) ? $json_data['ad_warranty'] : '';
				$ad_years   			= isset($json_data['ad_years']) ? $json_data['ad_years'] : '';
				$ad_body_types   		= isset($json_data['ad_body_types']) ? $json_data['ad_body_types'] : '';
				$ad_transmissions   	= isset($json_data['ad_transmissions']) ? $json_data['ad_transmissions'] : '';
				$ad_engine_capacities  	= isset($json_data['ad_engine_capacities']) ? $json_data['ad_engine_capacities'] : '';
				$ad_engine_types   		= isset($json_data['ad_engine_types']) ? $json_data['ad_engine_types'] : '';
				$ad_assembles   		= isset($json_data['ad_assembles']) ? $json_data['ad_assembles'] : '';
				$ad_colors  	   		= isset($json_data['ad_colors']) ? $json_data['ad_colors'] : '';
				$ad_insurance   		= isset($json_data['ad_insurance']) ? $json_data['ad_insurance'] : '';
				$ad_currency   			= isset($json_data['ad_currency']) ? $json_data['ad_currency'] : '';
				$ad_price_types			= isset($json_data['ad_price_type']) ? $json_data['ad_price_type'] : '';
				$ad_price   			= isset($json_data['ad_price']) ? $json_data['ad_price'] : '';
				$ad_yvideo   			= isset($json_data['ad_yvideo']) ? $json_data['ad_yvideo'] : '';
				$add_type               = isset($json_data['add_type']) ? $json_data['add_type'] : '';
				$features    			= isset($json_data['ad_features']) ? $json_data['ad_features'] : '';
				$ad_mileage   			= isset($json_data['ad_mileage']) ? $json_data['ad_mileage'] : '';

				$ad_avg_hwy   			= isset($json_data['ad_avg_hwy']) ? $json_data['ad_avg_hwy'] : '';
				$ad_avg_city   			= isset($json_data['ad_avg_city']) ? $json_data['ad_avg_city'] : '';

				$ad_phone_number = isset($json_data['ad_phone_number']) ? $json_data['ad_phone_number'] : '';
				$ad_user_address = isset($json_data['ad_user_address']) ? $json_data['ad_user_address'] : '';
				$ad_country = isset($json_data['ad_country']) ? $json_data['ad_country'] : '';
				$ad_colors = isset($json_data['ad_colors']) ? $json_data['ad_colors'] : '';
				$ad_insurance = isset($json_data['ad_insurance']) ? $json_data['ad_insurance'] : '';
				$ad_make = isset($json_data['ad_make']) ? $json_data['ad_make'] : '';
				$ad_model = isset($json_data['ad_model']) ? $json_data['ad_model'] : '';
				$ad_version = isset($json_data['ad_version']) ? $json_data['ad_version'] : '';
				$ad_4thlevel = isset($json_data['ad_4thlevel']) ? $json_data['ad_4thlevel'] : '';
				//

			} else {

				$ad_tags 				= isset($json_data['custom_fields']['ad_tags']) ? trim($json_data['custom_fields']['ad_tags']) : '';
				$ad_condition   		= isset($json_data['custom_fields']['ad_condition']) ? $json_data['custom_fields']['ad_condition'] : '';
				$ad_type  	   			= isset($json_data['custom_fields']['ad_type']) ? $json_data['custom_fields']['ad_type'] : '';
				$ad_warranty   			= isset($json_data['custom_fields']['ad_warranty']) ? $json_data['custom_fields']['ad_warranty'] : '';
				$ad_years   			= isset($json_data['custom_fields']['ad_years']) ? $json_data['custom_fields']['ad_years'] : '';
				$ad_body_types   		= isset($json_data['custom_fields']['ad_body_types']) ? $json_data['custom_fields']['ad_body_types'] : '';
				$ad_transmissions   	= isset($json_data['custom_fields']['ad_transmissions']) ? $json_data['custom_fields']['ad_transmissions'] : '';
				$ad_engine_capacities  	= isset($json_data['custom_fields']['ad_engine_capacities']) ? $json_data['custom_fields']['ad_engine_capacities'] : '';
				$ad_engine_types   		= isset($json_data['custom_fields']['ad_engine_types']) ? $json_data['custom_fields']['ad_engine_types'] : '';
				$ad_assembles   		= isset($json_data['custom_fields']['ad_assembles']) ? $json_data['custom_fields']['ad_assembles'] : '';
				$ad_colors  	   		= isset($json_data['custom_fields']['ad_colors']) ? $json_data['custom_fields']['ad_colors'] : '';
				$ad_insurance   		= isset($json_data['custom_fields']['ad_insurance']) ? $json_data['custom_fields']['ad_insurance'] : '';
				$ad_currency   			= isset($json_data['custom_fields']['ad_currency']) ? $json_data['custom_fields']['ad_currency'] : '';
				$ad_price_types			= isset($json_data['custom_fields']['ad_price_type']) ? $json_data['custom_fields']['ad_price_type'] : '';
				$ad_price   			= isset($json_data['custom_fields']['ad_price']) ? $json_data['custom_fields']['ad_price'] : '';
				$ad_yvideo   			= isset($json_data['custom_fields']['ad_yvideo']) ? $json_data['custom_fields']['ad_yvideo'] : '';
				$ad_usage   			= isset($json_data['custom_fields']['ad_Usage']) ? $json_data['custom_fields']['ad_Usage'] : '';
				$ad_mileage   			= isset($json_data['custom_fields']['ad_mileage']) ? $json_data['custom_fields']['ad_mileage'] : '';
				$add_type               = isset($json_data['custom_fields']['add_type']) ? $json_data['custom_fields']['add_type'] : '';
				$ad_avg_hwy   			= isset($json_data['custom_fields']['ad_avg_hwy']) ? $json_data['custom_fields']['ad_avg_hwy'] : '';
				$ad_avg_city   			= isset($json_data['custom_fields']['ad_avg_city']) ? $json_data['custom_fields']['ad_avg_city'] : '';


				$features    			= isset($json_data['custom_fields']['ad_features']) ? $json_data['custom_fields']['ad_features'] : '';

				$ad_phone_number = isset($json_data['custom_fields']['ad_phone_number']) ? $json_data['custom_fields']['ad_phone_number'] : '';
				$ad_user_address = isset($json_data['custom_fields']['ad_user_address']) ? $json_data['custom_fields']['ad_user_address'] : '';
				$ad_country = isset($json_data['custom_fields']['ad_country']) ? $json_data['custom_fields']['ad_country'] : '';
				$ad_colors = isset($json_data['custom_fields']['ad_colors']) ? $json_data['custom_fields']['ad_colors'] : '';
				$ad_insurance = isset($json_data['custom_fields']['ad_insurance']) ? $json_data['custom_fields']['ad_insurance'] : '';
				$ad_make = isset($json_data['custom_fields']['ad_make']) ? $json_data['custom_fields']['ad_make'] : '';
				$ad_model = isset($json_data['custom_fields']['ad_model']) ? $json_data['custom_fields']['ad_model'] : '';
				$ad_version = isset($json_data['custom_fields']['ad_version']) ? $json_data['custom_fields']['ad_version'] : '';
				$ad_4thlevel = isset($json_data['custom_fields']['ad_4thlevel']) ? $json_data['custom_fields']['ad_4thlevel'] : '';
			}
			$in_loop = $custom_fields;
		} else {

			$ad_tags 				= isset($json_data['ad_tags']) ? trim($json_data['ad_tags']) : '';
			$ad_condition   		= isset($json_data['ad_condition']) ? $json_data['ad_condition'] : '';
			$ad_type  	   			= isset($json_data['ad_type']) ? $json_data['ad_type'] : '';
			$ad_warranty   			= isset($json_data['ad_warranty']) ? $json_data['ad_warranty'] : '';
			$ad_years   			= isset($json_data['ad_years']) ? $json_data['ad_years'] : '';
			$ad_body_types   		= isset($json_data['ad_body_types']) ? $json_data['ad_body_types'] : '';
			$ad_transmissions   	= isset($json_data['ad_transmissions']) ? $json_data['ad_transmissions'] : '';
			$ad_engine_capacities  	= isset($json_data['ad_engine_capacities']) ? $json_data['ad_engine_capacities'] : '';
			$ad_engine_types   		= isset($json_data['ad_engine_types']) ? $json_data['ad_engine_types'] : '';
			$ad_assembles   		= isset($json_data['ad_assembles']) ? $json_data['ad_assembles'] : '';
			$ad_colors  	   		= isset($json_data['ad_colors']) ? $json_data['ad_colors'] : '';
			$ad_insurance   		= isset($json_data['ad_insurance']) ? $json_data['ad_insurance'] : '';
			$ad_currency   			= isset($json_data['ad_currency']) ? $json_data['ad_currency'] : '';
			$add_type               = isset($json_data['add_type']) ? $json_data['add_type'] : '';
			$ad_price_types			= isset($json_data['ad_price_type']) ? $json_data['ad_price_type'] : '';

			$ad_price   			= isset($json_data['ad_price']) ? $json_data['ad_price'] : '';
			$ad_yvideo   			= isset($json_data['ad_yvideo']) ? $json_data['ad_yvideo'] : '';

			$ad_mileage   			= isset($json_data['ad_mileage']) ? $json_data['ad_mileage'] : '';
			$ad_avg_hwy   			= isset($json_data['ad_avg_hwy']) ? $json_data['ad_avg_hwy'] : '';
			$ad_avg_city   			= isset($json_data['ad_avg_city']) ? $json_data['ad_avg_city'] : '';
			$features    =  isset($json_data['ad_features']) ? $json_data['ad_features'] : '';

			$ad_phone_number = isset($json_data['ad_phone_number']) ? $json_data['ad_phone_number'] : '';
			$ad_user_address = isset($json_data['ad_user_address']) ? $json_data['ad_user_address'] : '';
			$ad_country = isset($json_data['ad_country']) ? $json_data['ad_country'] : '';
			$ad_colors = isset($json_data['ad_colors']) ? $json_data['ad_colors'] : '';
			$ad_insurance = isset($json_data['ad_insurance']) ? $json_data['ad_insurance'] : '';
			$ad_make = isset($json_data['ad_make']) ? $json_data['ad_make'] : '';
			$ad_model = isset($json_data['ad_model']) ? $json_data['ad_model'] : '';
			$ad_version = isset($json_data['ad_version']) ? $json_data['ad_version'] : '';
			$ad_4thlevel = isset($json_data['ad_4thlevel']) ? $json_data['ad_4thlevel'] : '';
		}
		/* ads  */

		//print_r($json_data);
		$arrty = array($in_loop, $ad_yvideo, $ad_price, $ad_price_types, $ad_currency, $ad_warranty, $ad_type, $ad_condition, $ad_tags);


		$ad_yvideo			= update_post_meta($pid, '_carspot_ad_yvideo', $ad_yvideo);

		$tags	=	explode(',', $ad_tags);
		wp_set_object_terms($pid, $tags, 'ad_tags');
		/* Updating features starts */


		$ad_features = '';
		if (is_array($features) && count((array) $features) > 0) {
			foreach ($features as $feature) {
				$ad_features .= $feature . "|";
			}
			$ad_features = rtrim($ad_features, '|');
		}

		update_post_meta($pid, '_carspot_ad_features', $ad_features);
		/* Updating features  end*/

		/*setting taxonomoies selected*/

		carspotAPI_adPost_update_terms($pid, $ad_condition, 'ad_condition');
		carspotAPI_adPost_update_terms($pid, $ad_type, 'ad_type');
		carspotAPI_adPost_update_terms($pid, $ad_warranty, 'ad_warranty');
		carspotAPI_adPost_update_terms($pid, $ad_years, 'ad_years');
		carspotAPI_adPost_update_terms($pid, $ad_body_types, 'ad_body_types');
		carspotAPI_adPost_update_terms($pid, $ad_transmissions, 'ad_transmissions');
		carspotAPI_adPost_update_terms($pid, $ad_engine_capacities, 'ad_engine_capacities');
		carspotAPI_adPost_update_terms($pid, $ad_engine_types, 'ad_engine_types');
		carspotAPI_adPost_update_terms($pid, $ad_assembles, 'ad_assembles');
		carspotAPI_adPost_update_terms($pid, $ad_colors, 'ad_colors');
		carspotAPI_adPost_update_terms($pid, $ad_insurance, 'ad_insurance');
		//carspotAPI_adPost_update_terms($pid, $ad_warranty, 'ad_body_types' );


		carspotAPI_adPost_update_terms($pid, $ad_colors, 'ad_colors');
		carspotAPI_adPost_update_terms($pid, $ad_country, 'ad_country');
		carspotAPI_adPost_update_terms($pid, $ad_make, 'ad_make');
		carspotAPI_adPost_update_terms($pid, $ad_model, 'ad_model');
		carspotAPI_adPost_update_terms($pid, $ad_version, 'ad_version');
		carspotAPI_adPost_update_terms($pid, $ad_4thlevel, 'ad_4thlevel');


		if (isset($ad_mileage) && $ad_mileage != "") {
			update_post_meta($pid, '_carspot_ad_mileage', $ad_mileage);
		}
		update_post_meta($pid, '_carspot_ad_avg_city', $ad_avg_city);
		update_post_meta($pid, '_carspot_ad_avg_hwy', $ad_avg_hwy);

		$ad_bidding  = isset($json_data['ad_bidding']) ? $json_data['ad_bidding'] : '';
		update_post_meta($pid, '_carspot_ad_bidding', $ad_bidding);

		$ad_bidding_time_save  = isset($json_data['ad_bidding_time']) ? $json_data['ad_bidding_time'] : '';
		update_post_meta($pid, '_carspot_ad_bidding_date', $ad_bidding_time_save);

		if ($ad_price_types != "") {
			update_post_meta($pid, '_carspot_ad_price_type', $ad_price_types);
		}


		if ($ad_price != "") {
			$ad_price = ($ad_price_types == "on_call" || $ad_price_types == "free" || $ad_price_types == "no_price") ? '' : $ad_price;
			update_post_meta($pid, '_carspot_ad_price', $ad_price);
		}

		$ad_owner_name   		= isset($json_data['name']) ? $json_data['name'] : '';
		$ad_owner_ad_phone		= isset($json_data['ad_phone']) ? $json_data['ad_phone'] : '';
		$ad_owner_ad_location	= isset($json_data['ad_location']) ? $json_data['ad_location'] : '';
		$ad_owner_location_lat	= isset($json_data['location_lat']) ? $json_data['location_lat'] : '';
		$ad_owner_location_long	= isset($json_data['location_long']) ? $json_data['location_long'] : '';

		/*Store image for sorting*/
		$images_arr	= isset($json_data['images_arr']) ? $json_data['images_arr'] : '';
		if ($images_arr != "") {
			$img_ids	= trim($images_arr, ',');
			update_post_meta($pid, 'carspot_photo_arrangement_', $img_ids);
		} else {
			update_post_meta($pid, 'carspot_photo_arrangement_', '');
		}


		/* Update User Info */
		update_post_meta($pid, '_carspot_poster_name', $ad_owner_name);
		update_post_meta($pid, '_carspot_poster_contact', $ad_owner_ad_phone);
		update_post_meta($pid, '_carspot_ad_location', $ad_owner_ad_location);
		update_post_meta($pid, '_carspot_ad_map_lat', $ad_owner_location_lat);
		update_post_meta($pid, '_carspot_ad_map_long', $ad_owner_location_long);
		update_post_meta($pid, '_carspot_poster_name1', $desc);
		update_post_meta($pid, '_carspot_ad_usage', $ad_usage);
		update_post_meta($pid, '_carspot_ad_condition', $ad_condition);
		update_post_meta($pid, '_carspot_ad_price', $ad_price);
		update_post_meta($pid, '_carspot_ad_map_long_extra', $ad_map_long);
		update_post_meta($pid, '_carspot_ad_map_lat_extra', $ad_map_long);


		update_post_meta($pid, '_carspot_ad_phone_number', $ad_phone_number);
		update_post_meta($pid, '_carspot_ad_user_address', $ad_user_address);
		update_post_meta($pid, '_carspot_ad_user_name', $ad_user_name);

		/* Ad extra fields post meta starts */
		/*
	$sb_extra_fields   = isset( $json_data['sb_extra_fields'] ) ? $json_data['sb_extra_fields'] : '';	
	if( isset( $sb_extra_fields ) && count( $sb_extra_fields ) > 0)
	{
		for( $i = 1; $i <= $params['sb_total_extra']; $i++ )
		{
			update_post_meta($pid, "_sb_extra_" . $params["title_$i"], $params["sb_extra_$i"] );
		}
	}	
	*/
		/* Ad extra fields post meta ends */
		/* Ad Dynamic ad post meta starts */
		$custom_fields   = isset($json_data['custom_fields']) ? (array)$json_data['custom_fields'] : array();

		if (isset($custom_fields) && count($custom_fields) > 0) {
			$ios_array = array();

			foreach ($custom_fields as $key => $data) {
				if ($request_from == 'ios') {
					$is_chckbox = carspotAPI_post_ad_check_if_checkbox($ad_cats, 3, $key);
					if ($is_chckbox) {
						$data = @explode(",", $data);
					}
				}


				if (is_array($data)) {
					$dataArr = array();
					foreach ($data as $k) $dataArr[] = $k;
					$data = stripslashes(json_encode($dataArr, true));
				}


				$dataVal = ltrim($data, ",");
				$dataKey = "_carspot_tpl_field_" . $key;

				update_post_meta($pid, $dataKey, $dataVal);
			}
		}
		/* Ad Dynamic ad post meta ends */

		if ($isUpdate == true) {
			delete_user_meta($user_id, 'ad_in_progress'); //
		}

		/*Featured Ads Start*/
		$posted_featured = '';
		if (isset($json_data['ad_featured_ad']) && $json_data['ad_featured_ad'] == 1) {
			// Uptaing remaining ads.
			$featured_ad	=	get_user_meta($user_id, '_carspot_featured_ads', true);
			if ($featured_ad > 0 || $featured_ad == '-1') {
				update_post_meta($pid, '_carspot_is_feature', '1');
				update_post_meta($pid, '_carspot_is_feature_date', date('Y-m-d'));
				$featured_ad2 = $featured_ad;
				$featured_ad	=	$featured_ad - 1;
				if ($featured_ad2 != '-1') {
					update_user_meta($user_id, '_carspot_featured_ads', $featured_ad);
				}

				$posted_featured = ' ' . __("And Marked As Featured.", "carspot-rest-api");
			}
		}
		/*Featured Ads Ends */








		if ((isset($json_data['ad_id']) && $json_data['ad_id'] != "") && (isset($json_data['is_update']) && $json_data['is_update'] != "")) {

			$posted_bump = '';
			/*Bumping it up*/
			if (isset($json_data['ad_bump_ad']) && $json_data['ad_bump_ad']  == 1) {
				// Uptaing remaining ads.
				$bump_ads	=	get_user_meta($user_id, '_carspot_bump_ads', true);
				$allow_unlimited = (isset($carspot_theme['sb_allow_free_bump_up']) && $carspot_theme['sb_allow_free_bump_up']) ? true : false;
				/*if( $bump_ads > 0 || isset( $carspot_theme['sb_allow_free_bump_up']	) && $carspot_theme['sb_allow_free_bump_up'] )*/
				if ($bump_ads > 0 || $allow_unlimited == true  || $bump_ads == '-1') {
					wp_update_post(
						array(
							'ID'            => $pid, // ID of the post to update
							'post_date'     => current_time('mysql'),
							'post_date_gmt' => get_gmt_from_date(current_time('mysql'))
						)
					);
					if (!$carspot_theme['sb_allow_free_bump_up'] && $bump_ads != "-1") {
						$bump_ads	=	$bump_ads - 1;
						update_user_meta($user_id, '_carspot_bump_ads', $bump_ads);
					}
					$posted_bump = ' ' . __("And Marked As Bumped Ad.", "carspot-rest-api");
				}
			}

			$posted = __("Ad Updated Successfully.", "carspot-rest-api") . $posted_featured . $posted_bump;
		} else {
			$posted = __("Ad Posted Successfully", "carspot-rest-api") . $posted_featured;
		}



		update_post_meta($pid, '_carspot_add_type', $add_type);
		$postdData['ad_id'] = $pid;
		$response = array('success' => true, 'data' => $postdData, 'message' => $posted, 'extra' => $json_data);
		return $response;
	}
}
function parse_json($json)
{
	print_r($json);
	$parent_id = '';
	$op_array = array();
	foreach ($json as $key => $value) {
		echo $parent_id = $value['id'];
		if (isset($value['sub_cat']) && (count($value['sub_cat']) > 0)) {
			fetch_id($value['sub_cat'], $op_array);
		} else {
			array_push($op_array, $parent_id);
		}
		return implode(",", $op_array);
	}
}
function fetch_id($ip_array, &$op_array)
{
	array_push($op_array, $ip_array['id']);
	if (isset($ip_array['sub_cat']) && (count($ip_array['sub_cat']) > 0)) {
		fetch_id($ip_array['sub_cat'], $op_array);
	} else {
		return $op_array;
	}
}

add_action('rest_api_init', 'carspotAPI_postad_image_delete', 0);
function carspotAPI_postad_image_delete()
{

	register_rest_route(
		'carspot/v1',
		'/post_ad/image/delete',
		array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_delete_ad_image',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
}

if (!function_exists('carspotAPI_delete_ad_image')) {
	function carspotAPI_delete_ad_image($request)
	{

		$json_data 			= 	$request->get_json_params();
		$attachmentID		= 	(isset($json_data['img_id'])) 		? $json_data['img_id'] : '';
		$ad_id				= 	(isset($json_data['ad_id'])) 		? $json_data['ad_id'] : '';

		if ($attachmentID == '' || $ad_id == '') {
			$message = 	__("Something went wrong", "carspot-rest-api");
			$success = false;
		} else {
			$deleteImg = wp_delete_attachment($attachmentID);
			if ($deleteImg) {

				if (get_post_meta($ad_id, 'carspot_photo_arrangement_', true) != "") {
					$ids 		= get_post_meta($ad_id, 'carspot_photo_arrangement_', true);
					$res 		=  str_replace($attachmentID, "", $ids);
					$res 		=  str_replace(',,', ",", $res);
					$img_ids	= trim($res, ',');
					update_post_meta($ad_id, 'carspot_photo_arrangement_', $img_ids);
				}
				$message = 	__("Image deleted successfully.", "carspot-rest-api");
				$success = true;
			} else {
				$message = 	__("Something went wrong", "carspot-rest-api");
				$success = false;
			}
		}
		$images = array();
		if ($ad_id != "") {
			$images = carspotAPI_get_ad_image($ad_id, 5, 'thumb', false);
		}

		$maxLimit = (isset($carspotAPI['sb_upload_limit'])) ? $carspotAPI['sb_upload_limit'] : 5;
		$remaningImages = $maxLimit - count($images);

		if ($remaningImages <= 0) {
			$remaningImages = 0;
			$moer_message = __("you can not upload more images", "carspot-rest-api");
		} else {
			$moer_message = __("you can upload", "carspot-rest-api") . ' ' . $remaningImages . ' ' . __("more image", "carspot-rest-api");
		}

		$data['ad_images'] = $images;
		$data['images']['is_show'] = true;
		$data['images']['numbers'] = $remaningImages;
		$data['images']['message'] = $moer_message;


		return $response = array('success' => $success, 'data' => $data, 'message'  => $message);
	}
}

add_action('rest_api_init', 'carspotAPI_postad_image', 0);
function carspotAPI_postad_image()
{

	register_rest_route(
		'carspot/v1',
		'/post_ad/image/',
		array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_upload_ad_image',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
}


if (!function_exists('carspotAPI_upload_ad_limit')) {
	function carspotAPI_upload_ad_limit($ad_id = '')
	{
		$images = array();
		if ($ad_id != "") {
			global $carspotAPI;
			$maxLimit = (isset($carspotAPI['sb_upload_limit'])) ? $carspotAPI['sb_upload_limit'] : 5;

			$images = carspotAPI_get_ad_image($ad_id, -1, 'carspot-single-small');

			$remaningImages = $maxLimit - count($images);


			if ($remaningImages <= 0) {
				$remaningImages = 0;
				$moer_message = __("your can not upload more images", "carspot-rest-api");
			} else {
				$moer_message = __("your can upload", "carspot-rest-api") . ' ' . $remaningImages . ' ' . __("more image", "carspot-rest-api");
			}


			$data['ad_images'] = $images;
			$data['images']['is_show'] = true;
			$data['images']['numbers'] = $remaningImages;
			$data['images']['message'] = $moer_message;
		}
	}
}

if (!function_exists('carspotAPI_upload_ad_image')) {
	function carspotAPI_upload_ad_image($request)
	{
		$ad_id =  (isset($_POST['ad_id']) && $_POST['ad_id'] != "") ? $_POST['ad_id'] : '';

		$extra['ad_id'] = $ad_id;
		$extra['images'] = $_FILES;
		global $carspotAPI;
		if ($ad_id == '') {
		}
		$success  = false;
		$imgArr = array();
		$is_size_error1 = false;
		if (isset($_FILES) && count($_FILES) > 0) {
			global $wpdb;
			//if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			//}

			foreach ($_FILES as $key => $val) {

				$user = wp_get_current_user();
				$user_id = $user->data->ID;
				$uploadedfile = $_FILES["$key"];
				/******* user_photo Upload code ************/
				$upload_overrides = array('test_form' => false);
				$request_from = carspotAPI_getSpecific_headerVal('Carspot-Request-From');
				if ($request_from == 'ios' && false) {
					if (isset($carspotAPI['sb_standard_images_size']) && $carspotAPI['sb_standard_images_size']) {
						$uploadedfile_tmp_name = $_FILES["$key"]["tmp_name"];
						list($width, $height) = (getimagesize($uploadedfile_tmp_name));
						$is_size_error = false;
						if ($width < 760) {
							$is_size_error = true;
						}
						if ($height < 410) {
							$is_size_error = true;
						}
						if ($is_size_error) {
							$is_size_error1 = true;
							continue;
						}
					}
				}

				$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
				/*Added On 3 March 2018*/
				if ($movefile && !isset($movefile['error'])) {

					/******* Assign image to ad ************/
					$filename 		= $movefile['url'];
					$absolute_file	= $movefile['file'];
					$parent_post_id = $ad_id;
					$filetype = wp_check_filetype(basename($filename), null);
					$wp_upload_dir = wp_upload_dir();
					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
						'post_mime_type' => $filetype['type'],
						'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
						'post_content'   => '',
						/*'post_status'    => 'inherit'*/
					);
					// Insert the attachment.
					$attach_id = wp_insert_attachment($attachment, $absolute_file, $parent_post_id);
					$wpdb->query("UPDATE $wpdb->posts SET post_status = 'inherit' WHERE ID = '$attach_id' ");
					//require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata($attach_id, $absolute_file);
					//$attach_data = wp_get_attachment_image( $attach_id );
					wp_update_attachment_metadata($attach_id, $attach_data);
					set_post_thumbnail($parent_post_id, $attach_id);
					$imgaes = get_post_meta($ad_id, 'carspot_photo_arrangement_', true);

					if ($imgaes != "") {
						$imgaes = $imgaes . ',' . $attach_id;
						update_post_meta($ad_id, 'carspot_photo_arrangement_', $imgaes);
					}
					$imgArr[] = $movefile['url'];
				}
			}
			$success  = true;
		}

		$images = array();
		if ($ad_id != "") {
			$images = carspotAPI_get_ad_image($ad_id, -1, '', false);
		}

		$maxLimit = (isset($carspotAPI['sb_upload_limit'])) ? $carspotAPI['sb_upload_limit'] : 5;
		// $remaningImages = $maxLimit - count( $images );
		$remaningImages = 100;
		if ($remaningImages <= 0) {
			$remaningImages = 0;
		}
		$data['ad_images'] = $images;
		$data['images']['is_show'] = true;
		$data['images']['numbers'] = $remaningImages;
		$data['images']['message'] = __("your upload ad limit is ", "carspot-rest-api") . $remaningImages;
		$data['images']['per_limit'] = (isset($carspotAPI['sb_upload_limit_per'])) ? $carspotAPI['sb_upload_limit_per'] : 5;

		$message  = '';
		if ($is_size_error1) {
			$message =  __("Minimum image dimension should be", 'carspot-rest-api') . ' 760x410';
			$success  = false;
		}
		$response = array('success' => $success, 'data' => $data, 'message' => $message, "eaxtra" => $extra);
		return $response;
	}
}

if (!function_exists('carspotAPI_adPost_update_terms')) {
	function carspotAPI_adPost_update_terms($pid = '', $term_val = '', $term_type = '')
	{
		if ($pid == '' || $term_val == '') return '';

		$arrays	=	explode('|', $term_val);
		$ids    = (isset($arrays[0]) && $arrays[0] != "") ? $arrays[0] : '';
		$val    = (isset($arrays[1]) && $arrays[1] != "") ? $arrays[1] : '';

		wp_set_post_terms($pid, $ids, $term_type);
		update_post_meta($pid, '_carspot_' . $term_type, $val);

		$term = get_term($ids, $term_type);
		//wp_set_post_terms( $pid, $term_val, $term_type );

		return (isset($term->name) && $term->name != "") ? $term->name : '';
	}
}

if (!function_exists('carspotAPI_cat_ancestors')) {
	function carspotAPI_cat_ancestors($ad_cats = '', $term_type = 'ad_cats', $reverse_arr = true)
	{
		if ($ad_cats == "") return '';

		$ad_cats_ids = get_ancestors($ad_cats,  $term_type);
		$adsID[] = (int)$ad_cats;
		if (isset($ad_cats_ids) && count($ad_cats_ids) > 0) {
			foreach ($ad_cats_ids as $cid) {
				$adsID[] = $cid;
			}
		}

		return ($reverse_arr == false) ? $adsID : array_reverse($adsID);
	}
}

add_action('rest_api_init', 'carspotAPIpost_ad_subcats_get', 0);
function carspotAPIpost_ad_subcats_get()
{
	register_rest_route(
		'carspot/v1',
		'/post_ad/subcats/',
		array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_post_ad_subcats',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
}

function carspotAPI_post_ad_subcats($request)
{

	$json_data  		= $request->get_json_params();
	$subcat				= (isset($json_data['subcat'])) ? $json_data['subcat'] : '';
	if ($subcat == "") {
		$subcat = (isset($json_data['ad_cats1'])) ? $json_data['ad_cats1'] : '';
	}
	$mainTerm   		= get_term($subcat);
	$mainTermName 		=  htmlspecialchars_decode($mainTerm->name, ENT_NOQUOTES);
	/*Function TO setup categories bsed pricing starts*/
	global $carspot_theme;
	global $carspotAPI;
	global $woocommerce;
	/*category_based or package_based*/
	$is_redirect = (isset($carspotAPI['carspot_package_type']) && $carspotAPI['carspot_package_type'] == 'category_based') ? true : false;
	$package_type = (isset($carspotAPI['carspot_package_type']) && $carspotAPI['carspot_package_type'] == 'category_based') ? $carspotAPI['carspot_package_type'] : 'package_based';

	$extras['package_type']['is_redirect'] 	=  $is_redirect;
	if ($is_redirect) {
		$term_id 							= $subcat;
		$ad_id   							= (isset($json_data['ad_id'])) ? $json_data['ad_id'] : '';
		$extras['package_type']['name'] 	=  $package_type;


		$pkgCats 		= carspot_category_packageAPI($term_id);
		$child 			= get_term_by('id', $term_id, 'ad_cats');
		$cat_id 		= $child->term_id;
		$mainCatId 		= $product_id = '';
		if (count($pkgCats) > 0) {
			$mainCatId = $product_id = $productID = '';
			foreach ($pkgCats as $pkgCat) {
				$pkg_cats = $pkgCat['cats'];
				if (in_array($cat_id, $pkg_cats)) {
					foreach ($pkg_cats as $pkg_cat_id) {
						$mainCatId = ($pkg_cat_id == $cat_id) ? $pkg_cat_id : '';
						$product_id = ($pkg_cat_id == $cat_id) ? $pkgCat['id'] : '';
					}
				}
			}
		}

		$term_list = array();
		if ($product_id != "") {
			/* IF AD IS UPDATING */
			$term_list = ($id != "") ? wp_get_post_terms($id, 'ad_cats', array("fields" => "ids")) : array();
			if (in_array($mainCatId, $term_list)) {
				//carspot_unset_product_cartAPI();
			} else {
				//carspot_add_ad_to_cartAPI($product_id, 'no', 'no');
				$product_id_arr = carspot_product_ids();
				$product_id_arr = array_diff($product_id_arr, $product_id);
				foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
					$cart_item_id = $cart_item['data']->id;
					if (in_array($cart_item_id, $product_id_arr)) {
						//WC()->cart->remove_cart_item($cart_item_key);
					}
				}
			}
		}

		//carspot_removeProductsFrom_cartAPI($ad_id, $term_id);
		//$extras['package_type']['redirect_url'] 	=  wc_get_cart_url();
		//$total_in_cart = WC()->cart->total;
		//$extras['package_type']['cart_total'] 		=  $total_in_cart;
		//$extras['package_type']['can_redirect'] 	=  (WC()->cart->total > 0 ) ? true : false;
		//
		$total_text 	= esc_html__("Amount In Cart", "carspot-rest-api") . ': ';
		$extras['package_type']['cart_total_text'] 	= $total_text;
	}

	$extras = array();

	$data = carspotAPI_getSubCats('select',  'ad_cats1', 'ad_cats', $subcat, $mainTermName, '', false);
	return $response = array('success' => true, 'data' => $data, 'message'  => '', "extras" => $extras);
}

add_action('rest_api_init', 'carspotAPIpost_ad_sublocations_get', 0);
function carspotAPIpost_ad_sublocations_get()
{
	register_rest_route(
		'carspot/v1',
		'/post_ad/sublocations/',
		array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_post_ad_sublocations',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
}
if (!function_exists('carspotAPI_post_ad_sublocations')) {
	function carspotAPI_post_ad_sublocations($request)
	{

		$json_data = $request->get_json_params();
		$subcat		= (isset($json_data['ad_country'])) ? $json_data['ad_country'] : '';
		$mainTerm = get_term($subcat);
		$mainTermName =  htmlspecialchars_decode($mainTerm->name, ENT_NOQUOTES);

		$data = carspotAPI_getSubCats('select',  'ad_country', 'ad_country', $subcat, $mainTermName, '', false);
		return $response = array('success' => true, 'data' => $data, 'message'  => '');
	}
}


if (!function_exists('carspotAPI_post_ad_check_if_checkbox')) {
	function carspotAPI_post_ad_check_if_checkbox($term_id = '', $check_type = '', $slugs = '')
	{
		if ($term_id == "") return '';
		$formData  = '';
		$temp_term_id = carspot_dynamic_templateID($term_id);
		$template_result = get_term_meta($temp_term_id, '_sb_dynamic_form_fields', true);
		$myArray = array();
		if (isset($template_result) && $template_result != "") {
			$formData = sb_dynamic_form_data($template_result);
			foreach ($formData as $lists) {
				if ($lists['types'] != "" && $lists['slugs'] == $slugs && $check_type == $lists['types']) {
					$myArray[] = $lists['types'];
				}
			}
		}
		return in_array($check_type, $myArray);
	}
}

add_action('rest_api_init', 'carspotAPIpost_ad_cat_fields_get', 0);
function carspotAPIpost_ad_cat_fields_get()
{
	register_rest_route(
		'carspot/v1',
		'/post_ad/dynamic_fields/',
		array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_post_ad_fields',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
}



if (!function_exists('carspotAPI_post_ad_fields')) {
	function carspotAPI_post_ad_fields($request, $is_termID = '', $ad_id = '')
	{

		global $carspotAPI;
		global $carspot_theme;
		if (isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == false) {
			return $response = array('success' => true, 'data' => '', 'message'  => '', 'extras' => '');
		}
		$is_termID =  $ad_id = '';
		if ($is_termID != "") {
			$term_id = $is_termID;
		} else {
			$json_data  = $request->get_json_params();
			$term_id	= (isset($json_data['cat_id'])) ? $json_data['cat_id'] : '';
			$ad_id		= (isset($json_data['ad_id']))  ? $json_data['ad_id']  : '';
		}

		$arrays	    = array();
		//$result2    = carspotAPI_categoryForm_data($ad_id);
		$result 	= carspotAPI_dynamic_templateID($term_id);

		$templateID =   get_term_meta($result, '_sb_dynamic_form_fields', true);


		/* Show options*/
		$type 		= 	sb_custom_form_data($templateID, '_sb_default_cat_ad_type_show');
		$price 		= 	sb_custom_form_data($templateID, '_sb_default_cat_price_show');
		$type 		= 	sb_custom_form_data($templateID, '_sb_default_cat_ad_type_show');
		$priceType 	= 	sb_custom_form_data($templateID, '_sb_default_cat_price_type_show');
		$condition 	= 	sb_custom_form_data($templateID, '_sb_default_cat_condition_show');
		$warranty 	= 	sb_custom_form_data($templateID, '_sb_default_cat_warranty_show');
		$tags 		= 	sb_custom_form_data($templateID, '_sb_default_cat_tags_show');
		$video 		= 	sb_custom_form_data($templateID, '_sb_default_cat_video_show');
		$image      =   sb_custom_form_data($templateID, '_sb_default_cat_image_show');


		/* Required options*/
		$price_req 		= 	sb_custom_form_data($templateID, '_sb_default_cat_price_required');
		$priceType_req 	= 	sb_custom_form_data($templateID, '_sb_default_cat_price_type_required');
		$video_req 		= 	sb_custom_form_data($templateID, '_sb_default_cat_video_required');
		$image_req 		= 	sb_custom_form_data($templateID, '_sb_default_cat_image_required');
		$tags_req 		= 	sb_custom_form_data($templateID, '_sb_default_cat_tags_required');


		$pid = $is_update = $ad_id;


		$ad_type 			= get_post_meta($pid, '_carspot_ad_type', true);
		$ad_condition 		= get_post_meta($pid, '_carspot_ad_condition', true);
		$ad_warranty		= get_post_meta($pid, '_carspot_ad_warranty', true);
		$ad_price			= get_post_meta($pid, '_carspot_ad_price', true);
		$ad_price_typeVal  	= get_post_meta($pid, '_carspot_ad_price_type', true);
		$ad_yvideo			= get_post_meta($pid, '_carspot_ad_yvideo', true);
		$ad_bidding 		= get_post_meta($pid, '_carspot_ad_bidding', true);
		$ad_bidding_time	= get_post_meta($pid, '_carspot_ad_bidding_date', true);
		$ad_currency		= get_post_meta($pid, '_carspot_ad_currency', true);
		$tags_array 		= wp_get_object_terms($pid,  'ad_tags', array('fields' => 'names'));
		$ad_tags			= @implode(',', $tags_array);

		$showcatData = false;
		if (isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true) {
			$showcatData = true;
		}

		if ($priceType == 1 && $templateID != "" && $showcatData == true) {
			$ad_price_type = carspotAPI_adPrice_types($ad_price_typeVal);
			$arrays[] 		= carspotAPI_getPostAdFields('select', 'ad_price_type', $ad_price_type, 0, __("Price Type", "carspot-rest-api"), 'Price Type', '', 2, false, $ad_price_typeVal, $is_update);
		} else if ($templateID == "" || $showcatData == false) {
			$ad_price_type  	= carspotAPI_adPrice_types($ad_price_typeVal);
			$arrays[] 	= carspotAPI_getPostAdFields('select', 'ad_price_type', $ad_price_type, 0, __("Price Type", "carspot-rest-api"), 'Price Type', '', 2, false, $ad_price_typeVal, $is_update);
		}

		if ($price == 1 && $templateID != "" && $showcatData == true) {
			$arrays[] = carspotAPI_getPostAdFields('textfield', 'ad_price', '', 0, __("Ad Price", "carspot-rest-api"), '', '', '2', true, $ad_price);
			/*$ad_currency_count  = wp_count_terms( 'ad_currency' );
			if( isset($ad_currency_count) && $ad_currency_count > 0 )
			{	
				$arrays[] = carspotAPI_getPostAdFields('select','ad_currency','ad_currency',0, __("Ad Currency", "carspot-rest-api"),'', '', '2', false, '', $is_update);
			}	*/
		} else if ($templateID == "" || $showcatData == false) {
			$arrays[] = carspotAPI_getPostAdFields('textfield', 'ad_price', '', 0, __("Ad Price", "carspot-rest-api"), '', '', '2', true, $ad_price);
			/*$ad_currency_count  = wp_count_terms( 'ad_currency' );
			if( isset($ad_currency_count) && $ad_currency_count > 0 )
			{	
				$arrays[] = carspotAPI_getPostAdFields('select','ad_currency','ad_currency',0, __("Ad Currency", "carspot-rest-api"),'', '', '2', false, '', $is_update);
			}*/
		}

		if ($image == 1 && $templateID != "" && $showcatData == true) {

			$arrays[] 	= carspotAPI_getPostAdFields('images', 'ad_images', '', 0, __("Images", "carspot-rest-api"), 'Images', '', 1, false, $image_req, $is_update);
		} else if ($templateID == "" || $showcatData == false) {
			$arrays[] = carspotAPI_getPostAdFields('images', '', '', 0, __("Images", "carspot-rest-api"), '', '', '1', '', $image_req, $is_update);
		}

		if ($tags == 1 && $templateID != "" && $showcatData == true) {

			$arrays[] = carspotAPI_getPostAdFields('textfield', 'ad_tags', '', 0, __("Tags Comma(,) separated", "carspot-rest-api"), '', $ad_tags, '2', false, $ad_tags);
		} else if ($templateID == "" || $showcatData == false) {
			$arrays[] = carspotAPI_getPostAdFields('textfield', 'ad_tags', $ad_tags, 0, __("Tags Comma(,) separated", "carspot-rest-api"), '', '', '2', false, $ad_tags);
		}

		if ($video == 1 && $templateID != "" && $showcatData == true) {
			$arrays[] = carspotAPI_getPostAdFields('textfield', 'ad_yvideo', $ad_yvideo, 0, __("Youtube Video Link", "carspot-rest-api"), '', '', '2', false, $ad_yvideo);
		} else if ($templateID == "" || $showcatData == false) {
			$arrays[] = carspotAPI_getPostAdFields('textfield', 'ad_yvideo', $ad_yvideo, 0, __("Youtube Video Link", "carspot-rest-api"), '', '', '2', false, $ad_yvideo);
		}

		$isStatic = ($showcatData == true) ? 'dynamic' : 'static';
		/* Custom taxonomy feilds */
		$arrays2 = carspot_get_term_formAPI($result, $ad_id, $isStatic);
		$arrays = array_merge($arrays, $arrays2);

		$extras['hide_price']    = array("ad_price", "ad_price_type");
		$extras['hide_currency'] = array("ad_price", "ad_currency");

		/*if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true )
		if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] != true )
		{
			return $response = array( 'success' => true, 'data' => '', 'message'  => '' );
		}*/
		if (isset($templateID) && $templateID != "" && isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true) {
			$formData = sb_dynamic_form_data($templateID);
			foreach ($formData as $r) {
				$is_required = (isset($r['requires']) && $r['requires'] == 1) ? true : false;
				if (isset($r['types']) && trim($r['types']) != "" && isset($r['status']) && trim($r['status']) == 1) {
					///////Make chnages here
					$in_search = (isset($r['in_search']) && $r['in_search'] == "yes") ? 1 : 0;
					if ($r['titles'] != "" && $r['slugs'] != "") {

						$mainTitle = $name = $r['titles'];
						$fieldName = $r['slugs'];
						$fieldValue = (isset($_GET["custom"]) && isset($_GET['custom'][$r['slugs']])) ? $_GET['custom'][$r['slugs']] : '';

						$postMetaName  = '_carspot_tpl_field_' . $fieldName;
						$nameValue	= get_post_meta($ad_id, $postMetaName, true);

						$nameValue  = ($nameValue) ? $nameValue : '';

						if (isset($r['types']) && $r['types'] == 1) {
							$arrays[] = 	array("main_title" => $mainTitle, "field_type" => 'textfield', "field_type_name" => $fieldName, "field_val" => $nameValue, "field_name" => "", "title" => $name, "values" => $fieldValue, "has_page_number" => 2, "is_required" => $is_required);
						}

						/* Date type */
						if (isset($r['types']) && $r['types'] == 4) {

							$arrays[] = 	array("main_title" => $mainTitle, "field_type" => 'date', "field_type_name" => $fieldName, "field_val" => $nameValue, "field_name" => "", "title" => $name, "values" => $fieldValue, "has_page_number" => 2, "is_required" => $is_required);
						}
						/* Url */
						if (isset($r['types']) && $r['types'] == 5) {

							$arrays[] = 	array("main_title" => $mainTitle, "field_type" => 'link', "field_type_name" => $fieldName, "field_val" => $nameValue, "field_name" => "", "title" => $name, "values" => $fieldValue, "has_page_number" => 2, "is_required" => $is_required);
						}

						//select option
						if (isset($r['types']) && $r['types'] == 2 || isset($r['types']) && $r['types'] == 3) {


							if (isset($r['values']) && $r['values'] != "") {


								$varArrs = @explode("|", $r['values']);

								if ($r['types'] == 3) {
									$nameValue = json_decode($nameValue, true);
								}

								$varArrs = carspotAPI_arraySearch($varArrs, '', $nameValue);



								$termsArr = array();
								if ($r['types'] == 2 && $nameValue == "") {

									$termsArr[] = array(
										"id" => "",
										"name" => __("Select Option", "carspot-rest-api"),
										"has_sub" => false,
										"has_template" => false,

									);
								}
								foreach ($varArrs as $v) {

									if ($r['types'] == 3) {
										//print_r($varArrs);
										//print_r(json_decode(json_encode($nameValue), true));



										$is_checked = false;
										if (!is_array($nameValue) && $nameValue == $v) {
											$is_checked = true;
										} else {

											$exp_data = @explode(",", $nameValue);

											if (@in_array($v, $nameValue)) {
												$is_checked = true;
											}
										}

										if (isset($v) && is_array($v) && count($v) > 0) {
											$termsArr[] = array(
												"id" => $v,
												"name" => $v,
												"has_sub" => false,
												"has_template" => false,
												"is_checked" => $is_checked
											);
										}
									} else {
										$termsArr[] = array(
											"id" => $v,
											"name" => $v,
											"has_sub" => false,
											"has_template" => false,
										);
									}
								}

								$ftype = ($r['types'] == 2) ? 'select' : 'checkbox';



								$arrays[] = 	array("main_title" => $mainTitle, "field_type" => $ftype, "field_type_name" => $fieldName, "field_val" => $nameValue, "field_name" => "", "title" => $name, "values" => $termsArr, "has_page_number" => 2, "is_required" => $is_required);
							}
						}
					}
				}
			}
		}


		//$extras['cart_amount'] =  $woocommerce->cart->get_cart_total();
		/*Function TO setup categories bsed pricing ends*/
		if ($is_termID != "") {
			return $arrays;
		} else {
			$request_from = carspotAPI_getSpecific_headerVal('carspot-Request-From');
			if ($request_from == 'ios') {
				$newArr['fields'] = $arrays;
				return $response = array('success' => true, 'data' => $newArr, 'message'  => '', 'extras' => $extras);
			} else {
				return $response = array('success' => true, 'data' => $arrays, 'message'  => '', 'extras' => $extras);
			}
		}
	}
}

/* Remove Product From Cart */
if (!function_exists('carspot_removeProductsFrom_cartAPI')) {
	function carspot_removeProductsFrom_cartAPI($id = '', $cat_id = '')
	{

		global $woocommerce;
		wc()->frontend_includes();
		WC()->session = new WC_Session_Handler();
		WC()->session->init();
		WC()->customer = new WC_Customer(get_current_user_id(), true);
		WC()->cart = new WC_Cart();

		$extraFeatures = '';
		$pkgCats 	= carspot_category_packageAPI($cat_id);
		$child 		= get_term_by('id', $cat_id, 'ad_cats');

		if ($child->parent > 0) {
			return;
		} else {
			carspot_unset_product_cartAPI();
		}

		$cat_id = $child->term_id;

		if (count($pkgCats) > 0) {
			$mainCatId = $product_id = $productID = '';
			foreach ($pkgCats as $pkgCat) {
				$pkg_cats = $pkgCat['cats'];
				if (in_array($cat_id, $pkg_cats)) {
					foreach ($pkg_cats as $pkg_cat_id) {
						if ($pkg_cat_id == $cat_id) {
							$mainCatId = $pkg_cat_id;
							$product_id = ($pkgCat['id']);
						}
					}
				} else {
					carspot_unset_product_cartAPI();
				}
			}


			$term_list = array();
			if ($product_id != "") {
				/* IF AD IS UPDATING */
				$term_list = ($id != "") ? wp_get_post_terms($id, 'ad_cats', array("fields" => "ids")) : array();
				if (in_array($mainCatId, $term_list)) {

					carspot_unset_product_cartAPI();
				} else {
					carspot_add_ad_to_cartAPI($product_id, 'no', 'no');
					$product_id_arr = carspot_product_ids();
					$product_id_arr = array_diff($product_id_arr, $product_id);
					foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
						$cart_item_id = $cart_item['data']->id;
						if (in_array($cart_item_id, $product_id_arr)) {
							WC()->cart->remove_cart_item($cart_item_key);
						}
					}
				}
			}
		}
	}
}
/* Unset Product Cart */
if (!function_exists('carspot_unset_product_cartAPI')) {

	function carspot_unset_product_cartAPI()
	{
		$args = array(
			'post_type' => 'product',
			'meta_key' => 'carspot_package_type',
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'carspot_category_pricing'
				),
			),
			'meta_query' => array(
				'key' => 'carspot_package_type',
				'value' => 'category_based',
				'compare' => '=',
			),
		);

		$los = get_posts($args);
		wc()->frontend_includes();
		WC()->session = new WC_Session_Handler();
		WC()->session->init();
		WC()->customer = new WC_Customer(get_current_user_id(), true);
		WC()->cart = new WC_Cart();
		//$cart = WC()->instance()->cart;
		foreach ($los as $lo) {
			$id = $lo->ID;
			$cartId = WC()->cart->generate_cart_id($id);
			$cartItemKey = WC()->cart->find_product_in_cart($cartId);
			$removed = WC()->cart->set_quantity($cart_item_id, 0);
		}
	}
}

add_action('rest_api_init', 'carspotAPI_ad_post_adons_func_hook', 0);
function carspotAPI_ad_post_adons_func_hook()
{
	register_rest_route(
		'carspot/v1',
		'/ad_post/adons/',
		array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_ad_post_adons_func',
			'permission_callback' => function () {
				return carspotAPI_basic_auth();
			},
		)
	);
}

if (!function_exists('carspotAPI_ad_post_adons_func')) {
	function carspotAPI_ad_post_adons_func($request)
	{
		$json_data = $request->get_json_params();
		$adons_id	= (isset($json_data['adons_id'])) ? $json_data['adons_id'] : '';
		$ad_id		= (isset($json_data['ad_id'])) ? $json_data['ad_id'] : '';

		return carspot_add_ad_to_cartAPI($adons_id, 'yes', 'yes');
	}
}


if (!function_exists('carspot_add_ad_to_cartAPI')) {

	function carspot_add_ad_to_cartAPI($adon_id = '', $html = 'yes', $die = 'yes')
	{
		global $carspot_theme;
		global $woocommerce;
		if ($html == 'yes') {
			$adon_id = $adon_id;
		}
		if ($html == 'no') {
		}
		wc()->frontend_includes();
		WC()->session = new WC_Session_Handler();
		WC()->session->init();
		WC()->customer = new WC_Customer(get_current_user_id(), true);
		WC()->cart = new WC_Cart();

		$found = false;
		if ($adon_id == "") {
			return '';
		}
		$product_id = $adon_id;

		$popup_text1 	= esc_html__("Added To Cart Successfully.", "carspot-rest-api");
		$button_text1 	= esc_html__('Remove', 'carspot-rest-api');
		$popup_text2 	= esc_html__("Removed From Cart.", "carspot-rest-api");
		$button_text2 	= esc_html__('Add To Cart', 'carspot-rest-api');
		$popup_text3 	= esc_html__("Nothing In Cart.", "carspot-rest-api");

		$total_text 	= esc_html__("Amount In Cart", "carspot-rest-api") . ': ';
		//check if product already in cart
		if (sizeof(WC()->cart->get_cart()) > 0) {
			$found = false;
			foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
				$_product = $values['data'];
				if ($_product->id == $product_id) {
					$found = true;
				}
			}
			if (!$found) {
				WC()->cart->add_to_cart($product_id);
				if ($html == 'yes') {
					$cart_total = carspotAPI_convert_uniText(strip_tags($woocommerce->cart->get_cart_total()));
					return array("success" => true, "product_id" => $product_id, "text" => $popup_text1, "btn_text" => $button_text1, "total" => $cart_total, "total_text" => $total_text);
				}
			} else {
				$cart = WC()->instance()->cart;
				$id = $product_id;
				$cart_id = $cart->generate_cart_id($id);
				$cart_item_id = $cart->find_product_in_cart($cart_id);
				if ($cart_item_id) {
					WC()->cart->remove_cart_item($cart_id);
					if ($html == 'yes') {
						$cart_total = carspotAPI_convert_uniText(strip_tags($woocommerce->cart->get_cart_total()));
						return array("success" => true, "product_id" => $product_id, "text" => $popup_text2, "btn_text" => $button_text2, "total" => $cart_total, "total_text" => $total_text);
					}
					$cart->set_quantity($cart_item_id, 0);
				} else {
					if ($html == 'yes') {
						$cart_total = carspotAPI_convert_uniText(strip_tags($woocommerce->cart->get_cart_total()));
						return array("success" => true, "product_id" => $product_id, "text" => $popup_text3, "btn_text" => $button_text2, "total" => $cart_total, "total_text" => $total_text);
					}
				}
			}
		} else {
			// if no products in cart, add it
			WC()->cart->add_to_cart($product_id);
			if ($html == 'yes') {
				$cart_total = carspotAPI_convert_uniText(strip_tags($woocommerce->cart->get_cart_total()));
				return array("success" => true, "product_id" => $product_id, "text" => $popup_text1, "btn_text" => $button_text1, "total" => $cart_total, "total_text" => $total_text);
			}
		}
		/* die(); */
	}
}

function carspotAPI_matched_cart_items($search_products)
{
	$count = 0; // Initializing
	wc()->frontend_includes();
	WC()->session = new WC_Session_Handler();
	WC()->session->init();
	WC()->customer = new WC_Customer(get_current_user_id(), true);
	WC()->cart = new WC_Cart();

	if (!WC()->cart->is_empty()) {
		// Loop though cart items
		foreach (WC()->cart->get_cart() as $cart_item) {
			// Handling also variable products and their products variations
			$cart_item_ids = array($cart_item['product_id'], $cart_item['variation_id']);
			// Handle a simple product Id (int or string) or an array of product Ids 
			if ((is_array($search_products) && array_intersect($search_products, cart_item_ids))  || (!is_array($search_products) && in_array($search_products, $cart_item_ids))) {
				$count++; /*// incrementing items count*/
			}
		}
	}
	return $count; // returning matched items count 
}



/* category package */
if (!function_exists('carspot_category_packageAPI')) {

	function carspot_category_packageAPI($cat_id = '')
	{
		$arr = array();
		$simple_ads = get_user_meta(get_current_user_id(), '_sb_simple_ads', true);

		//if ($simple_ads == '-1' && $simple_ads > 0) return $arr;


		$extraFeatures = '';
		$args = array(
			'post_type' => 'product',
			'meta_key' => 'carspot_package_type',
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'carspot_category_pricing'
				),
			),
			'meta_query' =>
			array(
				'key' => 'carspot_package_type',
				'value' => 'category_based',
				'compare' => '=',
			),
			'posts_per_page' => -1,
		);

		$los = get_posts($args);
		$i = 0;
		wc()->frontend_includes();
		WC()->session = new WC_Session_Handler();
		WC()->session->init();
		WC()->customer = new WC_Customer(get_current_user_id(), true);
		WC()->cart = new WC_Cart();

		$cart = WC()->instance()->cart;
		if (count($los) > 0) {
			foreach ($los as $lo) {
				$arr[$i]['id'] = $lo->ID;
				$arr[$i]['title'] = $lo->post_title;
				$arr[$i]['cats'] = get_post_meta($lo->ID, 'carspot_package_cats', true);
				$i++;
			}
		}
		return $arr;
	}
}
/**
 * Auto Complete all WooCommerce orders.
 */
$my_theme = wp_get_theme();
if ($my_theme->get('Name') != 'carspot' && $my_theme->get('Name') != 'carspot child') {
	add_action('woocommerce_thankyou', 'carspotAPI_custom_woocommerce_auto_complete_order');
}
if (!function_exists('carspotAPI_custom_woocommerce_auto_complete_order')) {
	function carspotAPI_custom_woocommerce_auto_complete_order($order_id)
	{
		if (!$order_id) {
			return;
		}

		global $carspotAPI;
		if (isset($carspotAPI['sb_order_auto_approve']) && $carspotAPI['sb_order_auto_approve']) {
			$order = wc_get_order($order_id);
			$order->update_status('completed');
		}
	}
}

if (!function_exists('carspot_get_term_formAPI')) {
	function carspot_get_term_formAPI($term_id = '', $post_id = '', $formType = 'dynamic', $is_array = '')
	{
		global $carspotAPI;
		global $carspot_theme;
		$data = ($formType == 'dynamic' && $term_id != "") ? sb_text_field_value($term_id) : sb_getTerms('custom');

		if ($is_array == 'arr') return $data;
		$dataHTML = '';
		foreach ($data as $d) {
			$name 		= $d['name'];
			$slug 		= $d['slug'];
			if ($formType == 'static') {
				$showme = 1;
				if (isset($carspot_theme["allow_tax_condition"]) && $slug == 'ad_condition') {
					$showme = $carspot_theme["allow_tax_condition"];
				}
				if (isset($carspot_theme["allow_tax_warranty"]) && $slug == 'ad_warranty') {
					$showme = $carspot_theme["allow_tax_warranty"];
				}
				if (isset($carspot_theme["allow_ad_years"]) && $slug == 'ad_years') {
					$showme = $carspot_theme["allow_ad_years"];
				}
				if (isset($carspot_theme["allow_ad_body_types"]) && $slug == 'ad_body_types') {
					$showme = $carspot_theme["allow_ad_body_types"];
				}
				if (isset($carspot_theme["allow_ad_transmissions"]) && $slug == 'ad_transmissions') {
					$showme = $carspot_theme["allow_ad_transmissions"];
				}
				if (isset($carspot_theme["allow_ad_engine_capacities"]) && $slug == 'ad_engine_capacities') {
					$showme = $carspot_theme["allow_ad_engine_capacities"];
				}
				if (isset($carspot_theme["allow_ad_engine_types"]) && $slug == 'ad_engine_types') {
					$showme = $carspot_theme["allow_ad_engine_types"];
				}
				if (isset($carspot_theme["allow_ad_assembles"]) && $slug == 'ad_assembles') {
					$showme = $carspot_theme["allow_ad_assembles"];
				}
				if (isset($carspot_theme["allow_ad_colors"]) && $slug == 'ad_colors') {
					$showme = $carspot_theme["allow_ad_colors"];
				}
				if (isset($carspot_theme["allow_ad_insurance"]) && $slug == 'ad_insurance') {
					$showme = $carspot_theme["allow_ad_insurance"];
				}
				if (isset($carspot_theme["allow_ad_features"]) && $slug == 'ad_features') {
					$showme = $carspot_theme["allow_ad_features"];
				}
				$is_show = $showme;
				$is_this_req = 1;
			} else {
				$is_show 	= $d['is_show'];
				$is_this_req = $d['is_req'];
			}
			$is_req  	= 		$is_this_req;
			$is_search 	= 		$d['is_search'];
			$is_type 	= 		$d['is_type'];
			$required   = 		(isset($is_req) && $is_req == 1) ? true : false;
			if ($is_show == 1) {
				if ($is_type == 'textfield') {

					$inputVal = get_post_meta($post_id, '_carspot_' . $slug, true);
					$arrays[] = 	array("main_title" => ucfirst($name), "field_type" => 'textfield', "field_type_name" => $slug, "field_val" => $inputVal, "field_name" => "", "title" => $name, "values" => '', "has_page_number" => 2, "is_required" => $required);
				} else if ($slug == 'ad_features') {
					$adfeatures = $required = '';
					$ad_features	=	carspot_get_cats('ad_features', 0);
					$count          =   1;
					$adfeatures     =   get_post_meta($post_id, '_carspot_' . $slug, true);
					$frs = ($adfeatures != "") ? explode('|', $adfeatures) : array();
					foreach ($ad_features as $feature) {
						$selected          =  (in_array($feature->name, $frs)) ? true : false;
						$features_values[] =  array("name" => $feature->name, "is_checked" => $selected);
						$count++;
					}

					$arrays[] = 	array("main_title" => $name, "field_type" => 'checkbox', "field_type_name" => "ad_features", "field_val" => $inputVal, "field_name" => "", "title" => $name, "values" => $features_values, "has_page_number" => 2, "is_required" => $required);
				} else {
					$values	=	carspot_get_cats($slug, 0);
					$select_values = array();
					if (!empty($values) && count((array) $values) > 0) {
						$adfeaturesName     = get_post_meta($post_id, '_carspot_' . $slug, true);
						$select_values[] 	= array("value" => __("Select an option", "carspot-rest-api"), "selected" => '', "name" => '');
						foreach ($values as $val) {
							if (isset($val->term_id) && $val->term_id != "") {
								$id 				= $val->term_id;
								$name2 				= $val->name;
								$selected  			= ($adfeaturesName == $val->name) ? true : false;
								$select_values[] 	= array("value" => $id . '|' . $name2, "selected" => $selected, "name" => $name2);
							}
						}
						//Replaced By ScriptsBundle
						$arrays[] =  array("main_title" => $name, "field_type" => 'select', "field_type_name" => "" . $slug, "field_val" => $inputVal, "field_name" => "", "title" => $name, "values" => $select_values, "has_page_number" => 2, "is_required" => $required);
					}
				}
			}
		}
		return $arrays;
	}
}

function carspotAPI_dynamic_templateID($cat_id)
{
	$termTemplate = '';
	if ($cat_id != "") {
		$termTemplate	= get_term_meta($cat_id, '_sb_category_template', true);
		$go_next = ($termTemplate == "" || $termTemplate == 0) ? true : false;
		if ($go_next) {
			$parent = get_term($cat_id);
			if ($parent->parent > 0) {
				$cat_id = $parent->parent;
				$termTemplate	= get_term_meta($cat_id, '_sb_category_template', true);
				$go_next = ($termTemplate == "" || $termTemplate == 0) ? true : false;
				$parent = get_term($cat_id);
				if ($parent->parent > 0 && $go_next) {
					$cat_id = $parent->parent;
					$termTemplate	= get_term_meta($cat_id, '_sb_category_template', true);
					$parent = get_term($cat_id);
					$go_next = ($termTemplate == "" || $termTemplate == 0) ? true : false;
					if ($parent->parent > 0  && $go_next) {
						$cat_id = $parent->parent;
						$termTemplate	= get_term_meta($cat_id, '_sb_category_template', true);
						$parent = get_term($cat_id);
						$go_next = ($termTemplate == "" || $termTemplate == 0) ? true : false;
						if ($parent->parent > 0  && $go_next) {
							$cat_id = $parent->parent;
							$termTemplate	= get_term_meta($cat_id, '_sb_category_template', true);
							$parent = get_term($cat_id);
							$go_next = ($termTemplate == "" || $termTemplate == 0) ? true : false;
							if ($parent->parent > 0  && $go_next) {
								$cat_id = $parent->parent;
								$termTemplate	= get_term_meta($cat_id, '_sb_category_template', true);
							}
						}
					}
				}
			}
		}
	}
	return $termTemplate;
}
