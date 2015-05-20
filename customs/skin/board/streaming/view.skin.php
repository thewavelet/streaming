<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

add_stylesheet('<link href="//vjs.zencdn.net/4.12/video-js.css" rel="stylesheet">', 1);
add_javascript('<script src="//vjs.zencdn.net/4.12/video.js"></script>', 2);

// 관리자가 아니라면
if($is_admin != 'super') {

    // echo has_streaming_access($member['mb_id'], $view['wr_id']);
    // exit;

    // 방송종료 상태라면 '종료된 방송입니다.' 를 띄워주고 돌아가기.
    if(!is_streaming_onoff($view['wr_id'])) {
        alert('종료된 방송입니다.', $_SERVER['PHP_SELF']."?bo_table=streaming");
    }

    // 이미 엽전을 소모하여 이 방송을 볼 권한을 가지고 있지 않다면
    if(!has_streaming_access($member['mb_id'], $view['wr_id'])) {
        
        if(get_coin_sum($member['mb_id']) < 1) {
            // 엽전 갯수가 0 이라면 볼 수 없다. 엽전 충전 페이지로 이동하겠냐는 확인창을 보여주고 확인시 엽전 충전페이지로 이동.
            // 취소시 방송 리스트로 돌아감. (여기에는 REFERER 를 넣어줌.)
            confirm('엽전이 부족합니다. 엽전 충전 페이지로 이동하시겠습니까?', G5_BBS_URL.'/qalist.php', $_SERVER['PHP_SELF']."?bo_table=streaming");
        } else {
            // 엽전의 갯수가 1개 이상이라면 엽전을 소모하여 방송을 볼 것인지 물어본다. 방송을 볼 경우에는 엽전 1개 소모.
            // 방송을 보지 않을 것이라면 리스트로 돌아감. (여기는 방송보기 게시판 리스트 URL 을 넣어줌.)
            // confirm('방송을 보시려면 엽전이 1개 소모됩니다. 계속하시겠습니까?', $_SERVER['PHP_SELF'], G5_BBS_URL.'/board.php?bo_table=streaming');
            // 엽전 소모하여 방송보기를 하였을 경우에 다시 자기 자신을 호출하는 이유는 이미 has_streaming_access 를 수행시 엽전이 사용된 상태라 권한이 생겨 아래로 넘어갈 수 있기 때문.
            // 대신 confirm 전에 세션 변수로 streaming_access_request 를 1로 셋팅해야함.

            if (isset($confirmed)) {
                $confirmed = (int)$confirmed;
            } else {
                $confirmed = 0;
            }

            // 위의 주석에 해당 사항은 list.skin.php 적용. 여기서는 confirm 창 이후 동작을 제어할 수 없기에.
            // 하지만 직접 접근하는 사람의 경우 팝업이 뜨지 않는다. list.skin.php 뿐 아니라 메인페이지에도 링크가 있고 해서...
            // 결국 여기서 처리해야할 듯 함.
            if(!$confirmed) {
/*
// http://lovekmg.blogspot.kr/2008/08/%EC%84%9C%EB%B2%84%EB%B3%80%EC%88%98-server.html
echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];echo "<br>";
echo $_SERVER['HTTP_HOST'];echo "<br>"; // streaming.dev
echo $_SERVER['REQUEST_URI'];echo "<br>"; // /bbs/board.php?bo_table=streaming&wr_id=3
echo $_SERVER['QUERY_STRING'];echo "<br>"; // bo_table=streaming&wr_id=3
echo "<br>";
echo $_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];echo "<br>";
echo $_SERVER['PHP_SELF'];echo "<br>"; // /bbs/board.php
echo $_SERVER['QUERY_STRING'];echo "<br>"; // bo_table=streaming&wr_id=3
echo $_SERVER['PHP_SELF']."?bo_table=streaming&wr_id={$view['wr_id']}&confirmed=1";
echo $_SERVER['HTTP_REFERER'];
echo G5_BBS_URL.'/board.php?bo_table=streaming'; // http://streaming.dev/bbs/board.php?bo_table=streaming
exit;
*/
                confirm('방송을 보시려면 엽전이 1개 소모됩니다. 계속하시겠습니까?', 
                            $_SERVER['REQUEST_URI']."&confirmed=1", 
                            $_SERVER['PHP_SELF']."?bo_table=streaming");  

            } else {

                // 여기서 엽전 1개 소모.
                if(get_coin_sum($member['mb_id']) < 1) {
                    confirm('엽전이 부족합니다. 엽전 충전 페이지로 이동하시겠습니까?', G5_BBS_URL.'/qalist.php', $_SERVER['PHP_SELF']."?bo_table=streaming");
                } else {
                    insert_coin($member['mb_id'], -1, '방송보기', 'streaming', $view['wr_id']);
                    $new_uri = remove_querystring_var($_SERVER['REQUEST_URI'], 'confirmed');
                    goto_url($new_uri);
                }
                
            }
            

            /*

<?php if ($is_member && has_streaming_access($member['mb_id'], $view['wr_id'])) { ?>

<script>
document.getElementsByClassName = function( classname ) {
    var elArray = [];
    var tmp = document.getElementsByTagName("*");
    var regex = new RegExp("(^|\s)" + classname + "(\s|$)");
    for ( var i = 0; i < tmp.length; i++ ) {
 
        if ( regex.test(tmp[i].className) ) {
            elArray.push(tmp[i]);
        }
    }
 
    return elArray;
}

var streamingLinkEl = document.getElementsByClassName('streaming_link');
for (var i=0; i<streamingLinkEl.length; i++) {
    streamingLinkEl[i].onclick = function(e) {
        if(!confirm('방송을 보시려면 엽전 1개가 소모됩니다. 계속하시겠습니까?')) {
            e.preventDefault();
            document.location.replace("<?php echo G5_BBS_URL.'/board.php?bo_table=streaming'; ?>");
        }
    }
}

</script>

<?php } ?>

            */

        }

    } 

    

} 


?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<!-- 게시물 읽기 시작 { -->
<div id="bo_v_table"><?php echo $board['bo_subject']; ?></div>

<article id="bo_v" style="width:<?php echo $width; ?>">
    <header>
        <h1 id="bo_v_title">
            <?php
            if ($category_name) echo $view['ca_name'].' | '; // 분류 출력 끝
            echo cut_str(get_text($view['wr_subject']), 70); // 글제목 출력
            ?>
            <?php if($view['wr_2'] == '1') echo ' (방송중)'; else if($view['wr_2'] == '0') echo ' (방송종료)'; ?>
        </h1>
    </header>

    <section id="bo_v_info">
        <h2>페이지 정보</h2>
        작성자 <strong><?php echo $view['name'] ?><?php if ($is_ip_view) { echo "&nbsp;($ip)"; } ?></strong>
        <span class="sound_only">작성일</span><strong><?php echo date("y-m-d H:i", strtotime($view['wr_datetime'])) ?></strong>
        조회<strong><?php echo number_format($view['wr_hit']) ?>회</strong>
        댓글<strong><?php echo number_format($view['wr_comment']) ?>건</strong>
    </section>

    <?php
    if ($view['file']['count']) {
        $cnt = 0;
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                $cnt++;
        }
    }
     ?>

    <?php if($cnt) { ?>
    <!-- 첨부파일 시작 { -->
    <section id="bo_v_file">
        <h2>첨부파일</h2>
        <ul>
        <?php
        // 가변 파일
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
         ?>
            <li>
                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
                    <img src="<?php echo $board_skin_url ?>/img/icon_file.gif" alt="첨부">
                    <strong><?php echo $view['file'][$i]['source'] ?></strong>
                    <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                </a>
                <span class="bo_v_file_cnt"><?php echo $view['file'][$i]['download'] ?>회 다운로드</span>
                <span>DATE : <?php echo $view['file'][$i]['datetime'] ?></span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } 첨부파일 끝 -->
    <?php } ?>

    <?php
    if (implode('', $view['link'])) {
     ?>
     <!-- 관련링크 시작 { -->
    <section id="bo_v_link">
        <h2>관련링크</h2>
        <ul>
        <?php
        // 링크
        $cnt = 0;
        for ($i=1; $i<=count($view['link']); $i++) {
            if ($view['link'][$i]) {
                $cnt++;
                $link = cut_str($view['link'][$i], 70);
         ?>
            <li>
                <a href="<?php echo $view['link_href'][$i] ?>" target="_blank">
                    <img src="<?php echo $board_skin_url ?>/img/icon_link.gif" alt="관련링크">
                    <strong><?php echo $link ?></strong>
                </a>
                <span class="bo_v_link_cnt"><?php echo $view['link_hit'][$i] ?>회 연결</span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } 관련링크 끝 -->
    <?php } ?>

    <!-- 게시물 상단 버튼 시작 { -->
    <div id="bo_v_top">
        <?php
        ob_start();
         ?>
        <?php if ($prev_href || $next_href) { ?>
        <ul class="bo_v_nb">
            <?php /*
            <?php if ($prev_href) { ?><li><a href="<?php echo $prev_href ?>" class="btn_b01">이전글</a></li><?php } ?>
            <?php if ($next_href) { ?><li><a href="<?php echo $next_href ?>" class="btn_b01">다음글</a></li><?php } ?>
            */ ?>
            <li><a href="/custom/coin_present.php" class="btn_admin coin_present">엽전 선물 하기</a></li>
        </ul>
        <?php } ?>

        <ul class="bo_v_com">
            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>" class="btn_b01">수정</a></li><?php } ?>
            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" class="btn_b01" onclick="del(this.href); return false;">삭제</a></li><?php } ?>
            <?php if ($copy_href) { ?><li><a href="<?php echo $copy_href ?>" class="btn_admin" onclick="board_move(this.href); return false;">복사</a></li><?php } ?>
            <?php if ($move_href) { ?><li><a href="<?php echo $move_href ?>" class="btn_admin" onclick="board_move(this.href); return false;">이동</a></li><?php } ?>
            <?php if ($search_href) { ?><li><a href="<?php echo $search_href ?>" class="btn_b01">검색</a></li><?php } ?>
            <li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li>
            <?php /*
            <?php if ($reply_href) { ?><li><a href="<?php echo $reply_href ?>" class="btn_b01">답변</a></li><?php } ?>
            */ ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
        </ul>
        <?php
        $link_buttons = ob_get_contents();
        ob_end_flush();
         ?>
    </div>
    <!-- } 게시물 상단 버튼 끝 -->

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">본문</h2>

        <div id="bo_v_streaming" style="display:inline-block;">
            <video id="example_video_1" class="video-js vjs-default-skin"
            autoplay="autoplay"
            preload="auto" width="540" height="420"
            poster="http://video-js.zencoder.com/oceans-clip.png"
            data-setup='{"example_option":true}'>
            <source src="<?php echo $view['wr_1'] ?>" type='video/flv' />
            <source src="<?php echo $view['wr_1'] ?>" type='video/mp4' />
            <source src="<?php echo $view['wr_1'] ?>" type='video/webm' />
            <source src="<?php echo $view['wr_1'] ?>" type='video/ogg' />
            <!--
            <source src="http://video-js.zencoder.com/oceans-clip.mp4" type='video/mp4' />
            <source src="http://video-js.zencoder.com/oceans-clip.webm" type='video/webm' />
            <source src="http://video-js.zencoder.com/oceans-clip.ogv" type='video/ogg' />
            -->
            <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
            </video>
        </div>

 
        <embed height="420" width="180" src="http://www.gagalive.kr/livechat1.swf?chatroom=<?php echo 'streaming_bbs'.$view['wr_id']?>"></embed>



<?php if($is_admin == 'super') { ?>


        <script type="text/javascript">

            $(function() {

                //getCoinPresentLog();
                setInterval(function() {
                    getCoinPresentLog();
                    $('#coin-present-log')[0].scrollTop = $('#coin-present-log')[0].scrollHeight;
                }, 3000);

            });

            function getCoinPresentLog() {
                var datetime = "";
                $logs = $('#coin-present-log p');
                if($logs.length > 0) {
                    datetime = $logs.last().find('span').text();
                }

                // 그 담부터는 마지막 로그의 시간 기록보다 큰 데이터만 가져옴.
                $.post('/custom/coin_present_log.php',
                    {rel_table: "streaming", rel_id: <?php echo $view['wr_id'] ?>, datetime: datetime},
                    function(data) {
                        if(data.length > 0) {
                            for(var i = 0; i < data.length; i++)
                                $('#coin-present-log').append('<p>' + data[i].mb_id + ' 님께서 엽전 ' + Math.abs(data[i].co_coin) + ' 개를 선물하셨습니다. - <span>' + data[i].co_datetime + '</span></p>');    
                        }
                        
                    }, "json"
                );    
                
            }

            /**
             * You first need to create a formatting function to pad numbers to two digits…
             **/
            function twoDigits(d) {
                if(0 <= d && d < 10) return "0" + d.toString();
                if(-10 < d && d < 0) return "-0" + (-1*d).toString();
                return d.toString();
            }

            /**
             * …and then create the method to output the date string as desired.
             * Some people hate using prototypes this way, but if you are going
             * to apply this to more than one Date object, having it as a prototype
             * makes sense.
             **/
            Date.prototype.toMysqlFormat = function() {
                return this.getUTCFullYear() + "-" + twoDigits(1 + this.getUTCMonth()) + "-" + twoDigits(this.getUTCDate()) + " " + twoDigits(this.getUTCHours()) + ":" + twoDigits(this.getUTCMinutes()) + ":" + twoDigits(this.getUTCSeconds());
            };

        </script>

        <div id="coin-present-records">
            <h6>엽전 선물 내역</h6>
            <div id="coin-present-log" style="border:1px solid #d6d6d6;padding:5px;font-size:12px;height:100px;overflow-y:auto;">

            </div>
        </div>



<?php } ?>



        <?php
        // 파일 출력
        $v_img_count = count($view['file']);
        if($v_img_count) {
            echo "<div id=\"bo_v_img\">\n";

            for ($i=0; $i<=count($view['file']); $i++) {
                if ($view['file'][$i]['view']) {
                    //echo $view['file'][$i]['view'];
                    echo get_view_thumbnail($view['file'][$i]['view']);
                }
            }

            echo "</div>\n";
        }
         ?>

        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con"><?php echo get_view_thumbnail($view['content']); ?></div>
        <?php//echo $view['rich_content']; // {이미지:0} 과 같은 코드를 사용할 경우 ?>
        <!-- } 본문 내용 끝 -->

        <?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>

        <?php /*
        <!-- 스크랩 추천 비추천 시작 { -->
        <?php if ($scrap_href || $good_href || $nogood_href) { ?>
        <div id="bo_v_act">
            <?php if ($scrap_href) { ?><a href="<?php echo $scrap_href;  ?>" target="_blank" class="btn_b01" onclick="win_scrap(this.href); return false;">스크랩</a><?php } ?>
            <?php if ($good_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $good_href.'&amp;'.$qstr ?>" id="good_button" class="btn_b01">추천 <strong><?php echo number_format($view['wr_good']) ?></strong></a>
                <b id="bo_v_act_good"></b>
            </span>
            <?php } ?>
            <?php if ($nogood_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $nogood_href.'&amp;'.$qstr ?>" id="nogood_button" class="btn_b01">비추천  <strong><?php echo number_format($view['wr_nogood']) ?></strong></a>
                <b id="bo_v_act_nogood"></b>
            </span>
            <?php } ?>
        </div>
        <?php } else {
            if($board['bo_use_good'] || $board['bo_use_nogood']) {
        ?>
        <div id="bo_v_act">
            <?php if($board['bo_use_good']) { ?><span>추천 <strong><?php echo number_format($view['wr_good']) ?></strong></span><?php } ?>
            <?php if($board['bo_use_nogood']) { ?><span>비추천 <strong><?php echo number_format($view['wr_nogood']) ?></strong></span><?php } ?>
        </div>
        <?php
            }
        }
        ?>
        <!-- } 스크랩 추천 비추천 끝 -->
        */ ?>
    </section>

    <?php
    include_once(G5_SNS_PATH."/view.sns.skin.php");
    ?>

    <?php
    // 코멘트 입출력
    include_once('./view_comment.php');
     ?>

    <!-- 링크 버튼 시작 { -->
    <div id="bo_v_bot">
        <?php echo $link_buttons ?>
    </div>
    <!-- } 링크 버튼 끝 -->

</article>
<!-- } 게시판 읽기 끝 -->

<script>
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        if(!g5_is_member) {
            alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
            return false;
        }

        var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();

    $("a.coin_present").click(function(e) {
        e.preventDefault();
        var amount = prompt('몇개?');
        if(!amount || amount < 1) {
            alert("1개 이상을 입력해 주세요.");
            return false;
        }

        excute_coin_present(this.href, <?php echo $view['wr_id'] ?>, amount);
    });
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        }, "json"
    );
}

function excute_coin_present(href, wr_id, amount)
{
    $.post(
        href,
        {wr_id: wr_id, amount: amount},
        function(data) {
            if(data) {
                alert(data); return false;    
            }
            alert('선물하였습니다.');
            $outloginCoin = $('#ol_after_coin>strong')
            $outloginCoin.text(Number($outloginCoin.text()) - Math.abs(amount));
        }
    );
}

</script>
<!-- } 게시글 읽기 끝 -->
