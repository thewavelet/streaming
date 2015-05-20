<?php
// 회원가입시 중복되어도 될 ip 들을 입력할 수 있도록.
$sub_menu = "200500";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

if(!sql_query(" DESC {$g5['ip_exception_table']} ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['ip_exception_table']}` (
                    `ip_id` int(11) NOT NULL AUTO_INCREMENT,
                    `ip_ip` varchar(255) NOT NULL DEFAULT '',
                    `ip_content` varchar(255) NOT NULL DEFAULT '',
                    `ip_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    PRIMARY KEY (`ip_id`),
                    KEY `index1` (`ip_ip`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ", false);

    mysql_query($sql) or die(mysql_error() . "<p>" . $sql);
}

// 체크된 자료 삭제
if (isset($_POST['chk']) && is_array($_POST['chk'])) {
    for ($i=0; $i<count($_POST['chk']); $i++) {
        $ip_id = $_POST['chk'][$i];

        sql_query(" delete from {$g5['ip_exception_table']} where ip_id = '$ip_id' ", true);
    }
}

$sql = " select count(*) as cnt
            from {$g5['ip_exception_table']}
            order by ip_datetime desc ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = " select * 
            from {$g5['ip_exception_table']}
            order by ip_datetime desc ";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

$g5['title'] = '중복가입허용ip관리';
include_once('./admin.head.php');

$colspan = 4;
?>

<script>
var list_update_php = '';
var list_delete_php = 'ip_exception_list.php';
</script>

<div class="local_ov01 local_ov">
        <?php echo $listall ?>
        건수 : <?php echo number_format($total_count) ?>개
</div>

<form name="fipexceptionist" id="fipexceptionlist" method="post">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only">중복 가입 허용 ip 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">등록IP</th>
        <th scope="col">내용</th>
        <th scope="col">등록일</th>
        
        
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $content = get_text($row['ip_content']);
        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $content ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['ip_id'] ?>" id="chk_<?php echo $i ?>">
        </td>
        <td><?php echo $row['ip_ip'] ?></td>
        <td><?php echo $content ?></td>
        <td><?php echo $row['ip_datetime'] ?></td>
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>

</div>

<?php if ($is_admin == 'super'){ ?>
<div class="btn_list01 btn_list">
    <button type="submit">선택삭제</button>
</div>
<?php } ?>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<section id="coin_mng">
    <h2 class="h2_frm">중복 가입 허용 ip 추가</h2>

    <form name="fipexceptionlist2" method="post" id="fipexceptionlist2" action="./ip_exception_update.php" autocomplete="off">
    <input type="hidden" name="token" value="<?php echo $token ?>">

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <colgroup>
            <col class="grid_3">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="ip_ip">아이피<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="ip_ip" value="<?php echo $ip_ip ?>" id="ip_ip" class="required frm_input" required></td>
        </tr>
        <tr>
            <th scope="row"><label for="ip_content">내용<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="ip_content" id="ip_content" required class="required frm_input" size="80"></td>
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
$(function() {
    $('#fexceptionlist').submit(function() {
        if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
            if (!is_checked("chk[]")) {
                alert("선택삭제 하실 항목을 하나 이상 선택하세요.");
                return false;
            }

            return true;
        } else {
            return false;
        }
    });
});
</script>

<?php
include_once('./admin.tail.php');
?>