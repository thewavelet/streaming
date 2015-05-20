<?php
include_once('../../../common.php');

if ($is_guest)
    alert_close('회원만 조회하실 수 있습니다.');

$g5['title'] = $member['mb_nick'].' 님의 엽전 내역';
include_once(G5_PATH.'/head.sub.php');

$list = array();

$sql_common = " from {$g5['coin_table']} where mb_id = '".escape_trim($member['mb_id'])."' ";
$sql_order = " order by co_id desc ";

$sql = " select count(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

// include_once($member_skin_path.'/point.skin.php');

?>

<div id="coin" class="new_win">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>엽전 사용내역 목록</caption>
        <thead>
        <tr>
            <th scope="col">일시</th>
            <th scope="col">내용</th>
            <th scope="col">지급엽전</th>
            <th scope="col">사용엽전</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sum_coin1 = $sum_coin2 = $sum_coin3 = 0;

        $sql = " select *
                    {$sql_common}
                    {$sql_order}
                    limit {$from_record}, {$rows} ";
        $result = sql_query($sql);
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            $coin1 = $coin2 = 0;
            if ($row['co_coin'] > 0) {
                $coin1 = '+' .number_format($row['co_coin']);
                $sum_coin1 += $row['co_coin'];
            } else {
                $coin2 = number_format($row['co_coin']);
                $sum_coin2 += $row['co_coin'];
            }

            $co_content = $row['co_content'];
        ?>
        <tr>
            <td class="td_datetime"><?php echo $row['co_datetime']; ?></td>
            <td><?php echo $co_content; ?></td>
            <td class="td_numbig"><?php echo $coin1; ?></td>
            <td class="td_numbig"><?php echo $coin2; ?></td>
        </tr>
        <?php
        }

        if ($i == 0)
            echo '<tr><td colspan="5" class="empty_table">자료가 없습니다.</td></tr>';
        else {
            if ($sum_coin1 > 0)
                $sum_coin1 = "+" . number_format($sum_coin1);
            $sum_coin2 = number_format($sum_coin2);
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <th scope="row" colspan="2">소계</th>
            <td><?php echo $sum_coin1; ?></td>
            <td><?php echo $sum_coin2; ?></td>
        </tr>
        <tr>
            <th scope="row" colspan="2">보유엽전</th>
            <td colspan="2"><?php 
            echo number_format($sum_coin1 + $sum_coin2); ?>
            </td>
        </tr>
        </tfoot>
        </table>
    </div>

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr.'&amp;page='); ?>

    <div class="win_btn"><button type="button" onclick="javascript:window.close();">창닫기</button></div>
</div>



<?php


include_once(G5_PATH.'/tail.sub.php');
?>