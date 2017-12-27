<?php
$room_code  = STInput::get( 'room_code', '' );
$hotel_code = STInput::get( 'hotel_code', '' );
$start      = STInput::get( 'start', '' );
$end        = STInput::get( 'end', '' );
$rate_plan  = STInput::get( 'rate_plan', '' );
$room_list  = Colibri_PMS::cl_rt_get_list_rooms_of_hotel( $hotel_code, $start, $end, $rate_plan );
$item       = [];
foreach ( $room_list as $k => $v ) {
	if ( $v['id'] == $room_code ) {
		$item = $v;
	}
}
?>
<div class="booking-item-payment">
    <div style="display: none;" class="overlay-form"><i class="fa fa-refresh text-color"></i></div>
    <header class="clearfix" style="position: relative">
        <a class="booking-item-payment-img" href="#">
            <img width="98" height="74" src="<?php echo $item['thumb']; ?>" alt="<?php echo $item['name']; ?>"/> </a>
        <h5 class="booking-item-payment-title"><a><?php echo $item['name']; ?></a>
        </h5>
        <!--<ul class="icon-group booking-item-rating-stars">
			<li><i class="fa  fa-star"></i></li>
			<li><i class="fa  fa-star"></i></li>
			<li><i class="fa  fa-star"></i></li>
			<li><i class="fa  fa-star"></i></li>
			<li><i class="fa  fa-star-o"></i></li>
		</ul>
		<h5 class="booking-item-payment-title"><i class="fa fa-map-marker mr5"></i> Avenue of the
			Americas, New York, NY, United States</h5>-->
    </header>
    <ul class="booking-item-payment-details">
        <li>
            <h5><?php echo __('Room information', ST_TEXTDOMAIN); ?></h5>
            <!--<p class="booking-item-payment-item-title">Trip of New York – Discover America</p>-->
            <ul class="booking-item-features booking-item-features-sign clearfix">
				<?php
				foreach ( $item['guest_count'] as $key => $val ) {
					if ( $val['count'] > 0 ) {
						?>
                        <li rel="tooltip" data-placement="top" title=""
                            data-original-title="<?php echo $val['age']['text']; ?>"><i
                                    class="<?php echo $val['age']['icon']; ?>"></i><span
                                    class="booking-item-feature-sign"> x <?php echo $val['count']; ?></span>
                        </li>
						<?php
					}
				}
				if ( $item['bed'] != '' ) {
					?>
                    <li rel="tooltip" data-placement="top" title=""
                        data-original-title="<?php echo __( 'Bedroom', ST_TEXTDOMAIN ); ?>"><i
                                class="im im-bed"></i><span
                                class="booking-item-feature-sign"> x <?php echo $item['bed']; ?></span>
                    </li>
					<?php
				}
				if ( $item['num_of_unit'] != '' ) {
					?>
                    <li rel="tooltip" data-placement="top" title=""
                        data-original-title="<?php echo __( 'Number room', ST_TEXTDOMAIN ); ?>"><i
                                class="fa fa-server"></i><span
                                class="booking-item-feature-sign"> x <?php echo $item['num_of_unit']; ?></span>
                    </li>
					<?php
				}
				?>
            </ul>
        </li>
        <li>
            <ul class="booking-item-payment-price">
				<!--<?php
				foreach ( $item['guest_count'] as $key => $val ) {
					if ( $val['count'] > 0 ) {
						?>
                        <li>
                            <p class="booking-item-payment-price-title">
	                            <?php echo $val['age']['text']; ?> </p>
                            <p class="booking-item-payment-price-amount">
	                            <?php echo $val['count']; ?> </p>
                        </li>
						<?php
					}
				}
				?>
                <?php if ( $item['bed'] != '' ) { ?>
                    <li>
                        <p class="booking-item-payment-price-title">
	                        <?php echo __( 'Number room', ST_TEXTDOMAIN ); ?> </p>
                        <p class="booking-item-payment-price-amount">
				            <?php echo $item['bed']; ?> </p>
                    </li>
	            <?php } ?>

	            <?php if ( $item['num_of_unit'] != '' ) { ?>
                    <li>
                        <p class="booking-item-payment-price-title">
				            <?php echo __( 'Bedroom', ST_TEXTDOMAIN ); ?> </p>
                        <p class="booking-item-payment-price-amount">
				            <?php echo $item['num_of_unit']; ?> </p>
                    </li>
	            <?php } ?>-->

                <li>
                    <p class="booking-item-payment-price-title"><?php echo __('Arrival', ST_TEXTDOMAIN); ?></p>
                    <p class="booking-item-payment-price-amount">
                        <?php echo $start; ?> </p>
                </li>

                <li>
                    <p class="booking-item-payment-price-title"><?php echo __('Departure', ST_TEXTDOMAIN); ?></p>
                    <p class="booking-item-payment-price-amount">
	                    <?php echo $end; ?> </p>
                </li>


                <li>
                    <p class="booking-item-payment-price-title"><?php echo __('Duration', ST_TEXTDOMAIN); ?></p>
                    <p class="booking-item-payment-price-amount">
                        <?php echo Colibri_Helper::cl_get_number_of_day(TravelHelper::convertDateFormatColibri($start), TravelHelper::convertDateFormatColibri($end)); ?> night(s) </p>
                </li>
            </ul>
        </li>
    </ul>
    <div class="booking-item-payment-total text-right">
        <?php
        $cba_room_rates = STInput::get('room_rates', '');
        $room_rates = json_decode(stripcslashes($cba_room_rates));
        ?>
        <div class="room-rate">
            <table>
                <tr>
                    <td><b><?php echo __('Rates', ST_TEXTDOMAIN); ?></b></td>
                    <td><b><?php echo __('Quantity x Price', ST_TEXTDOMAIN); ?></b></td>
                </tr>
                <?php $total = 0; foreach ($room_rates as $kk => $vv): ?>
                    <tr>
                        <td><?php echo $vv[3]; ?></td>
                        <td><?php echo $vv[0] . ' x ' . Colibri_Helper::cl_format_money($vv[2], 'EUR', 'left', ' ', 2); ?></td>
                    </tr>
                    <?php $total = $total + $vv[0] * $vv[2]; endforeach; ?>
                <tr>
                    <td><b><?php echo __('TOTAL', ST_TEXTDOMAIN); ?></b></td>
                    <td><b><?php echo Colibri_Helper::cl_format_money($total, 'EUR', 'left', ' ', 2); ?></b></td>
                </tr>
            </table>
        </div>
    </div>
</div>