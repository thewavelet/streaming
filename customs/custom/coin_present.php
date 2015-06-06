<?php
include_once('./_common.php');

$error = $count = "";

if (!$is_member)
{
    die('회원만 가능합니다.');
}

$wr_id		= (int)$_POST['wr_id'];
$amount     = (int)$_POST['amount'];

if (!$wr_id || !$amount || $amount < 1) {
    die('값이 제대로 넘어오지 않았습니다.');
}

if(get_coin_sum($member['mb_id']) < $amount) {
	die('코인이 부족합니다.');
}

insert_coin($member['mb_id'], -1 * abs($amount), '선물하기', 'streaming', $wr_id);

die('');

?>