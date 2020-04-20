<?php
global $carspot_theme;
$pid = get_the_ID();
$poster_id = get_post_field('post_author', $pid);
$user_pic = carspot_get_user_dp($poster_id);
$address = get_post_meta($pid, '_carspot_ad_location', true);

$ribbon_html = '';
if (get_post_meta($pid, '_carspot_is_feature', true) == '1' && get_post_meta($pid, '_carspot_ad_status_', true) == 'active') {
    $ribbion = 'featured-ribbon';
    if (is_rtl()) {
        $ribbion = 'featured-ribbon-rtl';
    }
    $ribbon_html = '<div class="' . esc_attr($ribbion) . '"><span>' . esc_html__('Featured', 'carspot') . '</span></div>';
}

$css_classes = 'no-top';
$single_style_4 = '';
if (isset($carspot_theme['sb_header']) && $carspot_theme['sb_header'] == 'transparent' || $carspot_theme['sb_header'] == 'transparent2') {

    $css_classes = '';
}
if (isset($carspot_theme['single_ad_style']) && $carspot_theme['single_ad_style'] == '2') {
    $single_style_4 = ' single-listing-4';
}
?>
<?php
if (isset($carspot_theme['single_ad_style']) && $carspot_theme['single_ad_style'] == '2') {
    ?>
    <section class="dwt-carousal-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-xs-12 col-md-3">
                    <div class="row">
    <?php get_template_part('template-parts/layouts/ad_style/gallery', '5'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
                    <?php } ?>
<?php if (isset($carspot_theme['style_ad_720_1']) && $carspot_theme['style_ad_720_1'] != "") { ?>
    <div class="car-detail">
        <div class="advertising">
            <div class="container">
                <div class="banner">
    <?php echo "" . $carspot_theme['style_ad_720_1']; ?>
                </div>
            </div>
        </div>
    </div>
                <?php } ?>

<section class="single-page-title-bar">
    <div class="container">
        <div class="row">
<?php get_template_part('template-parts/layouts/ad_style/single-page-title', 'section'); ?>
        </div>
    </div>
</section>


<section class="new-title-button-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
                <div class="single-title-buttons">
                    <ul class="list-inline new-title-page-button-section">
                        <li> <a href="#short-desc" class="scroller"> <?php echo esc_html__('Description', 'carspot'); ?></a> </li>
                        <li> <a href="#features" class="scroller"> <?php echo esc_html__('Features', 'carspot'); ?></a> </li>
<?php if (get_post_meta($pid, '_carspot_ad_yvideo', true) != "") {
    ?>
                            <li> <a href="#video-area" class="scroller"> <?php echo esc_html__('Video', 'carspot'); ?></a> </li>
<?php } ?>

                        <?php
                        if ($carspot_theme['sb_enable_comments_offer'] == 1) {
                            if (isset($carspot_theme['sb_enable_comments_offer']) && $carspot_theme['sb_enable_comments_offer'] && get_post_meta($pid, '_carspot_ad_status_', true) != 'sold' && get_post_meta($pid, '_carspot_ad_status_', true) != 'expired' && get_post_meta($pid, '_carspot_ad_price', true) != "0") {
                                if (isset($carspot_theme['sb_enable_comments_offer_user']) && $carspot_theme['sb_enable_comments_offer_user'] && get_post_meta($pid, '_carspot_ad_bidding', true) == 1) {
                                    ?>
                                    <li> <a href="#bidding-area" class="scroller"> <?php echo esc_html__('Bidding', 'carspot'); ?> </a> </li> <?php
                                } else if (isset($carspot_theme['sb_enable_comments_offer_user']) && $carspot_theme['sb_enable_comments_offer_user'] && get_post_meta($pid, '_carspot_ad_bidding', true) == 0) {
                                    
                                } else {
                                    ?>
                                    <li> <a href="#bidding-area" class="scroller"> <?php echo esc_html__('Bidding', 'carspot'); ?> </a> </li> <?php
                                }
                            }
                        }
                        ?>


                        <?php if ($carspot_theme['Related_ads_on'] == true) {
                            ?>
                            <li> <a href="#"> <?php echo esc_html__('Related Ads', 'carspot'); ?></a> </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="main-content-area clearfix">
    <section class="section-padding <?php echo carspot_returnEcho($single_style_4); ?>">
        <!-- Main Container -->
        <div class="container">
            <!-- Row -->
            <div class="row">
                        <?php
                        if (isset($carspot_theme['sb_header']) && $carspot_theme['sb_header'] != 'transparent' && $carspot_theme['sb_header'] != 'transparent2') {
                            //get_template_part( 'template-parts/layouts/ad_style/short-summary', 'title' ); 
                        }
                        ?>
                <!-- Middle Content Area -->
                <div class="col-md-12 col-lg-8 col-xs-12 col-sm-12">
                    <!-- Single Ad -->
                    <div class="singlepage-detail ">
<?php
get_template_part('template-parts/layouts/ad_style/feature', 'notification');
?>
<?php
if (get_post_meta($pid, '_carspot_ad_status_', true) != "" && get_post_meta($pid, '_carspot_ad_status_', true) != 'active') {
    ?>
                            <div role="alert" class="alert alert-info alert-outline alert-dismissible">
                                <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">&#10005;</span></button>
                                <strong><?php echo esc_html__('Info', 'carspot'); ?></strong> - 
                    <?php echo esc_html__('This ad has been', 'carspot') . " "; ?>
                    <?php echo carspot_ad_statues(get_post_meta($pid, '_carspot_ad_status_', true)); ?>.
                            </div>
                    <?php
                }
                ?>
<?php get_template_part('template-parts/layouts/ad_style/key', 'features'); ?>
                        <?php
                        /* Short Description */
                        get_template_part('template-parts/layouts/ad_style/ad', 'detail2');
                        ?>
                        <div class="content-box-grid">
                            <!-- Short Features  --> 


                            <!-- Short Features  --> 
                            <div class="clearfix"></div>
                            <?php
                            if (get_post_meta($pid, '_carspot_ad_yvideo', true) != "") {
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', get_post_meta($pid, '_carspot_ad_yvideo', true), $match);
                                if (isset($match[1]) && $match[1] != "") {
                                    $video_id = $match[1];
                                    ?>
                                    <div class="short-features" id="video-area">
                                        <!-- Ad Video --> 
                                        <!-- Heading Area -->
                                        <div class="heading-panel">
                                            <h3 class="main-title text-left">
                                <?php echo esc_html__('Video', 'carspot'); ?>
                                            </h3>
                                        </div>
                                        <div class="short-feature-body">

        <?php
        $iframe = 'iframe';
        echo '<' . $iframe . ' width="700" height="370" src="https://www.youtube.com/embed/' . esc_attr($video_id) . '" frameborder="0" allowfullscreen></' . $iframe . '>';
        ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
<?php if (isset($carspot_theme['style_ad_720_2']) && $carspot_theme['style_ad_720_2'] != "") { ?>
                                <div class="margin-top-30 margin-bottom-30">
    <?php echo "" . $carspot_theme['style_ad_720_2']; ?>
                                </div>
                                        <?php } ?>
<?php
if ($carspot_theme['sb_enable_comments_offer'] == 1) {
    ?>
                                <div class="bidding-states" id="bidding-area">
                                        <?php
                                        if (isset($carspot_theme['sb_enable_comments_offer']) && $carspot_theme['sb_enable_comments_offer'] && get_post_meta($pid, '_carspot_ad_status_', true) != 'sold' && get_post_meta($pid, '_carspot_ad_status_', true) != 'expired' && get_post_meta($pid, '_carspot_ad_price', true) != "0") {
                                            if (isset($carspot_theme['sb_enable_comments_offer_user']) && $carspot_theme['sb_enable_comments_offer_user'] && get_post_meta($pid, '_carspot_ad_bidding', true) == 1) {
                                                echo carspot_bidding_stats($pid);
                                            } else if (isset($carspot_theme['sb_enable_comments_offer_user']) && $carspot_theme['sb_enable_comments_offer_user'] && get_post_meta($pid, '_carspot_ad_bidding', true) == 0) {
                                                
                                            } else {
                                                echo carspot_bidding_stats($pid);
                                            }
                                        }
                                        ?>

                                </div>
                                <?php
                                if (isset($carspot_theme['sb_enable_comments_offer']) && $carspot_theme['sb_enable_comments_offer'] && get_post_meta($pid, '_carspot_ad_status_', true) != 'sold' && get_post_meta($pid, '_carspot_ad_status_', true) != 'expired' && get_post_meta($pid, '_carspot_ad_price', true) != "0") {
                                    if (isset($carspot_theme['sb_enable_comments_offer_user']) && $carspot_theme['sb_enable_comments_offer_user'] && get_post_meta($pid, '_carspot_ad_bidding', true) == 1) {
                                        get_template_part('template-parts/layouts/ad_style/video', 'bidding2');
                                    } else if (isset($carspot_theme['sb_enable_comments_offer_user']) && $carspot_theme['sb_enable_comments_offer_user'] && get_post_meta($pid, '_carspot_ad_bidding', true) == 0) {
                                        
                                    } else {
                                        get_template_part('template-parts/layouts/ad_style/video', 'bidding2');
                                    }
                                }
                                ?>

                                <?php } ?>
                                <?php get_template_part('template-parts/layouts/ad_style/related', 'ads'); ?>
                        </div>
                        <!-- Share Ad  --> 
                    </div>
                    <!-- =-=-=-=-=-=-= Latest Ads End =-=-=-=-=-=-= -->
                </div>
                <div class="col-md-4 col-xs-12 col-sm-12">
                                <?php if (is_active_sidebar('carspot_ad_sidebar_top')) { ?>
                                    <?php dynamic_sidebar('carspot_ad_sidebar_top'); ?>
                                <?php } ?>
                    <div class="sidebar">
                            <?php
                            if (get_post_meta($pid, '_carspot_ad_status_', true) == 'expired') {
                                ?>
                            <div class="final_ad_status expired-out">
                                <p><?php echo carspot_ad_statues(get_post_meta($pid, '_carspot_ad_status_', true)); ?></p>
                            </div>
                                <?php
                            } else if (get_post_meta($pid, '_carspot_ad_status_', true) == 'sold') {
                                ?>
                            <div class="final_ad_status sold-out">
                                <?php echo carspot_ad_statues(get_post_meta($pid, '_carspot_ad_status_', true)); ?>
                            </div>
                                <?php
                            } else {
                                ?>
                                <?php
                                If (isset($carspot_theme['allow_ad_economy']) && $carspot_theme['allow_ad_economy'] && get_post_meta($pid, '_carspot_ad_avg_city', true) != '' && get_post_meta($pid, '_carspot_ad_avg_hwy', true) != '') {
                                    ?>
                                <div class="fule-economy">
                                    <h4><?php echo esc_html__('Fuel Economy', 'carspot'); ?></h4>
                                    <ul class="list-inline">
                                        <li>
                                            <h5><?php echo esc_html(get_post_meta($pid, '_carspot_ad_avg_city', true)); ?></h5>
                                            <p> <?php echo esc_html__('City MPG', 'carspot'); ?></p>
                                        </li>
                                        <li>
                                            <h5><?php echo esc_html(get_post_meta($pid, '_carspot_ad_avg_hwy', true)); ?></h5>
                                            <p> <?php echo esc_html__('Highway MPG', 'carspot'); ?></p>
                                        </li>
                                    </ul>
                                </div>
                            <?php } ?>

                            <?php
                            if ($carspot_theme['communication_mode'] == 'both' || $carspot_theme['communication_mode'] == 'message') {
                                ?>
                                <div class="category-list-icon">
                                <?php
                                if (isset($carspot_theme['communication_icon_message']) && $carspot_theme['communication_icon_message'] != "") {
                                    echo '<i class="green ' . $carspot_theme['communication_icon_message'] . '"></i>';
                                }
                                ?>
                                    <div class="category-list-title">
                                        <!-- Email Button trigger modal -->
                                    <?php
                                    if (get_current_user_id() == "") {
                                        ?>
                                            <h5><a href="<?php echo esc_url(get_the_permalink($carspot_theme['sb_sign_in_page'])); ?>"><?php echo esc_html__('Contact Seller Via Email', 'carspot'); ?></a></h5>
                                    <?php
                                } else {
                                    ?>
                                            <h5><a href="javascript:void(0)" data-toggle="modal" data-target=".price-quote"><?php echo esc_html__('Message Seller', 'carspot'); ?></a></h5>
                                    <?php
                                }
                                ?>
                                    </div>
                                </div>
        <?php
    }
    if ($carspot_theme['communication_mode'] == 'both' || $carspot_theme['communication_mode'] == 'phone') {
        ?>
                                <div class="category-list-icon">
        <?php
        if (isset($carspot_theme['communication_icon_phone']) && $carspot_theme['communication_icon_phone'] != "") {
            echo '<i class="purple ' . $carspot_theme['communication_icon_phone'] . '"></i>';
        }
        ?>
                                    <div class="category-list-title">
                                        <h5><a href="javascript:void(0)" class="number" data-last="<?php echo esc_attr(strip_tags_content(get_post_meta($pid, '_carspot_poster_contact', true))); ?>"><span><?php echo esc_html__('Click to View Phone No.', 'carspot'); ?></span></a></h5>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                                <?php
                            }
                            ?>
                            <?php if ($carspot_theme['make_offer_form_on'] || $carspot_theme['test_drive_form_on']) { ?>
                            <div class="additional-btns">
                                <ul>
    <?php if ($carspot_theme['make_offer_form_on']) { ?> 
                                        <li>
                                            <a href="" class="" data-toggle="modal" data-target="#make-offer-modal">
                                                <i class="la la-money"></i> <span><?php echo esc_html__('Make an Offer Price', 'carspot'); ?></span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if ($carspot_theme['test_drive_form_on']) { ?> 
                                        <li>
                                            <a href="" class="" data-toggle="modal" data-target="#test-drive-modal">
                                                <i class="la la-support"></i> <span> <?php echo esc_html__('Schedule Test Drive ', 'carspot'); ?> </span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
<?php } ?>
                        <?php
                        $mapType = carspot_mapType();
                        if ($mapType != 'no_map') {
                            ?>
                            <div class="widget ">
                                <div class="widget-heading">
                                    <h4 class="panel-title"><span><?php echo esc_html__('Location on map', 'carspot'); ?></span></h4>
                                </div>
                                <div class="singlemap-location">
                                    <div class="singlemap-location">
                                <?php
                                if (get_post_meta($pid, '_carspot_ad_map_lat', true) != "" && get_post_meta($pid, '_carspot_ad_map_long', true) != "") {
                                    ?> 
                                            <div id="itemMap"></div>
                                            <input type="hidden" id="lat" value="<?php echo esc_attr(get_post_meta($pid, '_carspot_ad_map_lat', true)); ?>" />
                                            <input type="hidden" id="lon" value="<?php echo esc_attr(get_post_meta($pid, '_carspot_ad_map_long', true)); ?>" />
                                <?php
                            }
                            ?>
                            <?php
                            if (get_post_meta($pid, '_carspot_ad_map_location', true) != "") {
                                ?>
                                            <div class="template-icons">
                                                <div class="icon-box-icon flaticon-location"></div>
                                                <div class="class-name"><?php echo strip_tags_content(get_post_meta($id, '_carspot_ad_map_location', true)); ?></div>
                                            </div>
                                        <?php
                                    }
                                    ?>  
                                    </div>
                                </div>
                            </div> 
                                    <?php
                                }
                                ?>
<?php if (is_active_sidebar('carspot_ad_sidebar_bottom')) { ?>
    <?php dynamic_sidebar('carspot_ad_sidebar_bottom'); ?>
<?php } ?>
                        <!-- Saftey Tips  -->
                                <?php
                                if ($carspot_theme['tips_title'] != '' && $carspot_theme['tips_for_ad'] != "") {
                                    ?>
                            <div class="widget">
                                <div class="widget-heading">
                                    <h4 class="panel-title"><span><?php echo($carspot_theme['tips_title']); ?></span></h4>
                                </div>
                                <div class="widget-content saftey">
    <?php echo($carspot_theme['tips_for_ad']); ?>
                                </div>
                            </div>
    <?php
}
?>
                                    <?php if ($carspot_theme['finacne_calc_on']) {
                                        ?>
                            <div class="widget">
                                <div class="widget-heading">
                                    <h4 class="panel-title"><span> <?php echo esc_html__('Financing Calculator', 'carspot'); ?></span></h4>
                                </div>
                                <div class="widget-content ">
                                        <?php get_template_part('template-parts/layouts/ad_style/finance', 'calculator'); ?>
                                </div>
                            </div>
                                    <?php } ?>
                    </div>
                </div>
            </div>

        </div>
</div>
</section>


                                    <?php
                                    //only for category based pricing
                                    if (isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'package_based') {
                                        //sticky action buttons
                                        get_template_part('template-parts/layouts/ad_style/sticky-buttons/sticky', 'buttons');
                                    }
                                    ?> 
</div>
                        <?php get_template_part('template-parts/layouts/ad_style/message', 'seller'); ?>



