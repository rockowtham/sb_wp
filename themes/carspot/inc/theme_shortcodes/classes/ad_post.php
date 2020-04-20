<?php

if (!class_exists('carspot_ad_post')) {

    class carspot_ad_post {
        /* user object */

        var $user_info;

        public function __construct() {
            $this->user_info = get_userdata(get_current_user_id());
        }

    }

}

/* Ad Posting... */
add_action('wp_ajax_sb_ad_posting', 'carspot_ad_posting');
if (!function_exists('carspot_ad_posting')) {
	
    function carspot_ad_posting() {
		check_ajax_referer( 'carspot_ad_post_secure', 'security' );
        global $carspot_theme;

        if (get_current_user_id() == "") {
            echo "0";
            die();
        }

        // Getting values
        $params = array();
        parse_str($_POST['sb_data'], $params);

        $cats = array();
        if (isset($params['ad_cat_sub_sub_sub']) != "") {
            $cats[] = $params['ad_cat_sub_sub_sub'];
        }
        if ($params['ad_cat_sub_sub'] != "") {
            $cats[] = $params['ad_cat_sub_sub'];
        }
        if ($params['ad_cat_sub'] != "") {
            $cats[] = $params['ad_cat_sub'];
        }
        if ($params['ad_cat'] != "") {
            $cats[] = $params['ad_cat'];
        }

        $get_ads = get_user_meta(get_current_user_id(), '_sb_simple_ads', true);


        $ad_status = 'publish';

        if ($_POST['is_update'] != "") {
            if ($carspot_theme['sb_update_approval'] == 'manual') {
                $ad_status = 'pending';
            }
            $pid = $_POST['is_update'];
            $media = get_attached_media('image', $pid);
            $is_imageallow = carspotCustomFieldsVals($pid, $cats);

            if ($is_imageallow == 1 && count((array) $media) == 0) {
                echo "img_req";
                die();
            }
        } else {
            if ($carspot_theme['sb_ad_approval'] == 'manual') {
                $ad_status = 'pending';
            }

            $pid = get_user_meta(get_current_user_id(), 'ad_in_progress', true);
            $media = get_attached_media('image', $pid);
            $is_imageallow = carspotCustomFieldsVals($pid, $cats);

            if ($is_imageallow == 1 && count((array) $media) == 0) {
                echo "img_req";
                die();
            }

            /* Now user can post new ad */
            delete_user_meta(get_current_user_id(), 'ad_in_progress');
            update_post_meta($pid, '_carspot_is_feature', '0');
            update_post_meta($pid, '_carspot_ad_status_', 'active');
            //send email
            carspot_get_notify_on_ad_post($pid);
        }



        /* Bad words filteration */
        $words = explode(',', $carspot_theme['bad_words_filter']);
        $replace = $carspot_theme['bad_words_replace'];
        $desc = carspot_badwords_filter($words, $params['ad_description'], $replace);
        $title = carspot_badwords_filter($words, $params['ad_title'], $replace);
        $my_post = array(
            'ID' => $pid,
            'post_title' => sanitize_text_field($title),
            'post_status' => $ad_status,
            'post_content' => $desc,
            'post_name' => sanitize_text_field($title)
        );

        wp_update_post($my_post);

        $category = array();
        if ($params['ad_cat'] != "") {
            $category[] = $params['ad_cat'];
        }
        if ($params['ad_cat_sub'] != "") {
            $category[] = $params['ad_cat_sub'];
        }
        if ($params['ad_cat_sub_sub'] != "") {
            $category[] = $params['ad_cat_sub_sub'];
        }
        if ($params['ad_cat_sub_sub_sub'] != "") {
            $category[] = $params['ad_cat_sub_sub_sub'];
        }

        if (isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'category_based' && class_exists('WooCommerce') && sizeof(WC()->cart->get_cart()) > 0 && $get_ads == 0) {
            $ad_status = 'pending';
            $my_post = array(
                'ID' => $pid,
                'post_status' => $ad_status,
            );
            wp_update_post($my_post);
            update_post_meta($pid, '_carspot_category_based_cats', $category);
            wp_set_post_terms($pid, $category, 'ad_cats');
        } else {
            wp_set_post_terms($pid, $category, 'ad_cats');
        }
        /* countries */
        $countries = array();
        if ($params['ad_country'] != "") {
            $countries[] = $params['ad_country'];
        }
        if ($params['ad_country_states'] != "") {
            $countries[] = $params['ad_country_states'];
        }
        if ($params['ad_country_cities'] != "") {
            $countries[] = $params['ad_country_cities'];
        }
        if ($params['ad_country_towns'] != "") {
            $countries[] = $params['ad_country_towns'];
        }
        wp_set_post_terms($pid, $countries, 'ad_country');
        /* setting taxonomoies selected */
        $type = '';
        if ($params['buy_sell'] != "") {
            $type_arr = explode('|', $params['buy_sell']);
            wp_set_post_terms($pid, $type_arr[0], 'ad_type');
            $type = $type_arr[1];
        }
        $conditon = '';
        if ($params['condition'] != "") {
            $condition_arr = explode('|', $params['condition']);
            wp_set_post_terms($pid, $condition_arr[0], 'ad_condition');
            $conditon = $condition_arr[1];
        }
        $warranty = '';
        if ($params['ad_warranty'] != "") {
            $warranty_arr = explode('|', $params['ad_warranty']);
            wp_set_post_terms($pid, $warranty_arr[0], 'ad_warranty');
            $warranty = $warranty_arr[1];
        }
        $ad_year = '';
        if ($params['ad_year'] != "") {
            $year_arr = explode('|', $params['ad_year']);
            wp_set_post_terms($pid, $year_arr[0], 'ad_year');
            $ad_year = $year_arr[1];
        }

        $ad_body_type = '';
        if ($params['ad_body_type'] != "") {
            $ad_body_type_arr = explode('|', $params['ad_body_type']);
            wp_set_post_terms($pid, $ad_body_type_arr[0], 'ad_body_type');
            $ad_body_type = $ad_body_type_arr[1];
        }

        $ad_transmission = '';
        if ($params['ad_transmission'] != "") {
            $ad_transmission_arr = explode('|', $params['ad_transmission']);
            wp_set_post_terms($pid, $ad_transmission_arr[0], 'ad_transmission');
            $ad_transmission = $ad_transmission_arr[1];
        }

        $ad_engine_capacity = '';
        if ($params['ad_engine_capacity'] != "") {
            $ad_engine_capacity_arr = explode('|', $params['ad_engine_capacity']);
            wp_set_post_terms($pid, $ad_engine_capacity_arr[0], 'ad_engine_capacity');
            $ad_engine_capacity = $ad_engine_capacity_arr[1];
        }

        $ad_engine_type = '';
        if ($params['ad_engine_type'] != "") {
            $ad_engine_type_arr = explode('|', $params['ad_engine_type']);
            wp_set_post_terms($pid, $ad_engine_type_arr[0], 'ad_engine_type');
            $ad_engine_type = $ad_engine_type_arr[1];
        }

        $ad_assemble = '';
        if ($params['ad_assemble'] != "") {
            $ad_assemble_arr = explode('|', $params['ad_assemble']);
            wp_set_post_terms($pid, $ad_assemble_arr[0], 'ad_assemble');
            $ad_assemble = $ad_assemble_arr[1];
        }

        $ad_color = '';
        if ($params['ad_color'] != "") {
            $ad_color_arr = explode('|', $params['ad_color']);
            wp_set_post_terms($pid, $ad_color_arr[0], 'ad_color');
            $ad_color = $ad_color_arr[1];
        }

        $ad_insurance = '';
        if ($params['ad_insurance'] != "") {
            $ad_insurance_arr = explode('|', $params['ad_insurance']);
            wp_set_post_terms($pid, $ad_insurance_arr[0], 'ad_insurance');
            $ad_insurance = $ad_insurance_arr[1];
        }
        $tags = explode(',', $params['tags']);
        wp_set_object_terms($pid, $tags, 'ad_tags');

        /* Update post meta */
        update_post_meta($pid, '_carspot_poster_name', sanitize_text_field($params['sb_user_name']));
        update_post_meta($pid, '_carspot_poster_contact', sanitize_text_field($params['sb_contact_number']));
        update_post_meta($pid, '_carspot_ad_mileage', sanitize_text_field($params['ad_mileage']));
        update_post_meta($pid, '_carspot_ad_price', sanitize_text_field($params['ad_price']));
        update_post_meta($pid, '_carspot_ad_map_lat', sanitize_text_field($params['ad_map_lat']));
        update_post_meta($pid, '_carspot_ad_map_long', sanitize_text_field($params['ad_map_long']));
        update_post_meta($pid, '_carspot_ad_bidding', sanitize_text_field($params['ad_bidding']));
        update_post_meta($pid, '_carspot_ad_price_type', sanitize_text_field($params['ad_price_type']));
        update_post_meta($pid, '_carspot_ad_map_location', sanitize_text_field($params['sb_user_address']));
        update_post_meta($pid, '_carspot_ad_avg_city', sanitize_text_field($params['ad_avg_city']));
        update_post_meta($pid, '_carspot_ad_avg_hwy', sanitize_text_field($params['ad_avg_hwy']));
        foreach ($params as $key => $val) {
            $pos = strpos($key, '_carspot_');
            if ($pos !== false) {
                $value = '';
                if ($val != "") {
                    $valueArr = explode('|', $val);
                    wp_set_post_terms($pid, $valueArr[0], 'ad_insurance');
                    $value = $valueArr[1];
                }
                update_post_meta($pid, $key, sanitize_text_field($value));
            }
        }
        if (isset($params['ad_yvideo']) && $params['ad_yvideo'] != "") {
            $video = explode("&t=", $params['ad_yvideo']);
            if (isset($video[0]) && $video[0] != "") {
                update_post_meta($pid, '_carspot_ad_yvideo', sanitize_text_field($video[0]));
            } else {
                update_post_meta($pid, '_carspot_ad_yvideo', sanitize_text_field($params['ad_yvideo']));
            }
        } else {
            update_post_meta($pid, '_carspot_ad_yvideo', sanitize_text_field($params['ad_yvideo']));
        }
        // Stroring Extra fileds in DB
        if ($params['sb_total_extra'] > 0) {
            for ($i = 1; $i <= $params['sb_total_extra']; $i++) {
                update_post_meta($pid, "_sb_extra_" . $params["title_$i"], sanitize_text_field($params["sb_extra_$i"]));
            }
        }

        //Add Dynamic Fields
        if (isset($params['cat_template_field']) && count($params['cat_template_field']) > 0) {
            foreach ($params['cat_template_field'] as $key => $data) {
                if (is_array($data)) {
                    $dataArr = array();
                    foreach ($data as $k)
                        $dataArr[] = $k;
                    $data = stripslashes(json_encode($dataArr, JSON_UNESCAPED_UNICODE));
                }
                update_post_meta($pid, $key, sanitize_text_field($data));
            }
        }

        $features = $params['ad_features'];
        if (count((array) $features) > 0) {
            $ad_features = '';
            foreach ($features as $feature) {
                $ad_features .= $feature . "|";
            }
            $ad_features = rtrim($ad_features, '|');
        }
        update_post_meta($pid, '_carspot_ad_features', sanitize_text_field($ad_features));


        //only for category based pricing
        if (isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'category_based' && class_exists('WooCommerce')) {
            if (sizeof(WC()->cart->get_cart()) > 0) {
                $carts = WC()->cart->get_cart();

                WC()->session->set('_carspot_ad_id', $pid);
                if (isset($carspot_theme['sb_checkout_page']) && $carspot_theme['sb_checkout_page'] != '') {
                    $pid = $carspot_theme['sb_checkout_page'];
                }
            }
        }

        //only for category based pricing
        if (isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'package_based') {
            if (isset($_POST['is_update']) && $_POST['is_update'] == "") {
                $simple_ads = get_user_meta(get_current_user_id(), '_sb_simple_ads', true);
                if ($simple_ads > 0 && !is_super_admin(get_current_user_id())) {
                    $simple_ads = $simple_ads - 1;
                    update_user_meta(get_current_user_id(), '_sb_simple_ads', $simple_ads);
                }
            }

            // Making it featured ad
            if (isset($params['sb_make_it_feature']) && $params['sb_make_it_feature']) {
                // Uptaing remaining ads.
                $featured_ad = get_user_meta(get_current_user_id(), '_carspot_featured_ads', true);
                if ($featured_ad > 0 || $featured_ad == '-1') {
                    update_post_meta($pid, '_carspot_is_feature', '1');
                    update_post_meta($pid, '_carspot_is_feature_date', date('Y-m-d'));
                    $featured_ad = $featured_ad - 1;
                    update_user_meta(get_current_user_id(), '_carspot_featured_ads', $featured_ad);
                }
            }
            // Bumping it up
            if (isset($params['sb_bump_up']) && $params['sb_bump_up']) {
                // Uptaing remaining ads.
                $bump_ads = get_user_meta(get_current_user_id(), '_carspot_bump_ads', true);
                if ($bump_ads > 0 || $bump_ads == '-1') {
                    wp_update_post(
                            array(
                                'ID' => $pid, // ID of the post to update
                                'post_date' => current_time('mysql'),
                                'post_date_gmt' => get_gmt_from_date(current_time('mysql'))
                            )
                    );
					if($bump_ads != '-1')
					{
						$bump_ads = $bump_ads - 1;
					}
                    update_user_meta(get_current_user_id(), '_carspot_bump_ads', $bump_ads);
                }
            }
        }
        echo esc_url(get_the_permalink($pid));
        die();
    }

}



// Get sub cats
add_action('wp_ajax_sb_get_sub_cat_search', 'carspot_get_sub_cats_search');
add_action('wp_ajax_nopriv_sb_get_sub_cat_search', 'carspot_get_sub_cats_search');
if (!function_exists('carspot_get_sub_cats_search')) {

    function carspot_get_sub_cats_search() {
        global $carspot_theme;
        $heading = '';
        if (isset($carspot_theme['cat_level_2']) && $carspot_theme['cat_level_2'] != "") {
            $heading = $carspot_theme['cat_level_2'];
        }
        $cat_id = $_POST['cat_id'];
        $ad_cats = carspot_get_cats('ad_cats', $cat_id);
        $res = '';
        if (count((array) $ad_cats) > 0) {
            $res .= '<label>' . $heading . '</label>';
            $res .= '<select class="search-select form-control" id="cats_response">';
            $res .= '<option label="' . esc_html__('Select Option', 'carspot') . '"></option>';
            foreach ($ad_cats as $ad_cat) {
                $res .= '<option value=' . esc_attr($ad_cat->term_id) . '>' . esc_html($ad_cat->name) . '</option>';
            }
            $res .= '</select>';
            echo($res);
        }
        die();
    }

}

// Get sub cats Version
add_action('wp_ajax_sb_get_sub_sub_cat_search', 'carspot_get_sub_sub_cats_search');
add_action('wp_ajax_nopriv_sb_get_sub_sub_cat_search', 'carspot_get_sub_sub_cats_search');
if (!function_exists('carspot_get_sub_sub_cats_search')) {

    function carspot_get_sub_sub_cats_search() {
        global $carspot_theme;
        $heading = '';
        if (isset($carspot_theme['cat_level_3']) && $carspot_theme['cat_level_3'] != "") {
            $heading = $carspot_theme['cat_level_3'];
        }
        $cat_id = $_POST['cat_id'];
        $ad_cats = carspot_get_cats('ad_cats', $cat_id);
        $res = '';
        if (count((array) $ad_cats) > 0) {
            $res .= '<label>' . $heading . '</label>';
            $res .= '<select class="search-select form-control"  id="select_version">';
            $res .= '<option label="' . esc_html__('Select An Option', 'carspot') . '"></option>';
            foreach ($ad_cats as $ad_cat) {
                $res .= '<option value=' . esc_attr($ad_cat->term_id) . '>' . esc_html($ad_cat->name) . '</option>';
            }
            $res .= '</select>';
            echo($res);
        }
        die();
    }

}

// Get sub cats Version 4th Level
add_action('wp_ajax_sb_get_sub_sub_sub_cat_search', 'carspot_get_sub_sub_sub_cats_forth_search');
add_action('wp_ajax_nopriv_sb_get_sub_sub_sub_cat_search', 'carspot_get_sub_sub_sub_cats_forth_search');
if (!function_exists('carspot_get_sub_sub_sub_cats_forth_search')) {

    function carspot_get_sub_sub_sub_cats_forth_search() {
        global $carspot_theme;
        $heading = '';
        if (isset($carspot_theme['cat_level_4']) && $carspot_theme['cat_level_4'] != "") {
            $heading = $carspot_theme['cat_level_4'];
        }
        $cat_id = $_POST['cat_id'];
        $ad_cats = carspot_get_cats('ad_cats', $cat_id);
        $res = '';
        if (count((array) $ad_cats) > 0) {
            $res .= '<label>' . $heading . '</label>';
            $res .= '<select class="search-select form-control"  id="select_forth">';
            $res .= '<option label="' . esc_html__('Select An Option', 'carspot') . '"></option>';
            foreach ($ad_cats as $ad_cat) {
                $res .= '<option value=' . esc_attr($ad_cat->term_id) . '>' . esc_html($ad_cat->name) . '</option>';
            }
            $res .= '</select>';
            echo($res);
        }
        die();
    }

}


/*  Get sub cats */

add_action('wp_ajax_sb_get_sub_cat', 'carspot_get_sub_cats');
if (!function_exists('carspot_get_sub_cats')) {

    function carspot_get_sub_cats() {
        $cat_id = $_POST['cat_id'];
        $ad_cats = carspot_get_cats('ad_cats', $cat_id);
        if (count((array) $ad_cats) > 0) {

            $cats_html = '<select class="category form-control" id="ad_cat_sub" name="ad_cat_sub">';
            $cats_html .= '<option label="' . esc_html__('Select Option', 'carspot') . '"></option>';
            foreach ($ad_cats as $ad_cat) {
                $cats_html .= '<option value="' . $ad_cat->term_id . '">' . $ad_cat->name . '</option>';
            }
            $cats_html .= '</select>';
            echo($cats_html);
            die();
        } else {
            return "";
            die();
        }
    }

}

if (!function_exists('carspot_check_author')) {

    function carspot_check_author($ad_id) {
        if (get_post_field('post_author', $ad_id) != get_current_user_id()) {
            return false;
        } else {
            return true;
        }
    }

}

add_action('wp_ajax_post_ad', 'carspot_post_ad_process');
if (!function_exists('carspot_post_ad_process')) {

    function carspot_post_ad_process() {
        if (isset($_POST['is_update']) && $_POST['is_update'] != "") {
            return '';
        }

        $title = ( isset($_POST['title'])) ? $_POST['title'] : "-";
        if (get_current_user_id() == "")
            die();
        if (!isset($title))
            die();

        $ad_id = get_user_meta(get_current_user_id(), 'ad_in_progress', true);
        if (get_post_status($ad_id) && $ad_id != "") {
            $my_post = array(
                'ID' => get_user_meta(get_current_user_id(), 'ad_in_progress', true),
                'post_title' => $title,
            );
            wp_update_post($my_post);
            return '';
        }
        // Gather post data.
        $my_post = array(
            'post_title' => sanitize_text_field($title),
            'post_status' => 'pending',
            'post_author' => get_current_user_id(),
            'post_type' => 'ad_post'
        );

// Insert the post into the database.
        $id = wp_insert_post($my_post);
        if ($id) {
            update_user_meta(get_current_user_id(), 'ad_in_progress', $id);
        }
        return '';
    }

}

add_action('wp_ajax_delete_ad_image', 'carspot_delete_ad_image');
if (!function_exists('carspot_delete_ad_image')) {

    function carspot_delete_ad_image() {
        if (get_current_user_id() == "")
            die();


        if ($_POST['is_update'] != "") {
            $ad_id = $_POST['is_update'];
        } else {
            $ad_id = get_user_meta(get_current_user_id(), 'ad_in_progress', true);
        }

        if (!is_super_admin(get_current_user_id()) && get_post_field('post_author', $ad_id) != get_current_user_id())
            die();


        $attachmentid = $_POST['img'];
        wp_delete_attachment($attachmentid, true);

        if (get_post_meta($ad_id, 'carspot_photo_arrangement_', true) != "") {
            $ids = get_post_meta($ad_id, 'carspot_photo_arrangement_', true);
            $res = str_replace($attachmentid, "", $ids);
            $res = str_replace(',,', ",", $res);
            $img_ids = trim($res, ',');
            update_post_meta($ad_id, 'carspot_photo_arrangement_', $img_ids);
        }
        echo "1";
        die();
    }

}
/* upload images with dropzone library and save it. */
add_action('wp_ajax_upload_ad_images', 'carspot_upload_ad_images');

if (!function_exists('carspot_upload_ad_images')) {

    function carspot_upload_ad_images() {

        global $carspot_theme;

        carspot_authenticate_check();

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $size_arr = explode('-', $carspot_theme['sb_upload_size']);
        $display_size = $size_arr[1];
        $actual_size = $size_arr[0];

        // Allow certain file formats
        $imageFileType = strtolower(end(explode('.', $_FILES['my_file_upload']['name'])));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo '0|' . esc_html__("Sorry, only JPG, JPEG, PNG & GIF files are allowed.", 'carspot');
            die();
        }

        // Check file size
        if ($_FILES['my_file_upload']['size'] > $actual_size) {
            echo '0|' . esc_html__("Max allowd image size is", 'carspot') . " " . $display_size;
            die();
        }


        // Let WordPress handle the upload.
        // Remember, 'my_image_upload' is the name of our file input in our form above.
        if ($_GET['is_update'] != "") {
            $ad_id = $_GET['is_update'];
        } else {
            $ad_id = get_user_meta(get_current_user_id(), 'ad_in_progress', true);
        }

        // Check max image limit
        $media = get_attached_media('image', $ad_id);
        if (count((array) $media) >= $carspot_theme['sb_upload_limit']) {
            echo '0|' . esc_html__("You can not upload more than ", 'carspot') . " " . $carspot_theme['sb_upload_limit'];
            die();
        }
        $attachment_id = media_handle_upload('my_file_upload', $ad_id);
        if (!is_wp_error($attachment_id)) {
            $imgaes = get_post_meta($ad_id, 'carspot_photo_arrangement_', true);
            if ($imgaes != "") {
                $imgaes = $imgaes . ',' . $attachment_id;
                update_post_meta($ad_id, 'carspot_photo_arrangement_', $imgaes);
            } else {
                update_post_meta($ad_id, 'carspot_photo_arrangement_', $attachment_id);
            }
            echo '' . $attachment_id;
            die();
        } else {
            echo '0|' . esc_html__("Something went wrong please try later", 'carspot');
            die();
        }
    }

}

add_action('wp_ajax_get_uploaded_ad_images', 'carspot_get_uploaded_ad_images');
if (!function_exists('carspot_get_uploaded_ad_images')) {

    function carspot_get_uploaded_ad_images() {
        if ($_POST['is_update'] != "") {
            $ad_id = $_POST['is_update'];
        } else {
            $ad_id = get_user_meta(get_current_user_id(), 'ad_in_progress', true);
        }
        $media = carspot_fetch_listing_gallery($ad_id);
        if (count((array) $media) > 0) {
            foreach ($media as $m) {
                $mid = '';
                if (isset($m->ID)) {
                    $mid = $m->ID;
                } else {
                    $mid = $m;
                }
                $image = wp_get_attachment_image_src($mid, 'carspot-ad-thumb');
                $img = $image[0];
                $obj = array();
                $obj['display_name'] = basename(get_attached_file($mid));
                $obj['name'] = $img;
                $obj['size'] = filesize(get_attached_file($mid));
                $obj['id'] = $mid;
                $result[] = $obj;
            }
        }


        header('Content-type: text/json');
        header('Content-type: application/json');
        echo json_encode($result);
        die();
    }

}

if (!function_exists('carspot_delete_post_taxonomies')) {

    function carspot_delete_post_taxonomies($object_id, $taxonomy) {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = '$object_id'");
        if (count((array) $rows) > 0) {
            foreach ($rows as $row) {
                $rs = $wpdb->get_row("SELECT taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = '" . $row->term_taxonomy_id . "'");
                if ($rs->taxonomy == $taxonomy) {
                    echo "DELETE FROM $wpdb->term_relationships WHERE object_id = '$object_id' AND term_taxonomy_id = '" . $row->term_taxonomy_id . "'";

                    $wpdb->delete($wpdb->term_relationships, array('object_id' => $object_id, 'term_taxonomy_id' => $row->term_taxonomy_id));
                }
            }
        }
    }

}
if (!function_exists('carspot_get_ad_cats')) {

    function carspot_get_ad_cats($id, $by = 'name') {
        $terms = wp_get_post_terms($id, 'ad_cats');
        $cats = array();
        $myparentID = '';
        foreach ($terms as $term) {
            if ($term->parent == 0) {
                $myparent = $term;
                $myparentID = $myparent->term_id;
                $cats[] = array('name' => $myparent->name, 'id' => $myparent->term_id);
                break;
            }
        }

        if ($myparentID != "") {
            $mychildID = '';
            // Right, the parent is set, now let's get the children
            foreach ($terms as $term) {
                if ($term->parent == $myparentID) { // this ignores the parent of the current post taxonomy
                    $child_term = $term; // this gets the children of the current post taxonomy 
                    $mychildID = $child_term->term_id;
                    $cats[] = array('name' => $child_term->name, 'id' => $child_term->term_id);
                    break;
                }
            }
            if ($mychildID != "") {
                $mychildchildID = '';
                // Right, the parent is set, now let's get the children
                foreach ($terms as $term) {
                    if ($term->parent == $mychildID) { // this ignores the parent of the current post taxonomy
                        $child_term = $term; // this gets the children of the current post taxonomy
                        $mychildchildID = $child_term->term_id;
                        $cats[] = array('name' => $child_term->name, 'id' => $child_term->term_id);
                        break;
                    }
                }
                if ($mychildchildID != "") {
                    // Right, the parent is set, now let's get the children
                    foreach ($terms as $term) {
                        if ($term->parent == $mychildchildID) { // this ignores the parent of the current post taxonomy
                            $child_term = $term; // this gets the children of the current post taxonomy   
                            $cats[] = array('name' => $child_term->name, 'id' => $child_term->term_id);
                            break;
                        }
                    }
                }
            }
        }
        return $cats;
        $post_categories = wp_get_object_terms($id, 'ad_cats', array('orderby' => 'term_group'));
        $cats = array();
        foreach ($post_categories as $c) {
            $cat = get_term($c);
            $cats[] = array('name' => $cat->name, 'id' => $cat->term_id);
        }
        return $cats;
    }

}
if (!function_exists('carspot_get_ad_country')) {

    function carspot_get_ad_country($id, $by = 'name') {
        $terms = wp_get_post_terms($id, 'ad_country');
        $cats = array();
        $myparentID = '';
        foreach ($terms as $term) {
            if ($term->parent == 0) {
                $myparent = $term;
                $myparentID = $myparent->term_id;
                $cats[] = array('name' => $myparent->name, 'id' => $myparent->term_id);
                break;
            }
        }

        if ($myparentID != "") {
            $mychildID = '';
            // Right, the parent is set, now let's get the children
            foreach ($terms as $term) {
                if ($term->parent == $myparentID) { // this ignores the parent of the current post taxonomy
                    $child_term = $term; // this gets the children of the current post taxonomy 
                    $mychildID = $child_term->term_id;
                    $cats[] = array('name' => $child_term->name, 'id' => $child_term->term_id);
                    break;
                }
            }
            if ($mychildID != "") {
                $mychildchildID = '';
                // Right, the parent is set, now let's get the children
                foreach ($terms as $term) {
                    if ($term->parent == $mychildID) { // this ignores the parent of the current post taxonomy
                        $child_term = $term; // this gets the children of the current post taxonomy
                        $mychildchildID = $child_term->term_id;
                        $cats[] = array('name' => $child_term->name, 'id' => $child_term->term_id);
                        break;
                    }
                }
                if ($mychildchildID != "") {
                    // Right, the parent is set, now let's get the children
                    foreach ($terms as $term) {
                        if ($term->parent == $mychildchildID) { // this ignores the parent of the current post taxonomy
                            $child_term = $term; // this gets the children of the current post taxonomy   
                            $cats[] = array('name' => $child_term->name, 'id' => $child_term->term_id);
                            break;
                        }
                    }
                }
            }
        }
        return $cats;

        $post_countries = wp_get_object_terms($id, array('ad_country'), array('orderby' => 'term_group'));
        $cats = array();
        foreach ($post_countries as $country) {
            $related_result = get_term($country);
            $cats[] = array('name' => $related_result->name, 'id' => $related_result->term_id);
        }
        return $cats;
    }

}

// Get all messages of particular ad
add_action('wp_ajax_sb_get_messages', 'carspot_get_messages');
if (!function_exists('carspot_get_messages')) {

    function carspot_get_messages() {
        carspot_authenticate_check();

        $ad_id = $_POST['ad_id'];
        $user_id = $_POST['user_id'];
        $authors = array($user_id, get_current_user_id());

        // Mark as read conversation
        update_comment_meta(get_current_user_id(), $ad_id . "_" . $user_id, 1);


        $parent = $user_id;
        if ($_POST['inbox'] == 'yes') {
            $parent = get_current_user_id();
        }
        $args = array(
            'author__in' => $authors,
            'post_id' => $ad_id,
            'parent' => $parent,
            'orderby' => 'comment_date',
            'order' => 'ASC',
        );
        $comments = get_comments($args);
        $messages = '';
        $i = 1;
        $total = count((array) $comments);
        if (count((array) $comments) > 0) {
            foreach ($comments as $comment) {
                $user_pic = '';
                $class = 'friend-message';
                if ($comment->user_id == get_current_user_id()) {
                    $class = 'my-message';
                }
                $user_pic = carspot_get_user_dp($comment->user_id);
                $id = '';
                if ($i == $total) {
                    $id = 'id="last_li"';
                }
                $i++;
                $messages .= '<li class="' . $class . ' clearfix" ' . $id . '>
							 <figure class="profile-picture">
								<img src="' . $user_pic . '" class="img-circle" alt="' . esc_html__('Profile Pic', 'carspot') . '">
							 </figure>
							 <div class="message">
								' . $comment->comment_content . '
								<div class="time"><i class="fa fa-clock-o"></i> ' . carspot_timeago($comment->comment_date) . '</div>
							 </div>
						  </li>';
            }
        }
        echo($messages);
        die();
    }

}

if (!function_exists('carspot_authenticate_check')) {

    function carspot_authenticate_check() {
        if (get_current_user_id() == "") {
            echo '0|' . esc_html__("You are not logged in.", 'carspot');
            die();
        }
    }
}



// Get States
add_action('wp_ajax_sb_get_sub_states', 'carspot_get_sub_states');
add_action('wp_ajax_nopriv_sb_get_sub_states_search', 'carspot_get_sub_states_search');
if (!function_exists('carspot_get_sub_states')) {

    function carspot_get_sub_states() {
        $cat_id = $_POST['cat_id'];
        $ad_cats = carspot_get_cats('ad_country', $cat_id);
        if (count((array) $ad_cats) > 0) {
            $cats_html = '<select class="category form-control" id="ad_cat_sub" name="ad_cat_sub">';
            $cats_html .= '<option label="' . esc_html__('Select Option', 'carspot') . '"></option>';
            foreach ($ad_cats as $ad_cat) {
                $cats_html .= '<option value="' . $ad_cat->term_id . '">' . $ad_cat->name . '</option>';
            }
            $cats_html .= '</select>';
            echo($cats_html);
            die();
        } else {
            echo "";
            die();
        }
    }

}

// Get States Search
add_action('wp_ajax_sb_get_sub_states_search', 'carspot_get_sub_states_search');
add_action('wp_ajax_nopriv_sb_get_sub_states_search', 'carspot_get_sub_states_search');
if (!function_exists('carspot_get_sub_states_search')) {

    function carspot_get_sub_states_search() {

        $cat_id = $_POST['country_id'];
        $ad_cats = carspot_get_cats('ad_country', $cat_id);
        $res = '';
        if (count((array) $ad_cats) > 0) {
            $res = '<label>' . carspot_get_taxonomy_parents($cat_id, 'ad_country', false) . '</label>';
            $res .= '<ul class="city-select-city" >';
            foreach ($ad_cats as $ad_cat) {
                $location_count = get_term($ad_cat->term_id);
                $count = $location_count->count;
                $id = 'ajax_states';
                $res .= '<li class="col-sm-3 col-md-4 col-xs-4"><a href="javascript:void(0);" data-country-id="' . esc_attr($ad_cat->term_id) . '" id="' . $id . '">' . $ad_cat->name . ' <span>(' . esc_html($count) . ')</span></a></li>';
            }
            $res .= '</ul>';
            echo($res);
        } else {
            echo "submit";
        }
        die();
    }

}

/* Top Most Term */
if (!function_exists('carspot_get_top_most_parents')) {

    function carspot_get_top_most_parents($id, $taxonomy) {
        $chain = '';
        $parent = get_term($id, $taxonomy);
        $parents = array();
        $name = $parent->name;
        if ($parent->parent && ($parent->parent != $parent->term_id)) {

            $term_id = $parent->term_id;
            $parents[] = array('name' => $name, 'id' => $term_id);
        }
        return $parents;
    }

}

/* Fields Values */
if (!function_exists('carspotCustomFieldsVals')) {

    function carspotCustomFieldsVals($post_id = '', $terms = array()) {
        if ($post_id == "")
            return;
        /* $terms = wp_get_post_terms($post_id, 'ad_cats'); */
        $is_show = '';
        if (count((array) $terms) > 0) {

            foreach ($terms as $term) {
                $term_id = $term;
                $t = carspot_dynamic_templateID($term_id);
                if ($t)
                    break;
            }
            $templateID = carspot_dynamic_templateID($term_id);
            $result = get_term_meta($templateID, '_sb_dynamic_form_fields', true);

            $is_show = '';
            $html = '';

            if (isset($result) && $result != "") {
                $is_show = sb_custom_form_data($result, '_sb_default_cat_image_required');
            }
        }
        return ($is_show == 1) ? 1 : 0;
    }

}





// LOCATION DROPSOWN IN SEARCH PAGE
// Get sub cats
add_action('wp_ajax_sb_get_sub_loc_search', 'carspot_get_sub_loc_search');
add_action('wp_ajax_sb_get_sub_loc_search', 'carspot_get_sub_loc_search');
if (!function_exists('carspot_get_sub_loc_search')) {

    function carspot_get_sub_loc_search() {
        global $carspot_theme;
		if ($carspot_theme['sb_location_titles'] != "") 
		{
			$titles_array = explode("|", $carspot_theme['sb_location_titles']);
			if (count((array) $titles_array) > 0) 
			{
				if (isset($titles_array[1]))
				{
					$heading = $titles_array[1];
				}
			}
        }
        $cat_id = $_POST['cat_id'];
        $ad_cats = carspot_get_cats('ad_country', $cat_id);
        $res = '';
        if (count((array) $ad_cats) > 0) {
            $res .= '<label>' . $heading . '</label>';
            $res .= '<select class="search-select form-control" id="loc_first_response">';
            $res .= '<option label="' . esc_html__('Select Option', 'carspot') . '"></option>';
            foreach ($ad_cats as $ad_cat) {
                $res .= '<option value=' . esc_attr($ad_cat->term_id) . '>' . esc_html($ad_cat->name) . '</option>';
            }
            $res .= '</select>';
            echo($res);
        }
        die();
    }

}

// Get sub cats Version
add_action('wp_ajax_sb_get_sub_sub_loc_search', 'carspot_get_sub_sub_loc_search');
add_action('wp_ajax_nopriv_sb_get_sub_sub_loc_search', 'carspot_get_sub_sub_loc_search');
if (!function_exists('carspot_get_sub_sub_loc_search')) {

    function carspot_get_sub_sub_loc_search() {
        global $carspot_theme;
        $heading = '';
        if ($carspot_theme['sb_location_titles'] != "") 
		{
			$titles_array = explode("|", $carspot_theme['sb_location_titles']);
			if (count((array) $titles_array) > 0) 
			{
				if (isset($titles_array[2]))
				{
					$heading = $titles_array[2];
				}
			}
        }

        $cat_id = $_POST['cat_id'];
        $ad_cats = carspot_get_cats('ad_country', $cat_id);
        $res = '';
        if (count((array) $ad_cats) > 0) {
            $res .= '<label>' . $heading . '</label>';
            $res .= '<select class="search-select form-control"  id="loc_second_response">';
            $res .= '<option label="' . esc_html__('Select An Option', 'carspot') . '"></option>';
            foreach ($ad_cats as $ad_cat) {
                $res .= '<option value=' . esc_attr($ad_cat->term_id) . '>' . esc_html($ad_cat->name) . '</option>';
            }
            $res .= '</select>';
            echo($res);
        }
        die();
    }

}

// Get sub cats Version 4th Level
add_action('wp_ajax_sb_get_sub_sub_sub_loc_search', 'carspot_get_sub_sub_sub_loc_search');
add_action('wp_ajax_nopriv_sb_get_sub_sub_sub_loc_search', 'carspot_get_sub_sub_sub_loc_search');
if (!function_exists('carspot_get_sub_sub_sub_loc_search')) {

    function carspot_get_sub_sub_sub_loc_search() {
        global $carspot_theme;
        $heading = '';
		if ($carspot_theme['sb_location_titles'] != "") 
		{
			$titles_array = explode("|", $carspot_theme['sb_location_titles']);
			if (count((array) $titles_array) > 0) 
			{
				if (isset($titles_array[3]))
				{
					$heading = $titles_array[3];
				}
			}
        }
        $cat_id = $_POST['cat_id'];
        $ad_cats = carspot_get_cats('ad_country', $cat_id);
        $res = '';
        if (count((array) $ad_cats) > 0) {
            $res .= '<label>' . $heading . '</label>';
            $res .= '<select class="search-select form-control"  id="loc_forth_response">';
            $res .= '<option label="' . esc_html__('Select An Option', 'carspot') . '"></option>';
            foreach ($ad_cats as $ad_cat) {
                $res .= '<option value=' . esc_attr($ad_cat->term_id) . '>' . esc_html($ad_cat->name) . '</option>';
            }
            $res .= '</select>';
            echo($res);
        }
        die();
    }

}