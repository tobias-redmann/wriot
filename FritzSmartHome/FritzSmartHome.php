<?php

class FritzSmartHome {

	const FRITZ_ADDRESS = 'http://fritz.box';
	const FRITZ_LOGIN_URL = '/login_sid.lua';
	const FRITZ_SWITCH_URL = '/webservices/homeautoswitch.lua';
	const UNVALID_SESSION_ID = '0000000000000000';

	protected $username = NULL;
	protected $password = NULL;
	protected $generatedPassword = NULL;

	protected $address = NULL;
	protected $sid = NULL;

	/**
	 * FritzSmartHome constructor.
	 *
	 * @param null $address
	 */
	function __construct($address = NULL) {

		// use setted url
		if ($address === NULL) {
			$this->address = self::FRITZ_ADDRESS;
		} else {
			$this->address = $address;
		}

	}

	/**
	 * Build a login url
	 *
	 * @return string
	 */
	private function getLoginUrl()
	{

		return $this->address. self::FRITZ_LOGIN_URL;

	}

	/**
	 * Will return the switch url
	 *
	 * @return string
	 */
	private function getSwitchUrl()
	{

		return $this->address. self::FRITZ_SWITCH_URL;

	}


	public function connect($username, $password)
	{

		$this->username = $username;
		$this->password = $password;

		$responseXml = $this->post(
			$this->getLoginUrl(),
			array(

			)
		);

		$xmlObj = simplexml_load_string($responseXml);

		$sid = (String) $xmlObj->SID;


		if ($sid == self::UNVALID_SESSION_ID) {

			$resp = $this->post(
				$this->getLoginUrl(),
				array(
					'username' => $this->username,
					'response' => $this->getHash( (String) $xmlObj->Challenge)
				)
			);

			$sessionResponseObj = simplexml_load_string($resp);

			$realSid = (String) $sessionResponseObj->SID;

			if ($realSid != self::UNVALID_SESSION_ID) {

				$this->sid = $realSid;

				return true;

			} else {

				return false;

			}

		}

	}


	/**
	 * Get the list of connected devices
	 *
	 * @param null $filter
	 * @return List of devices
	 */
	public function getConnectedDevices($filter = NULL)
	{

		$response = $this->post(
			$this->getSwitchUrl(),
			array(
				'switchcmd' => 'getdevicelistinfos',
				'sid'       => $this->sid,

			)
		);

		// @todo: create a device class an return a list of devices
		return $response;

	}


	/**
	 * Toggle switch on/off
	 *
	 * @param $identifier
	 */
	public function toggleSwitch($identifier)
	{

		$response = $this->post(
			$this->getSwitchUrl(),
			array(
				'ain'           => $identifier,
				'switchcmd'     => 'setswitchtoggle',
				'sid'           => $this->sid
			)
		);

	}


	/**
	 * Generate Login Hash
	 *
	 * @param $challenge
	 *
	 * @return string
	 */
	private function getHash($challenge) {

		return $challenge. '-' . md5(mb_convert_encoding($challenge."-".$this->password,"UTF-16LE"));

	}


	/**
	 * @param $url
	 * @param $params
	 *
	 * @return string
	 */
	private function post($url, $params)
	{

		$u = $url.'?'.http_build_query($params);

		return file_get_contents($u);

	}



}

/*
class FritzDevice {

	protected $smarthome = NULL;

	public $identifier = NULL;
	public $productname = NULL;
	public $manufacturer = NULL;
	public $name = NULL;
	public $switchState = NULL;
	public $present = NULL;
	public $switchMode = NULL;


	static function createFromXml($smarthome, $xml)
	{

		$device = new self;





		return $device;


	}


}*/
