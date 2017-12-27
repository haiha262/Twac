<?php
    $aff_id = st()->get_option('hotelscb_aff_id', '152932');
    $searchbox_id = st()->get_option('hotelscb_searchbox_id', '409377');
    if($aff_id != '' && $searchbox_id != ''){
        echo '<script src="https://sbhc.portalhc.com/'. $aff_id .'/SearchBox/'. $searchbox_id .'" ></script>';
    }else{
        echo __('No data for search box.', ST_TEXTDOMAIN);
    }
?>
