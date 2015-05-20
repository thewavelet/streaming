<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if ($w == '') {
	// 이곳에 가입한 회원에게 1 엽전을 지급하는 로직 추가.
	insert_coin($mb_id, 1, '회원가입');
}

?>