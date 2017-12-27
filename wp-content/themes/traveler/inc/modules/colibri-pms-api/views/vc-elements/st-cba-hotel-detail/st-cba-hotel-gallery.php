<?php
wp_enqueue_script( 'fotorama.js' );
wp_enqueue_script('magnific.js' );

//if(!is_singular('st_hotel') or !isset($attr)) return false;

$default=array(
    'style'=>'slide'
);

global $cldt_dtht;

extract(wp_parse_args($attr,$default));


$gallery = $cldt_dtht['photo'];

if(!empty($cldt_dtht['photo'])) {
    echo '<input type="hidden" value="'. $cldt_dtht['photo'][0] .'" id="cba-co-thumb-hotel" />';
    switch ($style) {
        case "grid":
            ?>
            <div class="row row-no-gutter popup-gallery">
                <?php
                    foreach ($gallery as $key => $value) {
                        ?>
                        <div class="col-md-3 col-xs-4">
                            <a class="hover-img popup-gallery-image"
                               href="<?php echo $value; ?>"
                               data-effect="mfp-zoom-out">

                                <img src="<?php echo $value; ?>" alt="<?php echo $cldt_dtht['name']; ?>" />
                                <i class="fa fa-plus round box-icon-small hover-icon i round"></i>
                            </a>
                        </div>
                        <?php
                    }
                ?>
            </div>
            <?php
            break;
        case "slide";
        default :
            ?>
            <div class="fotorama" data-allowfullscreen="true" data-nav="thumbs">
                <?php
                    foreach ($gallery as $key => $value) {
                        ?>
                        <a href="<?php echo $value; ?>">
                            <img src="<?php echo $value; ?>" alt="<?php echo $cldt_dtht['name']; ?>" />
                        </a>
                        <?php
                    }
                ?>

            </div>
            <?php
            break;
    }
}
?>
