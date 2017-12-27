<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Hotel reviews summary
 *
 * Created by ShineTheme
 *
 */
global $cldt_dtht;
$avg=$cldt_dtht['rating'];
?>
<div class="booking-item-meta">
    <?php if($avg>=2):?>
        <h2 class="lh1em">
            <?php
            if($avg<2){

            }
            elseif($avg<=3){
                st_the_language('pleasant');
            }elseif($avg<=4)
            {
                st_the_language('good');
            }elseif($avg<5){
                st_the_language('very_good');
            }elseif($avg==5){
                st_the_language('wonderful');
            }
            ?>
        </h2>
    <?php endif;?>
    <div class="booking-item-rating">
        <ul class="icon-list icon-group booking-item-rating-stars">
            <?php
            echo  Colibri_Helper::rate_to_string($avg);
            ?>
        </ul><span class="booking-item-rating-number"><b><?php echo balanceTags($avg)?></b> <?php echo __('of 5 hotel rating', ST_TEXTDOMAIN); ?></span>
    </div>
    <?php echo st()->load_template('hotel/share') ?>
    <?php echo st()->load_template('user/html/html_add_wishlist',null,array("title"=>st_get_language('add_to_wishlist'),'class'=>'btn-sm')) ?>
</div>