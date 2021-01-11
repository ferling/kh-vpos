<?php

namespace KHPaymentGW;

use KHPaymentGW\CommunicationException;

class KHPaymentGateway
{
	private $ccy;
	private $mid;
	private $live = false;
	private $lang;
	private $privateKey;
	private $check = false;

	public $liveUrl = 'https://pay.khpos.hu/pay/v1';
	public $testUrl = 'https://pay.sandbox.khpos.hu/pay/v1';


	function __construct($mid, $ccy, $lang, $privateKey)
	{
		$this->mid = $mid;
		$this->ccy = $ccy;
		$this->lang = $lang;
		$this->privateKey = $privateKey;
	}

	public function setLive($live = true)
	{
		$this->live = $live;
	}

	public function setCheckSignature($check = true)
	{
		$this->check = $check;
	}

	public function startPayment($amount, $txid)
	{
		$data = [
			'mid'    => $this->mid,
			'txid'   => $txid,
			'type'   => 'PU',
			'amount' => $amount * 100,
			'ccy'    => $this->ccy
		];
		$data['sign'] = $this->sign($data);
		if ( ! $this->check) {
			$data['nocheck'] = 1;
		}

		$data['lang'] = $this->lang;

		return $this->baseUrl() . '/PGPayment?' . http_build_query($data);
	}

	public function getResults($txid)
	{
		$data = [
			'mid'  => $this->mid, 
			'txid' => $txid
		];
		$results = explode("\r\n",$this->post('/PGResult',$data));
		if ($results[0] == "UTX" || $results[0] == "ERR") {
			throw new CommunicationException($results[0], "Unknown transacion ID or other error");
		}
		return $results;
	}

	public function refund($amount, $txid)
	{
		$data = [
			'mid'    => $this->mid, 
			'txid'   => $txid, 
			'type'   => 'RE', 
			'amount' => $amount * 100,
			'ccy'    => $this->ccy
		];
		$data['sign'] = $this->sign($data);
		if ( ! $this->check) {
			$data['nocheck'] = 1;
		}
		$results = explode("\r\n",$this->post('/PGPayment',$data));
		if ($results[0] == "UTX" || $results[0] == "ERR") {
			throw new CommunicationException($results[0], "Unknown transacion ID or other error");
		}
		return $results;
	}

	public function post($url, $data)
	{
		$url = $this->baseUrl() . $url;

		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data)
		    )
		);
		$context = stream_context_create($options);
		return file_get_contents($url, false, $context);
	}

	public function baseUrl()
	{
		if ($this->live) {
			return $this->liveUrl;
		}else{
			return $this->testUrl;
		}
	}

	public function sign($data)
	{
		if (is_array($data)) {
			$data = http_build_query($data);
		}

		$pkeyid = openssl_get_privatekey($this->privateKey);

		$signature = '';
		// compute signature
		openssl_sign($data, $signature, $pkeyid);

		// free the key from memory
		openssl_free_key($pkeyid);

		return bin2hex($signature);
	}
}