<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Hotel loop room item
 *
 * Created by ShineTheme
 *
 */

//check is booking with modal
$st_is_booking_modal = apply_filters('st_is_booking_modal', false);
$single_book = st()->get_option('hotel_single_book_room', 'off');
$room_id = get_the_ID();
$item_id = get_post_meta(get_the_ID(), 'room_parent', true);
if (empty($item_id)) {
    $item_id = $room_id;
}
$extra_price = get_post_meta(get_the_ID(), 'extra_price', true);
$external = STRoom::get_external_url();
global $post;
?>
<li <?php post_class() ?> itemscope itemtype="http://schema.org/Hotel">
    <?php if($single_book == 'on'){ ?>
    <form id="form-booking-inpage" class="" method="get">
        <input type="hidden" name="check_in" value="<?php echo STInput::request('start'); ?>" />
        <input type="hidden" name="check_out" value="<?php echo STInput::request('end'); ?>" />
        <input type="hidden" name="room_num_search" value="<?php echo STInput::request('room_num_search'); ?>" />
        <input type="hidden" name="adult_number" value="<?php echo STInput::request('adult_number'); ?>" />
        <input type="hidden" name="child_number" value="<?php echo STInput::request('child_number'); ?>" />
        <input name="action" value="hotel_add_to_cart" type="hidden">
        <input name="item_id" value="<?php echo $item_id; ?>" type="hidden">
        <input name="room_id" value="<?php echo $room_id; ?>" type="hidden">
        <input type="hidden" name="start" value="<?php echo STInput::request('start'); ?>" />
        <input type="hidden" name="end" value="<?php echo STInput::request('end'); ?>" />
        <input type="hidden" name="is_search_room" value="<?php echo STInput::request('is_search_room'); ?>">
        <?php } ?>
        <div class="booking-item">
            <div class="row">
                <div class="col-md-3">
                    <?php
                    $link = get_the_permalink();
                    if (STInput::request('start') and STInput::request('end')) {
                        $link = esc_url(
                            add_query_arg(array(
                                'check_in' => STInput::request('start'),
                                'check_out' => STInput::request('end'),
                                'room_num_search' => STInput::request('room_num_search'),
                                'child_number' => STInput::request('child_number'),
                                'adult_number' => STInput::request('adult_number')
                            ), $link)
                        );
                    }

                    ?>
                    <a href="<?php echo esc_url($link); ?>" class="hover-img">
                        <?php
                        if (has_post_thumbnail() and get_the_post_thumbnail()) {
                            the_post_thumbnail('thumbnail', array('alt' => TravelHelper::get_alt_image(get_post_thumbnail_id(get_the_ID()))));
                        } else {
                            if (function_exists('st_get_default_image'))
                                echo st_get_default_image();
                        }
                        ?>
                    </a>
                </div>
                <div class="col-md-6">
                    <h5 class="booking-item-title"><a href="<?php echo esc_url($link); ?>"
                                                      title=""><?php the_title() ?></a>
                    </h5>
                    <div class="text-small">
                        <p style="margin-bottom: 10px;">
                            <?php
                            $excerpt = $post->post_excerpt;
                            $excerpt = strip_tags($excerpt);
                            echo TravelHelper::cutnchar($excerpt, 120);
                            ?>
                        </p>
                    </div>

                    <ul class="booking-item-features booking-item-features-sign clearfix">
                        <?php if ($adult = get_post_meta(get_the_ID(), 'adult_number', true)): ?>
                            <li rel="tooltip" data-placement="top" title=""
                                data-original-title="<?php st_the_language('adults_occupany') ?>"><i class="fa fa-male"></i><span class="booking-item-feature-sign">x <?php echo esc_html($adult) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if ($child = get_post_meta(get_the_ID(), 'children_number', true)): ?>
                            <li rel="tooltip" data-placement="top" title=""
                                data-original-title="<?php st_the_language('childs') ?>"><i class="im im-children"></i><span class="booking-item-feature-sign">x <?php echo esc_html($child) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if ($bed = get_post_meta(get_the_ID(), 'bed_number', true)): ?>
                            <li rel="tooltip" data-placement="top" title=""
                                data-original-title="<?php st_the_language('bebs') ?>"><i class="im im-bed"></i><span class="booking-item-feature-sign">x <?php echo esc_html($bed) ?></span>
                            </li>
                        <?php endif; ?>


                        <?php if ($room_footage = get_post_meta(get_the_ID(), 'room_footage', true)): ?>

                            <li rel="tooltip" data-placement="top" title=""
                                data-original-title="<?php st_the_language('room_footage') ?>"><i class="im im-width"></i><span class="booking-item-feature-sign"><?php echo esc_html($room_footage) ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <ul class="booking-item-features booking-item-features-small clearfix">
                        <?php get_template_part('single-hotel/room-facility', 'list'); ?>

                    </ul>
                    <!-- Start extra -->
                    <?php
                    $start = TravelHelper::convertDateFormat(STInput::request('start'));
                    $end = TravelHelper::convertDateFormat(STInput::request('end'));
                    $numberday = STDate::dateDiff($start, $end);
                    $is_search_room = STInput::request('is_search_room');
                    ?>
                    <?php if ($start and $end and $is_search_room) { ?>
                    <div class="sroom-extra-service">
                        <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse"
                                data-target="#extra-service-sroom-<?php echo get_the_ID(); ?>" aria-expanded="false"
                                aria-controls="extra-service-sroom-<?php echo get_the_ID(); ?>">
                            <?php echo __('Extra services', ST_TEXTDOMAIN); ?>
                        </button>
                        <div class="collapse" id="extra-service-sroom-<?php echo get_the_ID(); ?>">
                            <div class="well">
                                <?php if (is_array($extra_price) && count($extra_price)): ?>
                                    <?php $extra = STInput::request("extra_price");
                                    if (!empty($extra['value'])) {
                                        $extra_value = $extra['value'];
                                    }
                                    ?>
                                    <div class="extra-price">
                                        <table class="table" style="table-layout: fixed;">
                                            <?php $inti = 0; ?>
                                            <?php foreach ($extra_price as $key => $val): ?>
                                                <tr class="<?php echo ($inti > 4) ? 'extra-collapse-control extra-none' : '' ?>">
                                                    <td width="70%">
                                                        <label for="field-<?php echo $val['extra_name']; ?>"
                                                               class="ml20 mt5"><?php echo $val['title'] . ' (' . TravelHelper::format_money($val['extra_price']) . ')'; ?></label>
                                                        <input type="hidden"
                                                               name="extra_price[price][<?php echo $val['extra_name']; ?>]"
                                                               value="<?php echo $val['extra_price']; ?>">
                                                        <input type="hidden"
                                                               name="extra_price[title][<?php echo $val['extra_name']; ?>]"
                                                               value="<?php echo $val['title']; ?>">
                                                    </td>
                                                    <td>
                                                        <select style="width: 100px"
                                                                class="form-control app extra-service-select"
                                                                name="extra_price[value][<?php echo $val['extra_name']; ?>]"
                                                                id="field-<?php echo $val['extra_name']; ?>"
                                                                data-extra-price="<?php echo $val['extra_price']; ?>">
                                                            <?php
                                                            $max_item = intval($val['extra_max_number']);
                                                            if ($max_item <= 0) $max_item = 1;
                                                            for ($i = 0; $i <= $max_item; $i++):
                                                                $check = "";
                                                                if (!empty($extra_value[$val['extra_name']]) and $i == $extra_value[$val['extra_name']]) {
                                                                    $check = "selected";
                                                                }
                                                                ?>
                                                                <option <?php echo esc_html($check) ?>
                                                                        value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                            <?php endfor; ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php $inti++; endforeach; ?>
                                            <?php if (count($extra_price) > 5) {
                                                echo '<tr><td colspan="2" class="extra-collapse text-center"><a href="#"><i class="fa fa-angle-double-down"></i></a></td></tr>';
                                            } ?>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <!-- End extra -->
                </div>
                <div class="col-md-3">
                    <?php if ($start and $end and $is_search_room) { ?>
                        <?php
                        $room_id = get_the_ID();
                        $room_num_search = intval(STInput::request('room_num_search', 1));
                        $sale_price = STPrice::getRoomPrice($room_id, strtotime($start), strtotime($end), $room_num_search);
                        $total_price = STPrice::getRoomPriceOnlyCustomPrice($room_id, strtotime($start), strtotime($end), $room_num_search);
                        ?>
                        <?php if ($sale_price < $total_price): ?>
                            <span class="text-lg  onsale mr20">
                            <?php echo TravelHelper::format_money($total_price) ?>
                        </span>
                            <br/>
                        <?php endif; ?>
                        <span class="booking-item-price">
                            <?php echo TravelHelper::format_money($sale_price) ?>
                        </span>
                        <span class="booking-item-price-unit"><?php printf(__('/ %d night(s)', ST_TEXTDOMAIN), $numberday) ?></span>
                        <br>


                        <?php
                        $st_is_woocommerce_checkout = apply_filters('st_is_woocommerce_checkout', false);
                        $st_is_booking_modal = apply_filters('st_is_booking_modal', false);
                        if ($external) { ?>
                            <a class=" btn btn-primary btn_hotel_booking"
                               href="<?php echo esc_url($external); ?>"><?php echo __('Book Now', ST_TEXTDOMAIN); ?></a>
                        <?php } else {
                            if($single_book != 'on') {
                                ?>
                                <a href="<?php echo $link; ?>"
                                   class="btn btn-primary btn_hotel_booking"><?php echo st_get_language('book'); ?></a>
                                <?php
                            }else{
                                if ($st_is_booking_modal && !$st_is_woocommerce_checkout) {
                                    ?>
                                    <a class=" btn btn-primary btn-st-add-cart" data-effect="mfp-zoom-out" onclick="return false"
                                       data-target="#hotel_booking_<?php echo get_the_ID(); ?>"
                                       type="submit"><?php echo __('Book Now', ST_TEXTDOMAIN); ?> <i class="fa fa-spinner fa-spin"></i></a>
                                    <?php
                                }else{
                                    ?>
                                    <input class=" btn btn-primary btn_hotel_booking"
                                           value="<?php echo __('Book Now', ST_TEXTDOMAIN); ?>" type="submit">
                                    <?php
                                }
                            }
                        }
                        ?>
                    <?php } else { ?>
                        <button class="btn btn-primary btn-show-price"
                                type="button"><?php _e("Show Price", ST_TEXTDOMAIN) ?></button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if($single_book == 'on'){ ?>
    </form>
    <?php } ?>
</li>
<?php
if (st()->get_option('booking_modal', 'off') == 'on') {
    ?>
    <div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide"
         id="hotel_booking_<?php the_ID() ?>">
        <?php echo st()->load_template('hotel/modal_booking'); ?>
    </div>

<?php } ?>