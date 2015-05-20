<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$g5['coin_table'] = G5_TABLE_PREFIX . 'coin';

// 사용자 엽전 내역 합계
function get_coin_sum($mb_id)
{
    global $g5, $config;

    // 엽전 합
    $sql = " select sum(co_coin) as sum_co_coin
                from {$g5['coin_table']}
                where mb_id = '$mb_id' ";
    $row = sql_fetch($sql);

    return $row['sum_co_coin'];
}

// 엽전 부여
function insert_coin($mb_id, $coin, $content='', $rel_table='', $rel_id='', $expired=0)
{
    global $config;
    global $g5;
    global $is_admin;

    // 엽전이 없다면 업데이트 할 필요 없음
    //if ($coin == 0) { return 0; }
    // 엽전이 0으로 넘어와도 업데이트 가능해야 함. 방송 권한 주기를 할 때는 0 임.

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mb_id == '') { return 0; }
    $mb = sql_fetch(" select mb_id from {$g5['member_table']} where mb_id = '$mb_id' ");
    if (!$mb['mb_id']) { return 0; }


    $sql = " insert into {$g5['coin_table']}
                set mb_id = '$mb_id',
                    co_datetime = '".G5_TIME_YMDHIS."',
                    co_content = '".addslashes($content)."',
                    co_coin = '$coin',
                    co_rel_table = '$rel_table',
                    co_rel_id = '$rel_id',
                    co_expired = $expired ";
    sql_query($sql);

    return 1;
}

// 해당 방송 총 사용 엽전 합계
function get_streaming_coin_sum($rel_table, $rel_id)
{
    global $g5;

    if (!$rel_table || !$rel_id) { return 0; }

    // 엽전 합
    $sql = " select sum(co_coin) as sum_co_coin
                from {$g5['coin_table']}
                where co_rel_table = '{$rel_table}' and co_rel_id = '{$rel_id}' ";
    $row = sql_fetch($sql);

    return $row['sum_co_coin'];
}

// 해당 방송의 해당 회원의 총 사용 엽전 합계
function get_streaming_member_coin_sum($mb_id, $rel_table, $rel_id)
{
    global $g5;

    if(!$mb_id || !$rel_table || !$rel_id) { return 0; }

        // 엽전 합
    $sql = " select sum(co_coin) as sum_co_coin
                from {$g5['coin_table']}
                where co_mb_id = '{$mb_id}' and co_rel_table = '{$rel_table}' and co_rel_id = '{$rel_id}' ";
    $row = sql_fetch($sql);

    return $row['sum_co_coin'];
}

// 해당 
function get_view_coin_sum($rel_table, $rel_id)
{
    global $g5;

    if (!$rel_table || !$rel_id) { return 0; }

    // 엽전 합
    $sql = " select sum(co_coin) as sum_co_coin
                from {$g5['coin_table']}
                where co_rel_table = '{$rel_table}' and co_rel_id = '{$rel_id}' and co_content = '방송보기' ";
    $row = sql_fetch($sql);

    return $row['sum_co_coin'];
}

// 해당
function get_present_coin_sum($rel_table, $rel_id)
{
    global $g5;

    if (!$rel_table || !$rel_id) { return 0; }

    // 엽전 합
    $sql = " select sum(co_coin) as sum_co_coin
                from {$g5['coin_table']}
                where co_rel_table = '{$rel_table}' and co_rel_id = '{$rel_id}' and co_content = '선물하기' ";
    $row = sql_fetch($sql);

    return $row['sum_co_coin'];
}

// 방송 볼 권한을 가진 회원은 co_content='방송보기', co_rel_table='streaming', co_rel_id='방송보기게시판글id', co_expired=0 인 것이 한개 이상 있어야 함.
function has_streaming_access($mb_id, $rel_id)
{
    global $g5;

    $sql = " select count(*) as cnt 
                from {$g5['coin_table']} 
                where co_content = '방송보기' 
                and mb_id = '{$mb_id}' 
                and co_rel_table = 'streaming' 
                and co_rel_id = '{$rel_id}' 
                and co_expired = 0";
    $row = sql_fetch($sql);

    return $row['cnt'];
}

// 방송상태, return 이 1 이면 방송중, return 이 0 이면 방송종료.
function is_streaming_onoff($wr_id)
{
    global $g5;

    $write_table = $g5['write_prefix'] . 'streaming';

    $sql = " select wr_2 from {$write_table}
                where wr_id = {$wr_id}";
    $row = sql_fetch($sql);

    return (int)$row['wr_2'];
}



?>