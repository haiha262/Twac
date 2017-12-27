<?php
$default=array(
    'title'=>'',
    'is_required'=>'',
    'placeholder'=> ''
);

if(isset($data)){
    extract(wp_parse_args($data,$default));
}else{
    extract($default);
}
if(!isset($field_size)) $field_size='lg';
if($is_required == 'on'){
    $is_required = 'required';
}

?>

<div data-tp-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>" class="form-group input-daterange  form-group-<?php echo esc_attr($field_size)?> form-group-icon-left">
    <label for="field-depart-date"><?php echo esc_html( $title)?></label>
    <i class="fa fa-calendar input-icon input-icon-highlight"></i>
    <input id="field-depart-date" placeholder="<?php echo TravelHelper::getDateFormatJs(); ?>" class="form-control <?php echo esc_attr($is_required) ?>" readonly value="" type="text" name="start" />
    <input type="hidden" class="tp-date-from ss_depart" value="">
</div>
