<?php
global $cldt;
$default=array(
    'style'=>'1',
);
if(isset($atts)){
    extract(wp_parse_args($atts,$default));
}else{
    extract($default);
}

$arr_start = $post_per_page * ($paged - 1);
$arr_end = $arr_start + $post_per_page;

if ($style == '1') {
    if ($cldt->found_posts > 0) {
        echo '<ul class="booking-list loop-hotel style_list">';
        //foreach ($cldt->posts as $key => $value){
        for($k = $arr_start; $k < $arr_end; $k++ ){
            if(!empty($cldt->posts[$k])) {
                $value = $cldt->posts[$k];
                echo st_cba_load_view('vc-elements/st-cba-hotel-search-results/hotel-loop-list', false, array('atts' => $value));
            }
        }
        echo "</ul>";
    }else{
        echo __('No Hotels found', ST_TEXTDOMAIN);
    }
} else {
    if ($cldt->found_posts > 0) {
        ?>
        <div class="row row-wrap loop_hotel loop_grid_hotel style_box">
            <?php
            //foreach ($cldt->posts as $key => $value){
            for($k = $arr_start; $k < $arr_end; $k++ ){
                if(!empty($cldt->posts[$k])) {
                    $value = $cldt->posts[$k];
                    echo st_cba_load_view('vc-elements/st-cba-hotel-search-results/hotel-loop-grid', false, array('atts' => $value));
                }
            }
            ?>
        </div>
        <?php
    }else{
        echo __('No Hotels found', ST_TEXTDOMAIN);
    }
}