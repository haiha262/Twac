<?php
/*if( STUser_f::st_check_edit_partner(STInput::request('id')) == false ){
    return false;
}*/
wp_enqueue_script('st_post_select_ajax');
wp_enqueue_style('st_post_select_ajax');
wp_enqueue_script( 'user_upload.js' );

$post_id = STInput::request('id');
$title = $content = $excerpt = "";
if(!empty($post_id)){
    $post = get_post( $post_id );
    $title = $post->post_title;
    $content = $post->post_content;
    $excerpt = $post->post_excerpt;
}

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
<?php $validator= STUser_f::$validator; ?>
<div class="st-create">
    <h2 class="pull-left">
        <?php if(!empty($post_id)){?>
            <?php _e("Edit rental room",ST_TEXTDOMAIN) ?>
        <?php }else{ ?>
            <?php _e("Add rental room",ST_TEXTDOMAIN) ?>
        <?php } ?>
    </h2>
    <?php if(!empty($post_id)){  ?>
        <a target="_blank" href="<?php echo get_the_permalink($post_id) ?>" class="btn btn-default pull-right"><?php _e("Preview",ST_TEXTDOMAIN) ?></a>
    <?php }else{ ?>
        <span class="btn btn-default pull-right btn_save_and_preview"><?php _e("Save & Preview",ST_TEXTDOMAIN) ?></span>
    <?php } ?>
</div>
<div class="msg">
    <?php echo STTemplate::message() ?>
    <?php echo STUser_f::get_msg(); ?>
    <?php echo STUser_f::get_control_data(); ?>
</div>
<form action="" method="post" enctype="multipart/form-data" id="st_form_add_partner">
    <?php wp_nonce_field('user_setting','st_update_rental_room'); ?>

    <div class="form-group form-group-icon-left">
        
        <label for="title" class="head_bol"><?php echo __('Title', ST_TEXTDOMAIN); ?> <span class="text-small text-danger">*</span>:</label>
        <i class="fa  fa-file-text input-icon input-icon-hightlight"></i>
        <input id="title" name="st_title" type="text" placeholder="<?php st_the_language('user_create_room_title') ?>" class="form-control" value="<?php echo stripslashes(STInput::request("st_title",$title)) ?>">
        <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('st_title'),'danger') ?></div>
    </div>
    <div class="form-group form-group-icon-left">
        <label for="st_content" class="head_bol"><?php  st_the_language('user_create_room_content') ?> <span class="text-small text-danger">*</span>:</label>
        <?php wp_editor( stripslashes(STInput::request("st_content",$content)) ,'st_content'); ?>
        <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('st_content'),'danger') ?></div>
    </div>
    <div class="form-group">
        <label for="desc" class="head_bol"><?php _e("Room description",ST_TEXTDOMAIN) ?> <span class="text-small text-danger">*</span>:</label>
        <textarea id="desc" rows="6" name="st_desc" class="form-control"><?php echo stripslashes(STInput::request("st_desc",$excerpt)) ?></textarea>
        <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('st_desc'),'danger') ?></div>
    </div>
    <div class="form-group form-group-icon-left">
        <label for="id_featured_image" class="head_bol"><?php _e("Featured image",ST_TEXTDOMAIN) ?> <span class="text-small text-danger">*</span>:</label>
        <div class="upload-wrapper upload-partner-wrapper">
            <button class="upload-button-partner btn btn-primary btn-sm" data-uploader_title="<?php _e('Select a image to upload', ST_TEXTDOMAIN); ?>" data-uploader_button_text="<?php _e('Use this image', ST_TEXTDOMAIN); ?>"><?php echo __('Upload', ST_TEXTDOMAIN); ?></button>
            <div class="upload-items">
                <?php 
                    $thumbnail = STInput::request('id_featured_image', get_post_thumbnail_id( $post_id ));
                    $thumbnail_url = wp_get_attachment_url( $thumbnail );
                    if( !empty( $thumbnail_url ) ):
                ?>
                <div class="upload-item">
                    <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo TravelHelper::get_alt_image() ?>" class="frontend-image img-responsive">
                    <a href="javascript: void(0);" class="delete">&times;</a>
                </div>
                <?php endif; ?>
            </div>
            <input type="hidden" class="save-image-id" name="id_featured_image" value="<?php echo $thumbnail; ?>">
        </div>
        <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('featured_image'),'danger') ?></div>
    </div>
    <div class="tabbable tabs_partner">
        <ul class="nav nav-tabs" id="">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php _e("General Settings",ST_TEXTDOMAIN) ?></a></li>
            <li><a href="#tab-room-facility" data-toggle="tab"><?php _e("Room Facility",ST_TEXTDOMAIN) ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active" id="tab-general">

                <div class="row">
                    <?php
                    $taxonomies = (get_object_taxonomies('rental_room'));
                    if (is_array($taxonomies) and !empty($taxonomies)){
                        foreach ($taxonomies as $key => $value) {
                            ?>
                            <div class="col-md-12">
                                <?php
                                $category = STUser_f::get_list_taxonomy($value);
                                $taxonomy_tmp = get_taxonomy( $value );
                                $taxonomy_label =  ($taxonomy_tmp->label );
                                $taxonomy_name =  ($taxonomy_tmp->name );
                                if(!empty($category)):
                                    ?>
                                    <div class="form-group form-group-icon-left">
                                        <label for="check_all"> <?php echo esc_html($taxonomy_label); ?>:</label>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="checkbox-inline checkbox-stroke">
                                                    <label for="check_all">
                                                        <i class="fa fa-cogs"></i>
                                                        <input name="check_all" class="i-check check_all" type="checkbox"  /><?php _e("All",ST_TEXTDOMAIN) ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php foreach($category as $k=>$v):
                                                $icon = get_tax_meta($k,'st_icon');
                                                $icon = TravelHelper::handle_icon($icon);
                                                $check = '';
                                                if(STUser_f::st_check_post_term_partner( $post_id  ,$value , $k) == true ){
                                                    $check = 'checked';
                                                }
                                                ?>
                                                <div class="col-md-3">
                                                    <div class="checkbox-inline checkbox-stroke">
                                                        <label for="taxonomy">
                                                            <i class="<?php echo esc_html($icon) ?>"></i>
                                                            <input name="taxonomy[]" class="i-check item_tanoxomy" type="checkbox" <?php echo esc_html($check) ?> value="<?php echo esc_attr($k.','.$taxonomy_name) ?>" /><?php echo esc_html($v) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                <?php endif ?>
                            </div>
                        <?php
                        }
                    } else { ?>
                        <input name="no_taxonomy" type="hidden" value="no_taxonomy">
                    <?php } ?>
                </div>
                <div class="col-md-12">
                    <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('taxonomy[]'),'danger') ?></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-icon-left">
                            <label for="room_parent"><?php echo __('Select Rental', ST_TEXTDOMAIN); ?> <span class="text-small text-danger">*</span>:</label>
                            <?php $room_parent =STInput::request('room_parent', get_post_meta($post_id , 'room_parent' ,true)); ?>
                            <input type="text" name="room_parent" placeholder="<?php st_the_language('user_create_room_search') ?>" id="room_parent" data-pl-name="<?php echo get_the_title($room_parent) ?>" data-pl-desc="" value="<?php echo esc_html($room_parent) ?>" class="st_post_select_ajax" data-author="<?php echo esc_attr($data->ID)?>" data-post-type="st_rental" style="width: 100%">
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('room_parent'),'danger') ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class='form-group form-group-icon-left'>
                            
                            <label for="st_custom_layout"><?php _e( "Detail Room Rental Layout" , ST_TEXTDOMAIN ) ?>:</label>
                            <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                            <?php $layout = st_get_layout('rental_room');
                            if(!empty($layout) and is_array($layout)):
                                ?>
                                <select class='form-control' name='st_custom_layout' id="st_custom_layout">
                                    <?php
                                    $st_custom_layout = STInput::request('st_custom_layout',get_post_meta($post_id , 'st_custom_layout' , true));
                                    foreach($layout as $k=>$v):
                                        if($st_custom_layout == $v['value']) $check = "selected"; else $check = '';
                                        echo '<option '.$check.' value='.$v['value'].'>'.$v['label'].'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group form-group-icon-left">
                            <label for="id_gallery"><?php _e("Gallery",ST_TEXTDOMAIN) ?> <span class="text-small text-danger">*</span>:</label>
                            <div class="upload-wrapper upload-mul-partner-wrapper">
                            <?php 
                                $gallery = STInput::request('id_gallery', get_post_meta( $post_id, 'gallery', true ));
                                $gallery_arr = explode( ',', $gallery);
                                $gallery_arr = array_filter($gallery_arr, function($value){ return $value != '';});
                            ?>
                                <div class="clearfix">
                                    <button class="mr5 upload-button-partner-multi btn btn-primary btn-sm" data-uploader_title="<?php _e('Select a image to upload', ST_TEXTDOMAIN); ?>" data-uploader_button_text="<?php _e('Use this image', ST_TEXTDOMAIN); ?>"><?php echo __('Upload', ST_TEXTDOMAIN); ?></button>
                                    <?php 
                                    if( !empty( $gallery_arr)):
                                    ?>
                                        <button class=" btn btn-primary btn-sm delete-gallery"><?php echo __('Delete', ST_TEXTDOMAIN); ?></button>
                                    <?php endif; ?>
                                </div>
                                <div class="upload-items">
                                    <?php 
                                        
                                        if( !empty( $gallery_arr ) ):
                                            foreach( $gallery_arr as $image):
                                                $gallery_url = wp_get_attachment_url( $image );
                                    ?>
                                    <div class="upload-item">
                                        <img src="<?php echo $gallery_url; ?>" alt="<?php echo TravelHelper::get_alt_image() ?>" class="frontend-image img-responsive">
                                    </div>
                                    <?php endforeach; endif; ?>
                                </div>
                                <input type="hidden" class="save-image-id" name="id_gallery" value="<?php echo $gallery; ?>">
                            </div>
                        </div>
                        <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('gallery'),'danger') ?></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-room-facility">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-icon-left">
                            
                            <label for="adult_number"><?php _e("Adults Number",ST_TEXTDOMAIN) ?>:</label>
                            <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                            <input id="adult_number" name="adult_number" type="text" placeholder="<?php _e("Adults Number",ST_TEXTDOMAIN) ?>" class="form-control" value="<?php echo STInput::request('adult_number',get_post_meta( $post_id , 'adult_number' , true)); ?>" >
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('adult_number'),'danger') ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-icon-left">
                            
                            <label for="children_number"><?php _e("Children Number",ST_TEXTDOMAIN) ?>:</label>
                            <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                            <input id="children_number" name="children_number" type="text" placeholder="<?php _e("Children Number",ST_TEXTDOMAIN) ?>" class="form-control" value="<?php echo STInput::request('children_number',get_post_meta( $post_id , 'children_number' , true)); ?>" >
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('children_number'),'danger') ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-icon-left">
                            
                            <label for="bed_number"><?php _e("Beds Number",ST_TEXTDOMAIN) ?>:</label>
                            <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                            <input id="bed_number" name="bed_number" type="text" placeholder="<?php _e("Beds Number",ST_TEXTDOMAIN) ?>" class="form-control" value="<?php echo STInput::request('bed_number',get_post_meta( $post_id , 'bed_number' , true)); ?>" >
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('bed_number'),'danger') ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-icon-left">
                            
                            <label for="room_footage"><?php _e("Room Footage (square feet)",ST_TEXTDOMAIN) ?>:</label>
                            <i class="fa fa-cogs input-icon input-icon-hightlight"></i>
                            <input id="room_footage" name="room_footage" type="text" placeholder="<?php _e("Room footage (square feet)",ST_TEXTDOMAIN) ?>" class="form-control" value="<?php echo STInput::request('room_footage',get_post_meta( $post_id , 'room_footage' , true)); ?>" >
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('room_footage'),'danger') ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group form-group-icon-left">
                            <label for="add_facility"><?php _e("Add a Facility",ST_TEXTDOMAIN) ?>:</label>
                        </div>
                    </div>
                    <div class="content_data_add_new_facility">
                        <?php if(!empty($post_id)){ ?>
                            <?php $add_new_facility = get_post_meta($post_id,'add_new_facility',true);
                            if(!empty($add_new_facility) and is_array($add_new_facility)):
                                foreach($add_new_facility as $k=>$v){

                                    ?>
                                    <div class="add_new_facility_item">
                                        <div class="col-md-3">
                                            <label for="add_new_facility_title"><?php _e( "Title" , ST_TEXTDOMAIN ) ?></label>
                                            <input type="text" name="add_new_facility_title[]" class="form-control" value="<?php echo esc_html($v['title']) ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="add_new_facility_value"><?php _e( "Value" , ST_TEXTDOMAIN ) ?></label>
                                            <input type="text" name="add_new_facility_value[]" class="form-control" value="<?php echo esc_html($v['value']) ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="add_new_facility_icon"><?php _e( "Icon" , ST_TEXTDOMAIN ) ?></label>
                                            <input type="text" name="add_new_facility_icon[]" class="form-control" value="<?php if(!empty($v['facility_icon'])) echo esc_html($v['facility_icon']) ?>">
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group form-group-icon-left">
                                                <div class="btn btn-danger btn_del_facility btn_del_custom_partner" style="margin-top: 27px">
                                                    X
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            endif; ?>
                        <?php }else{ ?>
                            <?php
                            $add_new_facility_title = STInput::request('add_new_facility_title');
                            $add_new_facility_value = STInput::request('add_new_facility_value');
                            $add_new_facility_icon = STInput::request('add_new_facility_icon');
                            if(!empty($add_new_facility_title)){
                                foreach($add_new_facility_title as $k=>$v){
                                    if(!empty($v) and !empty($add_new_facility_value[ $k ])) {
                                        ?>
                                        <div class="add_new_facility_item">
                                            <div class="col-md-3">
                                                <label for="add_new_facility_title"><?php _e( "Title" , ST_TEXTDOMAIN ) ?></label>
                                                <input type="text" name="add_new_facility_title[]" class="form-control" value="<?php echo esc_attr($v) ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="add_new_facility_value"><?php _e( "Value" , ST_TEXTDOMAIN ) ?></label>
                                                <input type="text" name="add_new_facility_value[]" class="form-control" value="<?php echo esc_attr($add_new_facility_value[$k]) ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="add_new_facility_icon"><?php _e( "Icon" , ST_TEXTDOMAIN ) ?></label>
                                                <input type="text" name="add_new_facility_icon[]" placeholder="<?php _e("(eg: fa-facebook)",ST_TEXTDOMAIN) ?>" class="form-control" value="<?php echo esc_attr($add_new_facility_icon[$k]) ?>">
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group form-group-icon-left">
                                                    <div class="btn btn-danger btn_del_facility btn_del_custom_partner" style="margin-top: 27px">
                                                        X
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        <?php } ?>
                    </div>
                    <div class="col-md-12 div_btn_add_custom">
                        <div class="form-group form-group-icon-left">
                            <button id="btn_add_custom_add_new_facility" class="btn btn-info" type="button"><?php _e("Add New",ST_TEXTDOMAIN) ?></button>
                            <br>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="room_description"><?php _e("Description",ST_TEXTDOMAIN) ?>:</label>
                            <textarea id="room_description" rows="6" name="room_description" class="form-control"><?php echo STInput::request('room_description',get_post_meta( $post_id , 'room_description' , true)); ?></textarea>
                            <div class="st_msg"><?php echo STUser_f::get_msg_html($validator->error('room_description'),'danger') ?></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="text-center div_btn_submit">
        <?php if(!empty($post_id)){ ?>
            <input name="btn_update_post_type_rental_room" id="btn_insert_rental_room" type="submit"  class="btn btn-primary btn-lg btn_partner_submit_form" value="<?php _e("UPDATE ROOM RENTAL",ST_TEXTDOMAIN) ?>">
        <?php }else{ ?>
            <input  type="hidden"  class="save_and_preview" name="save_and_preview" value="false">
            <input  type="hidden" id=""  class="" name="action_partner" value="add_partner">
            <input name="btn_insert_post_type_rental_room" id="btn_insert_rental_room" type="submit" disabled  class="btn btn-primary btn-lg btn_partner_submit_form" value="<?php _e("SUBMIT ROOM RENTAL",ST_TEXTDOMAIN) ?>">
        <?php } ?>
    </div>
</form><!-- Template -->
<div class="add_new_facility_html">
    <div class="add_new_facility_item" style="display: none;">
        <div class="col-md-3">
            <label for="add_new_facility_title"><?php _e( "Title" , ST_TEXTDOMAIN ) ?></label>
            <input type="text" name="add_new_facility_title[]" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="add_new_facility_value"><?php _e( "Value" , ST_TEXTDOMAIN ) ?></label>
            <input type="text" name="add_new_facility_value[]" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="add_new_facility_icon"><?php _e( "Icon" , ST_TEXTDOMAIN ) ?></label>
            <input type="text" name="add_new_facility_icon[]" placeholder="<?php _e("(eg: fa-facebook)",ST_TEXTDOMAIN) ?>" class="form-control">
        </div>
        <div class="col-md-1">
            <div class="form-group form-group-icon-left">
                <div class="btn btn-danger btn_del_facility btn_del_custom_partner" style="margin-top: 27px">
                    X
                </div>
            </div>
        </div>
    </div>
</div>
