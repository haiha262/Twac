<form role="search" method="get" class="" autocomplete="off" action="">
    <?php
    $get = STInput::get();
    if (!empty($get) and empty($hidde_button)) {
        foreach ($get as $key => $value) {
            if (is_array($value)) {
                if (!empty($value)) {
                    foreach ($value as $key2 => $value2) {

                        echo "<input type='hidden' name='{$key}[{$key2}]' value='$value2' >";
                    }
                }
            } else {
                if ($key != "start" && $key != "end")
                    echo "<input type='hidden' name='$key' value='$value' >";
            }
        }
    }
    ?>
    <div class="cba-date">
        <div data-tp-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>"
             class="form-group input-daterange  form-group form-group-icon-left">
            <label for="field-depart-date"><?php echo __('Start', ST_TEXTDOMAIN); ?></label>
            <i class="fa fa-calendar input-icon input-icon-highlight"></i>
            <input id="field-start-date" placeholder="<?php echo TravelHelper::getDateFormatJs(); ?>"
                   class="form-control"
                   readonly value="<?php echo STInput::get('start', ''); ?>" type="text" name="start"/>
            <input type="hidden" class="tp-date-from ss_depart" value="">
        </div>

        <div data-tp-date-format="<?php echo TravelHelper::getDateFormatJs(); ?>"
             class="form-group input-daterange form-group form-group-icon-left">
            <label for="field-return-date"><?php echo __('End', ST_TEXTDOMAIN); ?></label>
            <i class="fa fa-calendar input-icon input-icon-highlight"></i>
            <input id="field-end-date" placeholder="<?php echo TravelHelper::getDateFormatJs(); ?>" readonly
                   class="form-control" value="<?php echo STInput::get('end', ''); ?>" type="text" name="end"/>
            <input type="hidden" class="tp-date-to ss_return" value="">
        </div>

        <button style="margin-top: 4px;" type="submit"
                class="btn btn-primary btn-filter-date cba-filter-date"><?php st_the_language('filter') ?></button>
    </div>
</form>