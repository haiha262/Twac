<?php $check_los = true; if (!$check_los): ?>
    <div class="alert alert-danger">
        <?php echo __('Minimal Length of Stay is bigger than selected date range', ST_TEXTDOMAIN); ?>
    </div>
<?php endif; ?>
<?php
$rates = Colibri_PMS::cl_get_rates($hotel_code, $start, $end, $rate_code);
if (!empty($rates['cancel'])):
    $i = 0;
    ?>

    <?php
    foreach ($rates['cancel'] as $kk => $vv) {
        if (!empty($vv['amount_percent'])) {
            foreach ($vv['amount_percent'] as $kkk => $vvv) {
                if (trim($vvv['unit']) != '') {
                    if ($i == 0) {
                        ?>
                        <strong><?php echo __('Cancellation: ', ST_TEXTDOMAIN); ?></strong>
                        <br/>
                        <?php
                    }
                    printf(__('- Cancellation within %s %s before the date of arrival %s will be charged;', ST_TEXTDOMAIN), $vv['deadline']['unit'], $vv['deadline']['time_unit'], $vvv['unit'] . ' ' . $vvv['text']);
                    echo '<br />';
                }
            }
            $i++;
        }
    }
else:
    echo __('No data', ST_TEXTDOMAIN);
endif;
?>