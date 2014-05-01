<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
/**
* Example of simple product POST using Admin account via Magento REST API. OAuth authorization is used
*/


//we'll need this later
if( !function_exists( 'http_parse_headers' ) ) {
     function http_parse_headers( $header )
     {
         $retVal = array();
         $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
         foreach( $fields as $field ) {
             if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                 $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                 if( isset($retVal[$match[1]]) ) {
                     $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                 } else {
                     $retVal[$match[1]] = trim($match[2]);
                 }
             }
         }
         return $retVal;
     }
}

$host = "http://thethingsandstuff.com";
$callbackUrl = $host."/static/report/test.php";
$temporaryCredentialsRequestUrl = $host."/oauth/initiate?oauth_callback=" . urlencode($callbackUrl);
$adminAuthorizationUrl = $host.'/admin/oauth_authorize';
$accessTokenRequestUrl = $host.'/oauth/token';
$apiUrl = $host.'/api/rest';
$consumerKey = 'b98lbkxvjpfe1ot8gx2bzlcxgqw7hnkr';
$consumerSecret = 'u5663plc0df72ojjkydnqu2libdrujgs';

session_start();
extract($_POST);
$token = "52eb285a49e57";
$token |= $_GET['token'];
if (!isset($_GET['oauth_token']) && isset($_SESSION['state']) && $_SESSION['state'] == 1) {
    $_SESSION['state'] = 0;
}
try {
    $authType = ($_SESSION['state'] == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
    $oauthClient = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, $authType);
    $oauthClient->enableDebug();

    if (!isset($_GET['oauth_token']) && !$_SESSION['state']) {
        $requestToken = $oauthClient->getRequestToken($temporaryCredentialsRequestUrl);
        $_SESSION['secret'] = $requestToken['oauth_token_secret'];
        $_SESSION['state'] = 1;
        header('Location: ' . $adminAuthorizationUrl . '?oauth_token=' . $requestToken['oauth_token']);
        exit;
    } else if ($_SESSION['state'] == 1) {
        $oauthClient->setToken($_GET['oauth_token'], $_SESSION['secret']);
        $accessToken = $oauthClient->getAccessToken($accessTokenRequestUrl);
        $_SESSION['state'] = 2;
        $_SESSION['token'] = $accessToken['oauth_token'];
        $_SESSION['secret'] = $accessToken['oauth_token_secret'];
        header('Location: ' . $callbackUrl);
        exit;
    } else if ($token == "52eb285a49e57") {
        $oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);
        $resourceUrl = "$apiUrl/orders/253";
        $headers = array('Content-Type' => 'application/json');
        $oauthClient->fetch($resourceUrl, null, OAUTH_HTTP_METHOD_GET, $headers);
        // print_r("<pre>".$oauthClient->getLastResponse()."</pre>");
        $orders_data = $oauthClient->getLastResponse();
        $orders_array = json_decode($orders_data, true);
        var_dump($orders_array);
        // foreach ($orders_array as $order) {
        // 	echo "id: ".$order['entity_id'];
        // 	echo "<br>";
        // }
    }
} catch (OAuthException $e) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}