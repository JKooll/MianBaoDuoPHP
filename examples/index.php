<?php 
require_once('../vendor/autoload.php');

use MianBaoDuoPHP\App;

$app = new App([
  'developer_key' => 'your_developer_key'
]);

$orderDetail = $app->orderDetail('your_order_code');
var_dump($orderDetail);

$messageSettings = $app->messageSettings();
var_dump($messageSettings);

$productChart = $app->productChart('your_urlkey');
var_dump($productChart);

$productDetail = $app->productDetail('your_urlkey);
var_dump($productDetail);

$orderList = $app->orderList();
var_dump($orderList);

$unreadMentions = $app->unreadMentions();
var_dump($unreadMentions);

$productList = $app->productList();
var_dump($productList);

$createDiscount = $app->createDiscount('your_urlkey', your_rate);
var_dump($createDiscount);

$setUserInfo = $app->setUserInfo("your_brief", 'your_nickname', your_post_setting);
var_dump($setUserInfo);
