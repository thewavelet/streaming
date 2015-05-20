<?php
$sub_menu = "400100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

check_token();


$count = count($_POST['chk']);
if(!$count)
    alert($_POST['act_button'].' 하실 항목을 하나 이상 체크하세요.');


if($_POST['act_button'] == '선택방송종료') {

	for ($i=0; $i<$count; $i++)
	{
	    // 실제 번호를 넘김
	    $k = $_POST['chk'][$i];

	    // streaming 게시판의 방송 정보
	    $sql = " select * from {$g5['write_prefix']}streaming where wr_id = '{$_POST['wr_id'][$k]}' ";
	    $row = sql_fetch($sql);

	    if(!$row['wr_id'] || $row['wr_2'] == '0')
	        continue;

	    // 방송종료가 아나라면 방송종료로 변경
	    $sql = " update {$g5['write_prefix']}streaming set wr_2 = '0' where wr_id = '{$_POST['wr_id'][$k]}' ";
	    sql_query($sql);
	}

} else if($_POST['act_button'] == '선택방송중') {

	for ($i=0; $i<$count; $i++)
	{
	    // 실제 번호를 넘김
	    $k = $_POST['chk'][$i];

	    // streaming 게시판의 방송 정보
	    $sql = " select * from {$g5['write_prefix']}streaming where wr_id = '{$_POST['wr_id'][$k]}' ";
	    $row = sql_fetch($sql);

	    if(!$row['wr_id'] || $row['wr_2'] == '1')
	        continue;

	    // 방송종료가 아나라면 방송종료로 변경
	    $sql = " update {$g5['write_prefix']}streaming set wr_2 = '1' where wr_id = '{$_POST['wr_id'][$k]}' ";
	    sql_query($sql);
	}

}


goto_url('./streaming_list.php?'.$qstr);


?>