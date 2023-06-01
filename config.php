<?php

/* php dotenv 사용을 위해 vendor 폴더 내부의 autoload.php require 함. */
require_once "vendor/autoload.php";

/* php dotenv 사용법 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 카카오 클라이언트 ID 값, 비밀키 값
$client_id = $_ENV['CLIENT_ID'];
$client_secret = $_ENV['CLIENT_SECRET'];

// 설정 파일 조정 필요
$kakaoConfig = array(
  // ![수정필요] 카카오 REST API 키값 , 카카오 개발자 사이트 > 내 애플리케이션 > 요약정보에서 REST API 키값
  'client_id' => $client_id,

  // ![수정필요] 카카오 개발자 사이트 > 내 애플리케이션 > 카카오로그인 > 보안 에서 생성가능
  'client_secret' => $client_secret,

  // 로그인 인증 URL
  'login_auth_url' => 'https://kauth.kakao.com/oauth/authorize?response_type=code&client_id={client_id}&redirect_uri={redirect_uri}&state={state}',

  // 로그인 인증토큰 요청 URL
  'login_token_url' => 'https://kauth.kakao.com/oauth/token?grant_type=authorization_code&client_id={client_id}&redirect_uri={redirect_uri}&client_secret={client_secret}&code={code}',

  // 프로필정보 호출 URL
  'profile_url' => 'https://kapi.kakao.com/v2/user/me',

  // ![수정필요] 로그인 인증 후 Callback url 설정 - 변경시 URL 수정 필요, 카카오 개발자 사이트 > 내 애플리케이션 > 카카오로그인 > Redirect URI 에서 등록가능
  'redirect_uri' => 'http' . (!empty($_SERVER['HTTPS']) ? 's' : null) . '://' . $_SERVER['HTTP_HOST'] . '/kakaoLogin/oauth.php',
);

// 함수: 카카오 curl 통신
function curl_kakao($url, $headers = array())
{
  if (empty($url)) {
    return false;
  }

  // URL에서 데이터를 추출하여 쿼리문 생성
  $purl = parse_url($url);
  $postfields = array();
  if (!empty($purl['query']) && trim($purl['query']) != '') {
    $postfields = explode("&", $purl['query']);
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
  if (count($headers) > 0) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }

  ob_start(); // prevent any output
  $data = curl_exec($ch);
  ob_end_clean(); // stop preventing output

  if (curl_error($ch)) {
    return false;
  }

  curl_close($ch);
  return $data;
}
