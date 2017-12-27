<?php
extract( $data );
if(isset($_GET['country']) && $_GET['country'] != ''){
    $country = $_GET['country'];
}
?>
<div class="cba-filter-hac">
    <small><i><?php echo __( 'Select Country', ST_TEXTDOMAIN ); ?></i></small>
    <form method="get" action="" id="cba-city-form">
        <?php
        $get = STInput::get();
        if (!empty($get) and empty($hidde_button)) {
            foreach ($get as $key => $value) {
                if (is_array($value)) {
                    if (!empty($value)) {
                        foreach ($value as $key2 => $value2) {
                            echo "<input type='hidden' name='{$key}[{$key2}]' value='$value2' >";
                        }
                    }
                } else {
                    if ($key != "country")
                        echo "<input type='hidden' name='$key' value='$value' >";
                }
            }
        }
        ?>
        <select id="cl-country" name="country" class="form-control">
            <?php
            foreach ( Colibri_Helper::cl_parse_country_code() as $key => $val ) {
                $select = '';
                if ( $val == $country ) {
                    $select = ' selected';
                }
                echo '<option value="' . $val . '" ' . $select . '>' . $key . '</option>';
            }
            ?>
        </select>
    </form>

    <div id="cba-filter-city">
        <small><i><?php echo __( 'List city', ST_TEXTDOMAIN ); ?></i></small>
        <div id="cover-list-city">
            <span class="over-starttime-helper"></span>
            <img src="<?php echo get_template_directory_uri() . '/img/loading-filter-ajax.gif'; ?>"/>
        </div>
        <div class="cba-list-city">
			<?php
			$list_city = Colibri_PMS::cl_get_list_city( $country, 0, 300 );
			if ( ! empty( $list_city->posts ) && $list_city->posts_found > 0 ) {
				foreach ( $list_city->posts as $key => $val ) {
                    $checked = TravelHelper::checked_array( explode( ',' , STInput::get( 'city_code' ) ) , $val['code'] );
                    $check = '';
                    if($checked) {
                        $check = 'checked';
                        $link = TravelHelper::build_url_auto_key( 'city_code' , $val['code'] , false );
                    } else {
                        $link = TravelHelper::build_url_auto_key( 'city_code' , $val['code'] );
                    }

					$te = '';
					$te .= '<div class="checkbox">';
					$te .= '<label>';
					$te .= '<input '. $check .'  value="' . $val['code'] . '" name="city_code" data-url="'. $link .'" class="i-check i-check-tax" type="checkbox"/>';
					$te .= $val['name'];
					$te .= '</label>';
					$te .= '</div>';
					echo $te;
				}
			}else{
			    echo '<div>' . __('No city', ST_TEXTDOMAIN) . '</div>';
            }
			?>
        </div>
		<?php
		if($list_city->posts_found > 7){
            //echo '<div class="cba-load-more"><i class="fa fa-angle-double-down" aria-hidden="true"></i></div>';
		}
		?>
        <input id="cba-no-city" type="hidden" value="<?php echo __('No city', ST_TEXTDOMAIN) ?>" />
    </div>
</div>