<?php
wp_enqueue_script('magnific.js');
$page_link = st()->get_option('cba_page_detail_hotel', '');
$page_slug = get_post($page_link)->post_name;
$thumb_url = $atts['hotel_thumb'];
$start = STInput::get('start', '');
$end = STInput::get('end', '');
?>
    <li <?php post_class('booking-item') ?>>
        <div class="row">

            <div class="col-md-3">
                <div class="booking-item-img-wrap st-popup-gallery">
                    <a href="<?php echo $thumb_url != '' ? $thumb_url : $atts['hotel_photos'][0]; ?>"
                       class="st-gp-item">
                        <?php
                        if ($thumb_url != '') {
                            echo '<img src="' . $thumb_url . '" />';
                        } else {
                            if (!empty($atts['hotel_photos'])) {
                                echo '<img src="' . $atts['hotel_photos'][0] . '" />';
                            } else {
                                echo st_get_default_image();
                            }
                        }
                        ?>
                    </a>
                    <?php
                    $count = 0;
                    if (!empty($atts['hotel_photos'])) {
                        $count = count($atts['hotel_photos']);
                    }
                    if ($count) {
                        echo '<div class="booking-item-img-num"><i class="fa fa-picture-o"></i>';
                        echo esc_attr($count);
                        echo '</div>';
                    }
                    ?>
                    <div class="hidden">
                        <?php
                        if (!empty($atts['hotel_photos'])) {
                            if ($thumb_url == '') {
                                unset($atts['hotel_photos'][0]);
                            }
                        }

                        if (!empty($atts['hotel_photos'])) {
                            foreach ($atts['hotel_photos'] as $key_gal => $val_gal) {
                                echo "<a class='st-gp-item' href='{$val_gal}'></a>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">


                <div class="booking-item-rating">
                    <ul class="icon-list icon-group booking-item-rating-stars">
                        <span class="pull-left mr10"><?php echo __('Hotel star', ST_TEXTDOMAIN); ?></span>
                        <?php
                        echo TravelHelper::rate_to_string($atts['rating']);
                        ?>
                    </ul>
                    <span class="booking-item-rating-number"><b><?php echo esc_html($atts['rating']) ?></b> <?php st_the_language('of_5') ?></span>
                </div>

                <a class="color-inherit"
                   href="<?php echo get_site_url() . '/' . $page_slug . '?id=' . $atts['hotel_code'] . '&start=' . $start . '&end=' . $end; ?>">
                    <h5 class="booking-item-title"><?php echo $atts['hotel_name']; ?>

                    </h5>
                </a>

                <p class="booking-item-address"><i
                            class="fa fa-map-marker"></i> <?php echo esc_html($atts['address_line'] . ', ' . $atts['city_name'] . ', ' . $atts['country']); ?>
                </p>
            </div>

            <div class="col-md-3">
                <?php
                $price = $atts['min_price'];
                ?>
                <span class="booking-item-price-from"><?php _e("Price from", ST_TEXTDOMAIN) ?></span>
                <span
                        class="booking-item-price"><?php echo Colibri_Helper::cl_format_money($price, $atts['currency_code'], 'left', '', 2); ?></span><span></span>
                <br>
                <a
                        class="btn btn-primary btn_book"
                        href="<?php //echo esc_url($link) ?><?php echo get_site_url() . '/' . $page_slug . '?id=' . $atts['hotel_code'] . '&start=' . $start . '&end=' . $end; ?>"><?php st_the_language('book_now') ?></a>

                <?php if ($discount_text = get_post_meta(get_the_ID(), 'discount_text', true)) { ?>
                    <?php if (!empty($count_sale)) { ?>
                        <?php STFeatured::get_sale($count_sale); ?>
                    <?php } ?>
                <?php } ?>
            </div>

        </div>
    </li>