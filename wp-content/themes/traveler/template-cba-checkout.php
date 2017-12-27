<?php
/**
 * Template Name: Colibri PMS API CheckOut
 */

get_header();
echo st()->load_template( 'search-loading' );
get_template_part( 'breadcrumb' );
?>
<div class="mfp-with-anim mfp-dialog mfp-search-dialog mfp-hide" id="search-dialog">
	<?php echo st()->load_template( 'hotel/search-form-2' ); ?>
</div>
<div class="container mb20">
    <div class="booking-item-details cba-hotel-detail">
        <div class="row">
            <div class="col-lg-8">
                <br/>
                <h4>Booking Submission</h4>
				<?php echo st_cba_load_view( 'checkout/form', false ); ?>
            </div>
            <div class="col-lg-4">
                <br/>
                <h4>Your Booking:</h4>
	            <?php echo st_cba_load_view( 'checkout/checkout-item', false ); ?>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>






