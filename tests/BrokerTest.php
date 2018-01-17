<?php

use PHPUnit\Framework\TestCase;
use Xoptov\INDXConnector\Connector;
use Xoptov\INDXConnector\Credential;

class BrokerTest extends TestCase
{
	public function testStart()
	{
		$connector = new Connector("https://secure.indx.ru/api/v1/tradejson.asmx");
		$credential = new Credential();
	}
}