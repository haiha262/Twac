<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 6/22/2017
 * Version: 1.0
 */

wp_enqueue_script( 'st-filter-colibri-api-select2' );
?>
<div class="cba-filter-hac">
    <div id="cba-list-amnity">
		<?php
		foreach ( Colibri_Helper::get_data_hac() as $key => $val ) {
			$checked = TravelHelper::checked_array( explode( ',' , STInput::get( 'amenity' ) ) , $val['id'] );
			$check = '';
			if($checked) {
				$check = 'checked';
				$link = TravelHelper::build_url_auto_key( 'amenity' , $val['id'] , false );
			} else {
				$link = TravelHelper::build_url_auto_key( 'amenity' , $val['id'] );
			}

			$te = '';
			$te .= '<div class="checkbox">';
			$te .= '<label>';
			$te .= '<input '. $check .' value="' . $val['id'] . '" name="amenity" data-url="'. $link .'" class="i-check i-check-tax" type="checkbox"/>';
			$te .= $val['title'];
			$te .= '</label>';
			$te .= '</div>';
			echo $te;
		}
		?>
    </div>
    <div class="cba-load-more" style="display: none;"><i class="fa fa-angle-double-down" aria-hidden="true"></i></div>
</div>