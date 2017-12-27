<?php
if (!class_exists('ST_Inbox')) {
    class ST_Inbox
    {
        static $_inst = FALSE;
        function __construct()
        {

            add_action('wp_ajax_send_message_partner', array($this, '_send_message_partner'));

            add_action('wp_ajax_inbox_reply_message', array($this, '_reply_message'));

            add_action('wp_ajax_check_inbox_notification', array($this, '_check_inbox_notification'));
            add_action('wp_ajax_nopriv_check_inbox_notification', array($this, '_check_inbox_notification'));

            add_action('wp_ajax_inbox_get_last_message', array($this, '_inbox_get_last_message'));

            add_action('wp_ajax_inbox_remove_message', array($this, '_inbox_remove_message'));

            add_action('st_after_send_message_inbox', array($this, '_hadding_email'), 13, 2);

        }

        function _inbox_remove_message(){
            $message_id = STInput::post('message_id');
            $user_id = get_current_user_id();
            $total = ST_Inbox_Admin::inst()->remove_messages($message_id,$user_id);
            $res = array(
                'total_message' => $total
            );
            wp_send_json($res);
        }

        function _send_message_partner(){

            $post_id = STInput::post('id');
            $to_user = STInput::post('to_user');
            $title = STInput::post('title');
            $message = STInput::post('message');
            $res = array(
                'status' => 0
            );

            $validator = new STValidate();
            $validator->set_rules( 'title', __( "Title", ST_TEXTDOMAIN ), 'required|min_length[6]|max_length[100]' );
            $validator->set_rules( 'message', __( "Message", ST_TEXTDOMAIN ), 'required|min_length[6]|max_length[200]' );
            $result = $validator->run();
            if ( !$result ) {
                $res = array(
                    'status' => 0,
                    'message'=>$validator->error_string()
                );
            }else{
                if ( wp_verify_nonce( STInput::request( 'st_send_message', '' ), 'user_setting' ) ) {
                    if(!empty($title) && !empty($message) && !empty($to_user) and wp_verify_nonce( STInput::request( 'st_send_message' ), 'user_setting' )){
                        $send = ST_Inbox_Admin::inst()->send_message($to_user,$title,$message,$post_id);
                        if($send){
                            $user_link = get_permalink( st()->get_option( 'page_my_account_dashboard' ) );
                            $url =  TravelHelper::get_user_dashboared_link($user_link, 'inbox');
                            $url = add_query_arg(array('message_id'=>$send ), $url);
                            $res['status'] = 1;
                            $res['link_detail'] = $url;
                            do_action('st_after_send_message_inbox', $to_user, $send);
                        }
                    }

                }
            }
            wp_send_json($res);

        }

        function _reply_message(){
            $post_id = STInput::post('post_id');
            $to_user = STInput::post('to_user');
            $message = STInput::post('content');
            $parent_id = STInput::post('parent_id');
            $title = '';

            $res = array(
                'status' => 0
            );

            if(!empty($message) && !empty($to_user)){
                $send = ST_Inbox_Admin::inst()->send_message($to_user,$title,$message,$post_id,$parent_id);
                if($send){
                    $res['status'] = 1;
                    $res['data'] = array(
                        'content' => nl2br(stripslashes(ST_Inbox_Admin::inst()->find_link($message))),
                        'created_at' => esc_html__('just now',ST_TEXTDOMAIN),
                        'avatar' => get_avatar(get_current_user_id(),50),
                        'username' => ST_Inbox_Admin::inst()->get_user_by('id',get_current_user_id(), 'user_nicename' )
                    );
                    //do_action('st_after_send_message_inbox', $to_user, $send);
                }
            }

            wp_send_json($res);
        }

        function _inbox_get_last_message(){
            $post_id = STInput::post('post_id');
            $parent_id = STInput::post('message_id');
            $last_message_id = STInput::post('last_message_id');
            $user_id = STInput::post('user_id');
            $data = ST_Inbox_Admin::inst()->get_last_message($parent_id,$last_message_id,$user_id);
            $data_res = array();
            if(!empty($data)){
                foreach($data as $k=>$v){
                    $data_res[] =  array(
                        'id' => $v['id'],
                        'content' => nl2br(stripslashes(ST_Inbox_Admin::inst()->find_link($v['content']))),
                        'created_at' => esc_html__('just now',ST_TEXTDOMAIN),
                        'avatar' => get_avatar($user_id,50),
                        'username' => ST_Inbox_Admin::inst()->get_user_by('id',$user_id, 'user_nicename' )
                    );
                }
            }
            ST_Inbox_Admin::inst()->masked_as_read($parent_id);
            wp_send_json($data_res);
        }

        function _check_inbox_notification(){
            $res = array(
                'status' => 0
            );
            $count = ST_Inbox_Admin::inst()->check_new_message();
            if(!empty($count)){
                $user_link = get_permalink( st()->get_option( 'page_my_account_dashboard' ) );
                $url =  TravelHelper::get_user_dashboared_link($user_link, 'inbox');
                $res['status'] = 1;
                $res['new_message'] = $count;
                $res['message'] = sprintf(_n('You have %s new message', 'You have %s new messages', $count, ST_TEXTDOMAIN), $count);
                $res['inbox_link'] = $url;
                if(!empty($old_count = $_SESSION['new_count'])){
                    $res['old_count'] = $old_count;
                }
                $_SESSION['new_count'] = $count;
            }else{
                $_SESSION['new_count'] = false;
            }
            $enable_inbox = st()->get_option('enable_inbox');
            if($enable_inbox != 'on'){
                $res['status'] = 0;
            }
            wp_send_json($res);
        }

        function _hadding_email($user_id, $message_id){
            $user_data = get_userdata($user_id);
            $email_partner = st()->get_option('enable_send_email_partner');
            $email_user = st()->get_option('enable_send_email_buyer');
            if(!empty($user_data)){
                $user = new STUser_f();
                if($user::_is_user_partner($user_id)){
                    if($email_partner == "on"){
                        $this->_send_email($user_data, $message_id);
                    }
                }else{
                    if($email_user == "on"){
                        $this->_send_email($user_data, $message_id);
                    }
                }

            }
        }
        function _send_email($user_data, $message_id){
            $to = $user_data->user_email;
            $current_user_data = get_userdata(get_current_user_id());
            $data_message = ST_Inbox_Admin::inst()->get_message($message_id);
            $subject = '';
            $content = '';
            if(!empty($data_message)) {
                $user_link = get_permalink( st()->get_option( 'page_my_account_dashboard' ) );
                $url =  TravelHelper::get_user_dashboared_link($user_link, 'inbox');
                if(!empty($data_message['is_parent'])) {
                    $url = add_query_arg(array('message_id' => $data_message['is_parent']), $url);
                }else{
                    $url = add_query_arg(array('message_id' => $message_id), $url);
                }
                $blogname = get_bloginfo('name');
                $subject = esc_html__('[New Message]You have a new message from ', ST_TEXTDOMAIN).esc_attr($blogname);
                $service_html = '';
                if(!empty($data_message['post_id'])){
                    $service_html = esc_html__('to ',ST_TEXTDOMAIN).get_the_title($data_message['post_id']);
                }
                $content .= '<div class="content">
                                        <div class="content-header">
                                            <h3 class="title">' . esc_html__("Your have a new message", "wpbooking-partner") . '</h3>
                                        </div>
                                        <div class="content-center">
                                            <p>'.esc_html__('Hello ',ST_TEXTDOMAIN).'<b>'.$user_data->user_login.'</b>'.'</p>
                                            <p>'.sprintf(esc_html__('You have a message from %s %s with the content:',ST_TEXTDOMAIN), $current_user_data->user_login, $service_html).'</p>
                                            <p><b>'.nl2br(stripslashes($data_message['content'])).'</b></p>';
                $content .= '<p>'.esc_html__('you can answer for them ',ST_TEXTDOMAIN).'<a target="_blank" href="'.esc_url($url).'">'.esc_html__('here',ST_TEXTDOMAIN).'</a>'.esc_html__(' or going to the section message administration to follow.').'</p>';
                $content .= '<div class="content-footer">';
                $content .= '<a class="btn btn-default" href="'.esc_url($url).'">'.esc_html__('Message Management',ST_TEXTDOMAIN).'</a>';
                $content .=             '</div></div>
                                    </div>';
            }
            $content = do_shortcode($content);
            $this->_send($to, $subject, $content);
        }

        function _send( $to, $subject, $message, $attachment = false )
        {

            if ( !$message ) return [
                'status'  => false,
                'data'    => '',
                'message' => __( "Email content is empty", ST_TEXTDOMAIN )
            ];
            $from = st()->get_option( 'email_from' );
            $from_address = st()->get_option( 'email_from_address' );
            $headers = [];

            if ( $from and $from_address ) {
                $headers[] = 'From:' . $from . ' <' . $from_address . '>';
            }

            add_filter( 'wp_mail_content_type', [ __CLASS__, 'set_html_content_type' ] );

            $check = @wp_mail( $to, $subject, $message, $headers, $attachment );

            remove_filter( 'wp_mail_content_type', [ __CLASS__, 'set_html_content_type' ] );

            return [
                'status' => $check,
                'data'   => [
                    'to'      => $to,
                    'subject' => $subject,
                    'message' => $message,
                    'headers' => $headers
                ]
            ];
        }
        static function set_html_content_type()
        {
            return 'text/html';
        }
        static function inst(){
            if(!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }
    ST_Inbox::inst();
}