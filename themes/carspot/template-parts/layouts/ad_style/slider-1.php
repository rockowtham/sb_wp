<div id="single-slider" class="flexslider">
    <ul class="slides">
        <?php
        global $carspot_theme;
        $ad_id = get_the_ID();
        $media = carspot_fetch_listing_gallery($ad_id);
        $title = get_the_title();
        if (count((array) $media) > 0) {
            foreach ($media as $m) {
                $mid = '';
                if (isset($m->ID)) {
                    $mid = $m->ID;
                } else {
                    $mid = $m;
                }
                $img = wp_get_attachment_image_src($mid, 'carspot-single-post');
                $full_img = wp_get_attachment_image_src($mid, 'full');
                if (wp_attachment_is_image($mid)) {
                    ?>
                    <li>
                        <a href="<?php echo esc_url($full_img[0]); ?>" data-fancybox="group">
                            <img alt="<?php echo esc_attr($title); ?>" src="<?php echo esc_url($img[0]); ?>">
                        </a>
                    </li>
                    <?php
                } else {
                    ?>
                    <li>
                        <a href="<?php echo esc_url($carspot_theme['default_related_image']['url']); ?>" data-fancybox="group">
                            <img alt="<?php echo esc_attr($title); ?>" src="<?php echo esc_url($carspot_theme['default_related_image']['url']); ?>">
                        </a>
                    </li>
                    <?php
                }
            }
        }
        ?>
    </ul>
</div>
<!-- Listing Slider Thumb --> 
<div id="carousel" class="flexslider">
    <ul class="slides">
        <?php
        if (count((array) $media) > 0) {
            foreach ($media as $m) {
                $mid = '';
                if (isset($m->ID)) {
                    $mid = $m->ID;
                } else {
                    $mid = $m;
                }
                $img = wp_get_attachment_image_src($mid, 'carspot-ad-thumb');
                if (wp_attachment_is_image($mid)) {
                    ?>
                    <li><img alt="<?php echo esc_attr($title); ?>" draggable="false" src="<?php echo esc_url($img[0]); ?>"></li>
                    <?php
                } else {
                    ?>
                    <li><img alt="<?php echo esc_attr($title); ?>" draggable="false" src="<?php echo esc_url($carspot_theme['default_related_image']['url']); ?>"></li>
                    <?php
                }
            }
        }
        ?>
    </ul>
</div>