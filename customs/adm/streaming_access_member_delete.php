<?php
$sub_menu = "400100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

check_token();

$wr_id = $_POST['wr_id'];
if (!isset($wr_id) || !$wr_id)
	alert('wr_id 값이 존재하지 않습니다.', "./streaming_list.php");

$count = count($_POST['chk']);
if(!$count)
    alert($_POST['act_button'].' 하실 항목을 하나 이상 체크하세요.');


if($_POST['act_button'] == '선택회원권한만료') {

	for ($i=0; $i<$count; $i++)
	{
	    // 실제 번호를 넘김
	    $k = $_POST['chk'][$i];

	    // streaming 게시판의 방송 정보
	    $sql = " select * from {$g5['coin_table']} where co_id = '{$_POST['co_id'][$k]}' ";
	    $row = sql_fetch($sql);

	    if(!$row['co_id'] || $row['co_expired'] == 1)
	        continue;

	    $sql = " update {$g5['coin_table']} set co_expired = 1 where co_id = '{$_POST['co_id'][$k]}' ";

	    sql_query($sql);
	}

}


goto_url("./streaming_access_member.php?wr_id={$wr_id}");


?>