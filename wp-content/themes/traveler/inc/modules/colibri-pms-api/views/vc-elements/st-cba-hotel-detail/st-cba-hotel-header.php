<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * Hotel header
 *
 * Created by ShineTheme
 *
 */
global $cldt_dtht;
$address = $cldt_dtht['address_line'] . ', ' . $cldt_dtht['city_name'] . ', ' . $cldt_dtht['country_name'];
?>
<header class="">
    <h1 class="lh1em featured_single" itemprop="name"><?php echo $cldt_dtht['name']; ?></h1>
    <p class="lh1em text-small" itemprop="address"><i class="fa fa-map-marker"></i> <?php echo esc_html($address); ?></p>
</header>