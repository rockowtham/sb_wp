<?php 
global $carspot_theme; 
$pid	=	get_the_ID();
$poster_id	=	get_post_field( 'post_author', $pid );
$user_pic	=	carspot_get_user_dp($poster_id);
$address	=	get_post_meta($pid, '_carspot_ad_location', true ); 

$ribbon_html = '';
if( get_post_meta( $pid, '_carspot_is_feature', true ) == '1' && get_post_meta($pid, '_carspot_ad_status_', true ) == 'active' )
{
$ribbion = 'featured-ribbon';
if( is_rtl())
{
	$ribbion = 'featured-ribbon-rtl';
}
	$ribbon_html = '<div class="'.esc_attr( $ribbion ).'"><span>'.esc_html__('Featured','carspot').'</span></div>';
}

$top_padding ='no-top';
if(isset( $carspot_theme['sb_header'] ) &&  $carspot_theme['sb_header'] == 'transparent' ||  $carspot_theme['sb_header'] == 'transparent2')
{
	$top_padding ='';	
}
?>
<div class="main-content-area clearfix">
 <section class="section-padding <?php echo carspot_returnEcho($top_padding); ?> gray">
    <!-- Main Container -->
    <div class="container">
       <!-- Row -->
       <div class="row">
       <?php 
	   if($carspot_theme['sb_header'] == 'transparent' ||  $carspot_theme['sb_header'] == 'transparent2' )
	   {
		   
		}
		else 
		{
			get_template_part( 'template-parts/layouts/ad_style/short-summary', 'title' ); 
		}
	    ?>
       <?php
	   if(isset($carspot_theme['ad_slider_type']) && $carspot_theme['ad_slider_type'] == 2)
	   {
	  	 get_template_part( 'template-parts/layouts/ad_style/gallery', $carspot_theme['ad_slider_type'] );
	   }
	   ?>
       <?php 
		if(isset($carspot_theme['ad_slider_type']) && $carspot_theme['ad_slider_type'] == 4)
		{
		?>
			 <?php echo (get_template_part( 'template-parts/layouts/ad_style/gallery', $carspot_theme['ad_slider_type'])); ?>
		<?php
		}
		?>
          <!-- Middle Content Area -->
          <div class="col-md-8 col-xs-12 col-sm-12">
             <!-- Single Ad -->
             <div class="singlepage-detail">
             <?php
			get_template_part( 'template-parts/layouts/ad_style/feature', 'notification' );
			 ?>
             <?php
			 if( get_post_meta($pid, '_carspot_ad_status_', true ) != ""  && get_post_meta($pid, '_carspot_ad_status_', true ) != 'active' )
			 {
			?>
             <div role="alert" class="alert alert-info alert-outline alert-dismissible">
<button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">&#10005;</span></button>
<strong><?php echo esc_html__('Info','carspot'); ?></strong> - 
<?php echo esc_html__('This ad has been','carspot') . " "; ?>
<?php echo carspot_ad_statues(get_post_meta($pid, '_carspot_ad_status_', true )); ?>.
             </div>
            <?php
			 }
			 ?>
             <div class="rebbon-clear">
                <?php 
			if($carspot_theme['ad_slider_type'] == 1 || $carspot_theme['ad_slider_type'] == 3)
			 {
			  echo ( $ribbon_html );
			 }

				/*Listing Slider*/
				if(isset($carspot_theme['ad_slider_type']) && $carspot_theme['ad_slider_type'] == 1)
				{
					get_template_part( 'template-parts/layouts/ad_style/slider', $carspot_theme['ad_slider_type'] );
				}
				?>
                
                <?php 
				if(isset($carspot_theme['ad_slider_type']) && $carspot_theme['ad_slider_type'] == 3)
				{
					get_template_part( 'template-parts/layouts/ad_style/gallery', $carspot_theme['ad_slider_type'] );
				}
				?>
             </div>  
                 <?php get_template_part( 'template-parts/layouts/ad_style/key', 'features' ); ?>
                 <?php 
				 	/*Short Description*/
				 	get_template_part( 'template-parts/layouts/ad_style/ad', 'detail' ); 
				 ?>
                
                <div class="clearfix"></div>
                <?php if(isset($carspot_theme['style_ad_720_2']) && $carspot_theme['style_ad_720_2'] !=""){?>
                 <div class="margin-top-30 margin-bottom-30">
                 <?php echo "" . $carspot_theme['style_ad_720_2']; ?>
                 </div>
                 <?php } ?>
                <?php 
					/*Share Ad report Ad*/
					get_template_part( 'template-parts/layouts/ad_style/ad', 'tabs' ); 
				?>
                <div class="clearfix"></div>
                
                  
                <?php 
					get_template_part( 'template-parts/layouts/ad_style/video', 'bidding' ); 
				?>
                <div class="clearfix"></div>
                
                
             </div>
			 <?php get_template_part( 'template-parts/layouts/ad_style/related', 'ads' ); ?>
          </div>
          <div class="col-md-4 col-xs-12 col-sm-12">
			<?php if ( is_active_sidebar( 'carspot_ad_sidebar_top' ) ) { ?>
            <?php dynamic_sidebar( 'carspot_ad_sidebar_top' ); ?>
            <?php } ?>
             <div class="sidebar">
             <?php
			 	if( get_post_meta($pid, '_carspot_ad_status_', true ) == 'expired' )
				{
			 ?>
                <div class="final_ad_status expired-out">
                   <p><?php echo carspot_ad_statues(get_post_meta($pid, '_carspot_ad_status_', true )); ?></p>
                </div>
             <?php
				}
			 	else if( get_post_meta($pid, '_carspot_ad_status_', true ) == 'sold' )
				{
			 ?>
                <div class="final_ad_status sold-out">
                   <?php echo carspot_ad_statues(get_post_meta($pid, '_carspot_ad_status_', true )); ?>
                </div>
             <?php
				}
				else
				{
			?>
            <?php If( isset($carspot_theme['allow_ad_economy']) && $carspot_theme['allow_ad_economy'] && get_post_meta($pid, '_carspot_ad_avg_city', true) != '' && get_post_meta($pid, '_carspot_ad_avg_hwy', true) != '') 
					{
			 ?>
				<div class="fule-economy">
                    <h4><?php echo esc_html__('Fuel Economy','carspot'); ?></h4>
                    <ul class="list-inline">
                        <li>
                            <h5><?php echo esc_html(get_post_meta($pid, '_carspot_ad_avg_city', true)); ?></h5>
                            <p> <?php echo esc_html__('City MPG','carspot'); ?></p>
                        </li>
                        <li>
                            <h5><?php echo esc_html(get_post_meta($pid, '_carspot_ad_avg_hwy', true)); ?></h5>
                            <p> <?php echo esc_html__('Highway MPG','carspot'); ?></p>
                        </li>
                    </ul>
                </div>
                <?php } ?>
       <?php
	   	if( $carspot_theme['communication_mode'] == 'both' || $carspot_theme['communication_mode'] == 'message' )
		{
	   ?>
       <div class="category-list-icon">
       <?php
		if( isset( $carspot_theme['communication_icon_message'] ) && $carspot_theme['communication_icon_message'] != "" )
		{
			echo '<i class="green '.$carspot_theme['communication_icon_message'].'"></i>';
		}
		?>
            <div class="category-list-title">
            <!-- Email Button trigger modal -->
            <?php
            if( get_current_user_id() == "" )
            {
            ?>
            <h5><a href="<?php echo esc_url(get_the_permalink( $carspot_theme['sb_sign_in_page'] )); ?>"><?php echo esc_html__( 'Contact Seller Via Email', 'carspot' ); ?></a></h5>
            <?php
            }
            else
            {
            ?>
             <h5><a href="javascript:void(0)" data-toggle="modal" data-target=".price-quote"><?php echo esc_html__( 'Message Seller', 'carspot' ); ?></a></h5>
            <?php
            }
            ?>
            </div>
        </div>
      <?php
		}
		if( $carspot_theme['communication_mode'] == 'both' || $carspot_theme['communication_mode'] == 'phone' )
		{
		?>
        <div class="category-list-icon">
          <?php
			if( isset( $carspot_theme['communication_icon_phone'] ) && $carspot_theme['communication_icon_phone'] != "" )
			{
				echo '<i class="purple '.$carspot_theme['communication_icon_phone'].'"></i>';
			}
			?>
          <div class="category-list-title">
             <h5><a href="javascript:void(0)" class="number" data-last="<?php echo esc_attr(strip_tags_content(get_post_meta($pid, '_carspot_poster_contact', true))); ?>"><span><?php echo esc_html__('Click to View', 'carspot' ); ?></span></a></h5>
          </div>
        </div>
	  <?php
		}
	   ?>
				<?php
				}
				?>
                <?php if($carspot_theme['make_offer_form_on'] || $carspot_theme['test_drive_form_on']) { ?>
                    <div class="additional-btns">
                        <ul>
                            <?php if($carspot_theme['make_offer_form_on']) { ?> 
                            <li>
                                <a href="" class="" data-toggle="modal" data-target="#make-offer-modal">
                                    <i class="la la-money"></i> <span><?php echo esc_html__('Make an Offer Price','carspot'); ?></span>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if($carspot_theme['test_drive_form_on']) { ?> 
                            <li>
                                <a href="" class="" data-toggle="modal" data-target="#test-drive-modal">
                                    <i class="la la-support"></i> <span> <?php echo esc_html__('Schedule Test Drive ','carspot'); ?> </span>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                     <?php } ?>
                     
                     
                     
                 	  <?php
			  if( isset( $carspot_theme['sb_enable_comments_offer'] ) && $carspot_theme['sb_enable_comments_offer'] && get_post_meta($pid, '_carspot_ad_status_', true ) != 'sold' && get_post_meta($pid, '_carspot_ad_status_', true ) != 'expired' && get_post_meta($pid, '_carspot_ad_price', true ) != "0" )
		{
			if( isset( $carspot_theme['sb_enable_comments_offer_user'] ) && $carspot_theme['sb_enable_comments_offer_user'] && get_post_meta($pid, '_carspot_ad_bidding', true ) == 1 )
			{
				echo carspot_bidding_stats( $pid );
			}
			else if( isset( $carspot_theme['sb_enable_comments_offer_user'] ) && $carspot_theme['sb_enable_comments_offer_user'] && get_post_meta($pid, '_carspot_ad_bidding', true ) == 0 )
			{
				
			}
			else
			{
				echo carspot_bidding_stats( $pid );
			}
		}
		?>
	                 <div class="white-bg user-contact-info">
                   <div class="user-info-card">
                      <div class="user-photo col-md-4 col-sm-3  col-xs-4">
                        <a href="<?php echo esc_url(get_author_posts_url($poster_id)); ?>" class="link">
                        	<img class="img-circle" src="<?php echo esc_url( $user_pic ); ?>" alt="<?php echo esc_html__('Profile Pic', 'carspot' ); ?>">
                      	</a>
                      </div>
                      <div class="user-information  col-md-8 col-sm-9 col-xs-8">
                      <?php
					  	$poster_name	=	get_post_meta($pid, '_carspot_poster_name', true );
					  	if( $poster_name == "" )
						{
							$user_info	=	get_userdata( $poster_id );
							$poster_name	=	$user_info->display_name;
						}
					  ?>
                         <span class="user-name">
                         <a class="hover-color" href="<?php echo esc_url(get_author_posts_url($poster_id)); ?>">
                         <?php echo esc_html($poster_name); ?>
                         </a>
                         </span>
                         <div class="item-date">
                            <span class="ad-pub"><?php echo esc_html__('Logged in at', 'carspot') . ': '.carspot_get_last_login( $poster_id ). ' ' . esc_html__('Ago','carspot'); ?></span>
									<?php
                                        if(isset($carspot_theme['sb_enable_user_ratting']) && $carspot_theme['sb_enable_user_ratting'] )
										{
											echo avg_user_rating($poster_id).' (';
											echo carspot_dealer_review_count($poster_id).')';
										}
									?>
                                                            <?php
						if( get_user_meta($poster_id, '_sb_badge_type', true ) != "" && get_user_meta($poster_id, '_sb_badge_text', true ) != "" && isset( $carspot_theme['sb_enable_user_badge'] ) && $carspot_theme['sb_enable_user_badge'] && $carspot_theme['sb_enable_user_badge'])
						{
						?>
						<span class="label <?php echo esc_attr(get_user_meta($poster_id, '_sb_badge_type', true )); ?>">
						<?php echo esc_html(get_user_meta($poster_id, '_sb_badge_text', true )); ?>
						</span>
						<?php
						}
						?>
                         </div>
                      </div>
                      <div class="clearfix"></div>
                   </div>
                 </div>
                 <?php
					$mapType = carspot_mapType();
					if($mapType != 'no_map' )
					{ ?>
                 <div class="singlemap-location">
                 <?php   										
                if( get_post_meta($pid, '_carspot_ad_map_location', true ) != "")
                {
                ?>
                <div class="template-icons">
                        <div class="icon-box-icon flaticon-location"></div>
                        <div class="class-name"><?php echo esc_html(get_post_meta($id, '_carspot_ad_map_location', true)); ?></div>
                     </div>
                  <?php
                }
                ?>    
				<?php   										
                if( get_post_meta($pid, '_carspot_ad_map_lat', true ) != "" && get_post_meta($pid, '_carspot_ad_map_long', true ) != "" )
                {
                ?> 
                    <div id="itemMap"></div>
                    <input type="hidden" id="lat" value="<?php echo esc_attr(get_post_meta($pid, '_carspot_ad_map_lat', true )); ?>" />
                    <input type="hidden" id="lon" value="<?php echo esc_attr(get_post_meta($pid, '_carspot_ad_map_long', true )); ?>" />
                <?php
                }
                ?>
                </div>
                 <?php 
					}
				 if ( is_active_sidebar( 'carspot_ad_sidebar_bottom' ) ) { ?>
                <?php dynamic_sidebar( 'carspot_ad_sidebar_bottom' ); ?>
				<?php } ?>
                <!-- Saftey Tips  -->
                <?php
				if( $carspot_theme['tips_title'] != '' &&  $carspot_theme['tips_for_ad'] != "" )
				{
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
                <?php if( $carspot_theme['finacne_calc_on'] )
						{ ?>
						<div class="widget">
						   <div class="widget-heading">
							  <h4 class="panel-title"><span> <?php echo esc_html__('Financing Calculator','carspot'); ?></span></h4>
						   </div>
						   <div class="widget-content ">
							  <?php get_template_part( 'template-parts/layouts/ad_style/finance', 'calculator' ); ?>
						   </div>
					   </div>
			   <?php } ?>
             </div>
             </div>
             
          </div>
       </div>
 </section>
 

<?php
	//only for category based pricing
	if(isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'package_based')
	{ 
		//sticky action buttons
		get_template_part( 'template-parts/layouts/ad_style/sticky-buttons/sticky', 'buttons');
	}
?> 
</div>
<?php get_template_part( 'template-parts/layouts/ad_style/message', 'seller' ); ?>