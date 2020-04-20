<?php global $carspot_theme; 
$author_id = get_query_var( 'author' );

if($author_id == "" && is_page_template('page-dealer-reviews.php'))
{
	$author_id = $_GET["user_id"];
}
if(isset($_GET['page-type']) && $_GET['page-type']=="my_ratings") 
{
	$author_id = get_current_user_id();
}
$current_user_id = get_current_user_id();
$comment_count = carspot_dealer_review_count($author_id);
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$limit = $carspot_theme['sb_reviews_count_limit'];

if(isset($limit) && $limit !="")
 {
	 $pages = ceil($comment_count)/$limit;
 }

				$args = array(
					'user_id' => $author_id,
					'type' => 'dealer_review',
					'order'=> 'DESC',
					'paged' => $paged,
					'number'	=> $limit,
				);
				
			$get_rating = get_comments( $args );
			if( count((array) $get_rating ) > 0 )
				{ ?>

<div class="profile-review-section">
  <?php
                        foreach( $get_rating as $get_ratings )
                        {
                            $comment_ids = $get_ratings->comment_ID;
                            
                            $service_stars = get_comment_meta($comment_ids, '_rating_service', true);
                            $process_stars = get_comment_meta($comment_ids, '_rating_proces', true);
                            $selection_stars = get_comment_meta($comment_ids, '_rating_selection', true);
                            
                            $single_avg = 0;
                            $total_stars = 	$service_stars+$process_stars+$selection_stars;
                            $single_avg		=  round($total_stars/"3", 1);
                            //echo  $single_avg;
                            ?>
  <div class="review-box">
    <div class="main-review">
      <div class="review-avg-rating"> <span> <?php echo  $single_avg ?></span>
        <ul class="">
          <?php 
                                            for( $i = 1; $i<=5; $i++ )
                                            {
                                                if( $i <= $single_avg )
                                                    echo '<li class="star colored-star"><i class="fa fa-star"></i></li>';
                                                else
                                                    echo '<li class="star"><i class="fa fa-star"></i></li>';	
                                            }
                                            ?>
        </ul>
      </div>
      <div class="review-content-box">
        <div class="review-content-meta">
          <div class="review-author-name">
            <h4><?php echo strip_tags_content(get_comment_meta($comment_ids, '_rating_title', true)); ?></h4>
          </div>
          <div class="review-text-box">
            <p><?php echo strip_tags_content($get_ratings->comment_content); ?></p>
          </div>
          <div class="review-date"> <span class="user-profile"> <a href="<?php echo esc_url(get_author_posts_url($get_ratings->comment_post_ID)); ?>">
            <?php 
                                                        $comment_poster = get_userdata($get_ratings->comment_post_ID);
                                                        echo esc_html($comment_poster->display_name);
                                                     ?>
            </a>
            <?php 
													$recomment = get_comment_meta($comment_ids, '_rating_recommand', true);
													if($recomment != '')
													{
													echo esc_html__('has', 'carspot' ); 
													
												?>
            <span class="recommend">
            <?php 
                                                            
                                                            if($recomment =='yes' )
                                                            {
                                                                echo esc_html__('Recommended', 'carspot' );
                                                            }
                                                            else
                                                            {
                                                                echo esc_html__('Not Recommended', 'carspot' );
                                                            }
                                                            ?>
            </span>
            <?php 
                                                            echo esc_html__('this vendor on', 'carspot' );
													}
														?>
            </span> <span><?php echo date_i18n( get_option( 'date_format' ), strtotime( $get_ratings->comment_date ) ); ?></span>
            <?php 
              if(!get_comment_meta($comment_ids, '_rating_reply', true))
              {
                  if(isset($_GET['page-type']) && $_GET['page-type']=="my_ratings")
                  {
              ?>
<span class="review-toggle-angle collapsed" data-toggle="collapse" data-target="#review-<?php echo ($comment_ids); ?>"> <?php echo esc_html__('Reply', 'carspot' ); ?> </span>
<?php 
                  }
              } 
              ?>
          </div>
        </div>
        <div class="rating-stars-box">
          <div class="rating-stars">
            <label>
              <?php 
                if( isset( $carspot_theme['sb_first_rating_stars_title'] ) && $carspot_theme['sb_first_rating_stars_title'] )
                {
                    echo esc_html($carspot_theme['sb_first_rating_stars_title']);
                }
            ?>
            </label>
            <ul>
              <?php 
                                            for( $i = 1; $i<=5; $i++ )
                                            {
                                                if( $i <= $service_stars )
                                                    echo '<li class="star colored-star"><i class="fa fa-star"></i></li>';
                                                else
                                                    echo '<li class="star"><i class="fa fa-star"></i></li>';	
                                            }
                                            ?>
            </ul>
          </div>
          <div class="rating-stars">
            <label>
              <?php 
                                                if( isset( $carspot_theme['sb_second_rating_stars_title'] ) && $carspot_theme['sb_second_rating_stars_title'] )
                                                {
                                                    echo esc_html($carspot_theme['sb_second_rating_stars_title']);
                                                }
                                            ?>
            </label>
            <ul>
              <?php 
                                            for( $i = 1; $i<=5; $i++ )
                                            {
                                                if( $i <= $process_stars )
                                                    echo '<li class="star colored-star"><i class="fa fa-star"></i></li>';
                                                else
                                                    echo '<li class="star"><i class="fa fa-star"></i></li>';	
                                            }
                                            ?>
            </ul>
          </div>
          <div class="rating-stars">
            <label>
              <?php 
                                                if( isset( $carspot_theme['sb_third_rating_stars_title'] ) && $carspot_theme['sb_third_rating_stars_title'] )
                                                {
                                                    echo esc_html($carspot_theme['sb_third_rating_stars_title']);
                                                }
                                            ?>
            </label>
            <ul>
              <?php 
                                            for( $i = 1; $i<=5; $i++ )
                                            {
                                                if( $i <= $selection_stars )
                                                    echo '<li class="star colored-star"><i class="fa fa-star"></i></li>';
                                                else
                                                    echo '<li class="star"><i class="fa fa-star"></i></li>';	
                                            }
                                            ?>
            </ul>
          </div>
        </div>
        <?php 
                                                if(get_comment_meta($comment_ids, '_rating_reply', true))
                                                {
                                                    ?>
        <div class="review-reply-messgae">
          <h5>
            <?php 
																if(isset($_GET['page-type']) && $_GET['page-type']=="my_ratings")
																{
																	echo esc_html__('Your reply:', 'carspot' ); 
																}
																else
																{
																	echo esc_html__('Dealer Reply:', 'carspot' );
																}
															?>
          </h5>
          <p class="profile-review-reply"> <?php echo esc_html(get_comment_meta($comment_ids, '_rating_reply', true)); ?></p>
        </div>
        <?php 	
                                                }
												?>
      </div>
    </div>
    <?php 
								if(isset($_GET['page-type']) && $_GET['page-type']=="my_ratings")
								{
									if(!get_comment_meta($comment_ids, '_rating_reply', true))
									{
										?>
    <div class="profile-review-reply-box collapse" id="review-<?php echo ($comment_ids); ?>" >
      <form  class="sb-reply-rating-form" <?php if(isset($carspot_theme['sb_demo_mode']) && $carspot_theme['sb_demo_mode'] != true){ ?> data-commentid="<?php echo ($comment_ids); ?>" <?php } ?>>
        <div class="form-group">
          <textarea name="review-reply" class="form-control review-reply-<?php echo ($comment_ids); ?>" placeholder="<?php echo esc_html__('Your reply to this review ','carspot'); ?>" cols="20" rows="3" id="review-reply" data-parsley-required="true" data-parsley-error-message="<?php echo esc_html__( 'This field is required.', 'carspot' ); ?>"></textarea>
        </div>
        <?php
											if(isset($carspot_theme['sb_demo_mode']) && $carspot_theme['sb_demo_mode'] == true)
											{
											?>
        <input type="submit" class="btn btn-theme validate-review-form" value="Submit Reply " id="validate-review-form" disabled>
        <?php
											}
											else
											{
											?>
        <input type="submit" class="btn btn-theme validate-review-form" value="Submit Reply" id="validate-review-form">
        <input type="hidden" name="comment_id" value="<?php echo ($comment_ids); ?>" />
        <input type="hidden" id="rating_reply_nonce" value="<?php echo wp_create_nonce('carspot_rating_reply_secure') ?>"  />
        <?php
											}
											?>
      </form>
    </div>
    <?php 
									}
								}
								?>
  </div>
  <?php 
                        }
						?>
</div>
<?php 
					if(isset($_GET['page-type']) && $_GET['page-type']=="my_ratings")
					{
						?>
<div class="rating-pagination pagination-lg"> <?php echo review_pagination( $pages, $paged ); ?> </div>
<?php 
					}
					else
					{
						if($comment_count > $limit)
						{
							if(isset($_GET["user_id"]) && $_GET["user_id"] != "")
							{
							?>
<div class="rating-pagination pagination-lg"> <?php echo review_pagination( $pages, $paged ); ?> </div>
<?php
							}
							else
							{
								?>
<div class="extra-button"> <a href="<?php echo esc_url(get_the_permalink( $carspot_theme['dealer_reviews']));?>?user_id=<?php echo esc_attr($author_id); ?>" class="btn btn-theme btn-block">
    <?php echo esc_html__('Read all reviews', 'carspot' ); ?>
  </a> </div>
<?php
							}
						}	
					}							  
				}
				else
				{ ?>
<div class="alert custom-alert custom-alert-info" role="alert">
  <div class="custom-alert_top-side"> <span class="alert-icon custom-alert_icon la la-info-circle"></span>
    <div class="custom-alert_body">
      <h6 class="custom-alert_heading"> <?php echo esc_html__('No Review Available. ','carspot'); ?> </h6>
      <div class="custom-alert_content">
        <?php 
						  		if(isset($_GET['page-type']) && $_GET['page-type']=="my_ratings") 
								{ 
									echo esc_html__('Your reviews will be visible here. ','carspot');
								}
								else
								{
									echo esc_html__('Be the first one to post a review. ','carspot');
								}
						  ?>
      </div>
    </div>
  </div>
</div>
<?php } ?>