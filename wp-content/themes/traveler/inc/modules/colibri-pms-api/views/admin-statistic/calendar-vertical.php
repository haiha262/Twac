<?php
$start = date('m/d/Y');
$end = $date = date('m/d/Y', strtotime('+1 month'));
if (isset($_GET['from']))
    $start = $_GET['from'];
if (isset($_GET['to']))
    $end = $_GET['to'];
$hotel_code = '';
if (isset($_GET['hotel_code'])) {
    $hotel_code = $_GET['hotel_code'];
}
?>
<?php
//Get data booking form to
$data_booking = Colibri_Helper::cl_ad_get_data_booking_by_date($start, $end);

$number_of_days = Colibri_Helper::cl_get_number_of_day(TravelHelper::convertDateFormat($start), TravelHelper::convertDateFormat($end));

$list_rates = [];
if ($hotel_code != '') {
    $list_rates = Colibri_PMS::cl_get_rates_plan_of_hotel($hotel_code, $start, $end);
}

$properties = Colibri_Helper::cl_ad_get_list_for_options($start, $end);

$rooms_data = [];
$rooms_data = Colibri_PMS::cl_rt_get_list_rooms_of_hotel($hotel_code, $start, date("m/d/Y", strtotime("+1 day", strtotime(TravelHelper::convertDateFormat($end)))));

$unique_rooms = [];
if (!empty($rooms_data)) {
    foreach ($rooms_data as $k => $v) {
        $unique_rooms[$v['id']] = $v;
    }
}
?>
<div class="wrap">
    <?php
    echo '<h1>' . __('Colibri PMS Statistic', ST_TEXTDOMAIN) . '</h1>';
    ?>
    <div id="jbs-booking-calendar-wrap">
        <div class="overlay-form"><i class="fa fa-refresh text-color"></i></div>

        <div class="cba-stas-toolbar">
            <form method="GET">
                <input type="hidden" name="page" value="cba-statistic-menu"/>
                <b><?php echo __('Date Range', ST_TEXTDOMAIN); ?></b>&nbsp; &nbsp;&nbsp; &nbsp;

                <div class="input-daterange" id="datepicker">
                    <?php echo __('From', ST_TEXTDOMAIN); ?>
                    <input type="text" name="from" class="cba-stas-date input-sm form-control" id="start"
                           value="<?php echo $start; ?>"/>

                    <?php echo __('To', ST_TEXTDOMAIN); ?>
                    <input type="text" name="to" class="cba-stas-date input-sm form-control" id="end"
                           value="<?php echo $end; ?>"/>
                </div>
                &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
                <b><?php echo __('Property', ST_TEXTDOMAIN); ?></b>
                <select name="hotel_code" class="input-sm form-control">
                    <?php foreach ($properties as $k => $v): ?>
                        <option value="<?php echo $v['code']; ?>"><?php echo $v['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
                <button type="submit" class="input-sm form-control"><?php echo __('Show', ST_TEXTDOMAIN); ?></button>

            </form>
        </div>

        <div class="cba-stats-content">
            <?php if (!empty($unique_rooms)): ?>
                <h2>
                    <?php
                    $hotel_name = __('Property', ST_TEXTDOMAIN);
                    if (isset($_GET['hotel_code'])) {
                        foreach ($properties as $kh => $vh) {
                            if ($vh['code'] == $_GET['hotel_code']) {
                                $hotel_name = $vh['name'];
                            }
                        }
                    }
                    echo $hotel_name;
                    ?>
                </h2>
                <table class="cba-stas-main-table">
                    <tr>
                        <th><?php echo __('Properties', ST_TEXTDOMAIN); ?></th>
                        <?php
                        for ($i = strtotime(TravelHelper::convertDateFormat($start)); $i <= strtotime(TravelHelper::convertDateFormat($end)); $i = strtotime("+1 day", $i)) {
                            echo '<th><span>' . date_i18n('D', $i) . '</span>' . date("d", $i) . '</th>';
                        }
                        ?>
                    </tr>
                    <?php
                    if (!empty($unique_rooms)) {
                        foreach ($unique_rooms as $k => $v) {
                            echo '<tr>';
                            ?>
                            <td><span class=""><b><?php echo $v['name']; ?></b></span></td>
                            <?php
                            for ($i = strtotime(TravelHelper::convertDateFormat($start)); $i <= strtotime(TravelHelper::convertDateFormat($end)); $i = strtotime("+1 day", $i)) {
                                /*$rooms_data_td = Colibri_PMS::cl_rt_get_list_rooms_of_hotel($_GET['hotel_code'], date('d/m/Y', $i), date('d/m/Y', strtotime("+1 day", $i)));
                                $unique_rooms_td = [];
                                foreach ($rooms_data_td as $kk => $vv) {
                                    $unique_rooms_td[$vv['id']] = $vv;
                                }*/
                                //$n = $unique_rooms_td[$v['id']]['num_of_unit'];
                                $n = rand(0, 5);
                                $class = 'n0';
                                if ($n == 1) {
                                    $class = 'n1';
                                }
                                if ($n == 2) {
                                    $class = 'n2';
                                }
                                if ($n >= 3) {
                                    $class = 'n3';
                                }
                                echo '<td class="' . $class . '">' . $n . '</td>';
                            }
                            ?>
                            <?php
                            echo '</tr>';
                            if (!empty($list_rates)) {
                                foreach ($list_rates as $k_r => $v_r) {
                                    ?>
                                    <tr>
                                        <td><?php echo $v_r; ?></td>
                                        <?php
                                        $ii = 0;
                                        for ($i = strtotime(TravelHelper::convertDateFormat($start)); $i <= strtotime(TravelHelper::convertDateFormat($end)); $i = strtotime("+1 day", $i)) {
                                            $aclass = Colibri_Helper::add_class_to_table($data_booking, $i, $k, $k_r);
                                            $aclass="";
                                            if (trim($aclass) != '') {
                                                echo '<td class="' . $aclass . '" data-time="' . $i . '">';
                                            } else {
                                                echo '<td>';
                                            }

                                            echo '<span>' . Colibri_Helper::cl_ad_get_rates_by_date($rooms_data, $k, $k_r, date('m/d/Y', $i), date('m/d/Y', strtotime("+1 day", $i))) . '</span>';
                                            echo '</td>';
                                            $ii++;
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </table>
                <div id="cba-stas-hover">
                    <h4>Number of booking: <span id="cba-stas-number-booking">2</span></h4>
                    <h4>Date Range: <span id="cba-stas-date-booking">20/11/2017 - 20/12/2017</span></h4>
                </div>
                <div class="cba-stas-detail-wrapper">
                    <h2>
                        <?php echo __('Detail information', ST_TEXTDOMAIN); ?>

                    </h2>
                    <div id="cba-stas-detail">

                    </div>
                </div>
                <?php
            else:
                echo __('No data', ST_TEXTDOMAIN);
            endif;
            ?>
        </div>
    </div>
</div>
