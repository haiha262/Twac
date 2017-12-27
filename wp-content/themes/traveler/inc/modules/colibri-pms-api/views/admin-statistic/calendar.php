<?php
extract($args);
$arrDay = array();
//$start_date = date_i18n('l',$start_timestamp);
?>
<div id="jbs-booking-calendar-wrap">
	<table class="jbs-booking-calendar jbs-booking-calendar-month">
		<thead>
		<tr>
			<!--<th class="jbs-booking-calendar-expand-week"></th>
			<?php //for($i = 0; $i < 7; $i++) : ?>
				<th><?php //echo date_i18n( 'D', strtotime( "next {$start_date} + {$i} day" ) ); ?></th>
			<?php //endfor; ?>-->
            <th class="jbs-booking-calendar-expand-week"></th>
            <?php for ( $index = get_option( 'start_of_week', 1 ); $index < get_option( 'start_of_week', 1 ) + 7; $index++ ) : ?>
                <th><?php echo date_i18n( 'D', strtotime( "next sunday +{$index} day" ) ); ?></th>
            <?php endfor; ?>
		</tr>
		</thead>

		<tbody>
		<tr>
			<td class="jbs-booking-calendar-expand-week"></td>
			<?php
			$current_day  = strtotime( 'midnight' );
			$timestamp    = $start_timestamp;
			$index        = 0;
			while ( $timestamp <= $end_timestamp ) :
				$day_class = date( 'n', $timestamp ) != absint( $month ) ? 'other-month' : 'current-month';
				$this_day = date( 'd', $timestamp );

				$day_class .= $timestamp === $current_day ? ' today' : '';
				$has_schedule_class = '';
				if(in_array($this_day, $arrDay) && $day_class != ''){
					$has_schedule_class = 'has-schedule-booking';
				}
				$day_of_other_month = '';

				?>
				<td class="jbs-booking-calendar-day-container <?php echo $day_class . ' ' . $has_schedule_class; ?>">
					<div class="jbs-booking-calendar-day"><b><?php echo $this_day; ?></b></div>
					<?php
					if(in_array($this_day, $arrDay) && $day_class != ''){

						$args = array(
							'post_type' => JBS()->booking->post_type_name,
							'showposts' => '-1',
							'meta_query' => array(
								array(
									'key' => 'jbs_booking_info_day',
									'value' => intval($this_day)
								),
								array(
									'key' => 'jbs_booking_info_month',
									'value' => intval($month)
								),
								array(
									'key' => 'jbs_booking_info_year',
									'value' => intval($year)
								)
							)
						);

						$calendar_item_query = new WP_Query($args);

						if ($calendar_item_query->have_posts()){
							$i == 0;
							$arr_id = array();
							while ($calendar_item_query->have_posts()){
								$calendar_item_query->the_post();
								array_push($arr_id, get_the_ID());
								$i++;
							}
							wp_reset_postdata();
							echo '<b>' . $this_day . '/' . $month . '</b>';
							echo '<h3>Có <span>'. $i .'</span> lịch hẹn</h3>';
							echo '<a href="#xem-chi-tiet" class="view-calendar-item" data-id="'. implode(',', $arr_id) .'">Xem chi tiết</a>';

							$i = 0;
						}else{
							echo "NO";
						}




					}
					?>
				</td>
				<?php
				$timestamp = strtotime( '+1 day', $timestamp );
				$index++;

				if ( $index % 7 === 0 && $timestamp <= $end_timestamp ) {
					echo '</tr><tr>';
					echo '<td class="jbs-booking-calendar-expand-week"></td>';
				}
			endwhile;
			?>
		</tr>
		</tbody>
	</table>

</div>
