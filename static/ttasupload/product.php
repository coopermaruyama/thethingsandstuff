<?php
// ini_set('display_errors',1);
// error_reporting(E_ALL);
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
$callbackUrl = $host."/static/ttasupload/product.php";
$temporaryCredentialsRequestUrl = $host."/oauth/initiate?oauth_callback=" . urlencode($callbackUrl);
$adminAuthorizationUrl = $host.'/admin/oauth_authorize';
$accessTokenRequestUrl = $host.'/oauth/token';
$apiUrl = $host.'/api/rest';
$consumerKey = 'b98lbkxvjpfe1ot8gx2bzlcxgqw7hnkr';
$consumerSecret = 'u5663plc0df72ojjkydnqu2libdrujgs';

session_start();
extract($_POST);
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
        $resourceUrl = "$apiUrl/products";
        if ($notes == "") {$notes = "[coming soon]";}
        $productData = json_encode(array(
            'type_id'           => 'simple',
            'attribute_set_id'  => 4,
            'sku'               => $unique_id,
            'url_key'           => $unique_id,
            'weight'            => 1,
            'status'            => 2,
            'visibility'        => 4,
            'name'              => "(#$unique_id)",
            'description'       => "$notes",
            'short_description' => "$notes",
            'price'             => 1.00,
            'tax_class_id'      => 0,
            'price_tier'        => $priceTier,
            'condition'         => $condition,
            'height'            => $height,
            'width'             => $width,
            'depth'             => $depth,
            'owner'             => $ownerCode,
            'size_frontend'     => $size,
            'size'              => "1",
            'stock_data' => array(
                'qty'               => '1',
                'is_in_stock'       => 1,
                'manage_stock'      => 1
            )
        ));
        $headers = array('Content-Type' => 'application/json');
        $oauthClient->fetch($resourceUrl, $productData, OAUTH_HTTP_METHOD_POST, $headers);
        // print_r($oauthClient->getLastResponseInfo());
        $headers = $oauthClient->getLastResponseHeaders();
        $headersArray = http_parse_headers($headers);
        $location = $headersArray['Location'];
        preg_match("/([0-9]+)$/", $location, $matches);
        echo $matches[0];
    }
} catch (OAuthException $e) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}