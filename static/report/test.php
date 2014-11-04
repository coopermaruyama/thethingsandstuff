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
function generateResourceUrl($page, $apiUrl) {
	return "$apiUrl/orders?limit=100&page=$page";
}

function moreRecordsExist($page, $oauthClient, $apiUrl) {
	echo "checking if more records exist in page #" . $page . "...\n<br><br>\n";
	$resourceUrl = generateResourceUrl($page, $apiUrl);
	$headers = array('Content-Type' => 'application/json');
	$oauthClient->fetch($resourceUrl, null, OAUTH_HTTP_METHOD_GET, $headers);
	$data = $oauthClient->getLastResponse();
	$array = json_decode($data, true);
	
	    // if (count($array) == 100) {
	    //     $next_page_has_records = true;
	    // } elseif (count($array) < 100) {
	    //     $next_page_has_records = false;
	    // }
	    // $is_last_page = false;
	    // if ( (count($array) < 100) && (count($array) > 0) ) {
	    //     $is_last_page = true;
	    // }
		// if ( $next_page_has_records || $is_last_page ) {
	 //        return true;
	 //    } elseif ( !$next_page_has_records ) {
	 //        return false;
	 //    }
	    return count($array) < 100 ? false : true;
	
}

$host = "http://thethingsandstuff.com";
$callbackUrl = $host."/static/report/update.php";
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
    	// PDO Connection to MySQL
    	$conn = new PDO('mysql:host=205.186.146.64;dbname=revenue_report', 'root', '3120MacT!');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);
        $resourceUrl = "$apiUrl/orders";
       	$resourceUrl .= "?limit=100&page=1";
        $headers = array('Content-Type' => 'application/json');

        $order_ids = array();
        $page = 1;
        while (moreRecordsExist($page, $oauthClient, $apiUrl)) {
            $resourceUrl = generateResourceUrl($page, $apiUrl);
        	$oauthClient->fetch($resourceUrl, null, OAUTH_HTTP_METHOD_GET, $headers);
            $data = $oauthClient->getLastResponse();
            $array = json_decode($data, true);
            foreach ($array as $order) {
                array_push($order_ids, intval($order['entity_id']));
            }
        	$page++;
        }
        foreach ($order_ids as $order_id) { 
            echo "getting order id $order_id...";
            $query = $conn->prepare("SELECT * from `revenue_report`.`revenue_records` WHERE magento_order_id=$order_id");
            $query->execute();
            $result = $query->fetch();
            if (!$result) {
                $resourceUrl = "$apiUrl/orders/$order_id";
                echo "couldnt find order id $order_id in db - fetching from $resourceUrl";
                $headers = array('Content-Type' => 'application/json');
                $oauthClient->fetch($resourceUrl, null, OAUTH_HTTP_METHOD_GET, $headers);
                $order_data = $oauthClient->getLastResponse();
                $order_array = json_decode($order_data, true);
                $order_date = $order_array['created_at'];
                $total_paid = $order_array['grand_total'];
                $order_type = "sale";
                foreach ($order_array['addresses'] as $address) {
                    if ($address['address_type'] == "shipping") {
                        $customer_name = $address['firstname'] . ' ' . $address['lastname'];
                        $shipping_street = $address['street'];
                        $shipping_city = $address['city'];
                        $shipping_region = $address['region'];
                        $shipping_postal = $address['postcode'];
                    }
                }
                //Insert row
                $query = $conn->prepare("INSERT INTO  `revenue_report`.`revenue_records` (`id`, `manual_entry`, `order_date`, `order_type`, `shipping_street`,`shipping_city`,`shipping_region`,`shipping_postal`,`locked`,`deleted`,`magento_order_id`,`customer_name`,`total_paid`)VALUES (NULL,0,'$order_date','$order_type','$shipping_street','$shipping_city','$shipping_region','$shipping_postal',1,0,$order_id,'$customer_name',$total_paid)");
                $query->execute();
                $revenue_record_id = intval($conn->lastInsertId());
                foreach ($order_array['order_items'] as $item) {
                    $item_name = $item['name'];
                    $item_price = number_format((float)floatval($item['price']), 2, '.', '');
                    $query = $conn->prepare("INSERT INTO  `revenue_report`.`revenue_record_items` (`id`,`revenue_record_id`,`item`,`price`)VALUES (NULL,$revenue_record_id,'$item_name','$item_price')");
                    $query->execute();
                }   
            }
        }
        $query = $conn->prepare("INSERT INTO `revenue_report`.`magento_updates` (`id`)VALUES (NULL);");
        $query->execute();
    }
} catch (OAuthException $e) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}