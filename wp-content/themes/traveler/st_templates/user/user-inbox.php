<div class="st-create">
    <h2><?php esc_html_e("Inbox",ST_TEXTDOMAIN) ?></h2>
</div>
<?php
$message = STInput::request('message_id');
if(!empty($message)){
    ST_Inbox_Admin::inst()->masked_as_read($message);
    echo st()->load_template('user/inbox/live',false,array('message_id'=>$message));
}else{
    echo st()->load_template('user/inbox/list');
}