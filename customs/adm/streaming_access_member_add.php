<?php
$sub_menu = "400100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

check_token();

$mb_id = $_POST['mb_id'];
$wr_id = $_POST['wr_id'];

$mb = get_member($mb_id);

if (!$mb['mb_id'])
    alert('존재하는 회원아이디가 아닙니다.', "./streaming_access_member.php?wr_id={$wr_id}");

if (!isset($wr_id) || !$wr_id)
	alert('wr_id 값이 존재하지 않습니다.', "./streaming_list.php");

$row = sql_fetch(" select count(*) as cnt 
				from {$g5['coin_table']} 
				where mb_id = '{$mb_id}' 
				and co_content = '방송보기' 
				and co_rel_table = 'streaming' 
				and co_rel_id = '{$wr_id}' 
				and co_expired = 0 ");

if($row['cnt'] > 0) {
	alert('이미 해당 회원은 방송보기 권한을 가지고 있습니다.');
}

insert_coin($mb_id, 0, '방송보기', 'streaming', $wr_id, 0);
/*
$row2 = sql_fetch(" select count(*) as cnt 
				from {$g5['coin_table']} 
				where mb_id = '{$mb_id}' 
				and co_content = '방송보기' 
				and co_rel_table = 'streaming' 
				and co_rel_id = '{$wr_id}' 
				and co_expired = 1 ");

if($row2['cnt'] > 0) {
	// 만료된 방송보기 권한을 가지고 있다면 그것의 co_expired 만 0 으로 해준다.
	sql_query(" update $g5['coin_table'] set co_expired = 0 
				where mb_id = '{$mb_id}' 
				and co_content = '방송보기' 
				and co_rel_table = 'streaming' 
				and co_rel_id = '{$wr_id}' 
				and co_expired = 1 ");
} else {
	insert_coin($mb_id, 0, '방송보기', 'streaming', $wr_id, 0);
}
*/
goto_url("./streaming_access_member.php?wr_id={$wr_id}");
?>
