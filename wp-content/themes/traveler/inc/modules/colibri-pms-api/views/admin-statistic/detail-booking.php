<?php
foreach ($data as $k => $v){
    $data = json_decode($v['data']);
    $data_res = json_decode($v['data_res']);
    $data_user = json_decode($v['data_user']);
    //echo '<h3>Reservation Info: </h3>';
    echo "<b>Res ID: " . $data_res->res_value . '</b><br />';
    echo "<b>Res Source: " . $data_res->res_source . '</b><br />';
    echo "<b>Booking Date: " . $v['booking_date'] . '</b><br />';
    echo "<b>From : " . $data->start . ' to: ' . $data->end . '</b><br />';
    //echo '<h3>User Info: </h3>';
    echo "<b>Full name: " . $data_user->first_name . ' ' . $data_user->last_name . '</b><br />';
    echo "<b>Email: " . $data_user->email . '</b><br />';
    echo "<hr />";
}
?>