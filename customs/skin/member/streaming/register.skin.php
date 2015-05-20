<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);

// 여기에서 member 테이블에 접속한 사용자의 ip 가 있다면 '같은 ip 주소로 가입한 회원이 존재합니다.' 메시지를 보여주고 메인으로 이동.
// 만약 회원관리 > 중복가입허용ip관리 에 ip 가 존재한다면 무조건 통과. 

$sql = " select count(*) as cnt
            from {$g5['ip_exception_table']}
            where ip_ip = '{$_SERVER['REMOTE_ADDR']}' ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

if($total_count > 0) {

} else {
    $sql = " select count(*) as cnt
                from {$g5['member_table']}
                where mb_ip = '{$_SERVER['REMOTE_ADDR']}' ";
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];

    if($total_count > 0) {
        alert('같은 ip 주소로 가입한 회원이 존재합니다.');    
    }
}

?>

<!-- 회원가입약관 동의 시작 { -->
<div class="mbskin">
    <form  name="fregister" id="fregister" action="<?php echo $register_action_url ?>" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">

    <p>회원가입약관 및 개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.</p>

    <section id="fregister_term">
        <h2>회원가입약관</h2>
        <textarea readonly><?php echo get_text($config['cf_stipulation']) ?></textarea>
        <fieldset class="fregister_agree">
            <label for="agree11">회원가입약관의 내용에 동의합니다.</label>
            <input type="checkbox" name="agree" value="1" id="agree11">
        </fieldset>
    </section>

    <section id="fregister_private">
        <h2>개인정보처리방침안내</h2>
        <textarea readonly><?php echo get_text($config['cf_privacy']) ?></textarea>
        <fieldset class="fregister_agree">
            <label for="agree21">개인정보처리방침안내의 내용에 동의합니다.</label>
            <input type="checkbox" name="agree2" value="1" id="agree21">
        </fieldset>
    </section>

    <div class="btn_confirm">
        <input type="submit" class="btn_submit" value="회원가입">
    </div>

    </form>

    <script>
    function fregister_submit(f)
    {
        if (!f.agree.checked) {
            alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
            f.agree.focus();
            return false;
        }

        if (!f.agree2.checked) {
            alert("개인정보처리방침안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
            f.agree2.focus();
            return false;
        }

        return true;
    }
    </script>
</div>
<!-- } 회원가입 약관 동의 끝 -->