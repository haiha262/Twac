<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Content search cbapi
 *
 * Created by ShineTheme
 *
 */

wp_enqueue_script('bootstrap-datepicker.js');
wp_enqueue_script('bootstrap-datepicker-lang.js');
wp_enqueue_script('st.travelpayouts');

$fields = array(
    array(
        'title' => esc_html__('Start date', ST_TEXTDOMAIN),
        'name' => 'start_date',
        'placeholder' => esc_html__('Start date', ST_TEXTDOMAIN),
        'layout_col' => '4',
        'layout2_col' => '4',
        'is_required' => 'on'
    ),
    array(
        'title' => esc_html__('End date', ST_TEXTDOMAIN),
        'name' => 'end_date',
        'placeholder' => esc_html__('End date', ST_TEXTDOMAIN),
        'layout_col' => '4',
        'layout2_col' => '4',
        'is_required' => 'off'
    )
);

$st_direction = !empty($st_direction) ? $st_direction : "horizontal";

if (!isset($field_size)) $field_size = '';
?>
<h2 class='mb20'><?php echo esc_html($st_title_search) ?></h2>
<?php
$page = st()->get_option('cba_page_list_hotel', '');
$page_slug = get_post($page)->post_name;
$enable_cba = st()->get_option('cba_enable', 'off');
if($enable_cba == 'on'){
?>
<form role="search" method="get" class="search main-search" autocomplete="off" action="<?php echo $page_slug; ?>">
    <div class="row">
        <?php
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                $default = array(
                    'placeholder' => ''
                );
                $value = wp_parse_args($value, $default);
                $name = $value['name'];

                $size = '4';
                if ($st_style_search == "style_1") {
                    $size = $value['layout_col'];
                } else {
                    if (!empty($value['layout2_col'])) {
                        $size = $value['layout2_col'];
                    }
                }

                if ($st_direction == 'vertical') {
                    $size = '12';
                }
                $size_class = " col-md-" . $size . " col-lg-" . $size . " col-sm-12 col-xs-12 ";
                ?>
                <div class="<?php echo esc_attr($size_class); ?>">
                    <?php echo st()->load_template('colibri-api/search/field-' . $name, false, array('data' => $value, 'field_size' => $field_size, 'placeholder' => $value['placeholder'], 'st_direction' => $st_direction, 'is_required' => $value['is_required'])) ?>
                </div>
                <?php
            }
        } ?>
        <div class="<?php echo esc_attr($size_class); ?>">
            <div class="form-group form-group-lg">
                <label class="cba-label-submit"><?php echo __('Getting list of hotels', ST_TEXTDOMAIN); ?></label>
                <button class="btn btn-primary btn-lg"
                        type="submit"><?php echo esc_html__('Search for hotels', ST_TEXTDOMAIN); ?></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="search_advance cba-search-advance">
                <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
                <div class="expand_search_box form-group form-group-<?php echo esc_attr($field_size); ?>">
                    <span class="expand_search_box-more"> <i
                                class="btn btn-primary fa fa-plus mr10"></i><?php echo __("Advanced Search", ST_TEXTDOMAIN); ?></span>
                    <span class="expand_search_box-less"> <i
                                class="btn btn-primary fa fa-minus mr10"></i><?php echo __("Advanced Search", ST_TEXTDOMAIN); ?></span>
                </div>
                <div class="view_more_content_box">
                    <div>
                        <?php echo st()->load_template('colibri-api/search/field-hac', false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    /*$country = st()->get_option('ss_market_country', 'US');
    $currency = st()->get_option('ss_currency', 'USD');
    $locale = st()->get_option('ss_locale', 'en-US');
    $api_key = st()->get_option('ss_api_key','prtl674938798674');
    $api_key = substr($api_key,0,16);*/
    ?>

</form>
<?php }else{
    echo __('Colibri API is disabled!', ST_TEXTDOMAIN);
} ?>