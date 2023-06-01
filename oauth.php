<?php

try {

  // ![수정필요] 카카오 API 환경설정 파일
  include_once dirname(__FILE__) . "/config.php";

  // 기본 응답 설정
  $res = array('rst' => 'fail', 'code' => (__LINE__ * -1), 'msg' => '');

  $code = $_GET['code'] ?? null;
  $state = $_GET['state'] ?? null;

  $cookieState = $_COOKIE['state'] ?? null;

  // code && state 체크
  if (empty($code) || empty($state) || $state != $cookieState) {
    throw new Exception("인증실패", (__LINE__ * -1));
  }

  // 토큰 요청
  $replace = array(
    '{grant_type}' => 'authorization_code',
    '{client_id}' => $kakaoConfig['client_id'],
    '{redirect_uri}' => $kakaoConfig['redirect_uri'],
    '{client_secret}' => $kakaoConfig['client_secret'],
    '{code}' => $_GET['code']
  );

  $login_token_url = str_replace(array_keys($replace), array_values($replace), $kakaoConfig['login_token_url']);

  $token_data = json_decode(curl_kakao($login_token_url));

  if (empty($token_data)) {
    throw new Exception("토큰요청 실패", (__LINE__ * -1));
  }

  if (!empty($token_data->error) || empty($token_data->access_token)) {
    throw new Exception("토큰인증 에러", (__LINE__ * -1));
  }


  // 프로필 요청
  $header = array("Authorization: Bearer " . $token_data->access_token);
  $profile_url = $kakaoConfig['profile_url'];
  $profile_data = json_decode(curl_kakao($profile_url, $header));

  if (empty($profile_data) || empty($profile_data->id)) {
    throw new Exception("프로필요청 실패", (__LINE__ * -1));
  }

  // 프로필정보 저장 -- DB를 통해 저장하세요

  /*  echo '<pre>';
   print_r($profile_data);
   echo '</pre>';

   foreach ($profile_data as $key => $value) {
     if ($key == 'properties') {
       foreach ($value as $k => $v) {
         echo $k . ' : ' . $v . '<br>';
       }
     } elseif ($key == 'kakao_account') {
       foreach ($value as $k => $v) {
         if ($k == 'profile') {
           foreach ($v as $key => $item) {
             echo $key . ' : ' . $item . '<br>';
           }
         } else {
           if ($v == null) {
             continue;
           } else {
             echo $k . ' : ' . $v . '<br>';
           }
         }
       }
     } else {

       if ($key == "id") {
         setcookie('id', time() + 3600 * 24 * 30);
       } else {
         echo $key . ' : ' . $value . '<br>';
       }
     }
   } */

  $is_member = true; // 기존회원인지(true) / 비회원인지(false) db 체크

  // 로그인 회원일 경우
  if ($is_member === true) {

  }

  // 새로 가입일 경우
  else {

  }

  // 최종 성공 처리
  $res['rst'] = 'success';

} catch (Exception $e) {
  if (!empty($e->getMessage())) {
    $res['msg'] = $e->getMessage();
  }
  if (!empty($e->getMessage())) {
    $res['code'] = $e->getCode();
  }
}


// 성공처리
if ($res['rst'] == 'success') {

}

// 실패처리
else {

}

// state 초기화
setcookie('state', '', time() - 3000); // 300 초동안 유효

// header("Location: /arrayvalues");
exit;
