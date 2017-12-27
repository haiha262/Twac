<?php
if (isset($attr)) {
    $default = array(
        'cba_attribute' => '',
        'item_col' => '',
        'title' => '',
        'font_size' => '4',
    );
    extract(wp_parse_args($attr, $default));

    global $cldt_dtht;

    $arr_att = array();



    if($cba_attribute == 'amn'){
        if(trim($cldt_dtht['amen']) != '')
            $arr_att = explode(',', trim($cldt_dtht['amen']));
    }
    if($cba_attribute == 'srv'){
        if(trim($cldt_dtht['service']) != '')
            $arr_att = explode(',', trim($cldt_dtht['service']));
    }

    ?>
    <div class="cba_hotel_desc">
        <h<?php echo esc_attr($font_size) ?>><?php echo apply_filters('widget_title', $title); ?></h<?php echo esc_attr($font_size) ?>>
        <?php if(!empty($arr_att)): ?>
        <div class="booking-item-features booking-item-features-expand mb30 clearfix">
            <div class="row">
            <?php
            foreach ($arr_att as $key => $value) {
                $class = 'col-sm-12';
                if($item_col){
                    $class = 'col-sm-' . (12 / $item_col);
                }
                ?>
                <div class="<?php echo $class; ?> item-attr">
                    <?php
                        echo '<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>&nbsp;';
                        echo trim(ucwords($value));
                    ?>
                </div>
                <?php
            }
            ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php


}