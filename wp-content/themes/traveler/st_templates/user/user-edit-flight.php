<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * User create tours
 *
 * Created by ShineTheme
 *
 */
    wp_enqueue_script( 'bootstrap-datepicker.js' ); 
    wp_enqueue_script( 'bootstrap-datepicker-lang.js' ); 
    wp_enqueue_script( 'user_upload.js' );
    wp_enqueue_script( 'flight-create.js' );

$post_id = STInput::request('id');
$title = $content = $excerpt = "";
if(!empty($post_id)){
    $post = get_post( $post_id );
    $title = $post->post_title;
    $content = $post->post_content;
    $excerpt = $post->post_excerpt;
}
$validator= STUser_f::$validator;

if(empty($post_id)){

    //=== Validate package
    $admin_packages = STAdminPackages::get_inst();
    $author = get_current_user_id();
    $count_item_publish = $admin_packages->count_item_can_public($author);
    if($admin_packages->enabled_membership() && $admin_packages->get_user_role() == 'partner'){
        if( $count_item_publish !== 'unlimited' && $count_item_publish<= 0){
            $user_link = get_permalink( );
            echo '<div class="alert alert-warning mt20">'. __('You can not create a new item. Your items can be created is ', ST_TEXTDOMAIN). $admin_packages->count_item_package($author) .'. '.'<a href="'.TravelHelper::get_user_dashboared_link($user_link, 'setting').'" target="_blank">'.__('More Details', ST_TEXTDOMAIN).'</a>'.'</div>';
            return false;
        }
    }

}

?>
<div class="st-create">
    <h2 class="pull-left">
        <?php if(!empty($post_id)){?>
            <?php _e("Edit Flight",ST_TEXTDOMAIN) ?>
        <?php }else{ ?>
            <?php _e("Add Flight  ",ST_TEXTDOMAIN) ?>
        <?php } ?>
    </h2>
</div>
<div class="msg">
    <?php echo STTemplate::message() ?>
    <?php echo STUser_f::get_msg(); ?>
    <?php echo STUser_f::get_control_data(); ?>
</div>
<form action="" method="post" enctype="multipart/form-data" id="st_form_add_partner">
    <?php wp_nonce_field('user_setting','st_update_post_flight'); ?>
    <div class="form-group form-group-icon-left">
        <label for="title" class="head_bol"><?php echo __('Name of flight', ST_TEXTDOMAIN); ?> <span class="text-small text-danger">*</span>:</label>
        <i class="fa  fa-file-text input-icon input-icon-hightlight"></i>
        <input id="title" name="st_title" type="text" placeholder="<?php echo __('Name of flight', ST_TEXTDOMAIN); ?>" class="form-control" value="<?php echo stripslashes(STInput::request("st_title",$title) ); ?>">
        <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('st_title'),'danger') ?></div>
    </div>

    <div class="tabbable tabs_partner">
        <ul class="nav nav-tabs" id="">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php _e("General Settings",ST_TEXTDOMAIN) ?></a></li>
            <li><a href="#tab-flight-time" data-toggle="tab"><?php _e("Flight Time",ST_TEXTDOMAIN) ?></a></li>
            <?php
            if(!empty($post_id)){
            ?>
            <li><a href="#availablility_tab" data-toggle="tab"><?php _e("Booking Details",ST_TEXTDOMAIN) ?></a></li>
            <?php } ?>
			<li><a href="#tab-tax-options" data-toggle="tab"><?php _e('Tax Options',ST_TEXTDOMAIN) ?></a></li>
            <?php $st_is_woocommerce_checkout=apply_filters('st_is_woocommerce_checkout',false);
            if(!$st_is_woocommerce_checkout):?>
                <li><a href="#tab-payment" data-toggle="tab"><?php _e("Payment Settings",ST_TEXTDOMAIN) ?></a></li>
            <?php endif ?>
            <?php $custom_field = st()->get_option( 'tours_unlimited_custom_field' );
            if(!empty( $custom_field ) and is_array( $custom_field )) { ?>
                <li><a href="#tab-custom-fields" data-toggle="tab"><?php _e("Custom Fields",ST_TEXTDOMAIN) ?></a></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab-general">
                <div class="row">
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left'>
                            <label for="airline"><?php _e( "Airline Company" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-plane input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('airline',get_post_meta( $post_id, 'airline', true ));
                            $args = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'airline' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airline' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args );
                            ?>
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('airline'),'danger') ?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
					<div class="col-md-6">
                        <div class='form-group form-group-icon-left'>
                            <label for="origin"><?php _e( "Origin" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-map-marker input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('origin', get_post_meta( $post_id, 'origin', true ));
                            $args_origin = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'origin' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airport' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args_origin );
                            ?>
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('origin'),'danger') ?></div>
                        </div>
					</div>
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left'>
                            <label for="destination"><?php _e( "Destination" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-map-marker input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('destination', get_post_meta( $post_id, 'destination', true ));
                            $args_des = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'destination' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airport' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args_des );
                            ?>
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('destination'),'danger') ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-flight-time">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-icon-left">
                            <label for="departure_time"><?php echo esc_html__('Departure time', ST_TEXTDOMAIN)?></label>
                            <?php
                            $departure_time = STInput::request('departure_time', get_post_meta($post_id, 'departure_time', true));
                            ?>
                            <i class="fa fa-clock-o input-icon input-icon-hightlight"></i>
                            <input id="departure_time" class="form-control st_timepicker" name="departure_time" value="<?php echo esc_attr($departure_time); ?>" />
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('departure_time'),'danger') ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group st-total-time">
                            <label for="departure_time"><?php echo esc_html__('Total time', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('total_time', get_post_meta($post_id, 'total_time', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="total_time[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="total_time[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('total_time'),'danger') ?></div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group st-condition-parent">
                            <label for="flight_type"><?php echo esc_html__('Flight Type', ST_TEXTDOMAIN)?></label>
                            <?php
                            $flight_type = STInput::request('flight_type', get_post_meta($post_id, 'flight_type', true));
                            ?>
                            <select class="form-control" name="flight_type" id="flight_type">
                                <option value="direct" <?php selected($flight_type, 'direct')?>><?php echo esc_html__('Direct', ST_TEXTDOMAIN); ?></option>
                                <option value="one_stop" <?php selected($flight_type, 'one_stop')?>><?php echo esc_html__('One stop', ST_TEXTDOMAIN); ?></option>
                                <option value="two_stops" <?php selected($flight_type, 'two_stops')?>><?php echo esc_html__('Two stops', ST_TEXTDOMAIN); ?></option>
                            </select>
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('flight_type'),'danger') ?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left condition-child' data-condition="flight_type:is(one_stop)">
                            <label for="airport_stop"><?php _e( "Stop Name" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-map-marker input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('airport_stop', get_post_meta( $post_id, 'airport_stop', true ));
                            $args_des = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'airport_stop' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airport' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args_des );
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left condition-child' data-condition="flight_type:is(one_stop)">
                            <label for="airport_stop"><?php _e( "Airline Company Stop" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-plane input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('airline_stop',get_post_meta( $post_id, 'airline_stop', true ));
                            $args = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'airline_stop' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airline' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args );
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group st-total-time condition-child" data-condition="flight_type:is(one_stop)">
                            <label for="arrival_stop"><?php echo esc_html__('Total time to stop', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('arrival_stop', get_post_meta($post_id, 'arrival_stop', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="arrival_stop[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="arrival_stop[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group st-total-time condition-child" data-condition="flight_type:is(one_stop)">
                            <label for="st_stopover_time"><?php echo esc_html__('Stopover time in stop', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('st_stopover_time', get_post_meta($post_id, 'st_stopover_time', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="st_stopover_time[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="st_stopover_time[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group st-total-time condition-child" data-condition="flight_type:is(one_stop)">
                            <label for="departure_stop"><?php echo esc_html__('Total time from stop to final destination', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('departure_stop', get_post_meta($post_id, 'departure_stop', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="departure_stop[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="departure_stop[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left condition-child' data-condition="flight_type:is(two_stops)">
                            <label for="airport_stop_1"><?php _e( "Name of stop 1" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-map-marker input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('airport_stop_1', get_post_meta( $post_id, 'airport_stop_1', true ));
                            $args_des = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'airport_stop_1' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airport' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args_des );
                            ?>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left condition-child' data-condition="flight_type:is(two_stops)">
                            <label for="airline_stop_1"><?php _e( "Airline Company Stop 1" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-plane input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('airline_stop_1',get_post_meta( $post_id, 'airline_stop_1', true ));
                            $args = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'airline_stop_1' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airline' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args );
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group st-total-time condition-child" data-condition="flight_type:is(two_stops)">
                            <label for="arrival_stop"><?php echo esc_html__('Total time to stop 1', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('arrival_stop_1', get_post_meta($post_id, 'arrival_stop_1', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="arrival_stop_1[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="arrival_stop_1[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group st-total-time condition-child" data-condition="flight_type:is(two_stops)">
                            <label for="st_stopover_time_1"><?php echo esc_html__('Stopover time in stop 1', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('st_stopover_time_1', get_post_meta($post_id, 'st_stopover_time_1', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="st_stopover_time_1[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="st_stopover_time_1[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left condition-child' data-condition="flight_type:is(two_stops)">
                            <label for="airport_stop_1"><?php _e( "Name of stop 2" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-map-marker input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('airport_stop_2', get_post_meta( $post_id, 'airport_stop_2', true ));
                            $args_des = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'airport_stop_2' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airport' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args_des );
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left condition-child' data-condition="flight_type:is(two_stops)">
                            <label for="airline_stop2"><?php _e( "Airline Company Stop 2" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-plane input-icon input-icon-hightlight"></i>
                            <?php
                            $value = STInput::request('airline_stop2',get_post_meta( $post_id, 'airline_stop2', true ));
                            $args = array(
                                'show_option_none' => 0,
                                'option_none_value' => '',
                                'hierarchical'      => 1 ,
                                'name'              => 'airline_stop2' ,
                                'class'             => 'form-control' ,
                                'id'             => '' ,
                                'taxonomy'          => 'st_airline' ,
                                'orderby' => 'name',
                                'selected' => $value,
                                'order' =>'ASC',
                                'hide_empty' => 0,
                            );
                            wp_dropdown_categories( $args );
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group st-total-time condition-child" data-condition="flight_type:is(two_stops)">
                            <label for="arrival_stop"><?php echo esc_html__('Total time from stop 1 to stop 2', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('arrival_stop_2', get_post_meta($post_id, 'arrival_stop_2', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="arrival_stop_2[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="arrival_stop_2[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group st-total-time condition-child" data-condition="flight_type:is(two_stops)">
                            <label for="st_stopover_time_2"><?php echo esc_html__('Stopover time in stop 2', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('st_stopover_time_2', get_post_meta($post_id, 'st_stopover_time_2', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="st_stopover_time_2[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="st_stopover_time_2[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group st-total-time condition-child" data-condition="flight_type:is(two_stops)">
                            <label for="departure_stop"><?php echo esc_html__('Total time from stop 2 to final destination', ST_TEXTDOMAIN)?></label>
                            <?php
                            $total_time = STInput::request('departure_stop_2', get_post_meta($post_id, 'departure_stop_2', true));
                            $hour = !empty($total_time['hour'])?$total_time['hour']:0;
                            $minute = !empty($total_time['minute'])?$total_time['minute']:0;
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td><?php echo esc_html__('Hour(s)', ST_TEXTDOMAIN);?></td>
                                    <td><?php echo esc_html__('Minute(s)', ST_TEXTDOMAIN); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="departure_stop_2[hour]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 48; $i++){
                                                    echo '<option value="'.$i.'" '.selected($hour,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="select-wrapper">
                                            <select name="departure_stop_2[minute]" class="form-control">
                                                <?php
                                                for($i = 0; $i <= 59; $i++){
                                                    echo '<option value="'.$i.'" '.selected($minute,$i, false).'>'.$i.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php  if(!empty($post_id)){ ?>
            <div class="tab-pane fade" id="availablility_tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-icon-left">
                            <label for="availability"><?php _e("Availability",ST_TEXTDOMAIN) ?>:</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php echo st()->load_template('availability/form-flight'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="tab-pane fade" id="tab-tax-options">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_ticket"><?php echo esc_html__('Max Ticket', ST_TEXTDOMAIN)?></label>
                            <?php
                            $max_ticket = STInput::request('max_ticket', get_post_meta($post_id, 'max_ticket', true));
                            ?>
                            <input id="max_ticket" class="form-control" name="max_ticket" value="<?php echo esc_attr($max_ticket); ?>" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-icon-left st-condition-parent" >
                            <label for="enable_tax"><?php echo esc_html__('Enable Tax', ST_TEXTDOMAIN)?></label>
                            <?php
                            $enable_tax = STInput::request('enable_tax', get_post_meta($post_id, 'enable_tax', true));
                            ?>
                            <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                            <select class="form-control" name="enable_tax" id="enable_tax">
                                <option value="no" <?php if($enable_tax == 'no') echo 'selected' ?>><?php _e( "No" , ST_TEXTDOMAIN ) ?></option>
                                <option value="yes_not_included" <?php if($enable_tax == 'yes_not_included') echo 'selected' ?>><?php _e( "Yes, Not included" , ST_TEXTDOMAIN ) ?></option>
                            </select>
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('enable_tax'),'danger') ?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group condition-child" data-condition="enable_tax:is(yes_not_included)">
                            <label for="vat_amount"><?php echo esc_html__('Tax Percent (%)', ST_TEXTDOMAIN)?></label>
                            <?php
                            $vat_amount = STInput::request('vat_amount', get_post_meta($post_id, 'vat_amount', true));
                            ?>
                            <input id="vat_amount" type="number" name="vat_amount" class="form-control" value="<?php echo esc_attr($vat_amount); ?>">
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('vat_amount'),'danger') ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-payment">
                <?php
                $data_paypment = STPaymentGateways::get_payment_gateways();
                if (!empty($data_paypment) and is_array($data_paypment)) {
                    foreach( $data_paypment as $k => $v ) {
                        $is_enable  = (st()->get_option('pm_gway_'.$k.'_enable'));
                        if ($is_enable =='off') {}else   {
                        ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-icon-left">

                                    <label for="is_meta_payment_gateway_<?php echo esc_attr($k) ?>"><?php echo esc_html($v->get_name()) ?>:</label>
                                    <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                                    <?php $is_pay = STInput::request('is_meta_payment_gateway_'.$k,get_post_meta($post_id , 'is_meta_payment_gateway_'.$k , true)) ?>
                                    <select class="form-control" name="is_meta_payment_gateway_<?php echo esc_attr($k) ?>" id="is_meta_payment_gateway_<?php echo esc_attr($k) ?>">
                                        <option value="on" <?php if($is_pay == 'on') echo 'selected' ?>><?php _e( "Yes" , ST_TEXTDOMAIN ) ?></option>
                                        <option value="off" <?php if($is_pay == 'off') echo 'selected' ?>><?php _e( "No" , ST_TEXTDOMAIN ) ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php
                    }}
                }
                ?>
            </div>
            <div class="tab-pane fade" id="tab-custom-fields">
                <?php
                $custom_field = st()->get_option( 'tours_unlimited_custom_field' );
                if(!empty( $custom_field ) and is_array( $custom_field )) {
                    ?>
                    <div class="row">
                        <?php
                        foreach( $custom_field as $k => $v ) {
                            $key   = str_ireplace( '-' , '_' , 'st_custom_' . sanitize_title( $v[ 'title' ] ) );
                            $class = 'col-md-12';
                            if($v[ 'type_field' ] == "date-picker") {
                                $class = 'col-md-6';
                            }
                            ?>
                            <div class="<?php echo esc_attr( $class ) ?>">
                                <div class="form-group">
                                    <label for="<?php echo esc_attr( $key ) ?>"><?php echo esc_html($v[ 'title' ]) ?></label>
                                    <?php if($v[ 'type_field' ] == "text") { ?>
                                        <input id="<?php echo esc_attr( $key ) ?>" name="<?php echo esc_attr( $key ) ?>" type="text"
                                               placeholder="<?php echo esc_html($v[ 'title' ]) ?>" class="form-control" value="<?php echo STInput::request($key,get_post_meta( $post_id , $key , true)); ?>">
                                    <?php } ?>
                                    <?php if($v[ 'type_field' ] == "date-picker") { ?>
                                        <input id="<?php echo esc_attr( $key ) ?>" name="<?php echo esc_attr( $key ) ?>" type="text"
                                               placeholder="<?php echo esc_html($v[ 'title' ]) ?>"
                                               class="date-pick form-control" value="<?php echo STInput::request($key,get_post_meta( $post_id , $key , true)); ?>">
                                    <?php } ?>
                                    <?php if($v[ 'type_field' ] == "textarea") { ?>
                                        <textarea id="<?php echo esc_attr( $key ) ?>" name="<?php echo esc_attr( $key ) ?>" class="form-control" ><?php echo STInput::request($key,get_post_meta( $post_id , $key , true)); ?></textarea>
                                    <?php } ?>

                                    <div class="st_msg console_msg_"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
    <div class="text-center div_btn_submit">
        <?php if(!empty($post_id)){?>
            <input  type="button" id="btn_check_insert_post_type_flight"  class="btn btn-primary btn-lg" value="<?php _e("UPDATE FLIGHT",ST_TEXTDOMAIN) ?>">
            <input name="btn_update_post_type_flight" id="btn_insert_post_type_flight" type="submit"  class="btn btn-primary hidden btn_partner_submit_form" value="SUBMIT">
        <?php }else{ ?>
            <input  type="hidden"  class="save_and_preview" name="save_and_preview" value="false">
            <input  type="hidden" id=""  class="" name="action_partner" value="add_partner">
            <input name="btn_insert_post_type_flight" id="btn_insert_post_type_flight" type="submit" disabled class="btn btn-primary btn-lg btn_partner_submit_form"  value="<?php _e("SUBMIT FLIGHT",ST_TEXTDOMAIN) ?>">
        <?php } ?>

    </div>
</form>