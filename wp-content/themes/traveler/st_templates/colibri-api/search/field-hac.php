<?php

$default = array(
    'title' => '',
    'is_required' => '',
    'placeholder' => ''
);

if (isset($data)) {
    extract(wp_parse_args($data, $default));
} else {
    extract($default);
}
if (!isset($field_size)) $field_size = 'lg';
if ($is_required == 'on') {
    $is_required = 'required';
}

//Data for country code
$country_data = Colibri_Helper::cl_parse_country_code();
?>

<div class="cba-hac-code">
    <div class="row">
        <div class="col-sm-6">
            <div class="cba-search-country">
                <label><?php echo __('Country', ST_TEXTDOMAIN); ?></label>
                <select id="cba-home-country" name="country" class="form-control">
                    <?php
                    foreach (Colibri_Helper::cl_parse_country_code() as $key => $val) {
                        $select = '';
                        if ($val == 'PT') {
                            $select = ' selected';
                        }
                        echo '<option value="' . $val . '" ' . $select . '>' . $key . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="cba-search-city">
                <label><?php echo __('City', ST_TEXTDOMAIN); ?></label>
                <?php
                $list_city = Colibri_PMS::cl_get_list_city('PT', 0, 300);
                if(!empty($list_city)) {
	                if ( ! empty( $list_city->posts ) && $list_city->posts_found > 0 ) {
		                ?>
                        <select id="cba-home-city" name="city_code" class="form-control">
			                <?php
			                foreach ( $list_city->posts as $key => $val ) {
				                echo '<option value="' . $val['code'] . '">' . $val['name'] . '</option>';
			                }
			                ?>
                        </select>
                        <div id="cba-home-city-alert"><?php echo __( 'No city found!', ST_TEXTDOMAIN ); ?></div>
		                <?php
	                } else {
		                echo __( 'No city found!', ST_TEXTDOMAIN );
	                }
                }else{
                    echo __("No data.", ST_TEXTDOMAIN);
                }
                ?>
            </div>
        </div>
    </div>
    <br/>
    <label><?php echo __('Hotel Amenities', ST_TEXTDOMAIN); ?></label>
    <div class="row">
        <div class="cba-home-amenity">
            <input type="hidden" id="cba-home-amenity-total" name="amenity"/>
            <?php
            $i = 0;
            $hac_data = Colibri_Helper::get_data_hac();
            $count_hac = count($hac_data);
            foreach (Colibri_Helper::get_data_hac() as $key => $val) {
                $checked = TravelHelper::checked_array(explode(',', STInput::get('amenity')), $val['id']);
                $check = '';
                if ($checked) {
                    $check = 'checked';
                    $link = TravelHelper::build_url_auto_key('amenity', $val['id'], false);
                } else {
                    $link = TravelHelper::build_url_auto_key('amenity', $val['id']);
                }

                $te = '';
                $te .= '<div class="checkbox">';
                $te .= '<label>';
                $te .= '<input ' . $check . ' value="' . $val['id'] . '" class="i-check i-check-amenity" type="checkbox"/>';
                $te .= $val['title'];
                $te .= '</label>';
                $te .= '</div>';

                if ($i == 0 || $i == $count_hac / 2 + 2) {
                    echo '<div class="col-sm-6">';
                }
                echo $te;
                if ($i == $count_hac / 2 + 1 || $i == $count_hac - 1) {
                    echo '</div>';
                }
                $i++;
            }
            ?>
        </div>
    </div>
</div>
