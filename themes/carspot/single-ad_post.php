<?php get_header(); ?>
<?php global $carspot_theme; ?>
<?php 
/* Only need on this page so inluded here don't want to increase page size for optimizaion by adding extra scripts in all the web*/
wp_enqueue_script( 'carspot-search');
if ( have_posts() )
{ 
    while ( have_posts() )
    { the_post();
		$aid	=	get_the_ID();
		// Make expired to featured ad
		if( get_post_meta($aid, '_carspot_is_feature', true ) == '1' && $carspot_theme['featured_expiry'] != '-1' )
		{
			if(isset( $carspot_theme['featured_expiry'] ) &&  $carspot_theme['featured_expiry'] != '-1' )
			{
				$now = time(); // or your date as well
				$featured_date	= strtotime(get_post_meta( $aid, '_carspot_is_feature_date', true ));
	
				$featured_days	= carspot_days_diff( $now, $featured_date );
				$expiry_days	=	$carspot_theme['featured_expiry'];
				if( $featured_days > $expiry_days )
				{
					update_post_meta( $aid, '_carspot_is_feature', 0 );
				}
			}
		}
		
		carspot_setPostViews( $aid );
		if($carspot_theme['single_ad_style'] == "1")
		{
			get_template_part( 'template-parts/layouts/ad_style/style', '1');
		}
		else
		{
			get_template_part( 'template-parts/layouts/ad_style/style', '2');	
		}
		
	}
}
else
{
    get_template_part( 'template-parts/content', 'none' );
}
get_footer(); ?>