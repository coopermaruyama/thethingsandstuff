<?php
// ini_set('display_errors',1);
// error_reporting(E_ALL);

/**
* Example of simple product POST using Admin account via Magento REST API. OAuth authorization is used
*/
$host = "http://thethingsandstuff.com";
$callbackUrl = $host."/static/ttasupload/category.php";
$temporaryCredentialsRequestUrl = $host."/oauth/initiate?oauth_callback=" . urlencode($callbackUrl);
$adminAuthorizationUrl = $host.'/admin/oauth_authorize';
$accessTokenRequestUrl = $host.'/oauth/token';
$apiUrl = $host.'/api/rest';
$consumerKey = 'b98lbkxvjpfe1ot8gx2bzlcxgqw7hnkr';
$consumerSecret = 'u5663plc0df72ojjkydnqu2libdrujgs';

session_start();
extract($_POST);
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
        $resourceUrl = "$apiUrl/products/$product_id/categories";
        $productData = json_encode(array(
            'category_id'           => 106
        ));
        $headers = array('Content-Type' => 'application/json');
        $oauthClient->fetch($resourceUrl, $productData, OAUTH_HTTP_METHOD_POST, $headers);
        print_r($oauthClient->getLastResponseInfo());
    }
} catch (OAuthException $e) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}