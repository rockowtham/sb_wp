<?php
class adds_post_metabox {

    public function __construct() {

        if (is_admin()) {
            add_action('load-post.php', array($this, 'init_metabox'));
            add_action('load-post-new.php', array($this, 'init_metabox'));
        }
    }

    public function init_metabox() {

        add_action('add_meta_boxes', array($this, 'add_metabox'));
        add_action('save_post', array($this, 'save_adds_metabox'), 10, 2);
    }

    public function add_metabox() {
        /* metabox for bump the ad */
        add_meta_box('sb_carspot_bump_ad', __('Bump This Ad At Top', 'redux-framework'), array($this, 'sb_render_meta_bump'), 'ad_post', 'normal', 'high');
        /* metabox for products */
        add_meta_box('sb_thmemes_carspot_metaboxes', __('Reported', 'redux-framework'), array($this, 'sb_render_meta_ads'), 'ad_post', 'normal', 'high');
        /* extra information for backend */
        add_meta_box('cs_ad_fields_metaboxes', __('Ad Information', 'redux-framework'), array($this, 'cs_fields_meta_ads'), 'ad_post', 'normal', 'high');
        

    }

    /* callback for bump the ad */

    function sb_render_meta_bump($post) {
        ?>
        <div class="margin_top">

            <h3 class="alignleft"> <?php echo __("Current Date: ", "redux-framework") . '' . get_the_date() . __(' And Time: ', 'redux-framework') . get_the_time('', get_the_ID()); ?> </h3>
            <div class="clear"></div>
            <input class="button button-primary" id="ad-carspot-bump-btn" value="<?php echo __("Bump This Ad At Top", "redux-framework"); ?>" type="buttom">
            <script type="text/javascript">


                //Car Comparison
                jQuery('#ad-carspot-bump-btn').on('click', function () {
                    var post_id = '<?php echo esc_html(get_the_ID()); ?>';
                    var confrm = confirm('<?php echo __("Are Your Sure You Want To Bumb The Ad", "redux-framework"); ?>');
                    if (confrm != true)
                        return;
                    jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action: 'carspot_make_ad_bumb', post_id: post_id, }).done(function (response)
                    {
                        if (response == 1)
                        {
                            location.reload();
                        }

                    });
                });


            </script>
            <div class="clear"></div>

        </div>    
        <?php
    }

    /* callback for products */

    function sb_render_meta_ads($post) {
        global $wpdb;
        $pid = $post->ID;
        $rows = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$pid' AND meta_key LIKE  '_sb_user_id_%' ");
        ?>
        <div class="margin_top">
            <h3><?php echo count((array) $rows); ?> <?php echo __('Users report to this AD.', 'redux-framework'); ?></h3>
            <ul type="square">
                <?php
                foreach ($rows as $row) {
                    $user = get_userdata($row->meta_value);
                    ?>
                    <li>
                        <p>->
                            <strong>
                                <?php echo esc_html($user->display_name); ?>
                            </strong> <?php echo __('mark as', 'redux-framework'); ?>
                            <strong>
                                <?php echo esc_html(get_post_meta($pid, '_sb_report_option_' . $row->meta_value, true)); ?>
                            </strong>
                        </p>
                        <p><?php echo esc_html(get_post_meta($pid, '_sb_report_comments_' . $row->meta_value, true)); ?></p>
                    </li>
                    <?php
                }
                ?>
            </ul>

        </div>

        <?php
    }

    /* callback for  extra information for backend */

    function cs_fields_meta_ads($post) {
        global $carspot_theme;
        wp_enqueue_script('tagsinputs');
        // We'll use this nonce field later on when saving.
        wp_nonce_field('my_adds_post_meta_inform', 'adds_post_meta_inform');
        /* all variables */
        global $wpdb, $carspot_theme;
        $pid = $post->ID;
        $is_update = !empty($pid) ? $pid : '';
        $cats_html = '';
        $tags = '';
        $cats = '';
        $country_html = '';
        $country_states = '';
        $country_cities = '';
        $country_towns = '';
        $sub_cats_html = '';
        $sub_sub_cats_html = '';
        $sub_sub_sub_cats_html = '';
        $tags_array = array();

        /* choose form type default/category based form */
        $input_ad_post_form_type = ( $carspot_theme['carspot_form_type'] == "yes" ) ? 1 : 0;
        $customDynamicFields = '';

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

        $price = !empty(get_post_meta($pid, '_carspot_ad_price', true)) ? (get_post_meta($pid, '_carspot_ad_price', true)) : '';
        $ad_price_type = !empty(get_post_meta($pid, '_carspot_ad_price_type', true)) ? (get_post_meta($pid, '_carspot_ad_price_type', true)) : '';
        $avg_city = !empty(get_post_meta($pid, '_carspot_ad_avg_city', true)) ? (get_post_meta($pid, '_carspot_ad_avg_city', true)) : '';
        $avg_hwy = !empty(get_post_meta($pid, '_carspot_ad_avg_hwy', true)) ? (get_post_meta($pid, '_carspot_ad_avg_hwy', true)) : '';
        $ad_mileage = !empty(get_post_meta($pid, '_carspot_ad_mileage', true)) ? (get_post_meta($pid, '_carspot_ad_mileage', true)) : '';
        $ad_yvideo = !empty(get_post_meta($pid, '_carspot_ad_yvideo', true)) ? (get_post_meta($pid, '_carspot_ad_yvideo', true)) : '';
        $tags_array = !empty(wp_get_object_terms($pid, 'ad_tags', array('fields' => 'names'))) ? (wp_get_object_terms($pid, 'ad_tags', array('fields' => 'names'))) : '';
        $tags = !empty($tags_array) ? ( implode(',', $tags_array)) : '';
        $sb_user_name = !empty(get_post_meta($pid, '_carspot_poster_name', true)) ? (get_post_meta($pid, '_carspot_poster_name', true)) : '';
        $sb_contact_number = !empty(get_post_meta($pid, '_carspot_poster_contact', true)) ? (get_post_meta($pid, '_carspot_poster_contact', true)) : '';
        $ad_country = !empty(get_post_meta($pid, '_carspot_ad_country', true)) ? (get_post_meta($pid, '_carspot_ad_country', true)) : '';
        $ad_country_states = !empty(get_post_meta($pid, '_carspot_ad_country_states', true)) ? (get_post_meta($pid, '_carspot_ad_country_states', true)) : '';
        $ad_country_cities = !empty(get_post_meta($pid, '_carspot_ad_country_cities', true)) ? (get_post_meta($pid, '_carspot_ad_country_cities', true)) : '';
        $ad_country_towns = !empty(get_post_meta($pid, '_carspot_ad_country_towns', true)) ? (get_post_meta($pid, '_carspot_ad_country_towns', true)) : '';

        $ad_mapLocation = !empty(get_post_meta($pid, '_carspot_ad_map_location', true)) ? (get_post_meta($pid, '_carspot_ad_map_location', true)) : '';
        $ad_map_lat = !empty(get_post_meta($pid, '_carspot_ad_map_lat', true)) ? (get_post_meta($pid, '_carspot_ad_map_lat', true)) : '';
        $ad_map_long = !empty(get_post_meta($pid, '_carspot_ad_map_long', true)) ? (get_post_meta($pid, '_carspot_ad_map_long', true)) : '';

        /* Make Select cats selected on update ad */
        $cats = (carspot_get_ad_cats($pid, 'ID'));
        $level = count($cats);
        $ad_cats = (carspot_get_cats('ad_cats', 0));
        foreach ($ad_cats as $ad_cat) {

            $selected = '';
            if ($level > 0 && $ad_cat->term_id == $cats[0]['id']) {
                $selected = 'selected="selected"';
            }
            $cats_html .= '<option value="' . $ad_cat->term_id . '" ' . $selected . '>' . $ad_cat->name . '</option>';
        }
        if ($level >= 2) {
            $ad_sub_cats = carspot_get_cats('ad_cats', $cats[0]['id']);

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

            foreach ($ad_sub_sub_sub_cats as $ad_cat) {
                $selected = '';
                if ($level > 0 && $ad_cat->term_id == $cats[3]['id']) {
                    $selected = 'selected="selected"';
                }
                $sub_sub_sub_cats_html .= '<option value="' . $ad_cat->term_id . '" ' . $selected . '>' . $ad_cat->name . '</option>';
            }
        }
        /* ============Select Make End============ */
        /* Condition New/Used */
        $ad_condition_html = '';
        $ad_condition = get_terms('ad_condition', array('hide_empty' => 0,));
        $ad_condition_db = get_post_meta($pid, '_carspot_ad_condition', true);
        foreach ($ad_condition as $ad_condtn) {
            $selected = '';
            if (isset($ad_condtn) && $ad_condtn->name == $ad_condition_db) {
                $selected = 'selected="selected"';
            }
            $ad_condition_html .= '<option value="' . $ad_condtn->term_id . "|" . $ad_condtn->name . '" ' . $selected . '>' . $ad_condtn->name . '</option>';
        }

        /* ============New/Used End============ */
        /* Type Buy/Sell */
        $ad_type_html = '';
        $ad_types = get_terms('ad_type', array('hide_empty' => 0,));
        $ad_type_db = get_post_meta($pid, '_carspot_ad_type', true);
        foreach ($ad_types as $ad_type) {
            $selected = '';
            if (isset($ad_type) && $ad_type->name == $ad_type_db) {
                $selected = 'selected="selected"';
            }
            $ad_type_html .= '<option value="' . $ad_type->term_id . "|" . $ad_type->name . '" ' . $selected . '>' . $ad_type->name . '</option>';
        }


        /* ============Buy/Sell End============ */
        /* Warenty Yes/No */
        $ad_warenty_html = '';
        $ad_warranty = get_terms('ad_warranty', array('hide_empty' => 0,));
        $ad_warranty_db = get_post_meta($pid, '_carspot_ad_warranty', true);
        foreach ($ad_warranty as $ad_warnty) {
            $selected = '';
            if (isset($ad_warnty) && $ad_warnty->name == $ad_warranty_db) {
                $selected = 'selected="selected"';
            }
            $ad_warenty_html .= '<option value="' . $ad_warnty->term_id . "|" . $ad_warnty->name . '" ' . $selected . '>' . $ad_warnty->name . '</option>';
        }


        /* ============Warranty Yes/No End============ */
        /* Years */
        $ad_year_html = '';
        $ad_years = get_terms('ad_years', array('hide_empty' => 0,));
        $ad_years_db = get_post_meta($pid, '_carspot_ad_years', true);                          // print_r($ad_years); echo "======"; exit();
        foreach ($ad_years as $ad_year) {
            $selected = '';
            if (isset($ad_year) && $ad_year->name == $ad_years_db) {
                $selected = 'selected="selected"';
            }
            $ad_year_html .= '<option value="' . $ad_year->term_id . "|" . $ad_year->name . '" ' . $selected . '>' . $ad_year->name . '</option>';
        }
        /* ============Years End============ */
        /* Body Type */
        $ad_body_types_html = '';
        $ad_body_types = get_terms('ad_body_types', array('hide_empty' => 0,));
        $ad_body_types_db = get_post_meta($pid, '_carspot_ad_body_types', true);
        foreach ($ad_body_types as $ad_body_type) {
            $selected = '';
            if (isset($ad_body_type) && $ad_body_type->name == $ad_body_types_db) {
                $selected = 'selected="selected"';
            }
            $ad_body_types_html .= '<option value="' . $ad_body_type->term_id . "|" . $ad_body_type->name . '" ' . $selected . '>' . $ad_body_type->name . '</option>';
        }
        /* ============Body Type End============ */
        /* transmissions */
        $ad_transmissions_html = '';
        $ad_transmissions = get_terms('ad_transmissions', array('hide_empty' => 0,));
        $ad_transmissions_db = get_post_meta($pid, '_carspot_ad_transmissions', true);
        foreach ($ad_transmissions as $ad_transmission) {
            $selected = '';
            if (isset($ad_transmission) && $ad_transmission->name == $ad_transmissions_db) {
                $selected = 'selected="selected"';
            }
            $ad_transmissions_html .= '<option value="' . $ad_transmission->term_id . "|" . $ad_transmission->name . '" ' . $selected . '>' . $ad_transmission->name . '</option>';
        }
        /* ============transmissions End============ */
        /* Engine Size */
        $ad_engine_capacities_html = '';
        $ad_engine_capacities = get_terms('ad_engine_capacities', array('hide_empty' => 0,));
        $ad_engine_capacities_db = get_post_meta($pid, '_carspot_ad_engine_capacities', true);
        foreach ($ad_engine_capacities as $ad_engine_capacitie) {
            $selected = '';
            if (isset($ad_engine_capacitie) && $ad_engine_capacitie->name == $ad_engine_capacities_db) {
                $selected = 'selected="selected"';
            }
            $ad_engine_capacities_html .= '<option value="' . $ad_engine_capacitie->term_id . "|" . $ad_engine_capacitie->name . '" ' . $selected . '>' . $ad_engine_capacitie->name . '</option>';
        }
        /* ============Engine Size End============ */
        /* Engine Type */
        $ad_engine_types_html = '';
        $ad_engine_types = get_terms('ad_engine_types', array('hide_empty' => 0,));
        $ad_engine_types_db = get_post_meta($pid, '_carspot_ad_engine_types', true);
        foreach ($ad_engine_types as $ad_engine_type) {
            $selected = '';
            if (isset($ad_engine_type) && $ad_engine_type->name == $ad_engine_types_db) {
                $selected = 'selected="selected"';
            }
            $ad_engine_types_html .= '<option value="' . $ad_engine_type->term_id . "|" . $ad_engine_type->name . '" ' . $selected . '>' . $ad_engine_type->name . '</option>';
        }
        /* ============Engine Type End============ */
        /* Assembly */
        $ad_assembles_html = '';
        $ad_assembles = get_terms('ad_assembles', array('hide_empty' => 0,));
        $ad_assembles_db = get_post_meta($pid, '_carspot_ad_assembles', true);
        foreach ($ad_assembles as $ad_assemble) {
            $selected = '';
            if (isset($ad_assemble) && $ad_assemble->name == $ad_assembles_db) {
                $selected = 'selected="selected"';
            }
            $ad_assembles_html .= '<option value="' . $ad_assemble->term_id . "|" . $ad_assemble->name . '" ' . $selected . '>' . $ad_assemble->name . '</option>';
        }
        /* ============Assembly End============ */
        /* Color */
        $ad_colors_html = '';
        $ad_colors = get_terms('ad_colors', array('hide_empty' => 0,));
        $ad_colors_db = get_post_meta($pid, '_carspot_ad_colors', true);
        foreach ($ad_colors as $ad_color) {
            $selected = '';
            if (isset($ad_color) && $ad_color->name == $ad_colors_db) {
                $selected = 'selected="selected"';
            }
            $ad_colors_html .= '<option value="' . $ad_color->term_id . "|" . $ad_color->name . '" ' . $selected . '>' . $ad_color->name . '</option>';
        }
        /* ============Color End============ */
        /* Insurance */
        $ad_insurance_html = '';
        $ad_insurance = get_terms('ad_insurance', array('hide_empty' => 0,));
        $ad_insurance_db = get_post_meta($pid, '_carspot_ad_insurance', true);
        foreach ($ad_insurance as $ad_insur) {
            $selected = '';
            if (isset($ad_insur) && $ad_insur->name == $ad_insurance_db) {
                $selected = 'selected="selected"';
            }
            $ad_insurance_html .= '<option value="' . $ad_insur->term_id . "|" . $ad_insur->name . '" ' . $selected . '>' . $ad_insur->name . '</option>';
        }
        /* ============Insurance End============ */
        /* Features */
        $slug = "ad_features";
        $required = '';
        $adfeatures = '';
        $adfeatures_HTML = '';
        $frs = array();
        $adfeatures_HTML .= '<div class="skin-minimal"><ul class="list">';
        $ad_features = carspot_get_cats('ad_features', 0);
        $count = 1;
        $adfeatures = get_post_meta($pid, '_carspot_' . $slug, true);
        if ($adfeatures != "") {
            $frs = explode('|', $adfeatures);
        }
        foreach ($ad_features as $feature) {
            $selected = (in_array($feature->name, $frs)) ? 'checked="checked"' : '';
            $adfeatures_HTML .= '<li class="col-md-4 col-sm-6 col-xs-12 no-padding">'
                    . '<input tabindex="7" type="checkbox" id="minimal-radio-' . esc_attr($count) . '" name="ad_features[]" value="' . $feature->name . '"' . $selected . ' ' . $required . '><label  for="minimal-radio-' . esc_attr($count) . '">' . $feature->name . '</label></li>';
            $count++;
        }
        $adfeatures_HTML .= '</ul></div>';
        /* ============Features End============ */

        /* Price Type */
        $priceTypeHTML = '';
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

                $priceTypeHTML .= '<select class="form-control select-2" id="ad_price_type" name="ad_price_type">
					<option value="">' . esc_html__('Select price type', 'redux-framework') . '</option>
					<option value="Fixed" ' . $price_fixed . '>' . esc_html__('Fixed', 'redux-framework') . '</option>
					<option value="Negotiable" ' . $price_negotiable . '>' . esc_html__('Negotiable', 'redux-framework') . '</option>
					<option value="on_call" ' . $price_on_call . '>' . esc_html__('Price on call', 'redux-framework') . '</option>
					<option value="free" ' . $free . '>' . esc_html__('Free', 'redux-framework') . '</option>
					<option value="no_price" ' . $no_price . '>' . esc_html__('No price', 'redux-framework') . '</option>
			</select>';
            }
        }

        /* =======Countries===== */
        $countries = carspot_get_ad_country($pid, 'ID');
        $levelz = count($countries);
        /* Make cats selected on update ad */
        $ad_countries = carspot_get_cats('ad_country', 0);

        foreach ($ad_countries as $ad_country) {
            $selected = '';
            if ($levelz > 0 && $ad_country->term_id == $countries[0]['id']) {
                $selected = 'selected="selected"';
            }
            $country_html .= '<option value="' . $ad_country->term_id . '" ' . $selected . '>' . $ad_country->name . '</option>';
        }
        /* state */
        if ($levelz >= 2) {
            $ad_states = carspot_get_cats('ad_country', $countries[0]['id']);
            foreach ($ad_states as $ad_state) {
                $selected = '';
                if ($levelz > 0 && $ad_state->term_id == $countries[1]['id']) {
                    $selected = 'selected="selected"';
                }
                $country_states .= '<option value="' . $ad_state->term_id . '" ' . $selected . '>' . $ad_state->name . '</option>';
            }
        }
        /* City */
        if ($levelz >= 3) {
            $ad_country_cities = carspot_get_cats('ad_country', $countries[1]['id']);

            foreach ($ad_country_cities as $ad_city) {
                $selected = '';
                if ($levelz > 0 && $ad_city->term_id == $countries[2]['id']) {
                    $selected = 'selected="selected"';
                }
                $country_cities .= '<option value="' . $ad_city->term_id . '" ' . $selected . '>' . $ad_city->name . '</option>';
            }
        }
        /* Town */
        if ($levelz >= 4) {
            $ad_country_town = carspot_get_cats('ad_country', $countries[2]['id']);

            foreach ($ad_country_town as $ad_town) {
                $selected = '';
                if ($levelz > 0 && $ad_town->term_id == $countries[3]['id']) {
                    $selected = 'selected="selected"';
                }
                $country_towns .= '<option value="' . $ad_town->term_id . '" ' . $selected . '>' . $ad_town->name . '</option>';
            }
        }

        /* Mapp settings */
        $loc_lvl_1 = esc_html__('Select Your Country', 'redux-framework');
        $loc_lvl_2 = esc_html__('Select Your State', 'redux-framework');
        $loc_lvl_3 = esc_html__('Select Your City', 'redux-framework');
        $loc_lvl_4 = esc_html__('Select Your Town', 'redux-framework');
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
        carspot_load_search_countries(1);
        $mapType = carspot_mapType();
        if ($mapType == 'google_map') {
            wp_enqueue_script('google-map-callback', '//maps.googleapis.com/maps/api/js?key=' . $carspot_theme['gmap_api_key'] . '&libraries=places&callback=' . 'carspot_location', false, false, true);
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
			  <div id="dvMap"></div>
			  <em><small>' . esc_html__('Drag pin for your pin-point location.', 'redux-framework') . '</small></em>
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
        $lat_long_html = $for_g_map . '<div class="row">
			  <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Latitude', 'redux-framework') . '</label>
				 <input class="form-control" type="text" name="ad_map_lat" id="ad_map_lat" value="' . $ad_map_lat . '">
			  </div>
			  <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_html__('Longitude', 'redux-framework') . '</label>
				 <input class="form-control" name="ad_map_long" id="ad_map_long" value="' . $ad_map_long . '" type="text">
			  </div>
		   </div>';
        /* For No Map  */
        $map_html = '';
        if ($mapType != 'no_map') {


            $map_html = '<div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				  <label class="control-label">' . esc_html__('Address', 'redux-framework') . '</label>
				 <input class ="form-control" name="sb_user_address" id="sb_user_address" value="' . $ad_mapLocation . '" />
			  </div>
		   </div>' . $lat_long_html . $lat_lon_script . '';
        }
        $custom_location = '';
        if (isset($carspot_theme['enable_custom_locationz']) && $carspot_theme['enable_custom_locationz'] == true) {
            global $carspot_theme;
            $custom_location = ' <div class="row">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
				 <label class="control-label">' . esc_attr($loc_lvl_1) . '</label>
				 <select class="country form-control select-2" id="ad_country" name="ad_country" data-parsley-required="true" data-parsley-error-message="' . esc_html__('This field is required.', 'redux-framework') . '">
					<option value="">Select Option</option>
					' . $country_html . '
				 </select>
				 <input type="hidden" name="ad_country_id" id="ad_country_id" value="" />
			  </div>
		   </div>
		   <div class="row" id="ad_country_sub_div">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" >
			  <label class="control-label">' . esc_attr($loc_lvl_2) . '</label>
				<select class="category form-control select-2" id="ad_country_states" name="ad_country_states">
					' . $country_states . '
				</select>
			  </div>
			</div>
			 <div class="row" id="ad_country_sub_sub_div" >
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
			  <label class="control-label">' . esc_attr($loc_lvl_3) . '</label>
				<select class="category form-control select-2" id="ad_country_cities" name="ad_country_cities">
					' . $country_cities . '
				</select>
			  </div>
			</div>
			 <div class="row" id="ad_country_sub_sub_sub_div">
			  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
			  <label class="control-label">' . esc_attr($loc_lvl_4) . '</label>
				<select class="category form-control select-2" id="ad_country_towns" name="ad_country_towns">
					' . $country_towns . '
				</select>
			  </div>
			</div>';
        }
        /* End Mapp Settings */
        ?>

        <table class="form-table forms-table" id="form-table">
            <tr>
                <th><label class="claimer_contact_label"><?php echo esc_html($heading1); ?><span><?php echo esc_html__(' Select suitable category for your ad', 'redux-framework'); ?></span></label></th>
                <td>
                    <select class="category form-contro select-2 select2-hidden-accessible" id="ad_cat" name="ad_cat" data-parsley-required="true" data-parsley-error-message="<?php echo esc_html__('This field is required.', 'redux-framework'); ?>">
                        <option value=""><?php echo esc_html__("Select Option", "redux-framework"); ?></option>
                        <?php echo ($cats_html); ?>
                    </select>
                </td>
            </tr>
            <tr id="ad_cat_sub_div">
                <th><label class="claimer_contact_label"><?php echo esc_html($heading2); ?></label></th>
                <td>
                    <select class="category form-control select-2" id="ad_cat_sub" name="ad_cat_sub">
                        <?php echo ($sub_cats_html); ?>
                    </select>
                </td>
            </tr>
            <tr id="ad_cat_sub_sub_div">
                <th><label class="claimer_contact_label"><?php echo esc_html($heading3); ?></label></th>
                <td>
                    <select class="category form-control select-2" id="ad_cat_sub_sub" name="ad_cat_sub_sub">
                        <?php echo ($sub_sub_cats_html); ?>
                    </select>
                </td>
            </tr>
            <tr id="ad_cat_sub_sub_sub_div">
                <th><label class="claimer_contact_label"><?php echo esc_html($heading4); ?></label></th>
                <td>
                    <select class="category form-control select-2" id="ad_cat_sub_sub_sub" name="ad_cat_sub_sub_sub">
                        <?php echo ($sub_sub_sub_cats_html); ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label class="claimer_contact_label"><?php echo __('Price Type', 'redux-framework'); ?></label></th>
                <td>
                    <?php echo ($priceTypeHTML); ?>
                </td>
            </tr>
            <?php
				$hide_price_filed_on_update = '';
				if($ad_price_type == 'on_call' || $ad_price_type == 'free' || $ad_price_type == 'no_price')
				{
					$hide_price_filed_on_update = "style='display:none;'";
				}
					?>
                <tr id="ad_pricees" class="ad_pricees" <?php echo ($hide_price_filed_on_update); ?> >
                    <th><label class="claimer_contact_label"><?php echo __('Price', 'redux-framework'); ?><span><?php echo __(' $ only', 'redux-framework'); ?></label></th>
                    <td>
                        <input name="ad_price" id="ad_price" type="number" value="<?php echo esc_html($price); ?>">
                    </td>
                </tr>
            <?php
            /* if we default form type then  */
            if ($input_ad_post_form_type === 0) {
                ?>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo __('Average in Highway', 'redux-framework'); ?></label></th>
                    <td>
                        <input name="ad_avg_hwy" id="ad_avg_hwy" type="text" value="<?php echo esc_html($avg_hwy); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo __('Average in City', 'redux-framework'); ?></label></th>
                    <td>
                        <input name="ad_avg_city" id="ad_avg_city" type="text" value="<?php echo esc_html($avg_city); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo __('Mileage', 'redux-framework'); ?></label></th>
                    <td>
                        <input name="ad_mileage" id="ad_mileage" type="text" value="<?php echo esc_html($ad_mileage); ?>">
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Condition', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2 cs-ads-form"  id="_carspot_ad_condition"  name="_carspot_ad_condition" >
                            <?php echo ($ad_condition_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Type', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="_carspot_ad_type" name="_carspot_ad_type">
                            <?php echo ($ad_type_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Warranty', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_warranty" name="_carspot_ad_warranty">
                            <?php echo ($ad_warenty_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Year', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_years" name="_carspot_ad_years">
                            <?php echo ($ad_year_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Body Type', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_body_types" name="_carspot_ad_body_types">
                            <?php echo ($ad_body_types_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Transmission', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_transmissions" name="_carspot_ad_transmissions">
                            <?php echo ($ad_transmissions_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Engine Size', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_engine_capacities" name="_carspot_ad_engine_capacities">
                            <?php echo ($ad_engine_capacities_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Engine Type', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_engine_types" name="_carspot_ad_engine_types">
                            <?php echo ($ad_engine_types_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Assembly', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_assembles" name="_carspot_ad_assembles">
                            <?php echo ($ad_assembles_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Color', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_colors" name="_carspot_ad_colors">
                            <?php echo ($ad_colors_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Insurance', 'redux-framework'); ?></label></th>
                    <td>
                        <select class="category form-control select-2" id="ad_insurance" name="_carspot_ad_insurance">
                            <?php echo ($ad_insurance_html); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Features', 'redux-framework'); ?></label></th>
                    <td>
                        <div>
                            <?php echo ($adfeatures_HTML); ?>
                        </div>
                    </td>
                </tr>
            </table>
            <?php
        } else {
            ?>
            <table class="form-table forms-table" id="form-table">
                <tr>

                    <td id="dynamic-fields_admin" class="dynamic-fields_admin">
                        <?php
                        $html = $mainCatId = '';
                        $cats = carspot_get_ad_cats($pid);
                        foreach ($cats as $cat) {
                            $mainCatId = $cat['id'];
                        }
                        $termTemplate = carspot_dynamic_templateID($mainCatId);
                        $html .= carspot_get_term_form($termTemplate, $pid);
                        $html .= carspot_get_dynamic_form($termTemplate, $pid);
                        echo ($html);
                    }
                    ?>
                </td>
            </tr>
        </table>
        <table class="form-table forms-table style-admin-fields" id="form-table">
            <tr>
                <th><label class="claimer_contact_label"><?php echo esc_html__('Ads Gallery', 'redux-framework'); ?></label></th>
                <td>
                    <!--gallery images-->
                    <?php
                    global $carspot_theme;
                    //$img_upload_limit = '';
                    // $img_upload_limit = $carspot_theme['sb_upload_limit'];
                    $meta = $meta_html = '';
                    if (get_post_meta($pid, 'carspot_photo_arrangement_', true) != "") {
                        $meta = get_post_meta($pid, 'carspot_photo_arrangement_', true);
                        //set limit for image upload only selected range.
                        //  $meta = array_slice($meta, 0, $img_upload_limit, true);
                        $media = carspot_fetch_listing_gallery($pid);
                        if (count((array) $media) > 0) {
                            $meta_html .= '<ul class="carspot_gallery">';
                            foreach ($media as $m) {
                                $mid = '';
                                if (isset($m->ID)) {
                                    $mid = $m->ID;
                                } else {
                                    $mid = $m;
                                }
                                $thumb_imgs = wp_get_attachment_image_src($mid, 'carspot_recent-posts');
                                $meta_html .= '<li><div class="carspot_gallery_container"><span class="carspot_delete_icon">
				 <img id="' . esc_attr($mid) . '" src="' . esc_url($thumb_imgs[0]) . '" alt="' . esc_html__('image not found', 'redux-framework') . '"style="max-width:100%;" /></span></div></li>';
                            }
                            $meta_html .= '</ul>';
                        }
                    }
                    ?>

                    <input id="carspot_photo_arrangement_" type="hidden" name="carspot_photo_arrangement_" value="<?php echo esc_attr($meta); ?>" />
                    <span id="carspot_admin_gall_render"><?php echo ($meta_html); ?></span>
                    <input id="carspot_ad_gallery_button" class="button button-primary button-large" type="button" value="<?php echo esc_attr__('Ads Gallery Images', 'redux-framework'); ?>" />
                </td>
                </td>
            </tr>
            <tr>
                <th><label class="claimer_contact_label"><?php echo __('Youtube Video Link', 'redux-framework'); ?></label></th>
                <td>
                    <input name="ad_yvideo" id="ad_yvideo" type="text" value="<?php echo esc_html($ad_yvideo); ?>">
                </td>
            </tr>
            <tr>
                <th><label class="claimer_contact_label"><?php echo __('Tags', 'redux-framework'); ?><small><?php echo __('Comma (,) sepataed', 'redux-framework'); ?></small></label></th>
                <td>
                    <div>
                        <input  name="tags" id="tags" value="<?php echo esc_attr($tags); ?>" >
                    </div>
                </td>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('#tags').tagsInput({
                        'width': '100%',
                        'height': '5px;',
                        'defaultText': '',
                    });
                });
            </script>
        </tr>
                        <?php
					if( isset( $carspot_theme['sb_enable_comments_offer'] ) && $carspot_theme['sb_enable_comments_offer'] != '' )
					{
						$bidding_status = get_post_meta($pid, '_carspot_ad_bidding', true);
				?>
                <tr>
                    <th><label class="claimer_contact_label"><?php echo esc_html__('Bidding', 'redux-framework'); ?></label></th>
                    <td>
                    <?php
					
					?>
                        <select class="category form-control select-2" id="bidding_on" name="_carspot_ad_bidding">
                            <option value="1" <?php if (isset($bidding_status) && $bidding_status == '1') { echo ($selected = 'selected="selected"'); } ?>><?php echo esc_html__('Yes', 'redux-framework'); ?></option>
                            <option value="0" <?php if (isset($bidding_status) && $bidding_status == '0') { echo ($selected = 'selected="selected"'); } ?>><?php echo esc_html__('No', 'redux-framework'); ?></option>
                        </select>
                    </td>
                </tr>
                <?php
					}
				?>

        <tr>
            <th><label class="claimer_contact_label"><?php echo __('Country', 'redux-framework'); ?></label></th>
            <td>
                <select class="country form-control select-2" id="ad_country" name="ad_country" data-parsley-required="true" data-parsley-error-message="<?php echo esc_html__('This field is required.', 'redux-framework') ?>">
                    <option value=""><?php echo __('Select Option', 'redux-framework'); ?></option>
                    <?php echo ($country_html); ?>
                </select>
            </td>
        </tr>
        <tr id="ad_country_sub_div">
            <th><label class="claimer_contact_label"><?php echo __('State', 'redux-framework'); ?></label></th>
            <td>
                <select class="category form-control select-2" id="ad_country_states" name="ad_country_states">
                    <option value=""><?php __('Select Option', 'redux-framework') ?></option>
                    <?php echo ($country_states); ?>
                </select>
            </td>
        </tr>
        <tr id="ad_country_sub_sub_div">
            <th><label class="claimer_contact_label"><?php echo __('City', 'redux-framework'); ?></label></th>
            <td>
                <select class="country form-control select-2" id="ad_country_cities" name="ad_country_cities" data-parsley-required="true" data-parsley-error-message="<?php echo esc_html__('This field is required.', 'redux-framework') ?>">
                    <option value=""><?php __('Select Option', 'redux-framework') ?></option>
                    <?php echo ($country_cities); ?>
                </select>
            </td>
        </tr>
        <tr id="ad_country_sub_sub_sub_div">
            <th><label class="claimer_contact_label"><?php echo __('Town', 'redux-framework'); ?></label></th>
            <td>
                <select class="country form-control select-2" id="ad_country_towns" name="ad_country_towns" data-parsley-required="true" data-parsley-error-message="<?php echo esc_html__('This field is required.', 'redux-framework') ?>">
                    <option value=""><?php __('Select Option', 'redux-framework') ?></option>
                    <?php echo ($country_towns); ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label class="claimer_contact_label"><?php echo __('Your Name', 'redux-framework'); ?></label></th>
            <td>
                <input name="sb_user_name" id="sb_user_name" type="text" value="<?php echo esc_html($sb_user_name); ?>">
            </td>
        </tr>
        <tr>
            <th><label class="claimer_contact_label"><?php echo __('Mobile Number', 'redux-framework'); ?></label></th>
            <td>
                <input name="sb_contact_number" id="sb_contact_number" type="text" value="<?php echo esc_html($sb_contact_number); ?>">
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <?php echo ($map_html); ?>
            </td>
        </tr>
        </table>
        <!-- used for ajax call when images are upload/browsed -->
        <input type="hidden" id="is_update" name="is_update" value="<?php echo esc_attr($is_update); ?>" />
        <input type="hidden" id="is_level" name="is_level" value="<?php echo esc_attr($level); ?>" />
        <input type="hidden" id="is_price_type" name="is_price_type" value="<?php echo esc_attr($ad_price_type); ?>" />
        <input type="hidden" id="country_level" name="country_level" value="<?php echo esc_attr($levelz); ?>" />
        <input type="hidden" id="hiden_pric" name="hiden_pric" value="<?php echo esc_html($price); ?>" />
        <input type="hidden" id="input_ad_post_form_type" value="<?php echo ($input_ad_post_form_type); ?>" />
        <?php
    }
    /* =======================
     * save mtabox data.
      ======================= */
    function save_adds_metabox($post_id) {
        // Bail if we're doing an auto save
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
// if our nonce isn't there, or we can't verify it, bail
        if (!isset($_POST['adds_post_meta_inform']) || !wp_verify_nonce($_POST['adds_post_meta_inform'], 'my_adds_post_meta_inform'))
            return;
        // if our current user can't edit this post, bail
        if (!current_user_can('edit_post'))
            return;

        // Make sure your data is set before trying to save it
        /* save Make, Model, Version, */
        /* we save record in two form
         * update post meta &
         * set post term
         *  */
        $categoryss = array();
        if ($_POST['ad_cat'] != "") {
            $categoryss[] = $_POST['ad_cat'];
        }
        if ($_POST['ad_cat_sub'] != "") {
            $categoryss[] = $_POST['ad_cat_sub'];
        }
        if ($_POST['ad_cat_sub_sub'] != "") {
            $categoryss[] = $_POST['ad_cat_sub_sub'];
        }
        if ($_POST['ad_cat_sub_sub_sub'] != "") {
            $categoryss[] = $_POST['ad_cat_sub_sub_sub'];
        }
        wp_set_post_terms($post_id, $categoryss, 'ad_cats');

        if (isset($_POST['ad_cat']) && !empty($_POST['ad_cat'])) {
            update_post_meta($post_id, 'ad_cat', ($_POST['ad_cat']));
        }
        if (isset($_POST['ad_cat_sub']) && !empty($_POST['ad_cat_sub'])) {
            update_post_meta($post_id, 'ad_cat_sub', ($_POST['ad_cat_sub']));
        }
        if (isset($_POST['ad_cat_sub_sub']) && !empty($_POST['ad_cat_sub_sub'])) {
            update_post_meta($post_id, 'ad_cat_sub_sub', ($_POST['ad_cat_sub_sub']));
        }
        if (isset($_POST['ad_cat_sub_sub_sub']) && !empty($_POST['ad_cat_sub_sub_sub'])) {
            update_post_meta($post_id, 'ad_cat_sub_sub_sub', ($_POST['ad_cat_sub_sub_sub']));
        }
        /* =========== */



        if (isset($_POST['ad_price']) && !empty($_POST['ad_price'])) {
            update_post_meta($post_id, '_carspot_ad_price', ($_POST['ad_price']));
        }
        if (isset($_POST['ad_price_type']) && !empty($_POST['ad_price_type'])) {
            update_post_meta($post_id, '_carspot_ad_price_type', ($_POST['ad_price_type']));
        }
        if (isset($_POST['ad_avg_hwy']) && !empty($_POST['ad_avg_hwy'])) {
            update_post_meta($post_id, '_carspot_ad_avg_hwy', ($_POST['ad_avg_hwy']));
        }
        if (isset($_POST['ad_avg_city']) && !empty($_POST['ad_avg_city'])) {
            update_post_meta($post_id, '_carspot_ad_avg_city', ($_POST['ad_avg_city']));
        }
        if (isset($_POST['ad_mileage']) && !empty($_POST['ad_mileage'])) {
            update_post_meta($post_id, '_carspot_ad_mileage', ($_POST['ad_mileage']));
        }

        /* saveing condition use/new */
        if (isset($_POST['_carspot_ad_condition']) && !empty($_POST['_carspot_ad_condition'])) {
            $condition_arr = explode('|', $_POST['_carspot_ad_condition']);
            $condition_arr = $condition_arr[1];
            update_post_meta($post_id, '_carspot_ad_condition', ($condition_arr));
        }
        /* Add type */
        if (isset($_POST['_carspot_ad_type']) && !empty($_POST['_carspot_ad_type'])) {
            $carspot_ad_type_arr = explode('|', $_POST['_carspot_ad_type']);
            $carspot_ad_type_arr = $carspot_ad_type_arr[1];
            update_post_meta($post_id, '_carspot_ad_type', ($carspot_ad_type_arr));
        }
        /* Warrenty */
        if (isset($_POST['_carspot_ad_warranty']) && !empty($_POST['_carspot_ad_warranty'])) {
            $ad_warranty_arr = explode('|', $_POST['_carspot_ad_warranty']);
            $ad_warranty_arr = $ad_warranty_arr[1];
            update_post_meta($post_id, '_carspot_ad_warranty', ($ad_warranty_arr));
        }
        /* Year */
        if (isset($_POST['_carspot_ad_years']) && !empty($_POST['_carspot_ad_years'])) {
            $ad_years_arr = explode('|', $_POST['_carspot_ad_years']);
            $ad_years_arr = $ad_years_arr[1];
            update_post_meta($post_id, '_carspot_ad_years', ($ad_years_arr));
        }
        /* Body Type */
        if (isset($_POST['_carspot_ad_body_types']) && !empty($_POST['_carspot_ad_body_types'])) {
            $ad_body_types_arr = explode('|', $_POST['_carspot_ad_body_types']);
            $ad_body_types_arr = $ad_body_types_arr[1];
            update_post_meta($post_id, '_carspot_ad_body_types', ($ad_body_types_arr));
        }
        /* Transmission */
        if (isset($_POST['_carspot_ad_transmissions']) && !empty($_POST['_carspot_ad_transmissions'])) {
            $ad_transmissions_arr = explode('|', $_POST['_carspot_ad_transmissions']);
            $ad_transmissions_arr = $ad_transmissions_arr[1];
            update_post_meta($post_id, '_carspot_ad_transmissions', ($ad_transmissions_arr));
        }
        /* Engine Size */
        if (isset($_POST['_carspot_ad_engine_capacities']) && !empty($_POST['_carspot_ad_engine_capacities'])) {
            $ad_engine_capacities_arr = explode('|', $_POST['_carspot_ad_engine_capacities']);
            $ad_engine_capacities_arr = $ad_engine_capacities_arr[1];
            update_post_meta($post_id, '_carspot_ad_engine_capacities', ($ad_engine_capacities_arr));
        }
        /* Engine Type */
        if (isset($_POST['_carspot_ad_engine_types']) && !empty($_POST['_carspot_ad_engine_types'])) {
            $ad_engine_types_arr = explode('|', $_POST['_carspot_ad_engine_types']);
            $ad_engine_types_arr = $ad_engine_types_arr[1];
            update_post_meta($post_id, '_carspot_ad_engine_types', ($ad_engine_types_arr));
        }
        /* Assembly */
        if (isset($_POST['_carspot_ad_assembles']) && !empty($_POST['_carspot_ad_assembles'])) {
            $ad_assembles_arr = explode('|', $_POST['_carspot_ad_assembles']);
            $ad_assembles_arr = $ad_assembles_arr[1];
            update_post_meta($post_id, '_carspot_ad_assembles', ($ad_assembles_arr));
        }
        /* Color */
        if (isset($_POST['_carspot_ad_colors']) && !empty($_POST['_carspot_ad_colors'])) {
            $ad_colors_arr = explode('|', $_POST['_carspot_ad_colors']);
            $ad_colors_arr = $ad_colors_arr[1];
            update_post_meta($post_id, '_carspot_ad_colors', ($ad_colors_arr));
        }
        /* Insurance */
        if (isset($_POST['_carspot_ad_insurance']) && !empty($_POST['_carspot_ad_insurance'])) {
            $ad_insurance_arr = explode('|', $_POST['_carspot_ad_insurance']);
            $ad_insurance_arr = $ad_insurance_arr[1];
            update_post_meta($post_id, '_carspot_ad_insurance', ($ad_insurance_arr));
        }
        /* Features */
        if (isset($_POST['ad_features']) && !empty($_POST['ad_features'])) {
            $features = $_POST['ad_features'];
            if (count((array) $features) > 0) {
                $ad_features = '';
                foreach ($features as $feature) {
                    $ad_features .= $feature . "|";
                }
                $ad_features = rtrim($ad_features, '|');
            }
            update_post_meta($post_id, '_carspot_ad_features', $ad_features);
        }


        /* Add Dynamic Fields */
        if (isset($_POST['cat_template_field']) && count($_POST['cat_template_field']) > 0) {
            foreach ($_POST['cat_template_field'] as $key => $data) {
                if (is_array($data)) {
                    $dataArr = array();
                    foreach ($data as $k)
                        $dataArr[] = $k;
                    $data = stripslashes(json_encode($dataArr, JSON_UNESCAPED_UNICODE));
                }
                update_post_meta($post_id, $key, sanitize_text_field($data));
            }
        }

        /* gallery */
        if (isset($_POST['carspot_photo_arrangement_'])) {
            update_post_meta($post_id, 'carspot_photo_arrangement_', ($_POST['carspot_photo_arrangement_']));
        }

        /* Youtube video */
        if (isset($_POST['ad_yvideo']) && !empty($_POST['ad_yvideo'])) {
            update_post_meta($post_id, '_carspot_ad_yvideo', ($_POST['ad_yvideo']));
        }
        /* save tags data in terms */
        $tags = explode(',', $_POST['tags']);
        wp_set_object_terms($post_id, $tags, 'ad_tags');
        /* Country */
        /* we save data in both update post meta and set post term */
        $countries = array();
        if ($_POST['ad_country'] != "") {
            $countries[] = $_POST['ad_country'];
        }
        if ($_POST['ad_country_states'] != "") {
            $countries[] = $_POST['ad_country_states'];
        }
        if ($_POST['ad_country_cities'] != "") {
            $countries[] = $_POST['ad_country_cities'];
        }
        if ($_POST['ad_country_towns'] != "") {
            $countries[] = $_POST['ad_country_towns'];
        }
        wp_set_post_terms($post_id, $countries, 'ad_country');
        if (isset($_POST['ad_country']) && !empty($_POST['ad_country'])) {
            update_post_meta($post_id, '_carspot_ad_country', ($_POST['ad_country']));
        }
        /* country-state */
        if (isset($_POST['ad_country_states']) && !empty($_POST['ad_country_states'])) {
            update_post_meta($post_id, '_carspot_ad_country_states', ($_POST['ad_country_states']));
        }
        /* country-city */
        if (isset($_POST['ad_country_cities']) && !empty($_POST['ad_country_cities'])) {
            update_post_meta($post_id, '_carspot_ad_country_cities', ($_POST['ad_country_cities']));
        }
        /* country-town */
        if (isset($_POST['ad_country_towns']) && !empty($_POST['ad_country_towns'])) {
            update_post_meta($post_id, '_carspot_ad_country_towns', ($_POST['ad_country_towns']));
        }
        /* user name */
        if (isset($_POST['sb_user_name']) && !empty($_POST['sb_user_name'])) {
            update_post_meta($post_id, '_carspot_poster_name', ($_POST['sb_user_name']));
        }
        /* user contact number */
        if (isset($_POST['sb_contact_number']) && !empty($_POST['sb_contact_number'])) {
            update_post_meta($post_id, '_carspot_poster_contact', ($_POST['sb_contact_number']));
        }
        /* user address */
        if (isset($_POST['sb_user_address']) && !empty($_POST['sb_user_address'])) {
            update_post_meta($post_id, '_carspot_ad_map_location', ($_POST['sb_user_address']));
        }
        /* Map lititude */
        if (isset($_POST['ad_map_lat']) && !empty($_POST['ad_map_lat'])) {
            update_post_meta($post_id, '_carspot_ad_map_lat', ($_POST['ad_map_lat']));
        }
        /* Map Longitude */
        if (isset($_POST['ad_map_long']) && !empty($_POST['ad_map_long'])) {
            update_post_meta($post_id, '_carspot_ad_map_long', ($_POST['ad_map_long']));
        }
		If(get_post_status( $post_id ) == 'publish')
		{
			update_post_meta($post_id, '_carspot_ad_status_', 'active');	
		}
		if (isset($_POST['_carspot_ad_bidding']) && $_POST['_carspot_ad_bidding'] != '') {
            $ad_bidding =  $_POST['_carspot_ad_bidding'];
            update_post_meta($post_id, '_carspot_ad_bidding', $ad_bidding);
        }
    }

}

new adds_post_metabox;