<?php
$text = urlencode($_GET['text']);
$from = urlencode($_GET['from']);
$to = urlencode($_GET['to']);

//Client ID of the application.
$clientID       = "gs_2nd_33_translator";
//Client Secret key of the application.
$clientSecret = "TpFALBTJIGUD8Gn7mHdp8p6Rz9wxhaXhil0W82p8cmo=";
//OAuth Url.
$authUrl      = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
//Application Scope Url
$scopeUrl     = "http://api.microsofttranslator.com";
//Application grant type
$grantType    = "client_credentials";
//Create the AccessTokenAuthentication object.
$authObj      = new AccessTokenAuthentication();
//Get the Access token.
$accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
//Create the authorization Header string.
$authHeader = 'Authorization : Bearer '. $accessToken;

$translateUrl = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate?text='.$text.'&from='.$from.'&to='.$to;

$ch = curl_init($translateUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader, 'Content-Type: text/xml'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
echo mb_substr($result, 2, mb_strlen($result) - 3);

class AccessTokenAuthentication {
  /*
   * Get the access token.
   *
   * @param string $grantType    Grant type.
   * @param string $scopeUrl     Application Scope URL.
   * @param string $clientID     Application client ID.
   * @param string $clientSecret Application client ID.
   * @param string $authUrl      Oauth Url.
   *
   * @return string.
   */
  function getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl){
    try {
      //Initialize the Curl Session.
      $ch = curl_init();
      //Create the request Array.
      $paramArr = array (
        'grant_type'    => $grantType,
        'scope'         => $scopeUrl,
        'client_id'     => $clientID,
        'client_secret' => $clientSecret
      );
      //Create an Http Query.//
      $paramArr = http_build_query($paramArr);
      //Set the Curl URL.
      curl_setopt($ch, CURLOPT_URL, $authUrl);
      //Set HTTP POST Request.
      curl_setopt($ch, CURLOPT_POST, TRUE);
      //Set data to POST in HTTP "POST" Operation.
      curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArr);
      //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
      curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
      //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      //Execute the  cURL session.
      $strResponse = curl_exec($ch);
      //Get the Error Code returned by Curl.
      $curlErrno = curl_errno($ch);
      if($curlErrno){
        $curlError = curl_error($ch);
        throw new Exception($curlError);
      }
      //Close the Curl Session.
      curl_close($ch);
      //Decode the returned JSON string.
      $objResponse = json_decode($strResponse);
      if (array_key_exists('error', $objResponse)){
        throw new Exception($objResponse->error_description);
      }
      return $objResponse->access_token;
    } catch (Exception $e) {
      echo "Exception-".$e->getMessage();
    }
  }
}
?>
