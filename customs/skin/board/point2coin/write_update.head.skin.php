<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// case 구문으로 $ca_name 에 따라 포인트 잔여량에 따른 권한 판별.
if ($ca_name == '1000포인트를 1엽전으로 변환') {
	if($member['mb_point'] < 1000) alert('포인트가 부족합니다.');
} else if ($ca_name == '3000포인트를 3엽전으로 변환') {
	if($member['mb_point'] < 3000) alert('포인트가 부족합니다.');
} else if ($ca_name == '5000포인트를 5엽전으로 변환') {
	if($member['mb_point'] < 5000) alert('포인트가 부족합니다.');
} else if ($ca_name == '8000포인트를 8엽전으로 변환') {
	if($member['mb_point'] < 8000) alert('포인트가 부족합니다.');
} else if ($ca_name == '10000포인트를 10엽전으로 변환') {
	if($member['mb_point'] < 10000) alert('포인트가 부족합니다.');
}

$wr_subject = $ca_name;
$wr_content = '아이디 '.$member['mb_id'].'님께서 '.$ca_name.'하였습니다.';

?>