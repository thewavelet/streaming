<?php
$sub_menu = "200400";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

check_token();

$mb_id = $_POST['mb_id'];
$co_coin = $_POST['co_coin'];
$co_content = $_POST['co_content'];

$mb = get_member($mb_id);
$mb_coin = get_coin_sum($mb['mb_id']);

if (!$mb['mb_id'])
    alert('존재하는 회원아이디가 아닙니다.', './coin_list.php?'.$qstr);

if (($co_coin < 0) && ($co_coin * (-1) > $mb_coin))
    alert('엽전를 깎는 경우 현재 엽전보다 작으면 안됩니다.', './coin_list.php?'.$qstr);

if (!ctype_digit($co_coin))
    alert('엽전은 숫자만 입력해주세요.', './coin_list.php?'.$qstr);

insert_coin($mb_id, $co_coin, $co_content." ".$co_coin);

goto_url('./coin_list.php?'.$qstr);
?>
