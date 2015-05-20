<?php
$sub_menu = "200400";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

if(!sql_query(" DESC {$g5['coin_table']} ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['coin_table']}` (
                    `co_id` int(11) NOT NULL AUTO_INCREMENT,
                    `mb_id` varchar(20) NOT NULL DEFAULT '',
                    `co_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `co_content` varchar(255) NOT NULL DEFAULT '',
                    `co_coin` int(11) NOT NULL DEFAULT '0',
                    `co_rel_table` varchar(20) NOT NULL DEFAULT '',
                    `co_rel_id` varchar(20) NOT NULL DEFAULT '',
                    `co_expired` tinyint(4) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`co_id`),
                    KEY `index1` (`mb_id`),
                    KEY `index2` (`co_rel_table`),
                    KEY `index3` (`co_rel_id`),
                    KEY `index4` (`co_expired`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ", false);

    mysql_query($sql) or die(mysql_error() . "<p>" . $sql);
    sleep(3);
}

$sql_common = " from {$g5['coin_table']} ";

$sql_search = " where (1) ";

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

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

$mb = array();
if ($sfl == 'mb_id' && $stx)
    $mb = get_member($stx);

$g5['title'] = '엽전관리';
include_once ('./admin.head.php');

$colspan = 11;

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
        echo '&nbsp;(' . $mb['mb_id'] .' 님 엽전 합계 : ' . number_format(get_coin_sum($mb['mb_id'])) . '개)';
    } else {
        $row2 = sql_fetch(" select sum(co_coin) as sum_coin from {$g5['coin_table']} ");
        echo '&nbsp;(전체 합계 '.number_format($row2['sum_coin']).'개)';
    }
    ?>
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
    <option value="co_content"<?php echo get_selected($_GET['sfl'], "co_content"); ?>>내용</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>

<form name="fcoinlist" id="fcoinlist" method="post" action="./coin_list_delete.php" onsubmit="return fcoinlist_submit(this);">
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
        <th scope="col">
            <label for="chkall" class="sound_only">엽전 내역 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('mb_id') ?>회원아이디</a></th>
        <th scope="col">이름</th>
        <th scope="col">닉네임</th>
        <th scope="col"><?php echo subject_sort_link('co_content') ?>엽전 내용</a></th>
        <th scope="col"><?php echo subject_sort_link('co_coin') ?>엽전</a></th>
        <th scope="col"><?php echo subject_sort_link('co_datetime') ?>일시</a></th>
        <th scope="col"><?php echo subject_sort_link('co_rel_table') ?>관련테이블</a></th>
        <th scope="col"><?php echo subject_sort_link('co_rel_id') ?>관련아이디</a></th>
        <th scope="col"><?php echo subject_sort_link('co_expired') ?>만료여부</a></th>
        <th scope="col">엽전합</th>
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
        <td class="td_chk">
            <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
            <input type="hidden" name="co_id[<?php echo $i ?>]" value="<?php echo $row['co_id'] ?>" id="co_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['co_content'] ?> 내역</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mbid"><a href="?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo $row['mb_id'] ?></a></td>
        <td class="td_mbname"><?php echo get_text($row2['mb_name']); ?></td>
        <td class="td_name sv_use"><div><?php echo $mb_nick ?></div></td>
        <td class="td_pt_log"><?php echo $row['co_content'] ?></td>
        <td class="td_num td_pt"><?php echo number_format($row['co_coin']) ?></td>
        <td class="td_datetime"><?php echo $row['co_datetime'] ?></td>
        <td><?php echo $row['co_rel_table'] ?></td>
        <td><?php echo $row['co_rel_id'] ?></td>
        <td><?php echo $row['co_expired'] ?></td>
        <td class="td_num td_pt"><?php echo number_format(get_coin_sum($row['mb_id'])) ?></td>
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<section id="coin_mng">
    <h2 class="h2_frm">개별회원 엽전 증감 설정</h2>

    <form name="fcoinlist2" method="post" id="fcoinlist2" action="./coin_update.php" autocomplete="off">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="<?php echo $token ?>">

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="mb_id">회원아이디<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="mb_id" value="<?php echo $mb_id ?>" id="mb_id" class="required frm_input" required></td>
        </tr>
        <tr>
            <th scope="row"><label for="co_content">엽전 내용<strong class="sound_only">필수</strong></label></th>
            <td>
                <select name="co_content" id="co_content" required class="required frm_input">
                    <option value="무통장입금">무통장입금</option>
                    <option value="문화상품권">문화상품권</option>
					<option value="기타">기타</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="co_coin">엽전<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="co_coin" id="co_coin" required class="required frm_input"></td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="확인" class="btn_submit">
    </div>

    </form>

</section>
<script>
function fcoinlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
