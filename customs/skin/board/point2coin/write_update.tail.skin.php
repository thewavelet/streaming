<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// goto_url(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr);
// goto_url($_SERVER['HTTP_REFERER']);

// insert_point($member['mb_id'], $board['bo_write_point'], "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');

// case 구문으로 $ca_name 에 따라 포인트 차감 및 엽전 추가.
if ($ca_name == '1000포인트를 1엽전으로 변환') {
	//insert_point($member['mb_id'], -1000, "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');
	insert_point($member['mb_id'], -1000, $ca_name, $bo_table, $wr_id, '쓰기');
	insert_coin($member['mb_id'], 1, $ca_name, $bo_table, $wr_id);
} else if ($ca_name == '3000포인트를 3엽전으로 변환') {
	//insert_point($member['mb_id'], -3000, "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');
	insert_point($member['mb_id'], -3000, $ca_name, $bo_table, $wr_id, '쓰기');
	insert_coin($member['mb_id'], 3, $ca_name, $bo_table, $wr_id);
} else if ($ca_name == '5000포인트를 5엽전으로 변환') {
	//insert_point($member['mb_id'], -5000, "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');
	insert_point($member['mb_id'], -5000, $ca_name, $bo_table, $wr_id, '쓰기');
	insert_coin($member['mb_id'], 5, $ca_name, $bo_table, $wr_id);
} else if ($ca_name == '8000포인트를 8엽전으로 변환') {
	//insert_point($member['mb_id'], -8000, "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');
	insert_point($member['mb_id'], -8000, $ca_name, $bo_table, $wr_id, '쓰기');
	insert_coin($member['mb_id'], 8, $ca_name, $bo_table, $wr_id);
} else if ($ca_name == '10000포인트를 10엽전으로 변환') {
	//insert_point($member['mb_id'], -10000, "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');
	insert_point($member['mb_id'], -10000, $ca_name, $bo_table, $wr_id, '쓰기');
	insert_coin($member['mb_id'], 10, $ca_name, $bo_table, $wr_id);
}

delete_cache_latest($bo_table);
alert('변환하였습니다.');

?>