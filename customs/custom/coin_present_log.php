<?php
include_once('./_common.php');

if ($is_admin != 'super')
{
    die('관리자만 가능합니다.');
}

$rel_table		= $_POST['rel_table'];
$rel_id     	= (int)$_POST['rel_id'];
$datetime		= $_POST['datetime'];

$sql = " select * 
            from {$g5['coin_table']} left join {$g5['member_table']}
            on {$g5['coin_table']}.mb_id = {$g5['member_table']}.mb_id 
            where co_content = '선물하기' and co_rel_table = '{$rel_table}' and co_rel_id = '{$rel_id}' ";
if($datetime) 
{
	$sql = $sql . " and co_datetime > '$datetime' ";
}


$result = sql_query($sql);
if(false === $result) {
	echo mysql_error();
	exit;
}

$num_result = mysql_num_rows($result);
$json = "[";
for($i = 0; $i < $num_result; $i++)
{
	$row = sql_fetch_array($result);
	$json = $json . '{ "co_id" : "' . $row['co_id'] . '", ';
	$json = $json . ' "mb_id" : "' . $row['mb_id'] . '", ';
	$json = $json . ' "mb_nick" : "' . $row['mb_nick'] . '", ';
	$json = $json . ' "co_datetime" : "' . $row['co_datetime'] . '", ';
	$json = $json . ' "co_content" : "' . $row['co_content'] . '", ';
	$json = $json . ' "co_coin" : "' . $row['co_coin'] . '", ';
	$json = $json . ' "co_rel_table" : "' . $row['co_rel_table'] . '", ';
	$json = $json . ' "co_rel_id" : "' . $row['co_rel_id'] . '", ';
	if($i == ($num_result - 1))
		$json = $json . ' "co_expired" : "' . $row['co_expired'] . '" } ';
	else
		$json = $json . ' "co_expired" : "' . $row['co_expired'] . '" }, ';
}
$json = $json . "]";


echo $json;




?>
