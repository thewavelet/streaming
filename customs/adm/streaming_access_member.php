<?php

$sub_menu = "400100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

if(isset($_GET['wr_id'])) {
	$wr_id = $_GET['wr_id'];
} else {
	alert('wr_id 값이 존재하지 않습니다.');
}

$streaming = sql_fetch(" select * from {$g5['write_prefix']}streaming where wr_id = '{$wr_id}' ");

if(!$streaming['wr_id']) {
	alert('존재하지 않는 방송입니다.');
}


$sql = " select *
            from {$g5['coin_table']}
            where (co_rel_table = 'streaming') and (co_rel_id = '{$wr_id}') and co_content = '방송보기' and co_expired = 0 ";
$result = sql_query($sql);

$g5['title'] = $streaming['wr_subject']. ' 에서의 방송 보기 가능 회원';
include_once ('./admin.head.php');

$colspan = 8;

?>

<div class="local_ov01 local_ov">
	총 <?php echo mysql_num_rows($result) ?> 명
</div>

<section id="coin_mng">
    <h2 class="h2_frm">방송 보기 권한 주기</h2>

    <form name="fcoinlist2" method="post" id="fcoinlist2" action="./streaming_access_member_add.php" autocomplete="off">
    <input type="hidden" name="token" value="<?php echo $token ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="mb_id">회원아이디<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="mb_id" id="mb_id" class="required frm_input" required></td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="확인" class="btn_submit">
    </div>

    </form>

</section>

<form name="fcoinlist" id="fcoinlist" method="post" action="./streaming_access_member_delete.php" onsubmit="return fcoinlist_submit(this);">
<input type="hidden" name="token" value="<?php echo $token ?>">
<input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only">방송 보기 회원 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">회원아이디</th>
        <th scope="col">이름</th>
        <th scope="col">닉네임</th>
        <th scope="col">엽전 내용</th>
        <th scope="col">일시</th>
        <th scope="col">관련테이블</th>
        <th scope="col">관련아이디</th>
        <th scope="col">만료여부</th>
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
            <input type="hidden" name="co_id[<?php echo $i ?>]" value="<?php echo $row['co_id'] ?>" id="co_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['mb_id'] ?> 회원</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mbid"><?php echo $row['mb_id'] ?></td>
        <td class="td_mbname"><?php echo get_text($row2['mb_name']); ?></td>
        <td class="td_name sv_use"><div><?php echo $mb_nick ?></div></td>
        <td class="td_pt_log"><?php echo $row['co_content'] ?></td>
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

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="선택회원권한만료" onclick="document.pressed=this.value">
</div>

</form>

<script>
function fcoinlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택회원권한만료") {
        if(!confirm("선택한 회원의 방송보기 권한을 만료(상실) 상태로 변경하시겠습니까?")) {
            return false;
        }
    }

    return true;
}
</script>


<?php
include_once ('./admin.tail.php');
?>
