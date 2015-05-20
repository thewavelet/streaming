<?php
$sub_menu = "200500";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

check_token();

$ip_ip = $_POST['ip_ip'];
$ip_content = $_POST['ip_content'];

$sql = " insert into {$g5['ip_exception_table']}
            set ip_ip = '$ip_ip',
            	ip_content = '".addslashes($ip_content)."',
            	ip_datetime = '".G5_TIME_YMDHIS."' ";
                
sql_query($sql);

goto_url('./ip_exception_list.php?'.$qstr);
?>
