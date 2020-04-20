<?php
// Ad Price Widget
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_price');
});
if (!class_exists('carspot_search_ad_price')) {

    class carspot_search_ad_price extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_price',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Price', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $expand = "";
            $is_show = carspot_getTemplateID('static', '_sb_default_cat_price_show');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }


            $min_price = $instance['min_price'];
            if (isset($_GET['min_price']) && $_GET['min_price'] != "") {
                $expand = "in";
                $min_price = $_GET['min_price'];
            }
            $max_price = $instance['max_price'];
            if (isset($_GET['max_price']) && $_GET['max_price'] != "") {
                $max_price = $_GET['max_price'];
            }
            global $carspot_theme;

            $min = 0;
            if (isset($instance['min_price'])) {
                $min = $instance['min_price'];
            }
            global $carspot_theme;
            $form_style = isset($instance['form_style']) && !empty($instance['form_style']) ? $instance['form_style'] : 'style_1';
            add_action('wp_footer', function () use ($form_style) {
                if ($form_style == 'style_2') {
                    ?>
                    <script>
                        (function ($) {
                            $('#price-slider').on('change', function ()
                            {
                                setTimeout(function () {
                                    $(".carspot-price-form").submit();
                                }, 2000);
                            });
							
							$('#min_selected').on('change paste keyup', function ()
                            {
                                setTimeout(function () {
                                    $(".carspot-price-form").submit();
                                }, 1200);
                            });
							$('#max_selected').on('change paste keyup', function ()
                            {
                                setTimeout(function () {
                                    $(".carspot-price-form").submit();
                                }, 1200);
                            });
                        })(jQuery);
                    </script>
                    <?php
                }
            });
            $price_style = '';
            if ($form_style == 'style_2') {
                //$price_style = ' style="display:none;"';
            }
            ?>
                    <div class="panel panel-default" id="red-price">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="headingfour">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-price" class="carspot-price-form">
                    <div id="collapsefour" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="headingfour">
                        <div class="panel-body">
                            <span class="price-slider-value"><?php echo esc_html__('Price', 'carspot');?>
                                (<?php echo esc_html($carspot_theme['sb_currency']);?>) 
                                <span id="price-min"></span>
                                - 
                                <span id="price-max"></span>
                            </span>
                            <div id="price-slider"></div>

                            <div class="input-group margin-top-10"<?php echo carspot_returnEcho($price_style);?>>
                                <input type="text" class="form-control" name="min_price" id="min_selected" value="<?php echo esc_attr($min_price);?>"  autocomplete="off"/>
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" name="max_price" id="max_selected" value="<?php echo esc_attr($max_price);?>" autocomplete="off"/>
                            </div>

                            <input type="hidden" id="min_price" value="<?php echo esc_attr($instance['min_price']);?>" />
                            <input type="hidden" id="max_price" value="<?php echo esc_attr($instance['max_price']);?>" />
                            <?php if ($form_style == 'style_1') {?>
                                <input type="submit" class="btn btn-theme btn-sm margin-top-10" value="<?php echo esc_html__('Search', 'carspot');?>" />
                            <?php }?>
                        </div>
                    </div>
                    <?php echo carspot_search_params('min_price', 'max_price');?>
                </form>
            </div>
            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Price', 'carspot');
            }

            if (isset($instance['min_price'])) {
                $min_price = $instance['min_price'];
            } else {
                $min_price = 1;
            }

            if (isset($instance['max_price'])) {
                $max_price = $instance['max_price'];
            } else {
                $max_price = esc_html__('10000000', 'carspot');
            }
            $form_style1 = isset($instance['form_style']) && !empty($instance['form_style']) && $instance['form_style'] == 'style_1' ? ' selected' : '';
            $form_style2 = isset($instance['form_style']) && !empty($instance['form_style']) && $instance['form_style'] == 'style_2' ? ' selected' : '';
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('min_price'));?>" >
                    <?php echo esc_html__('Min Price:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('min_price'));?>" name="<?php echo esc_attr($this->get_field_name('min_price'));?>" type="text" value="<?php echo esc_attr($min_price);?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('max_price'));?>" >
                    <?php echo esc_html__('Max Price:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('max_price'));?>" name="<?php echo esc_attr($this->get_field_name('max_price'));?>" type="text" value="<?php echo esc_attr($max_price);?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('form_style'));?>" >
                    <?php echo esc_html__('Style : ', 'carspot');?>
                </label> 
                <select class="search-select form-control" name="<?php echo esc_attr($this->get_field_name('form_style'));?>">
                    <option value="style_1"<?php echo esc_attr($form_style1);?>> <?php echo esc_html__('Style 1', 'carspot');?> </option>
                    <option value="style_2"<?php echo esc_attr($form_style2);?>> <?php echo esc_html__('Style 2', 'carspot');?> </option>
                </select>
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            $instance['min_price'] = (!empty($new_instance['min_price']) ) ? strip_tags($new_instance['min_price']) : '';
            $instance['max_price'] = (!empty($new_instance['max_price']) ) ? strip_tags($new_instance['max_price']) : '';
            $instance['form_style'] = (!empty($new_instance['form_style']) ) ? strip_tags($new_instance['form_style']) : '';
            return $instance;
        }

    }

    // Ad Price
}

// Ad Categories widget
add_action('widgets_init', function() {
    register_widget('carspot_search_cats');
});
if (!class_exists('carspot_search_cats')) {

    class carspot_search_cats extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_cats',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Categories', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {

            $new = '';
            $used = '';
            $expand = "";
            if (isset($_GET['cat_id']) && $_GET['cat_id'] != "") {
                $expand = "in";
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-category">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="headingOne">
                    <!-- Title -->
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                    <!-- Title End -->
                </div>
                <!-- Content -->
                <form method="get" id="make_search" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-category">
                    <div id="collapseOne" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="headingOne">

                        <?php
                        global $carspot_theme;
                        $heading = '';
                        if (isset($carspot_theme['cat_level_1']) && $carspot_theme['cat_level_1'] != "") {
                            $heading = $carspot_theme['cat_level_1'];
                        }
                        $ad_cats = carspot_get_cats('ad_cats', 0);
                        if (count((array) $ad_cats) > 0) {
                            ?>
                            <div class="panel-body">

                                <?php
                                if (isset($_GET['cat_id']) && $_GET['cat_id'] != "") {
                                    ?>
                                    <div class="cat_head"><span><?php echo carspot_get_taxonomy_parents($_GET['cat_id'], 'ad_cats', false);?></span></div>
                                    <?php
                                }
                                ?>
                                <label class="control-label"> <?php echo esc_attr($heading);?> </label>
                                <select class="search-select form-control" id="make_id">
                                    <option value=""> <?php echo esc_html__('Select Any Category', 'carspot');?> </option>
                                    <?php
                                    foreach ($ad_cats as $ad_cat) {
                                        $category = get_term($ad_cat->term_id);
                                        $cat_meta = get_option("taxonomy_term_$ad_cat->term_id");
                                        ?>
                                        <option value="<?php echo esc_attr($ad_cat->term_id);?>"><?php echo esc_html($ad_cat->name);?> </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <div id="select_modal" class="margin-top-10"></div>

                                <div id="select_modals" class="margin-top-10"></div>

                                <div id="select_forth_div" class="margin-top-10"></div>

                                <input type="submit" class="btn btn-theme btn-sm margin-top-10 margin-bottom-10" id="search_make" value="<?php echo esc_html__('Search', 'carspot');?>" />

                            </div>

                            <?php
                        }
                        ?>
                    </div>
                    <input type="hidden" name="cat_id" id="cat_id" value="" />

                    <?php echo carspot_search_params('cat_id');?>
                </form>

            </div>
            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Categories', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

    // Categories widget
}

// Ad title Widget
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_title');
});
if (!class_exists('carspot_search_ad_title')) {

    class carspot_search_ad_title extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_title',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Search', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $expand = "";
            $title = '';
            if (isset($_GET['ad_title']) && $_GET['ad_title'] != "") {
                $expand = "in";
                $title = $_GET['ad_title'];
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-title">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="headingFive">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-title">
                    <!-- Content -->
                    <div id="collapseFive" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="headingFive">
                        <div class="panel-body">
                            <div class="search-widget">
                                <input id="autocomplete-dynamic" autocomplete="off" class="form-control" placeholder="<?php echo esc_html__('search', 'carspot');?>" type="text" name="ad_title" value="<?php echo esc_attr($title);?>">
                                <button type="submit"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo carspot_search_params('ad_title');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Search', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

    // Ad title
}



/* Featured Ads Widget */
add_action('widgets_init', function() {
    register_widget('carspot_search_featured_ad');
});
if (!class_exists('carspot_search_featured_ad')) {

    class carspot_search_featured_ad extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_featured_ad',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Featured', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $max_ads = $instance['max_ads'];
            global $carspot_theme;
            ?>

            <div class="panel panel-default">
                <!-- Heading -->
                <div class="panel-heading" >
                    <h4 class="panel-title">
                        <a>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <div class="panel-collapse">
                    <div class="panel-body recent-ads">
                        <div class="featured-slider-3 owl-carousel owl-theme">
                            <!-- Featured Ads -->
                            <?php
                            $f_args = array(
                                'post_type' => 'ad_post',
                                'post_status' => 'publish',
                                'posts_per_page' => $max_ads,
                                'meta_query' => array(
                                    array(
                                        'key' => '_carspot_is_feature',
                                        'value' => 1,
                                        'compare' => '=',
                                    ),
                                ),
                                'orderby' => 'rand',
                            );
                            $f_ads = new WP_Query($f_args);
                            if ($f_ads->have_posts()) {
                                $number = 0;
                                while ($f_ads->have_posts()) {
                                    $f_ads->the_post();
                                    $pid = get_the_ID();
                                    $author_id = get_post_field('post_author', $pid);
                                    ;
                                    $author = get_user_by('ID', $author_id);

                                    $img = $carspot_theme['default_related_image']['url'];
                                    $media = carspot_fetch_listing_gallery($pid);
                                    $total_imgs = count((array) $media);
                                    if (count((array) $media) > 0) {
                                        foreach ($media as $m) {
                                            $mid = '';
                                            if (isset($m->ID)) {
                                                $mid = $m->ID;
                                            } else {
                                                $mid = $m;
                                            }

                                            $image = wp_get_attachment_image_src($mid, 'carspot-ad-related');
                                            $img = $image[0];
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="item">
                                        <div class="col-md-12 col-xs-12 col-sm-12 no-padding">
                                            <!-- Ad Box -->
                                            <div class="category-grid-box">
                                                <!-- Ad Img -->
                                                <div class="category-grid-img">
                                                    <?php echo carspot_video_icon();?>
                                                    <?php
                                                    if(wp_attachment_is_image($mid))
													{
														?>
														<img class="img-responsive" alt="<?php echo esc_attr(get_the_title());?>" src="<?php echo esc_url($img);?>">
                                                        <?php
													}
													else
													{
														?>
														<img class="img-responsive" alt="<?php echo esc_attr(get_the_title());?>" src="<?php echo esc_url($carspot_theme['default_related_image']['url']);?>">
                                                        <?php
													}
													?>
                                                    <!-- Ad Status -->
                                                    <!-- User Review -->
                                                    <div class="user-preview">
                                                        <a href="<?php echo esc_url(get_author_posts_url($author_id));?>?type=ads">
                                                            <img src="<?php echo esc_url(carspot_get_user_dp($author_id));?>" class="avatar avatar-small" alt="<?php echo esc_attr(get_the_title());?>">
                                                        </a>
                                                    </div>
                                                    <!-- View Details -->
                                                    <a href="<?php echo esc_url(get_the_permalink());?>" class="view-details">
                                                        <?php echo esc_html__('View Details', 'carspot');?>
                                                    </a>
                                                </div>
                                                <!-- Ad Img End -->
                                                <div class="short-description">
                                                    <!-- Ad Category -->
                                                    <div class="category-title">
                                                        <?php echo carspot_display_cats(get_the_ID());?>
                                                    </div>
                                                    <!-- Ad Title -->
                                                    <h3><a href="<?php echo esc_url(get_the_permalink());?>"><?php the_title();?></a></h3>
                                                    <!-- Price -->
                                                    <div class="price">
                                                        <?php echo(carspot_adPrice(get_the_ID()));?> 
                                                    </div>
                                                </div>
                                                <!-- Addition Info -->

                                                <div class="ad-info">
                                                    <?php
                                                    if (carspot_display_adLocation(get_the_ID()) != "") {
                                                        ?>	
                                                        <ul>
                                                            <li><i class="fa fa-map-marker"></i><?php echo carspot_display_adLocation(get_the_ID());?></li>
                                                        </ul>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <!-- Ad Box End -->
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            wp_reset_postdata();
                            ?>
                            <!-- Featured Ads -->
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Featured Ads', 'carspot');
            }
            if (isset($instance['max_ads'])) {
                $max_ads = $instance['max_ads'];
            } else {
                $max_ads = 5;
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('max_ads'));?>" >
                    <?php echo esc_html__('Max # of Ads:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('max_ads'));?>" name="<?php echo esc_attr($this->get_field_name('max_ads'));?>" type="text" value="<?php echo esc_attr($max_ads);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            $instance['max_ads'] = (!empty($new_instance['max_ads']) ) ? strip_tags($new_instance['max_ads']) : '';
            return $instance;
        }

    }

}
/* Featured Ads Widget */

/* Recent Ads Widget */
add_action('widgets_init', function() {
    register_widget('carspot_search_recent_ad');
});
if (!class_exists('carspot_search_recent_ad')) {

    class carspot_search_recent_ad extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_recent_ad',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ads Recent', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $max_ads = $instance['max_ads'];
            global $carspot_theme;
            ?>

            <div class="panel panel-default">
                <!-- Heading -->
                <div class="panel-heading" >
                    <h4 class="panel-title">
                        <a>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <div class="panel-collapse">
                    <div class="panel-body recent-ads">
                        <?php
                        $f_args = array(
                            'post_type' => 'ad_post',
                            'posts_per_page' => $max_ads,
                            'post_status' => 'publish',
                            'orderby' => 'ID',
                            'order' => 'DESC',
                        );
                        $f_ads = new WP_Query($f_args);
                        if ($f_ads->have_posts()) {
                            $number = 0;
                            while ($f_ads->have_posts()) {
                                $f_ads->the_post();
                                $pid = get_the_ID();
                                $author_id = get_post_field('post_author', $pid);
                                ;
                                $author = get_user_by('ID', $author_id);

                                $img = $carspot_theme['default_related_image']['url'];
                                $media = carspot_fetch_listing_gallery($pid);
                                $total_imgs = count((array) $media);
                                if (count((array) $media) > 0) {
                                    foreach ($media as $m) {
                                        $mid = '';
                                        if (isset($m->ID)) {
                                            $mid = $m->ID;
                                        } else {
                                            $mid = $m;
                                        }

                                        $image = wp_get_attachment_image_src($mid, 'carspot-listing-small');
                                        $img = $image[0];
                                        break;
                                    }
                                }
                                ?>
                                <div class="recent-ads-list">
                                    <div class="recent-ads-container">
                                        <div class="recent-ads-list-image">
                                            <a href="<?php the_permalink();?>" class="recent-ads-list-image-inner">
                                                <img alt="<?php echo esc_attr(get_the_title());?>" src="<?php echo esc_url($img);?>">
                                            </a><!-- /.recent-ads-list-image-inner -->
                                        </div>
                                        <!-- /.recent-ads-list-image -->
                                        <div class="recent-ads-list-content">
                                            <h3 class="recent-ads-list-title">
                                                <a href="<?php the_permalink();?>"><?php the_title();?></a>
                                            </h3>
                                            <ul class="recent-ads-list-location">
                                                <li><a href="javascript:void(0);"><?php echo esc_html(get_post_meta(get_the_ID(), '_carspot_ad_location', true));?></a></li>
                                            </ul>
                                            <div class="recent-ads-list-price">
                                                <?php echo(carspot_adPrice(get_the_ID()));?> 
                                            </div>
                                            <!-- /.recent-ads-list-price -->
                                        </div>
                                        <!-- /.recent-ads-list-content -->
                                    </div>
                                    <!-- /.recent-ads-container -->
                                </div>
                                <?php
                            }
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>               
            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Recent Ads', 'carspot');
            }
            if (isset($instance['max_ads'])) {
                $max_ads = $instance['max_ads'];
            } else {
                $max_ads = 5;
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('max_ads'));?>" >
                    <?php echo esc_html__('Max # of Ads:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('max_ads'));?>" name="<?php echo esc_attr($this->get_field_name('max_ads'));?>" type="text" value="<?php echo esc_attr($max_ads);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            $instance['max_ads'] = (!empty($new_instance['max_ads']) ) ? strip_tags($new_instance['max_ads']) : '';
            return $instance;
        }

    }

}
/* Recent Ads Widget */

/* Advertisement  Widget */
add_action('widgets_init', function() {
    register_widget('carspot_search_advertisement');
});
if (!class_exists('carspot_search_advertisement')) {

    class carspot_search_advertisement extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_advertisement',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Carspot Advertisement', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $ad_code = $instance['ad_code'];
            global $carspot_theme;
            ?>

            <div class="panel panel-default">
                <!-- Heading -->
                <div class="panel-heading" >
                    <h4 class="panel-title">
                        <a>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <div class="panel-collapse">
                    <div class="panel-body recent-ads">
                        <?php echo "" . $ad_code;?>
                    </div>
                </div>
            </div>               
            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Advertisement', 'carspot');
            }
            $ad_code = '';
            if (isset($instance['ad_code'])) {
                $ad_code = $instance['ad_code'];
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('300 X 250 Ad', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('ad_code'));?>" >
                    <?php echo esc_html__('Code:', 'carspot');?>
                </label> 
                <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('ad_code'));?>" name="<?php echo esc_attr($this->get_field_name('ad_code'));?>" type="text"><?php echo esc_attr($ad_code);?></textarea>
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            $instance['ad_code'] = (!empty($new_instance['ad_code']) ) ? $new_instance['ad_code'] : '';
            return $instance;
        }

    }

}
/* Advertisement  Widget */

/* ------------------------------------------------------------------------------------- */
/* Custom Dynamic Widgets */
add_action('widgets_init', function() {
    register_widget('carspot_search_custom_fields');
});
if (!class_exists('carspot_search_custom_fields')) {

    class carspot_search_custom_fields extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_custom_fields',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Custom Fields Search', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $ad_code = isset($instance['ad_code']) ? $instance['ad_code'] : '';
            global $carspot_theme;
            ?>
            <?php
            $term_id = '';
            $customHTML = '';
            if (isset($_GET['cat_id']) && $_GET['cat_id'] != "" && is_numeric($_GET['cat_id'])) {
                $term_id = $_GET['cat_id'];
                $result = carspot_dynamic_templateID($term_id);
                //$result =  carspot_dynamic_templateID($cat_id);
                $templateID = get_term_meta($result, '_sb_dynamic_form_fields', true);


                if (isset($templateID) && $templateID != "") {
                    $formData = sb_dynamic_form_data($templateID);
                    $customHTML .= '';
                    foreach ($formData as $r) {


                        if (isset($r['types']) && trim($r['types']) != "") {

                            if (isset($r['types']) && $r['types'] == 5) {
                                continue;
                            }

                            $in_search = (isset($r['in_search']) && $r['in_search'] == "yes") ? 1 : 0;
                            if ($r['titles'] != "" && $r['slugs'] != "" && $in_search == 1) {

                                $customHTML .= '<div class="panel panel-default" id="red-types">
  <div class="panel-heading" >
     <h4 class="panel-title"><a>' . esc_html($instance['title']) . ' ' . esc_html($r['titles']) . '</a></h4>
  </div>
  <div class="panel-collapse">
     <div class="panel-body recent-ads">
	 	<div class="skin-minimal">
			<form method="get" action="' . get_the_permalink($carspot_theme['sb_search_page']) . '#red-types" class="custom-search-form">';
                                $fieldName = "custom[" . esc_attr($r['slugs']) . "]";
                                $fieldValue = isset($_GET["custom"]) ? @$_GET['custom'][esc_attr($r['slugs'])] : '';
                                if (isset($r['types']) && $r['types'] == 1) {
                                    $customHTML .= '<div class="search-widget"><input placeholder="' . esc_attr($r['titles']) . '" name="' . $fieldName . '" value="' . $fieldValue . '" type="text"><button type="submit"><i class="fa fa-search"></i></button></div>';
                                }
                                if (isset($r['types']) && $r['types'] == 2) {
                                    $options = '';
                                    if (isset($r['values']) && $r['values'] != 1) {
                                        $varArrs = @explode("|", $r['values']);
                                        $options .= '<option value="0">' . esc_html__("Select Option", "carspot") . '</option>';
                                        foreach ($varArrs as $varArr) {
                                            $selected = ($fieldValue == $varArr) ? 'selected="selected"' : '';
                                            $options .= '<option value="' . esc_attr($varArr) . '" ' . $selected . '>' . esc_html($varArr) . '</option>';
                                        }
                                    }
                                    $customHTML .= '<select name="' . $fieldName . '" class="custom-search-select" >' . $options . '</select>';
                                }
                                if (isset($r['types']) && $r['types'] == 3) {
                                    $options = '';
                                    if (isset($r['values']) && $r['values'] != "") {
                                        $varArrs = @explode("|", $r['values']);

                                        $loop = 1;
                                        if (count($varArrs) > 0) {
                                            $options = '<select name="' . $fieldName . '" class="submit_on_select"><option></option>';
                                        }
                                        foreach ($varArrs as $val) {

                                            $checked = '';
                                            if (isset($fieldValue) && $fieldValue != "") {
                                                //$checked = in_array($val, $fieldValue) ? 'checked="checked"' : '';
                                                $checked = ($val == $fieldValue) ? 'selected="selected"' : '';
                                            }

                                            $options .= '<option value="' . $val . '"' . $checked . '>' . esc_html($val) . '</option>';
                                            $loop++;
                                        }
                                        $options .= '</select>';
                                    }
                                    //$customHTML .= '<select name="'.$fieldName.'" class="custom-search-select" >'.$options.'</select>';    
                                    $customHTML .= '<div class="skin-minimal"><ul class="list">' . $options . '</ul></div>';
                                }
                                if (isset($r['types']) && $r['types'] == 4) {
                                    $customHTML .= '<div class="search-widget"><input placeholder="' . esc_attr($r['titles']) . '" name="' . $fieldName . '" value="' . $fieldValue . '" type="text" class="dynamic-form-date-fields"><button type="submit" onclick="return false;"><i class="fa fa-calendar"></i></button></div>';
                                }

                                /* if(isset($r['types'] ) && $r['types'] == 5){ This is for website URL When Required} */

                                $customHTML .= carspot_search_params($fieldName);
                                $customHTML .= '</form></div></div></div></div> ';
                            }
                        }
                    }
                }
            }
            echo "" . $customHTML;
            ?>


            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {

            $title = ( isset($instance['title']) ) ? $instance['title'] : esc_html__('Search By:', 'carspot');
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?> <small><?php echo esc_html__('You can leave it empty as well', 'carspot');?></small>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            <p><?php echo esc_html__('You can show/hide the specific type from categories custom fields where you created it.', 'carspot');?> </p>
            </p>

            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}
/* Custom Dynamic Widgets */

// Simple or featured ad search
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_simple_feature');
});
if (!class_exists('carspot_search_ad_simple_feature')) {

    class carspot_search_ad_simple_feature extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_simple_feature',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Simple or feature ad search', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $simple = '';
            $featured = '';
            $expand = "";
            if (isset($_GET['ad']) && $_GET['ad'] != "") {
                $expand = "in";
                if ($_GET['ad'] == 0) {
                    $simple = "checked";
                }
                if ($_GET['ad'] == 1) {
                    $featured = "checked";
                }
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-ads-type">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="headingNine">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseNine" aria-expanded="true" aria-controls="collapseNine">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-ads-type">
                    <div id="collapseNine" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="headingNine">
                        <div class="panel-body">
                            <div class="skin-minimal">
                                <ul class="list">
                                    <li>
                                        <input tabindex="7" type="radio" id="minimal-radio-sb_1" name="ad" value="0" <?php echo esc_attr($simple);?>  >
                                        <label for="minimal-radio-sb_1" >
                                            <?php echo esc_html__('Simple Ads', 'carspot');?></label>
                                    </li>
                                    <li>
                                        <input tabindex="7" type="radio" id="minimal-radio-sb_2" name="ad" value="1" <?php echo esc_attr($featured);?>  >
                                        <label for="minimal-radio-sb_2" >
                                            <?php echo esc_html__('Featured Ads', 'carspot');?></label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo carspot_search_params('ad');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Simple or Featured', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

    // Simple or featured ad search
}

/* Assembly */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_assembly');
});
if (!class_exists('carspot_search_ad_assembly')) {

    class carspot_search_ad_assembly extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_assembly',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Assembly', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $is_show = carspot_getTemplateID('taxconomy', 'ad_assembles');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }
            $expand = "";

            if (isset($_GET['assembly']) && $_GET['assembly'] != "") {
                $expand = "in";
            }

            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-assembly">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="ad-assembly-type">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#ad-assembly" aria-expanded="true" aria-controls="ad-assembly-type">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-assembly">

                    <?php
                    $ad_assembly = carspot_get_cats('ad_assembles', 0);
                    if (count((array) $ad_assembly) > 0) {
                        $field_name = 'assembly';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>
                        <div id="ad-assembly" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="ad-assembly-type">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($ad_assembly as $assmbly) {
                                            ?>
                                            <li>
                                                <input  type="<?php do_action('carsport_search_option_type');?>" id="ad-assembly-<?php echo esc_attr($assmbly->term_id);?>" value="<?php echo esc_attr($assmbly->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'assembly', $assmbly->name);
                                                ?> name="<?php echo esc_attr($field_name);?>">
                                                <label for="ad-assembly-<?php echo esc_attr($assmbly->term_id);?>"><?php echo esc_html($assmbly->name);?></label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_assembles', $assmbly->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo carspot_search_params('assembly');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Assembly', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Ad type condition */
add_action('widgets_init', function() {
    register_widget('carspot_search_condition');
});
if (!class_exists('carspot_search_condition')) {

    class carspot_search_condition extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_conidtion',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Condition', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {

            $expand = $cur_con = '';

            $is_show = carspot_getTemplateID('taxconomy', 'ad_condition');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }


            if (isset($_GET['condition']) && $_GET['condition'] != "") {
                $cur_con = $_GET['condition'];
                $expand = "in";
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-condition">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="headingThree">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-condition">
                    <?php
                    $conditions = carspot_get_cats('ad_condition', 0);
                    echo carspot_search_params('condition');
                    if (count((array) $conditions) > 0) {
                        $field_name = 'condition';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>

                        <div id="collapseThree" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($conditions as $con) {
                                            ?>
                                            <li>
                                                <input tabindex="7" type="<?php do_action('carsport_search_option_type');?>" id="minimal-radio-<?php echo esc_attr($con->term_id);?>" name="<?php echo esc_attr($field_name);?>" value="<?php echo esc_attr($con->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'condition', $con->name);
                                                ?>  >
                                                <label for="minimal-radio-<?php echo esc_attr($con->term_id);?>" ><?php echo esc_html($con->name);?></label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_condition', $con->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Condition', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Ad type Widget */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_type');
});
if (!class_exists('carspot_search_ad_type')) {

    class carspot_search_ad_type extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_type',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Type', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {

            $expand = "";

            $is_show = carspot_getTemplateID('taxconomy', 'ad_type');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }


            if (isset($_GET['ad_type']) && $_GET['ad_type'] != "") {
                $expand = "in";
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-ad-type">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="headingSeven">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-ad-type">
                    <?php
                    $conditions = carspot_get_cats('ad_type', 0);
                    if (count((array) $conditions) > 0) {

                        $field_name = 'ad_type';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>
                        <div id="collapseSeven" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="headingSeven">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($conditions as $con) {
                                            ?>
                                            <li>
                                                <input tabindex="7" type="<?php do_action('carsport_search_option_type');?>" id="minimal-radio-<?php echo esc_attr($con->term_id);?>" name="<?php echo esc_attr($field_name);?>" value="<?php echo esc_attr($con->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'ad_type', $con->name);
                                                ?>  >
                                                <label for="minimal-radio-<?php echo esc_attr($con->term_id);?>" ><?php echo esc_html($con->name);?></label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_type', $con->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                    echo carspot_search_params('ad_type');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Type', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

    // Ad Type
}

/* Ad Warranty */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_warranty');
});
if (!class_exists('carspot_search_ad_warranty')) {

    class carspot_search_ad_warranty extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_warranty',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Warranty', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {

            $expand = "";

            $is_show = carspot_getTemplateID('taxconomy', 'ad_warranty');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }

            if (isset($_GET['warranty']) && $_GET['warranty'] != "") {
                $expand = "in";
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-warranty">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="headingEight">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseEight" aria-expanded="true" aria-controls="collapseEight">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-warranty">
                    <?php
                    $conditions = carspot_get_cats('ad_warranty', 0);
                    if (count((array) $conditions) > 0) {

                        $field_name = 'warranty';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>


                        <div id="collapseEight" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="headingEight">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($conditions as $con) {
                                            ?>
                                            <li>
                                                <input tabindex="7" type="<?php do_action('carsport_search_option_type');?>" id="minimal-radio-<?php echo esc_attr($con->term_id);?>" name="<?php echo esc_attr($field_name);?>" value="<?php echo esc_attr($con->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'warranty', $con->name);
                                                ?>  >
                                                <label for="minimal-radio-<?php echo esc_attr($con->term_id);?>" ><?php echo esc_html($con->name);?></label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_warranty', $con->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>


                        <?php
                    }
                    echo carspot_search_params('warranty');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Warranty', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

    // Ad Warranty
}

/* Ad Years */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_year');
});
if (!class_exists('carspot_search_ad_year')) {

    class carspot_search_ad_year extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_years',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Year', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {

            $is_show = carspot_getTemplateID('taxconomy', 'ad_years');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }

            $year_from = '';
            $year_to = '';
            $expand = "";
            if (isset($_GET['year_from']) && $_GET['year_from'] != "") {
                $year_from = $_GET['year_from'];
            }
            if (isset($_GET['year_to']) && $_GET['year_to'] != "") {
                $year_to = $_GET['year_to'];
            }
            if ($year_from != '' && $year_to != '') {
                $expand = "in";
            }

            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-years">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="headingYear">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#Yearcollapse" aria-expanded="true" aria-controls="Yearcollapse">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-years">

                    <?php
                    $ad_year = carspot_get_cats('ad_years', 0);
                    if (count((array) $ad_year) > 0) {
                        ?>
                        <div id="Yearcollapse" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="headingYear">
                            <div class="panel-body">
                                <div class="input-group  margin-top-10">
                                    <span class="input-group-addon"><?php echo esc_html__("From", "carspot")?></span>
                                    <select id="year_from" name="year_from" class="form-control">
                                        <?php
                                        foreach ($ad_year as $ad_years) {
                                            ?>
                                            <option value="<?php echo esc_attr($ad_years->name);?>"<?php echo "" . ($ad_years->name == $year_from) ? ' selected="selected"' : '';?>><?php echo esc_html($ad_years->name);?> </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo esc_html__("To", "carspot")?></span>
                                    <select id="year_to" name="year_to" class="form-control">
                                        <?php
                                        foreach ($ad_year as $ad_years) {
                                            ?>
                                            <option value="<?php echo esc_attr($ad_years->name);?>"<?php echo "" . ($ad_years->name == $year_to) ? ' selected="selected"' : '';?>><?php echo esc_html($ad_years->name);?> </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="submit" id="ad_year"  class="btn btn-theme btn-sm margin-top-10" value="<?php echo esc_html__('Search', 'carspot');?>" />
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo carspot_search_params('year_from', 'year_to');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Year', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Body Type */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_body_type');
});
if (!class_exists('carspot_search_ad_body_type')) {

    class carspot_search_ad_body_type extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_body_type',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Body Type', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $is_show = carspot_getTemplateID('taxconomy', 'ad_body_types');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }

            $expand = "";

            if (isset($_GET['body_type']) && $_GET['body_type'] != "") {
                $expand = "in";
            }

            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-body-type">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="bodytype">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#ad_bodytype" aria-expanded="true" aria-controls="body_type">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-body-type">
                    <?php
                    $ad_bodytype = carspot_get_cats('ad_body_types', 0);
                    if (count((array) $ad_bodytype) > 0) {
                        $field_name = 'body_type';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>
                        <div id="ad_bodytype" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="bodytype">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($ad_bodytype as $ad_body) {
                                            ?>
                                            <li>
                                                <input  type="<?php do_action('carsport_search_option_type');?>" id="body-type-<?php echo esc_attr($ad_body->term_id);?>" value="<?php echo esc_attr($ad_body->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'body_type', $ad_body->name);
                                                ?> name="<?php echo esc_attr($field_name);?>">
                                                <label for="body-type-<?php echo esc_attr($ad_body->term_id);?>"><?php echo esc_html($ad_body->name);?> </label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_body_types', $ad_body->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo carspot_search_params('body_type');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Body Type', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Transmission */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_transmissions');
});
if (!class_exists('carspot_search_ad_transmissions')) {

    class carspot_search_ad_transmissions extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_transmissions',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Transmission', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $is_show = carspot_getTemplateID('taxconomy', 'ad_transmissions');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }

            $expand = "";
            $body_type = "";
            if (isset($_GET['transmission']) && $_GET['transmission'] != "") {
                $expand = "in";
            }

            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-transmission">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="body_transmission">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#transmission" aria-expanded="true" aria-controls="body_transmission">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-transmission">

                    <?php
                    $ad_transmissions = carspot_get_cats('ad_transmissions', 0);

                    if (count((array) $ad_transmissions) > 0) {
                        $field_name = 'transmission';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>
                        <div id="transmission" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="body_transmission">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        


                                        foreach ($ad_transmissions as $ad_transmission) {
                                            ?>
                                            <li>
                                                <input  type="<?php do_action('carsport_search_option_type');?>" id="transmission-type-<?php echo esc_attr($ad_transmission->term_id);?>" value="<?php echo esc_attr($ad_transmission->name);?>" 
                                                <?php
                                                do_action('carsport_search_option_checked', 'transmission', $ad_transmission->name);
                                                ?> name="<?php echo esc_attr($field_name);?>">
                                                <label for="transmission-type-<?php echo esc_attr($ad_transmission->term_id);?>"><?php echo esc_html($ad_transmission->name);?> </label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_transmissions', $ad_transmission->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo carspot_search_params('transmission');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Transmission', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Engine Type */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_engine_type');
});
if (!class_exists('carspot_search_ad_engine_type')) {

    class carspot_search_ad_engine_type extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_engine_type',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Engine Type', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $is_show = carspot_getTemplateID('taxconomy', 'ad_engine_types');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }

            $expand = "";

            if (isset($_GET['engine_type']) && $_GET['engine_type'] != "") {
                $expand = "in";
            }

            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-engine-type">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="ad_body_type">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#body_type" aria-expanded="true" aria-controls="ad_body_type">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-engine-type">

                    <?php
                    $ad_engine_types = carspot_get_cats('ad_engine_types', 0);
                    if (count($ad_engine_types) > 0) {
                        $field_name = 'engine_type';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>
                        <div id="body_type" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="ad_body_type">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($ad_engine_types as $ad_engine_type) {
                                            ?>
                                            <li>
                                                <input  type="<?php do_action('carsport_search_option_type');?>" id="engine-type-<?php echo esc_attr($ad_engine_type->term_id);?>" value="<?php echo esc_attr($ad_engine_type->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'engine_type', $ad_engine_type->name);
                                                ?> name="<?php echo esc_attr($field_name);?>">
                                                <label for="engine-type-<?php echo esc_attr($ad_engine_type->term_id);?>"><?php echo esc_html($ad_engine_type->name);?> </label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_engine_types', $ad_engine_type->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo carspot_search_params('engine_type');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Engine Type', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Engine Type Capacity */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_engine_engine_capacity');
});
if (!class_exists('carspot_search_ad_engine_engine_capacity')) {

    class carspot_search_ad_engine_engine_capacity extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_engine_engine_capacity',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Engine Capacity (CC)', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $is_show = carspot_getTemplateID('taxconomy', 'ad_engine_capacities');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }
            $expand = "";

            if (isset($_GET['engine_capacity']) && $_GET['engine_capacity'] != "") {
                $expand = "in";
            }

            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-engine-capacity">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="engince-capacity">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#engine_capacity" aria-expanded="true" aria-controls="engince-capacity">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-engine-capacity">

                    <?php
                    $ad_engine_capacities = carspot_get_cats('ad_engine_capacities', 0);
                    if (count($ad_engine_capacities) > 0) {
                        $field_name = 'engine_capacity';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>
                        <div id="engine_capacity" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="engince-capacity">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($ad_engine_capacities as $ad_engine_capacity) {
                                            ?>
                                            <li>
                                                <input  type="<?php do_action('carsport_search_option_type');?>" id="engine-capacity-<?php echo esc_attr($ad_engine_capacity->term_id);?>" value="<?php echo esc_attr($ad_engine_capacity->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'engine_capacity', $ad_engine_capacity->name);
                                                ?> name="<?php echo esc_attr($field_name);?>">
                                                <label for="engine-capacity-<?php echo esc_attr($ad_engine_capacity->term_id);?>"><?php echo esc_html($ad_engine_capacity->name);?> <?php echo esc_html__("cc", "carspot");?></label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_engine_capacities', $ad_engine_capacity->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo carspot_search_params('engine_capacity');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Engine Capacity (CC)', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Color Family */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_color_family');
});
if (!class_exists('carspot_search_ad_color_family')) {

    class carspot_search_ad_color_family extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_color_family',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Color Family', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {
            $is_show = carspot_getTemplateID('taxconomy', 'ad_colors');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return; 
            }

            $expand = "";

            if (isset($_GET['color_family']) && $_GET['color_family'] != "") {
                $expand = "in";
            }

            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-color-family">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="ad-color-family">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#ad-color" aria-expanded="true" aria-controls="ad-color-family">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-color-family">
                    <?php
                    $ad_colors = carspot_get_cats('ad_colors', 0);
                    if (count($ad_colors) > 0) {
                        $field_name = 'color_family';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>
                        <div id="ad-color" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="ad-color-family">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($ad_colors as $ad_color) {
                                            ?>
                                            <li>
                                                <input  type="<?php do_action('carsport_search_option_type');?>" id="ad_color-<?php echo esc_attr($ad_color->term_id);?>" value="<?php echo esc_attr($ad_color->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'color_family', $ad_color->name);
                                                ?> name="<?php echo esc_attr($field_name);?>">
                                                <label for="ad_color-<?php echo esc_attr($ad_color->term_id);?>"><?php echo esc_html($ad_color->name);?></label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_colors', $ad_color->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo carspot_search_params('color_family');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Color Family', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Insurance */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_insurance');
});
if (!class_exists('carspot_search_ad_insurance')) {

    class carspot_search_ad_insurance extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_insurance',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Insurance', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {

            $is_show = carspot_getTemplateID('taxconomy', 'ad_insurance');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }

            $expand = "";

            if (isset($_GET['insurance']) && $_GET['insurance'] != "") {
                $expand = "in";
            }

            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-isurance">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="ad-insurance">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#insurance" aria-expanded="true" aria-controls="ad-insurance">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-isurance">
                    <?php
                    $ad_insurance = carspot_get_cats('ad_insurance', 0);
                    if (count($ad_insurance) > 0) {
                        $field_name = 'insurance';
                        $field_name = apply_filters('carspot_search_option_name', $field_name);
                        ?>
                        <div id="insurance" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="ad-insurance">
                            <div class="panel-body">
                                <div class="skin-minimal">
                                    <ul class="list">
                                        <?php
                                        foreach ($ad_insurance as $ad_insurances) {
                                            ?>
                                            <li>
                                                <input  type="<?php do_action('carsport_search_option_type');?>" id="ad_color-<?php echo esc_attr($ad_insurances->term_id);?>" value="<?php echo esc_attr($ad_insurances->name);?>" <?php
                                                do_action('carsport_search_option_checked', 'insurance', $ad_insurances->name);
                                                ?> name="<?php echo esc_attr($field_name);?>">
                                                <label for="ad_color-<?php echo esc_attr($ad_insurances->term_id);?>"><?php echo esc_html($ad_insurances->name);?></label>
                                                <?php do_action('carsport_search_category_count', '_carspot_ad_insurance', $ad_insurances->name);?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <?php
                    echo carspot_search_params('insurance');
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Insurance', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Millage */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_mileage');
});
if (!class_exists('carspot_search_ad_mileage')) {

    class carspot_search_ad_mileage extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_mileage',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Mileage (Km)', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {


            $is_show = carspot_getTemplateID('taxconomy', 'ad_mileage');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }

            $mileage = '';
            $milage_from = '';
            $mileage_to = '';
            $expand = "";
            if (isset($_GET['mileage_from']) && $_GET['mileage_from'] != "") {
                $milage_from = $_GET['mileage_from'];
            }
            if (isset($_GET['mileage_to']) && $_GET['mileage_to'] != "") {
                $mileage_to = $_GET['mileage_to'];
            }
            if ($milage_from != '' && $mileage_to != '') {
                $expand = "in";
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-milage">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="ad-mileage">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#mileage" aria-expanded="true" aria-controls="ad-mileage">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                </div>
                <!-- Content -->
                <form method="get" id="get_mileage" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-milage">
                    <div id="mileage" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="ad-mileage">
                        <div class="panel-body">
                            <div class="input-group margin-top-10">
                                <input type="text" class="form-control" name="mileage_from" data-parsley-type="digits" data-parsley-required="true"  data-parsley-error-message="<?php echo esc_html__('Value should be numeric', 'carspot');?>" placeholder="<?php echo esc_html__("From", "carspot")?>" id="mileage_from" value="<?php echo esc_attr($milage_from);?>" />
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control" data-parsley-required="true" data-parsley-type="digits" data-parsley-error-message="<?php echo esc_html__('Value should be numeric', 'carspot');?>" placeholder="<?php echo esc_html__("To", "carspot")?>" name="mileage_to" id="mileage_to" value="<?php echo esc_attr($mileage_to);?>" />

                            </div>

                            <input type="submit" class="btn btn-theme btn-sm margin-top-10" value="<?php echo esc_html__('Search', 'carspot');?>" />
                        </div>
                    </div>        

                    <?php echo carspot_search_params('mileage_from', 'mileage_to');?>
                </form>
            </div>

            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Mileage (Km)', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Ad Location */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_location');
});
if (!class_exists('carspot_search_ad_location')) {

    class carspot_search_ad_location extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_location',
                'description' => esc_html__('Only for search and single ad sidebar.', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Location', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {

            $is_show = carspot_getTemplateID('taxconomy', 'ad_country');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }


            $expand = "";
            if (isset($_GET['country_id']) && $_GET['country_id'] != "") {
                $expand = "in";
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-country">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="location_heading">
                    <!-- Title -->
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#ad-location" aria-expanded="true" aria-controls="ad-location">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                    <!-- Title End -->
                </div>
                <!-- Content -->
                <form method="get" id="search_countries" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-country">
                    <div id="ad-location" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="location_heading">

                        <?php
                        $countries = carspot_get_cats('ad_country', 0);
                        if (count($countries) > 0) {
                            ?>
                            <div class="panel-body countries">
                                <?php
                                if (isset($_GET['country_id']) && $_GET['country_id'] != "") {
                                    echo carspot_get_taxonomy_parents($_GET['country_id'], 'ad_country', false);
                                }
                                ?>
                                <ul>
                                    <?php
                                    foreach ($countries as $country) {
                                        $category = get_term($country->term_id);
                                        $count = $category->count;
                                        ?>
                                        <li> 
                                            <a href="javascript:void(0);" data-country-id="<?php echo esc_attr($country->term_id);?>">
                                                <?php echo esc_html($country->name);?> 
                                                <span>(<?php echo esc_html($count);?>)</span>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>	
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <input type="hidden" name="country_id" id="country_id" value="" />
                    <?php echo carspot_search_params('country_id');?>
                </form>
                <div class="search-modal modal fade states_model" id="states_model" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&#10005;</span><span class="sr-only">Close</span></button>
                                <h3 class="modal-title text-center"> 
                                    <i class="icon-gears"></i> 
                                    <?php echo esc_html__('Select Your Location', 'carspot');?> 
                                </h3>
                            </div>
                            <div class="modal-body">
                                <!-- content goes here -->
                                <div class="search-block">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12 col-sm-12 popular-search" id="countries_response"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" id="country-btn" class="btn btn-lg btn-block"> <?php echo esc_html__('Submit', 'carspot');?> </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = esc_html__('Ad Location', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

/* Ad Location */
add_action('widgets_init', function() {
    register_widget('carspot_search_ad_by_zip');
});
if (!class_exists('carspot_search_ad_by_zip')) {

    class carspot_search_ad_by_zip extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            $widget_ops = array(
                'classname' => 'carspot_search_ad_by_zip',
                'description' => esc_html__('Only for search page sidebar and can only work if google map is enabled', 'carspot'),
            );
            // Instantiate the parent object
            parent::__construct(false, esc_html__('Ad Location with Radius', 'carspot'), $widget_ops);
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance) {

            /*$is_show = carspot_getTemplateID('taxconomy', 'ad_country');
            if ($is_show == '' || $is_show == 1) {
                
            } else {
                return;
            }*/


            $expand = "";
            if (isset($_GET['radius']) && $_GET['radius'] != "") {
                $expand = "in";
            }
            global $carspot_theme;
            ?>
            <div class="panel panel-default" id="red-radius">
                <!-- Heading -->
                <div class="panel-heading" role="tab" id="radius_search_heading">
                    <!-- Title -->
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#radius_search" aria-expanded="true" aria-controls="radius_search">
                            <i class="more-less glyphicon glyphicon-plus"></i>
                            <?php echo esc_html($instance['title']);?>
                        </a>
                    </h4>
                    <!-- Title End -->
                </div>
                <?php 
					$stricts = '';
					if( isset( $carspot_theme['sb_location_allowed'] ) && !$carspot_theme['sb_location_allowed'] && isset ($carspot_theme['sb_list_allowed_country'] ) )
					{
						$stricts = "componentRestrictions: {country: ". json_encode( $carspot_theme['sb_list_allowed_country'] ) . "}";
					}
				
			$mapType = carspot_mapType();
			if($mapType == 'google_map')
			{
			echo "<script>
			(function ($) {
				'use strict';
            	/*RADIUS SEARC PLACES ON SEARCH PAGE*/
				$( document ).ready(function() {
					function initMap() {
						
						var options = {
						  types: ['(regions)'],
						  ".$stricts."
						  //componentRestrictions: {country: ['NL','BE']} 
						 };
						var input = document.getElementById('searchMapInput');
						var autocomplete = new google.maps.places.Autocomplete(input, options);
						autocomplete.addListener('place_changed', function() {
							var place = autocomplete.getPlace();
							$('#location-snap').val(place.formatted_address); 
							$('#loc_lat').val(place.geometry.location.lat());
							$('#loc_long').val(place.geometry.location.lng());
						});
					}
					initMap();
				});
				})(jQuery);
            
            </script>";
			}
            ?>
                <!-- Content -->
                <form method="get" id="radius_search_countries" action="<?php echo esc_url(get_the_permalink($carspot_theme['sb_search_page']));?>#red-radius">
                    <div id="radius_search" class="panel-collapse collapse <?php echo esc_attr($expand);?>" role="tabpanel" aria-labelledby="radius_search_heading">
					<div class="panel-body">
                    <div class="form-group">
                        <input id="searchMapInput" class="form-control" type="text" name="radius_address" placeholder="<?php echo esc_html__('Search location', 'carspot');?>" value="<?php if(isset($_GET['radius_address']) && $_GET['radius_address'] != "") { echo esc_html($_GET['radius_address']); }else{echo '';}?>">
                    </div>

                                
                                <!--if($_GET['radius'] == '20')-->
								<!--<input type="text" class="form-control" id="radius_number" name="radius" placeholder="<?php echo esc_html__('Radius in km', 'carspot');?>" />-->
                                <select class="search-select form-control" id="radius_number" name="radius" data-placeholder="<?php echo esc_html__('Select Radius', 'carspot');?>">
                                    <option value=""> <?php echo esc_html__('Radius in km', 'carspot');?> </option>
                                    <option <?php if(isset($_GET['radius']) && $_GET['radius'] == '10') { ?> selected <?php }?> value="<?php echo esc_html__('10', 'carspot');?>"><?php echo esc_html__('10 KM', 'carspot');?> </option>
                                    <option <?php if(isset($_GET['radius']) && $_GET['radius'] == '20') { ?> selected <?php }?> value="<?php echo esc_html__('20', 'carspot');?>"><?php echo esc_html__('20 KM', 'carspot');?> </option>
                                    <option <?php if(isset($_GET['radius']) && $_GET['radius'] == '50') { ?> selected <?php }?> value="<?php echo esc_html__('50', 'carspot');?>"><?php echo esc_html__('50 KM', 'carspot');?> </option>
                                    <option <?php if(isset($_GET['radius']) && $_GET['radius'] == '100') { ?> selected <?php }?> value="<?php echo esc_html__('100', 'carspot');?>"><?php echo esc_html__('100 KM', 'carspot');?> </option>
                                    <option <?php if(isset($_GET['radius']) && $_GET['radius'] == '150') { ?> selected <?php }?> value="<?php echo esc_html__('150', 'carspot');?>"><?php echo esc_html__('150 KM', 'carspot');?> </option>
                                    <option <?php if(isset($_GET['radius']) && $_GET['radius'] == '200') { ?> selected <?php }?> value="<?php echo esc_html__('200', 'carspot');?>"><?php echo esc_html__('200 KM', 'carspot');?> </option>
                                    <option <?php if(isset($_GET['radius']) && $_GET['radius'] == '300') { ?> selected <?php }?> value="<?php echo esc_html__('300', 'carspot');?>"><?php echo esc_html__('300 KM', 'carspot');?> </option>
                                </select>
                                <!--<input type="submit" class="btn btn-theme btn-sm margin-top-10 margin-bottom-10" id="search_make" value="<?php echo esc_html__('Search', 'carspot');?>" />-->
                                <input type="hidden" name="loc_long" id="loc_long" value="<?php if(isset($_GET['loc_long']) && $_GET['loc_long'] != "") { echo esc_html($_GET['loc_long']); }else{echo '';}?>" />
                                <input type="hidden" name="loc_lat" id="loc_lat" value="<?php if(isset($_GET['loc_lat']) && $_GET['loc_lat'] != "") { echo esc_html($_GET['loc_lat']); }else{echo '';}?>" />
                                <input type="hidden" id="location-snap" value="">
                            </div>
                    </div>
                    <?php echo carspot_search_params( 'radius', 'loc_long', 'loc_lat', 'radius_address');?>

                </form>
            </div>
            <?php
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form($instance) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } 
			else {
                $title = esc_html__('Location with radius', 'carspot');
            }
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>" >
                    <?php echo esc_html__('Title:', 'carspot');?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($title);?>">
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    }

}

//******************************************************


if (!function_exists('carspot_getTemplateID')) {

    function carspot_getTemplateID($type = 'dynamic', $is_show = '') {
        if (isset($_GET['cat_id']) && $_GET['cat_id'] != "" && is_numeric($_GET['cat_id'])) {
            $term_id = $_GET['cat_id'];
            $result = carspot_dynamic_templateID($term_id);
            $templateID = get_term_meta($result, '_sb_dynamic_form_fields', true);

            if (isset($templateID) && $templateID != "") {
                if ($type == 'static') {
                    $formData = sb_custom_form_data($templateID, $is_show);
                } else if ($type == 'taxconomy') {

                    $formData = sb_text_field_value($result, $is_show);
                } else {
                    $formData = sb_dynamic_form_data($templateID);
                }
                return $formData;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

}