<?php

/**
 * Ths file is part of the JKooll/MianBaoDuoPHP.
 * 
 * (c) JKol <jerryzhao1212@gmail.com>
 * 
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace MianBaoDuoPHP;

use GuzzleHttp\Client;

/**
 * Factory Class
 */
class App
{
  /**
   * [
   *  'key' => 'xxx'
   * ]
   */
  private $options;

  private $base_url = 'https://x.mianbaoduo.com/api';

  // https://mianbaoduo.com/open_doc/#/通过订单号获取订单信息
  private $order_detail = '/order-detail?order_id=%s';

  private $message_settings = '/message-settings';

  private $product_chart = '/product-chart?urlkey=%s';

  private $product_detail = '/product-detail?urlkey=%s';

  private $order_list = '/order-list?page=%s&limit=%s';

  private $unread_mentions = '/unread-mentions';

  private $product_list = '/product-list?page=%s&limit=%s';

  private $create_discount = '/create-discount?urlkey=%s';

  private $set_user_info = '/set-user-info';

  // Http client
  private $client;

  public function __construct($options)
  {
    $this->options = $options;
    $this->client = new Client();
  }

  private function getRequest($url)
  {
    $response = $this->client->request('GET', $url, [
      'http_errors' => false,
      'headers' => [
        'x-token' => $this->options['developer_key']
      ]
    ]);

    return $response;
  }

  public function orderDetail($id)
  {
    $url = sprintf($this->base_url . $this->order_detail, $id);

    $response = $this->getRequest($url);

    $body = json_decode($response->getBody());

    $code = $body->code;
    
    switch($code) {
      case 200:
        return $body->result;
      case 403:
        return '认证失败';
      case 400:
        return '请求错误';
    }
  }

  public function messageSettings()
  {
    $url = sprintf($this->base_url . $this->message_settings);

    $response = $this->getRequest($url);

    $body = json_decode($response->getBody());

    $code = $body->code;

    switch($code) {
      case 200:
        return $body->result;
      case 403:
        return '认证失败';
    }
  }

  public function productChart($urlkey)
  {
    $url = sprintf($this->base_url . $this->product_chart, $urlkey);

    $response = $this->getRequest($url);

    $body = json_decode($response->getBody());

    $code = $body->code;

    switch ($code) {
      case 200:
        return $body->result;
      case 400:
        return '作品不存在';
      case 403:
        return '认证失败';
    }
  }

  public function productDetail($urlKey)
  {
    $url = sprintf($this->base_url . $this->product_detail, $urlKey);

    $response = $this->getRequest($url);

    $body = json_decode($response->getBody());

    $code = $body->code;

    switch ($code) {
      case 200:
        return $body->result;
      default:
        return $body->error_info;
    }
  }

  public function orderList($page = 1, $limit = 20)
  {
    $url = sprintf($this->base_url . $this->order_list, $page, $limit);
    
    $response = $this->getRequest($url);

    $body = json_decode($response->getBody());

    $code = $body->code;

    switch($code) {
      case 200:
        return $body->result;
      default:
        return $body->error_info;
    }
  }

  public function unreadMentions()
  {
    $url = sprintf($this->base_url . $this->unread_mentions);

    $response = $this->getRequest($url);

    $body = json_decode($response->getBody());
    
    return $this->dispatchResult($body);
  }

  public function productList($page = 1, $limit = 20)
  {
    $url = sprintf($this->base_url . $this->product_list, $page, $limit);

    $response = $this->getRequest($url);

    $body = json_decode($response->getBody());

    return $this->dispatchResult($body);
  }

  private function postRequest($url, $body)
  {
    $response = $this->client->request('POST', $url, [
      'body' => $body,
      'http_errors' => false,
      'headers' => [
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
        'x-token' => $this->options['developer_key']
      ]
    ]);

    return $response;
  }

  public function createDiscount($urlkey, $rate)
  {
    $url = sprintf($this->base_url . $this->create_discount, $urlkey);

    $body = json_encode([
      'rate' => $rate
    ]);

    $response = $this->postRequest($url, $body);

    $body = json_decode($response->getBody());

    var_dump($body);

    return $this->dispatchResult($body);
  }

  private function patchRequest($url, $body)
  {
    $response = $this->client->request('PATCH', $url, [
      'body' => $body,
      'http_errors' => false,
      'headers' => [
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
        'x-token' => $this->options['developer_key']
      ]
    ]);

    return $response;
  }

  /**
   * brief, name
   */
  public function setUserInfo($brief = null, $name = null, $post_setting)
  {
    $url = sprintf($this->base_url . $this->set_user_info);

    $bodyData = [];

    if ($brief) {
      $bodyData['brief'] = $brief;
    }

    if ($name) {
      $bodyData['name'] = $name;
    }

    if ($post_setting) {
      $bodyData['post_setting'] = $post_setting;
    }

    $body = json_encode($bodyData);

    $response = $this->patchRequest($url, $body);

    return $this->dispatchResult(json_decode($response->getBody()));
  }

  /**
   * Dispatch result content by status code.
   */
  private function dispatchResult($body)
  {
    $code = $body->code;

    switch($code) {
      case 200:
        return $body->result;
      default:
        return $body->error_info;
    }
  }
}