<?php
// ![수정필요] 카카오 API 환경설정 파일
include_once dirname(__FILE__) . "/config.php";

// 정보치환
$replace = array(
  '{client_id}' => $kakaoConfig['client_id'],
  '{redirect_uri}' => $kakaoConfig['redirect_uri'],
  '{state}' => md5(mt_rand(111111111, 999999999)),
);

setcookie('state', $replace['{state}'], time() + 300, '/'); // 300 초동안 유효

$login_auth_url = str_replace(array_keys($replace), array_values($replace), $kakaoConfig['login_auth_url']);

?>

<script src="//code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<div class="kakao-login">
  <a href="<?php echo $login_auth_url ?>" id="kakao-login">
    <img alt="resource preview" src="https://k.kakaocdn.net/14/dn/btroDszwNrM/I6efHub1SN5KCJqLm1Ovx1/o.jpg">
  </a>
</div>
