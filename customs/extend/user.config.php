<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// head.sub.php, tail.sub.php 파일의 이름을 지정할 수 있습니다.
// 지정된 파일은 head.sub.php, tail.sub.php 파일과 동일한 위치에 존재해야 합니다.
//define('G5_HEAD_SUB_FILE', 'user.head.sub.php');
//define('G5_TAIL_SUB_FILE', 'user.tail.sub.php');

// Remove a Query String Key=>Value, (http://davidwalsh.name/php-remove-variable)
function remove_querystring_var($url, $key) { 
	$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&'); 
	$url = substr($url, 0, -1); 
	return $url; 
}

?>