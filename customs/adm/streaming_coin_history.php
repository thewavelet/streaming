<?php

$sub_menu = "400100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

if(isset($_GET['wr_id'])) {
	$wr_id = $_GET['wr_id'];
} else {
	alert('wr_id 값이 존재하지 않습니다.');
}


$streaming = sql_fetch(" select * from {$g5['write_prefix']}streaming where wr_id = '{$wr_id}' ");

if(!$streaming['wr_id']) {
	alert('존재하지 않는 방송입니다.');
}


$sql_common = " from {$g5['coin_table']} ";

$sql_search = " where (co_rel_table = 'streaming') and (co_rel_id = '{$wr_id}') ";

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case 'mb_id' :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
        default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "co_id";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search}
            {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            {$sql_order}
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['PHP_SELF'].'?wr_id='.$wr_id.'" class="ov_listall">전체목록</a>';

$mb = array();
if ($sfl == 'mb_id' && $stx)
    $mb = get_member($stx);

$g5['title'] = $streaming['wr_subject']. ' 에서의 엽전 사용 내역';
include_once ('./admin.head.php');

$colspan = 9;

if (strstr($sfl, "mb_id"))
    $mb_id = $stx;
else
    $mb_id = "";


?>


<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    전체 <?php echo number_format($total_count) ?> 건
    <?php
    if (isset($mb['mb_id']) && $mb['mb_id']) {
        echo '&nbsp;(' . $mb['mb_id'] .' 님 사용 엽전 합계 : ' . number_format(get_streaming_member_coin_sum($mb['mb_id'], 'streaming', $wr_id)) . '개)';
    } else if (isset($stx) && $stx) {
    	$row2 = sql_fetch(" select sum(co_coin) as sum_coin from {$g5['coin_table']} where ({$sfl} like '%{$stx}%') ");
        echo '&nbsp;(전체 합계 '.number_format($row2['sum_coin']).'개)';
    } else {
        echo '&nbsp;(전체 합계 '.number_format(get_streaming_coin_sum('streaming', $wr_id)).'개)';
    }
    ?>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
<select name="sfl" id="sfl">
    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
    <option value="co_content"<?php echo get_selected($_GET['sfl'], "co_content"); ?>>내용</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>

<form name="fcoinlist" id="fcoinlist" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col"><?php echo subject_sort_link('mb_id') ?>회원아이디</a></th>
        <th scope="col">이름</th>
        <th scope="col">닉네임</th>
        <th scope="col"><?php echo subject_sort_link('co_content') ?>엽전 내용</a></th>
        <th scope="col"><?php echo subject_sort_link('co_coin') ?>엽전</a></th>
        <th scope="col"><?php echo subject_sort_link('co_datetime') ?>일시</a></th>
        <th scope="col"><?php echo subject_sort_link('co_rel_table') ?>관련테이블</a></th>
        <th scope="col"><?php echo subject_sort_link('co_rel_id') ?>관련아이디</a></th>
        <th scope="col"><?php echo subject_sort_link('co_expired') ?>만료여부</a></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        if ($i==0 || ($row2['mb_id'] != $row['mb_id'])) {
            $sql2 = " select mb_id, mb_name, mb_nick, mb_email, mb_homepage from {$g5['member_table']} where mb_id = '{$row['mb_id']}' ";
            $row2 = sql_fetch($sql2);
        }

        $mb_nick = get_sideview($row['mb_id'], $row2['mb_nick'], $row2['mb_email'], $row2['mb_homepage']);

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_mbid"><a href="?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo $row['mb_id'] ?></a></td>
        <td class="td_mbname"><?php echo get_text($row2['mb_name']); ?></td>
        <td class="td_name sv_use"><div><?php echo $mb_nick ?></div></td>
        <td class="td_pt_log"><?php echo $row['co_content'] ?></td>
        <td class="td_num td_pt"><?php echo number_format($row['co_coin']) ?></td>
        <td class="td_datetime"><?php echo $row['co_datetime'] ?></td>
        <td><?php echo $row['co_rel_table'] ?></td>
        <td><?php echo $row['co_rel_id'] ?></td>
        <td><?php echo $row['co_expired'] ?></td>
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>




<?php
include_once ('./admin.tail.php');
?>












