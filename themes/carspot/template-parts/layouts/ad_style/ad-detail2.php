<?php
global $carspot_theme;
$pid = get_the_ID();
$post_categories = wp_get_object_terms($pid, array('ad_cats'), array('orderby' => 'term_group'));
$class = '';
$type = $carspot_theme['cat_and_location'];
if (isset($carspot_theme['ad_slider_type']) && $carspot_theme['ad_slider_type'] == 4) {
    $class = '';
} else {
    $class = 'margin-top-20';
}
?>
<div class="content-box-grid <?php echo esc_attr($class); ?>">
    <?php
    if (get_post_meta($pid, '_carspot_ad_status_', true) == 'sold') {
        ?>
        <div class="ad-closed">
            <img class="img-responsive" src="<?php echo trailingslashit(get_template_directory_uri()); ?>images/sold-out.png" alt="<?php esc_html__('sold out', 'carspot'); ?>">
        </div>
        <?php
    }
    ?>
    <?php
    if (get_post_meta($pid, '_carspot_ad_status_', true) == 'expired') {
        ?>
        <div class="ad-expired">
            <img class="img-responsive" src="<?php echo trailingslashit(get_template_directory_uri()); ?>images/expired.png" alt="<?php esc_html__('sold out', 'carspot'); ?>">
        </div>
        <?php
    }
    ?>

    <div class="short-features" id="short-desc">
        <?php $current_adpost = get_option('_carspot_current_ad_post_template'); ?>
        <!-- Heading Area -->
        <div class="heading-panel">
            <h3 class="main-title text-left">
                <?php echo esc_html__('Description', 'carspot'); ?>
            </h3>
        </div>
        <div class="short-feature-body">
            <p><?php the_content(); ?> </p>
            <?php
            if (get_post_meta($pid, '_carspot_ad_price_type', true) == "no_price" || ( get_post_meta($pid, '_carspot_ad_price', true) == "" && get_post_meta($pid, '_carspot_ad_price_type', true) != "free" && get_post_meta($pid, '_carspot_ad_price_type', true) != "on_call" )) {
                
            } else {
                ?>
                <div class="col-sm-12 col-md-12 col-xs-12 no-padding categories-exist">
                    <span> <strong> 
                            <?php echo esc_html__('Category', 'carspot'); ?>: 
                        </strong></span>
                    <?php
                    foreach ($post_categories as $c) {
                        $cat = get_term($c);
                        if ($type == 'search') {
                            $link = get_the_permalink($carspot_theme['sb_search_page']) . '?cat_id=' . $cat->term_id;
                        } else {
                            $link = get_term_link($cat->term_id);
                        }
                        ?>
                        <a href="<?php echo esc_attr($link); ?>"><?php echo esc_html($cat->name); ?> </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-sm-4 col-md-4 col-xs-12 no-padding">
                    <span><strong><?php echo esc_html__('Price', 'carspot'); ?></strong> :</span>
                    <?php echo carspot_adPrice($pid); ?> 
                </div>
            <?php } ?>
            <?php
            $a = '1';
            if (get_post_meta($pid, '_carspot_ad_type', true) != "" && $current_adpost != "yes" && $a == 2) {
                ?>
                <div class="col-sm-4 col-md-4 col-xs-12 no-padding">
                    <span><strong><?php echo esc_html__('Type', 'carspot'); ?></strong> :</span> 
                    <?php echo esc_html(get_post_meta($pid, '_carspot_ad_type', true)); ?>
                </div>
            <?php } ?>
            <div class="col-sm-4 col-md-4 col-xs-12 no-padding">
                <span><strong><?php echo esc_html__('Date', 'carspot'); ?></strong> :</span> 
                <?php echo esc_html(get_the_date()); ?>
            </div>
            <?php
            if (get_post_meta($pid, '_carspot_ad_condition', true) != "" && isset($carspot_theme['allow_tax_condition']) && $carspot_theme['allow_tax_condition'] && $current_adpost != "yes" && $a == 2) {
                ?>
                <div class="col-sm-4 col-md-4 col-xs-12 no-padding">
                    <span><strong><?php echo esc_html__('Condition', 'carspot'); ?></strong> :</span> 
                    <?php echo esc_html(get_post_meta($pid, '_carspot_ad_condition', true)); ?>
                </div>
                <?php
            }
            if (get_post_meta($pid, '_carspot_ad_warranty', true) != "" && isset($carspot_theme['allow_tax_warranty']) && $carspot_theme['allow_tax_warranty'] && $current_adpost != "yes" && $a == 2) {
                ?>
                <div class="col-sm-4 col-md-4 col-xs-12 no-padding">
                    <span><strong><?php echo esc_html__('Warranty', 'carspot'); ?></strong> :</span> 
                    <?php echo esc_html(get_post_meta($pid, '_carspot_ad_warranty', true)); ?>
                </div>
                <?php
            }
            global $wpdb;
            $rows = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = '$pid' AND meta_key LIKE '_sb_extra_%'");
            foreach ($rows as $row) {
                $caption = explode('_', $row->meta_key);
                if ($row->meta_value == "") {
                    continue;
                }
                ?>
                <div class="col-sm-4 col-md-4 col-xs-12 no-padding">
                    <span><strong><?php echo esc_html(ucfirst($caption[3])); ?></strong> :</span>
                    <?php echo esc_html($row->meta_value); ?>
                </div>
                <?php
            }
            if (function_exists('carspotCustomFieldsHTML')) {
                echo carspotCustomFieldsHTML($pid);
            }
            ?>
            <?php if (carspot_display_adLocation($pid) != "") { ?> 
                <div class="col-sm-12 col-md-12 col-xs-12 location-exit no-padding">
                    <span><strong><?php echo esc_html__("Location", 'carspot'); ?></strong> :</span>
                    <?php echo carspot_display_adLocation($pid); ?>
                </div>
            <?php } ?>

        </div>
    </div>
    <div class="short-features" id="features">
        <?php get_template_part('template-parts/layouts/ad_style/car', 'features2'); ?>
    </div>

    <div class="clearfix"></div>
</div>