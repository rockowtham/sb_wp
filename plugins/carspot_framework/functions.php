<?php

function carspot_add_code($id, $func) {
    add_shortcode($id, $func);
}

function carspot_decode($html) {
    return base64_decode($html);
}

// Ajax handler for add to cart
add_action('wp_ajax_sb_mailchimp_subcribe', 'carspot_mailchimp_subcribe');
add_action('wp_ajax_nopriv_sb_mailchimp_subcribe', 'carspot_mailchimp_subcribe');

// Addind Subcriber into Mailchimp
function carspot_mailchimp_subcribe() {
    global $carspot_theme;

    //$apiKey = '97da4834058c44cd770dbbdbab0c5730-us14';
    // $listid = '9b80a80904';
    $sb_action = $_POST['sb_action'];

    $apiKey = $carspot_theme['mailchimp_api_key'];

    if ($sb_action == 'coming_soon')
        $listid = $carspot_theme['mailchimp_notify_list_id'];
    if ($sb_action == 'footer_action')
        $listid = $carspot_theme['mailchimp_footer_list_id'];


    // Getting value from form
    $email = $_POST['sb_email'];
    $fname = '';
    $lname = '';

    // MailChimp API URL
    $memberID = md5(strtolower($email));
    $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listid . '/members/' . $memberID;

    // member information
    $json = json_encode(array(
        'email_address' => $email,
        'status' => 'subscribed',
        'merge_fields' => array(
            'FNAME' => $fname,
            'LNAME' => $lname
        )
    ));

    // send a HTTP POST request with curl
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // store the status message based on response code
    $mcdata = json_decode($result);
    if (!empty($mcdata->error)) {
        echo 0;
    } else {
        echo 1;
    }
    die();
}

// Report Ad
add_action('wp_ajax_sb_report_ad', 'carspot_sb_report_ad');
add_action('wp_ajax_nopriv_sb_report_ad', 'carspot_sb_report_ad');

function carspot_sb_report_ad() {
	check_ajax_referer( 'carspot_report_secure', 'security' );
    carspot_authenticate_check();

    global $carspot_theme;
    $ad_id = $_POST['ad_id'];
    $option = $_POST['option'];
    $comments = $_POST['comments'];
    if (get_post_meta($ad_id, '_sb_user_id_' . get_current_user_id(), true) == get_current_user_id()) {
        echo '0|' . __("You have reported already.", 'redux-framework');
    } else {
        update_post_meta($ad_id, '_sb_user_id_' . get_current_user_id(), get_current_user_id());
        update_post_meta($ad_id, '_sb_report_option_' . get_current_user_id(), $option);
        update_post_meta($ad_id, '_sb_report_comments_' . get_current_user_id(), $comments);

        $count = get_post_meta($ad_id, '_sb_count_report', true);
        $count = $count + 1;
        update_post_meta($ad_id, '_sb_count_report', $count);
        if ($count >= $carspot_theme['report_limit']) {
            if ($carspot_theme['report_action'] == '1') {
                $my_post = array(
                    'ID' => $ad_id,
                    'post_status' => 'pending',
                );

                wp_update_post($my_post);
            } else {
                // Sending email
                $to = $carspot_theme['report_email'];
                $subject = __('Ad Reported', 'redux-framework');
                $body = '<html><body><p>' . __('Users reported this ad, please check it. ', 'redux-framework') . '<a href="' . get_the_permalink($ad_id) . '">' . get_the_title($ad_id) . '</a></p></body></html>';

                $from = get_bloginfo('name');
                if (isset($carspot_theme['sb_report_ad_from']) && $carspot_theme['sb_report_ad_from'] != "") {
                    $from = $carspot_theme['sb_report_ad_from'];
                }
                $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
                if (isset($carspot_theme['sb_report_ad_message']) && $carspot_theme['sb_report_ad_message'] != "") {


                    $subject_keywords = array('%site_name%', '%ad_title%');
                    $subject_replaces = array(get_bloginfo('name'), get_the_title($ad_id));

                    $subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_report_ad_subject']);

                    $author_id = get_post_field('post_author', $ad_id);
                    $user_info = get_userdata($author_id);

                    $msg_keywords = array('%site_name%', '%ad_title%', '%ad_link%', '%ad_owner%');
                    $msg_replaces = array(get_bloginfo('name'), get_the_title($ad_id), get_the_permalink($ad_id), $user_info->display_name);

                    $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_report_ad_message']);
                }
                wp_mail($to, $subject, $body, $headers);
            }
        }

        echo '1|' . __("Reported successfully.", 'redux-framework');
    }

    die();
}

// Send message to ad owner
add_action('wp_ajax_sb_send_message', 'carspot_send_message');

function carspot_send_message() {
	check_ajax_referer( 'carspot_message_secure', 'security' );
    carspot_authenticate_check();
    // Getting values
    $params = array();
    parse_str($_POST['sb_data'], $params);

    $time = current_time('mysql');


    $data = array(
        'comment_post_ID' => sanitize_text_field($params['ad_post_id']),
        'comment_author' => sanitize_text_field($params['name']),
        'comment_author_email' => sanitize_text_field($params['email']),
        'comment_author_url' => '',
        'comment_content' => sanitize_text_field($params['message']),
        'comment_type' => 'ad_post',
        'comment_parent' => sanitize_text_field($params['usr_id']),
        'user_id' => get_current_user_id(),
        'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
        'comment_date' => $time,
        'comment_approved' => 1,
    );

    global $carspot_theme;
    if ($carspot_theme['sb_send_email_on_message']) {
        $author_obj = get_user_by('id', $params['msg_receiver_id']);
        $to = $author_obj->user_email;
        $subject = __('New Message', 'redux-framework');
        $body = '<html><body><p>' . __('Got new message on ad', 'redux-framework') . ' ' . get_the_title($params['ad_post_id']) . '</p><p>' . $params['message'] . '</p></body></html>';
        $from = get_bloginfo('name');
        if (isset($carspot_theme['sb_message_from_on_new_ad']) && $carspot_theme['sb_message_from_on_new_ad'] != "") {
            $from = $carspot_theme['sb_message_from_on_new_ad'];
        }
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($carspot_theme['sb_message_on_new_ad']) && $carspot_theme['sb_message_on_new_ad'] != "") {


            $subject_keywords = array('%site_name%', '%ad_title%');
            $subject_replaces = array(get_bloginfo('name'), get_the_title($params['ad_post_id']));

            $subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_message_subject_on_new_ad']);

            $msg_keywords = array('%site_name%', '%ad_title%', '%ad_link%', '%message%', '%sender_name%');
            $msg_replaces = array(get_bloginfo('name'), get_the_title($params['ad_post_id']), get_the_permalink($params['ad_post_id']), $params['message'], $params['name']);

            $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_message_on_new_ad']);
        }
        wp_mail($to, $subject, $body, $headers);
    }
    $comment_id = wp_insert_comment($data);
    if ($comment_id) {
        update_comment_meta($params['msg_receiver_id'], $params['ad_post_id'] . "_" . get_current_user_id(), 0);
        echo '1|' . __("Message sent successfully .", 'redux-framework');
    } else {
        echo '0|' . __("Message not sent, please try again later.", 'redux-framework');
    }
    die();
}

// Ajax handler for Forgot Password
add_action('wp_ajax_sb_forgot_password', 'carspot_forgot_password');
add_action('wp_ajax_nopriv_sb_forgot_password', 'carspot_forgot_password');

// Forgot Password
function carspot_forgot_password() {
	check_ajax_referer( 'carspot_forget_pwd_secure', 'security' );
    global $carspot_theme;
    // Getting values
    $params = array();
    parse_str($_POST['sb_data'], $params);
    $email = $params['sb_forgot_email'];
    if (email_exists($email) == true) {
        $from = get_bloginfo('name');
        if (isset($carspot_theme['sb_forgot_password_from']) && $carspot_theme['sb_forgot_password_from'] != "") {
            $from = $carspot_theme['sb_forgot_password_from'];
        }
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($carspot_theme['sb_forgot_password_message']) && $carspot_theme['sb_forgot_password_message'] != "") {
            $subject_keywords = array('%site_name%');
            $subject_replaces = array(get_bloginfo('name'));
            $subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_forgot_password_subject']);
            $token = carspot_randomString(50);
            $user = get_user_by('email', $email);
            $msg_keywords = array('%site_name%', '%user%', '%reset_link%');
            $reset_link = trailingslashit(get_home_url()) . '?token=' . $token . '-sb-uid-' . $user->ID;
            $msg_replaces = array(get_bloginfo('name'), $user->display_name, $reset_link);

            $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_forgot_password_message']);

            $to = $email;
            $mail = wp_mail($to, $subject, $body, $headers);
            if ($mail) {
                update_user_meta($user->ID, 'sb_password_forget_token', $token);
                echo "1";
            } else {
                echo esc_html__('Email server not responding', 'redux-framework');
            }
        }
    } else {
        echo esc_html__('Email is not resgistered with us.', 'redux-framework');
    }
    die();
}

function carspot_get_notify_on_ad_post($pid) {
    global $carspot_theme;
    if (isset($carspot_theme['sb_send_email_on_ad_post']) && $carspot_theme['sb_send_email_on_ad_post']) {
        $to = $carspot_theme['ad_post_email_value'];
        $subject = __('New Ad', 'redux-framework') . '-' . get_bloginfo('name');
        $body = '<html><body><p>' . __('Got new ad', 'redux-framework') . ' <a href="' . get_edit_post_link($pid) . '">' . get_the_title($pid) . '</a></p></body></html>';
        $from = get_bloginfo('name');
        if (isset($carspot_theme['sb_msg_from_on_new_ad']) && $carspot_theme['sb_msg_from_on_new_ad'] != "") {
            $from = $carspot_theme['sb_msg_from_on_new_ad'];
        }
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($carspot_theme['sb_msg_on_new_ad']) && $carspot_theme['sb_msg_on_new_ad'] != "") {

            $author_id = get_post_field('post_author', $pid);
            $user_info = get_userdata($author_id);

            $subject_keywords = array('%site_name%', '%ad_owner%', '%ad_title%');
            $subject_replaces = array(get_bloginfo('name'), $user_info->display_name, get_the_title($pid));

            $subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_msg_subject_on_new_ad']);

            $msg_keywords = array('%site_name%', '%ad_owner%', '%ad_title%', '%ad_link%');
            $msg_replaces = array(get_bloginfo('name'), $user_info->display_name, get_the_title($pid), get_edit_post_link($pid));

            $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_msg_on_new_ad']);
        }
        wp_mail($to, $subject, $body, $headers);
    }
}

function carspot_send_email_new_rating($sender_id, $receiver_id, $rating = '', $comments = '') {
    global $carspot_theme;
    $receiver_info = get_userdata($receiver_id);
    $to = $receiver_info->user_email;
    $subject = __('New Rating', 'redux-framework') . '-' . get_bloginfo('name');


    $body = '<html><body><p>' . __('Got new Rating', 'redux-framework') . ' <a href="' . get_author_posts_url($receiver_id) . '">' . get_author_posts_url($receiver_id) . '</a></p></body></html>';
    $from = get_bloginfo('name');

    if (isset($carspot_theme['sb_new_rating_from']) && $carspot_theme['sb_new_rating_from'] != "") {
        $from = $carspot_theme['sb_new_rating_from'];
    }
    $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
    if (isset($carspot_theme['sb_new_rating_message']) && $carspot_theme['sb_new_rating_message'] != "") {
        $subject_keywords = array('%site_name%');
        $subject_replaces = array(get_bloginfo('name'));

        $subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_new_rating_subject']);

        // Rator info
        $sender_info = get_userdata($sender_id);

        $msg_keywords = array('%site_name%', '%receiver%', '%rator%', '%rating%', '%comments%', '%rating_link%');
        $msg_replaces = array(get_bloginfo('name'), $receiver_info->display_name, $sender_info->display_name, $rating, $comments, get_author_posts_url($receiver_id));

        $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_new_rating_message']);
    }
    wp_mail($to, $subject, $body, $headers);
}

function carspot_send_email_new_bid($sender_id, $receiver_id, $bid = '', $comments = '', $aid) {
    global $carspot_theme;
    $receiver_info = get_userdata($receiver_id);
    $to = $receiver_info->user_email;
    $from = '';
    if (isset($carspot_theme['sb_new_bid_from']) && $carspot_theme['sb_new_bid_from'] != "") {
        $from = $carspot_theme['sb_new_bid_from'];
    }
    $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
    if (isset($carspot_theme['sb_new_bid_message']) && $carspot_theme['sb_new_bid_message'] != "") {



        $subject_keywords = array('%site_name%');
        $subject_replaces = array(get_bloginfo('name'));

        $subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_new_bid_subject']);

        // Bidder info
        $sender_info = get_userdata($sender_id);

        $msg_keywords = array('%site_name%', '%receiver%', '%bidder%', '%bid%', '%comments%', '%bid_link%');
        $msg_replaces = array(get_bloginfo('name'), $receiver_info->display_name, $sender_info->display_name, $bid, $comments, get_the_permalink($aid) . '#tab1default');

        $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_new_bid_message']);
        wp_mail($to, $subject, $body, $headers);
    }
}

function carspot_social_share() {
    // check if plugin addtoany actiavted then load that otherwise builtin function
    if (in_array('add-to-any/add-to-any.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        return do_shortcode('[addtoany]');
    }

    // Get current page URL 
    $sbURL = esc_url(get_permalink());

    // Get current page title
    $sbTitle = str_replace(' ', '%20', esc_html(get_the_title()));

    // Get Post Thumbnail for pinterest
    $sbThumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(esc_html(get_the_ID())), 'sb-single-blog-featured');

    // Construct sharing URL without using any script
    $twitterURL = 'https://twitter.com/intent/tweet?text=' . $sbTitle . '&amp;url=' . $sbURL;
    $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $sbURL;
    $googleURL = 'https://plus.google.com/share?url=' . $sbURL;
    $bufferURL = 'https://bufferapp.com/add?url=' . $sbURL . '&amp;text=' . $sbTitle;

    // Based on popular demand added Pinterest too
    $pinterestURL = 'https://pinterest.com/pin/create/button/?url=' . $sbURL . '&amp;media=' . $sbThumbnail[0] . '&amp;description=' . $sbTitle;
    // Add sharing button at the end of page/page content
    return
            '<li><a href="' . esc_url($facebookURL) . '">' . __('Facebook', 'redux-framework') . ', </a></li>
	   <li><a href="' . esc_url($googleURL) . '">' . __('Google+', 'redux-framework') . ', </a></li>
	   <li><a href="' . esc_url($pinterestURL) . '">' . __('Pinterest', 'redux-framework') . ', </a></li>
	   <li><a href="' . esc_url($twitterURL) . '">' . __('Twitter', 'redux-framework') . '</a></li>';
}

function carspot_email_on_new_user($user_id, $social = '', $admin_email = true) {
    global $carspot_theme;

    if (isset($carspot_theme['sb_new_user_email_to_admin']) && $carspot_theme['sb_new_user_email_to_admin'] && $admin_email) {
        if (isset($carspot_theme['sb_new_user_admin_message']) && $carspot_theme['sb_new_user_admin_message'] != "" && isset($carspot_theme['sb_new_user_admin_message_from']) && $carspot_theme['sb_new_user_admin_message_from'] != "") {
            $to = get_option('admin_email');
            $subject = $carspot_theme['sb_new_user_admin_message_subject'];
            $from = $carspot_theme['sb_new_user_admin_message_from'];
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
            // User info
            $user_info = get_userdata($user_id);
            $msg_keywords = array('%site_name%', '%display_name%', '%email%');
            $msg_replaces = array(get_bloginfo('name'), $user_info->display_name, $user_info->user_email);
            $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_new_user_admin_message']);
            wp_mail($to, $subject, $body, $headers);
        }
    }

    if (isset($carspot_theme['sb_new_user_email_to_user']) && $carspot_theme['sb_new_user_email_to_user']) {
        if (isset($carspot_theme['sb_new_user_message']) && $carspot_theme['sb_new_user_message'] != "" && isset($carspot_theme['sb_new_user_message_from']) && $carspot_theme['sb_new_user_message_from'] != "") {
            // User info
            $user_info = get_userdata($user_id);
            $to = $user_info->user_email;
            $subject = $carspot_theme['sb_new_user_message_subject'];
            $from = $carspot_theme['sb_new_user_message_from'];
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
            $user_name = $user_info->user_email;
            if ($social != '')
                $user_name .= "(Password: $social )";
            $verification_link = '';
            if (isset($carspot_theme['sb_new_user_email_verification']) && $carspot_theme['sb_new_user_email_verification'] == '1') {
                $token = get_user_meta($user_id, 'sb_email_verification_token', true);
                if ($token == "") {
                    $token = carspot_randomString(50);
                }
                $verification_link = trailingslashit(get_the_permalink($carspot_theme['sb_sign_in_page'])) . '?verification_key=' . $token . '-sb-uid-' . $user_id;
                update_user_meta($user_id, 'sb_email_verification_token', $token);
            }

            $msg_keywords = array('%site_name%', '%user_name%', '%display_name%', '%verification_link%');
            $msg_replaces = array(get_bloginfo('name'), $user_name, $user_info->display_name, $verification_link);

            $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_new_user_message']);
            wp_mail($to, $subject, $body, $headers);
        }
    }
}

// Email on Ad approval
function carspot_get_notify_on_ad_approval($pid) {
    global $carspot_theme;
    $from = get_bloginfo('name');
    if (isset($carspot_theme['sb_active_ad_email_from']) && $carspot_theme['sb_active_ad_email_from'] != "") {
        $from = $carspot_theme['sb_active_ad_email_from'];
    }
    $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
    if (isset($carspot_theme['sb_active_ad_email_message']) && $carspot_theme['sb_active_ad_email_message'] != "") {

        $author_id = get_post_field('post_author', $pid);
        $user_info = get_userdata($author_id);
        $subject = $carspot_theme['sb_active_ad_email_subject'];
        $msg_keywords = array('%site_name%', '%user_name%', '%ad_title%', '%ad_link%');
        $msg_replaces = array(get_bloginfo('name'), $user_info->display_name, get_the_title($pid), get_the_permalink($pid));
        $to = $user_info->user_email;
        $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_active_ad_email_message']);
        wp_mail($to, $subject, $body, $headers);
    }
}

add_action('wp_ajax_sb_resend_email', 'adforest_resend_email');
add_action('wp_ajax_nopriv_sb_resend_email', 'adforest_resend_email');

function adforest_resend_email() {
    $email = $_POST['usr_email'];
    $user = get_user_by('email', $email);
    if (get_user_meta($user->ID, 'sb_resent_email', true) != 'yes') {

        carspot_email_on_new_user($user->ID, '', false);
        update_user_meta($user->ID, 'sb_resent_email', 'yes');
    }
    die();
}

/* TEST DRIVE EMAIL */

function carspot_send_email_test_drives($name, $email, $contact, $date_time, $message, $post_id) {
    global $carspot_theme;
    $to = "";
    if (isset($carspot_theme['test_drive_form_on']) && $carspot_theme['test_drive_form_on'] != '') {

        //$to = $carspot_theme['sb_test_drive_email_from'];
        $subject = __('Car test drive query', 'redux-framework') . '-' . get_the_title($post_id);
        $body = '<html><body><p>' . __('You have been contacted for test drive of your car', 'redux-framework') . ' <a href="' . get_the_permalink($post_id) . '">' . get_the_title($post_id) . '</a></p></body></html>';
        $from = get_bloginfo('name');
        if (isset($carspot_theme['sb_test_drive_email_from']) && $carspot_theme['sb_test_drive_email_from'] != "") {
            $from = $carspot_theme['sb_test_drive_email_from'];
        }

        $headers = array('Content-Type: text/html; charset=UTF-8', "From: $from");
        if (isset($carspot_theme['sb_test_drive_email_message']) && $carspot_theme['sb_test_drive_email_message'] != "") {
            $author_id = get_post_field('post_author', $pid);
            $user_info = get_userdata($author_id);
            $to = $user_info->user_email;

            $subject_keywords = array('%td_name%', '%td_email%', '%td_phone%', '%td_date_time%', '%td_msg%', '%td_ad_link%');
            $subject_replaces = array($name, $email, $contact, $date_time, $message, get_the_permalink($post_id));

            $subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_test_drive_email_subject']);

            $msg_keywords = array('%td_name%', '%td_email%', '%td_phone%', '%td_date_time%', '%td_msg%', '%td_ad_link%');
            $msg_replaces = array($name, $email, $contact, $date_time, $message, get_the_permalink($post_id));

            $body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_test_drive_email_message']);

            $sent_email = wp_mail($to, $subject, $body, $headers);
            if ($sent_email) {
                echo '1|' . __("Email sent to author successfully.", 'redux-framework');
                die();
            } else {
                echo '0|' . __("Something went wrong.", 'redux-framework');
                die();
            }
        }
    }
}

/*MAKE OFFER EMAIL*/

function carspot_send_email_make_offer($name, $email,$contact, $price, $message, $post_id)
{
	global $carspot_theme;
	$to ="";
	if( isset( $carspot_theme['make_offer_form_on'] ) && $carspot_theme['make_offer_form_on'] != '' )
	{
			$ad_post_author = get_post_field( 'post_author', $post_id );
			$to = get_the_author_meta( 'user_email', $ad_post_author );
			$subject = __('Someone make an offer for your ad', 'redux-framework') . '-' . get_the_title($post_id);
			$body = '<html><body><p>'.$name .__('You have been contacted for test drive of your car','redux-framework'). ' <a href="'.get_the_permalink($post_id).'">' . get_the_title($post_id) .'</a></p></body></html>';
			$from	=	get_bloginfo( 'name' );
			if( isset( $carspot_theme['sb_make_offer_email_from'] ) && $carspot_theme['sb_make_offer_email_from'] != "" )
			{
				$from	=	$carspot_theme['sb_make_offer_email_from'];
			}
			
				$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
			if( isset( $carspot_theme['sb_make_offer_email_message'] ) &&  $carspot_theme['sb_make_offer_email_message'] != "" )
			{
				$author_id = get_post_field ('post_author', $pid);
				$user_info = get_userdata($author_id);
				//$to = $user_info->user_email;
				
				$subject_keywords  = array('%mo_name%', '%mo_email%', '%mo_phone%', '%mo_price%', '%mo_msg%', '%mo_ad_link%'); 
				$subject_replaces  = array($name, $email, $contact, $price, $message, get_the_permalink($post_id));
				
				$subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_make_offer_email_subject']);
	
				$msg_keywords  = array('%mo_name%', '%mo_email%', '%mo_phone%', '%mo_price%', '%mo_msg%', '%mo_ad_link%'); 
				$msg_replaces  = array($name, $email, $contact, $price, $message, get_the_permalink($post_id));
				
				$body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_make_offer_email_message']);
			
				$sent_email = wp_mail( $to, $subject, $body, $headers );
				if($sent_email)
				{
					echo '1|'. __( "Email sent to author.", 'redux-framework' );
					die();
				}
				else
				{
					echo '0|'. __( "Something went wrong.", 'redux-framework' );
					die();
				}
			}
	}
}


/*DEALER CONTACT EMAIL*/

function carspot_send_email_dealer_contact($name, $email,$contact, $message, $dealer_id)
{
	global $carspot_theme;
	$to ="";
	if( isset( $carspot_theme['sb_dealer_contact'] ) && $carspot_theme['sb_dealer_contact'] != '' )
	{
			$to = get_the_author_meta( 'user_email', $dealer_id );
			$subject = '[' . get_bloginfo( 'name' ).'] - '.__('You have been contacted via profile on ', 'redux-framework').get_bloginfo( 'name' );
			$body = '<html><body><p>'.__('Someone has contacted you over ','redux-framework'). ''.get_bloginfo( 'name' ).' </a></p></body></html>';
			$from	=	get_bloginfo( 'name' );
			
			
			if( isset( $carspot_theme['sb_contact_dealer_email_from'] ) && $carspot_theme['sb_contact_dealer_email_from'] != "" )
			{
				$from	=	$carspot_theme['sb_contact_dealer_email_from'];
			}
			
				$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
			if( isset( $carspot_theme['sb_contact_dealer_email_message'] ) &&  $carspot_theme['sb_contact_dealer_email_message'] != "" )
			{
				//$author_id = get_post_field ('post_author', $pid);
				$user_info = get_userdata($dealer_id);
				$to = $user_info->user_email;
				
				$subject_keywords  = array('%dc_name%', '%dc_email%', '%dc_phone%', '%dc_msg%'); 
				$subject_replaces  = array($name, $email, $contact, $message);
				
				$subject = str_replace($subject_keywords, $subject_replaces, $carspot_theme['sb_contact_dealer_email_subject']);
	
				$msg_keywords  = array('%dc_name%', '%dc_email%', '%dc_phone%', '%dc_msg%'); 
				$msg_replaces  = array($name, $email, $contact, $message);
				
				$body = str_replace($msg_keywords, $msg_replaces, $carspot_theme['sb_contact_dealer_email_message']);
				$sent_email = wp_mail( $to, $subject, $body, $headers );
				if($sent_email)
				{
					echo '1|'. __( "Email sent", 'redux-framework' );
					die();
				}
				else
				{
					echo '0|'. __( "Something went wrong.", 'redux-framework' );
					die();
				}
			}
	}
}


