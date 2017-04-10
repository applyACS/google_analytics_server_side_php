<?php

class ssga {
	const GA_URL = 'https://www.google-analytics.com/collect';

	private $data = array(
		'v' => 1,
		'tid' => '',	//Tracking ID / Property ID.
		'aip' => true,	//boolean, anonymize ip
		'ds' => '', 	//data source, text 
		'z' => null		//Cache Buster, number
		'cid' => null, //Client ID, text
		'uid' => null, //User ID, text
		't' => 'pageview',
		'dp' => 'home.html'
		);

	private $tracking;


	public function __construct( $UA = null, $aip = true ) {
		$this->data['tid'] = $UA;
		$this->data['aip'] = $aip;
		$this->data['z'] = rand( 1000000000, 9999999999 );
		$this->data['cid'] = $this->gen_uuid();
	}
	
	/**
	* Generate client id
	**/
	function gen_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
	}

	/**
	 * Use CURL
	 * @return array|null 
	 */
	private function send() {

		$content = http_build_query($this->data);
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => 'http://www.google-analytics.com/collect?'.$content,
			CURLOPT_USERAGENT => 'Vanity-URL-Tracker',
		));
		curl_exec( $ch );
		curl_close( $ch );

		return;
	}


	/////////////
	// Product //
	/////////////

	public function set_product_code($index = 1, $var = null ) {
		return $this->data['pr'.$index.'id'] = $var;
	}

	public function set_product_name($index = 1, $var = null ) {
		return $this->data['pr'.$index.'nm'] = $var;
	}

	public function set_unit_price($index = 1, $var = null ) {
		return $this->data['pr'.$index.'pr'] = $var;
	}

	public function set_qty($index = 1, $var = null ) {
		return $this->data['pr'.$index.'qt'] = $var;
	}

	public function set_variation($index = 1, $var = null ) {
		return $this->data['pr'.$index.'va'] = $var;
	}

	//////////
	// Misc //
	//////////


	public function set_java( $var = null ) {
		return $this->data['je'] = $var;
	}


	public function set_encode_type( $var = null ) {
		return $this->data['de'] = $var;
	}

	public function set_flash_version( $var = null ) {
		return $this->data['fl'] = $var;
	}


	public function set_host( $var = null ) {
		return $this->data['hn'] = $var;
	}

	public function set_screen_depth( $var = null ) {
		return $this->data['sc'] = $var;
	}


	public function set_screen_resolution( $var = null ) {
		return $this->data['sr'] = $var;
	}

	public function set_lang( $var = null ) {
		return $this->data['ul'] = $var;
	}

	public function set_ga_version( $var = null ) {
		return $this->data['wv'] = isset( $var ) ? $var : $this->data['wv'];
	}

 	//////////
	// Page //
 	//////////

	public function set_page( $var = null ) {
		return $this->data['dp'] = $var;
	}


	public function set_page_title( $var = null ) {
		return $this->data['dt'] = $var;
	}


	public function set_doc_url( $var=null ) {
		return $this->data['dl'] = $var;
	}


	public function set_host_name( $var=null ) {
		return $this->data['dh'] = $var;
	}

	public function set_title( $var = null ) {
		return $this->data['dt'] = $var;
	}

	////////////
	// Events //
	////////////

	public function send_event( $category, $action, $label = '', $value = '') {
		$event_category = (string) $category;
		$event_action = (string) $action;


		$this->data['ec']=$event_category        // Event Category. Required.
		$this->data['ea']=$event_action         // Event Action. Required.
		$this->data['el']=$label      // Event label.
		$this->data['ev']=$value          // Event value.

		$this->data['t'] = 'event';
		$this->send();
		
		return $this;
	}

	
	////////////////////////
	// Ecommerce Tracking //
	////////////////////////
	
	private static $requests_for_this_session = 0;
	
	/**
	 * Create and send a transaction object
	 * 
	 * Parameter order from https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#ecom
	 */
	public function send_transaction($transaction_id, $affiliation, $total, $tax, $shipping, $curency) {
		
		$this->data['utmt'] = 'transaction';
		$this->data['ti'] = $transaction_id;
		$this->data['ta'] = $affiliation;
		$this->data['tr'] = $total;
		$this->data['tt'] = $tax;
		$this->data['ts'] = $shipping;
		$this->data['cu'] = $curency;
		
		$this->send();
		
		return $this;
	}
	
	/**
	 * Add item to the created $transaction_id
	 * 
	 * Parameter order from https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#ecom
	 */
	public function send_item($transaction_id, $sku, $product_name, $variation, $unit_price, $quantity) {

		$this->data['utmt'] = 'item';
		$this->data['ti'] = $transaction_id;
		$this->data['ic'] = $sku;
		$this->data['in'] = $product_name;
		$this->data['iv'] = $variation;
		$this->data['ip'] = $unit_price;
		$this->data['iq'] = $quantity;
		
		$this->send();
		
		return $this;
	}
}



/**
 * Instantiate new class and push data
 * @param  string $UA     The UA string of the GA account to use
 * @param  string $domain domain
 * @param  string $page   the page to set the pageview
 * @return null         
 */
function ssga_track( $UA = null, $domain = null, $page = null ) {
	$ssga = new ssga( $UA, $domain );
	$ssga->set_page( $page );
	$ssga->send();
	return $ssga;
}
