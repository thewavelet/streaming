<?php

// streaming_list.php 에 각 회차별 엽전 사용내역 및 방송보기권한 관리할 수 있는 기능. 우선 방송 리스트가 쭉 나오고, 
// 거기에 정보들 (방송중인지 종료인지 방송별로 총 엽전 몇개 사용되었는지 등등... 
// 그리고 클릭시에 streaming_view.php 를 보여주고 그곳에 회차별 엽전 사용내역이 각 사용자별로 얼마나 사용하였는지 정보들 나오고, 
// 방송보기 권한을 가진 사용자들이 나오고 그 사용자들을 삭제 또는 추가할 수 있도록.)

// 방송보기 권한을 가진 사용자들
// 제목, 방송 url, 방송상태, 총사용 엽전, 생성일, 엽전 사용 내역(버튼), 방송 보기 가능 회원(버튼).
// streaming_coin_history.php 는 사용자 엽전 사용 내역. 그 회차에 해당하는 엽전 사용 내역 리스트.
// streaming_access_member.php 는 방송 보기 가능 회원. 그 회차에 해당하는 방송 보기 가능 회원 리스트 및 추가 삭제할 수 있도록. 삭제시에는 co_expired=1 로 해줌.
// 즉, 방송 볼 권한을 가진 회원은 co_content='방송보기구매', co_rel_table='streaming', co_rel_id='방송보기게시판글id', co_expired=0 인 것이 한개 이상 있어야 함.

$sub_menu = "400100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

$write_table = $g5['write_prefix'] . 'streaming'; // 게시판 테이블 전체이름

$sql_common = " from {$write_table} ";

$sql_search = " where (1) ";

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        //case 'mb_id' :
        //    $sql_search .= " ({$sfl} = '{$stx}') ";
        //    break;
        default :
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "wr_id";
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

$g5['title'] = '방송관리';
include_once ('./admin.head.php');

$colspan = 7;

?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    전체 <?php echo number_format($total_count) ?> 건
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
	<!--
    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
    -->
    <option value="wr_subject"<?php echo get_selected($_GET['sfl'], "wr_subject"); ?>>제목</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>

<form name="fstreaminglist" id="fstreaminglist" method="post" action="./streaming_wr_2_onoff.php" onsubmit="return fstreaminglist_submit(this);">
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
    	<!-- 제목, 방송 url, 방송상태, 총사용 엽전, 생성일, 엽전 사용 내역(버튼), 방송 보기 가능 회원(버튼). -->
        <th scope="col">
            <label for="chkall" class="sound_only">방송 내역 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">제목</th>
        <th scope="col">방송 url</th>
        <th scope="col"><?php echo subject_sort_link('wr_2') ?>방송상태</th>
        <th scope="col">방송보기한 엽전</th>
        <th scope="col">선물한 엽전</th>
        <th scope="col">총 사용 엽전</th>
        <th scope="col"><?php echo subject_sort_link('wr_datetime') ?>생성일</th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $link1 = $link2 = '';
        $link1 = '<a href="'.G5_BBS_URL.'/board.php?bo_table=streaming'.'&amp;wr_id='.$row['wr_id'].'" target="_blank">';
        $link2 = '</a>';

        $expr = '';
        if($row['po_expired'] == 1)
            $expr = ' txt_expired';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <input type="hidden" name="wr_id[<?php echo $i ?>]" value="<?php echo $row['wr_id'] ?>" id="wr_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['wr_subject'] ?> 내역</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_subject"><?php echo $link1 ?><?php echo get_text($row['wr_subject']); ?><?php echo $link2 ?></td>
        <td class="td_wr_1"><?php echo $row['wr_1']; ?></td>
        <td class="td_wr_2"><div><?php if($row['wr_2'] == '1') echo '방송중'; else if($row['wr_2'] == '0') echo '방송종료'; ?></div></td>
        <td class="td_view_coin_sum"><?php echo get_view_coin_sum('streaming', $row['wr_id']); ?></td>
        <td class="td_present_coin_sum"><?php echo get_present_coin_sum('streaming', $row['wr_id']); ?></td>
        <td class="td_coin_sum"><?php echo get_streaming_coin_sum('streaming', $row['wr_id']); ?></td>
        <td class="td_datetime"><?php echo $row['wr_datetime'] ?></td>
        <td class="td_manage">
        	<a href="<?php echo G5_ADMIN_URL ?>/streaming_coin_history.php?wr_id=<?php echo $row['wr_id'] ?>" class="btn_win">엽전사용내역</a>
        	<a href="<?php echo G5_ADMIN_URL ?>/streaming_access_member.php?wr_id=<?php echo $row['wr_id'] ?>" class="btn_win">방송보기가능회원</a>
        </td>
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
    <input type="submit" name="act_button" value="선택방송종료" onclick="document.pressed=this.value">
	<input type="submit" name="act_button" value="선택방송중" onclick="document.pressed=this.value">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<script>
function fstreaminglist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택방송종료") {
        if(!confirm("선택한 방송을 정말 방송종료 상태로 변경하시겠습니까?")) {
            return false;
        }
    } else if(document.pressed == "선택방송중") {
    	if(!confirm("선택한 방송을 정말 방송중 상태로 변경하시겠습니까?")) {
    		return false;
    	}
    }

    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>