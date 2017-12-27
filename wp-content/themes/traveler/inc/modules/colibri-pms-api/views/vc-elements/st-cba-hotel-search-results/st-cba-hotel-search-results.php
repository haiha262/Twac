<?php
wp_enqueue_script('magnific.js' );
wp_enqueue_script('st-filter-colibri-api');
$default=array(
    'style'=>'1',
    'taxonomy'=>'',
);
if(isset($atts)){
    extract(wp_parse_args($atts,$default));
}else{
    extract($default);
}
if(isset($_GET['style'])){
    $style=$_GET['style'];
}
$allOrderby = ST_Colibri_Base_Controller::getOrderby();
global $cldt;
$total = $cldt->found_posts;
$post_per_page = st()->get_option('cba_number_post_list_hotel', 10);
$num_of_page = ceil($total/$post_per_page);
$paged = 1;
if(isset($_GET['cpage'])){
    $paged = $_GET['cpage'];
    if($paged > $num_of_page)
        $paged = 1;
}

$arr_start = $post_per_page * ($paged - 1);
$arr_end = $arr_start + $post_per_page;
?>
<div class="nav-drop booking-sort"></div>
<div class="sort_top">
    <div class="row">
        <div class="col-md-10 col-sm-9 col-xs-9">
            <ul class="nav nav-pills ajax-filter-wrapper-order">
                <?php
                $active = STInput::request('orderby');
                if(!empty($allOrderby) and is_array($allOrderby)):
                    foreach($allOrderby as $key=>$value)
                    {
                        if( is_front_page() ){
                            switch (get_page_template_slug( )) {
                                case 'template-cars-search.php':
                                    $link = add_query_arg(array('orderby'=>$key), home_url( '/?s=&post_type=st_cars' ));
                                    break;

                                case 'template-tour-search.php':
                                    $link = add_query_arg(array('orderby'=>$key), home_url( '/?s=&post_type=st_tours' ));
                                    break;
                                case 'template-rental-search.php':
                                    $link = add_query_arg(array('orderby'=>$key), home_url( '/?s=&post_type=st_rental' ));
                                    break;
                                case 'template-hotel-search.php':
                                    $link = add_query_arg(array('orderby'=>$key), home_url( '/?s=&post_type=st_hotel' ));
                                    break;
                                case 'template-activity-search.php':
                                    $link = add_query_arg(array('orderby'=>$key), home_url( '/?s=&post_type=st_activity' ));
                                    break;
                                case 'template-hotel-room-search.php':
                                    $link = add_query_arg(array('orderby'=>$key), home_url( '/?s=&post_type=hotel_room' ));
                                    break;
                            }
                        }else{
                            $link =  add_query_arg('orderby', $key);
                        }
                        if($active == $key){
                            echo '<li class="active"><a href="'.esc_url($link).'">'.$value['name'].'</a>';
                        }elseif($key == 'ID' and empty($active)){
                            echo '<li class="active"><a href="'.esc_url($link).'" class="checkbox-filter-ajax" data-type="order" data-value="' . $key . '">'.$value['name'].'</a>';
                        }else{
                            echo '<li><a href="'.esc_url($link).'" class="checkbox-filter-ajax" data-type="order" data-value="' . $key . '">'.$value['name'].'</a>';

                        }
                    }
                endif;
                ?>
            </ul>
        </div>
        <div class="col-md-2 col-sm-3 col-xs-3 text-center ajax-filter-wrapper-layout">
            <div class="sort_icon fist"><a class="<?php if($style=='2')echo'active'; ?> checkbox-filter-ajax" href="<?php echo esc_url(add_query_arg(array('style'=>2))) ?>" data-value="2"
                                           data-type="layout"><i class="fa fa-th-large "></i></a></div>
            <div class="sort_icon last"><a class="<?php if($style=='1')echo'active'; ?> checkbox-filter-ajax" href="<?php echo esc_url(add_query_arg(array('style'=>1))) ?>" data-value="1"
                                           data-type="layout"><i class="fa fa-list "></i></a></div>
        </div>
    </div>
</div>

<?php   //echo st()->load_template('hotel/loop',false,array('style'=>$style,"taxonomy"=>$taxonomy));?>
<?php
$atts = array(
    'style'=>$style,
    'post_per_page' => $post_per_page,
    'paged' => $paged
);
?>
<?php echo st_cba_load_view('vc-elements/st-cba-hotel-search-results/hotel-loop', false, array('atts' => $atts)); ?>
<div class="row" style="margin-bottom: 40px;">
    <div class="col-sm-12">
        <hr>
    </div>
    <div class="col-md-6">
        <p>
            <small>
                <?php
                    echo Colibri_Helper::get_results_string();
                    echo '&nbsp; &nbsp; ';
                    st_the_language('showing');
                    $last=$post_per_page*($paged);
                    if($last >= $total){
                        $last = $total;
                    }
                    echo ' '.($post_per_page*($paged-1)+1).' - '.$last;
                ?>
            </small>
        </p>
        <div class="row">
            <div class="col-xs-12">
                <?php Colibri_Helper::pag($total,$post_per_page,$paged,$num_of_page); ?>
            </div>
        </div>
    </div>
</div>
