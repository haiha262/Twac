<?php wp_enqueue_script('magnific.js'); ?>
<?php extract($hotel_map); ?>
<div class="div_item_map <?php echo 'div_map_item_' . $hotel_map['hotel_code'] ?>">
    <?php
    $link = '';
    //$link = st_get_link_with_search(get_permalink(), array('start', 'end', 'room_num_search', 'adult_number','children_num'), $_GET);
    $thumb = '';
    if (!empty($hotel_map['photo'])) {
        $thumb = $hotel_map['photo'][0];
    }
    ?>
    <div class="thumb item_map">
        <header class="thumb-header">
            <div class="booking-item-img-wrap st-popup-gallery">
                <a href="<?php echo $thumb != '' ? $thumb : '#' ?>" class="st-gp-item">
                    <?php
                    if ($thumb != '') {
                        echo '<img src="' . $thumb . '" alt="' . $hotel_map['name'] . '" />';
                    } else {
                        echo st_get_default_image();
                    }
                    ?>
                </a>
                <?php
                $count = 0;
                $gallery = $hotel_map['photo'];
                if(!empty($gallery)){
                    $count = count($gallery);

                }
                unset($gallery[0]);
                if ($count) {
                    echo '<div class="booking-item-img-num"><i class="fa fa-picture-o"></i>';
                    echo esc_attr($count);
                    echo '</div>';
                }
                ?>
                <div class="hidden">
                    <?php if (!empty($gallery)) {
                        foreach ($gallery as $key => $value) {
                            if (isset($value))
                                echo "<a class='st-gp-item' href='{$value}'></a>";
                        }
                    } ?>
                </div>
            </div>
        </header>
        <div class="thumb-caption">
            <ul class="icon-list icon-group booking-item-rating-stars text-color">
                <?php
                echo TravelHelper::rate_to_string($hotel_map['rating']);
                ?>
            </ul>
            <h5 class="thumb-title"><a class="text-darken"
                                       href="<?php echo esc_url($link) ?>"><?php echo $hotel_map['name']; ?></a></h5>
            <p class="mb0">
                <small> <?php echo esc_html($hotel_map['address_line'] . ', ' . $hotel_map['city_name'] . ', ' . $hotel_map['country_name']) ?></small>
            </p>
            <p class="mb0 text-darken item_price_map">
                <small><?php printf(__("from %s/night", ST_TEXTDOMAIN), '<span class="text-lg lh1em">' . TravelHelper::format_money($hotel_map['min_price']) . '</span>') ?></small>
            </p>
            <a class="btn btn-primary btn_book"
               href="<?php echo esc_url($link) ?>"><?php _e("Book Now", ST_TEXTDOMAIN) ?></a>
            <button class="btn btn-default pull-right close_map"
                    onclick="closeGmapThumbItem(this)"><?php _e("Close", ST_TEXTDOMAIN) ?></button>
        </div>
    </div>
</div>
