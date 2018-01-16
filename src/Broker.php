<?php

namespace Xoptov\INDXBroker;

use SplObserver;
use SplDoublyLinkedList;
use Xoptov\TradingCore\Channel;
use Xoptov\TradingCore\OrderBook;
use Xoptov\INDXConnector\Connector;
use Xoptov\INDXConnector\Credential;
use Xoptov\TradingCore\Model\Currency;

class Broker
{
    /** @var Credential */
    private $brokerCredential;

	/** @var Connector */
	private $connector;

	/** @var SplDoublyLinkedList */
	private $currencies;

	/** @var SplDoublyLinkedList */
	private $currencyPairs;

	/** @var SplDoublyLinkedList */
	private $observableCurrencies;

	/** @var OrderBook */
	private $orderBook;

	/** @var SplDoublyLinkedList */
	private $channels;

	/** @var bool */
	private $started = false;

	/**
	 * Broker constructor.
	 *
     * @param Credential $credential
	 * @param Connector $connector
	 * @param OrderBook $orderBook
	 */
	public function __construct(Credential $credential, Connector $connector, OrderBook $orderBook)
	{
	    $this->brokerCredential = $credential;
		$this->connector = $connector;
        $this->orderBook = $orderBook;

        // Internal properties.
        $this->currencies = new SplDoublyLinkedList();
        $this->currencyPairs = new SplDoublyLinkedList();
        $this->observableCurrencies = new SplDoublyLinkedList();
		$this->channels = new SplDoublyLinkedList();
	}

	/**
	 * Main loop
	 *
	 * @param int $tickInterval
     * @param bool $loop
	 */
	public function start($tickInterval = 1000, $loop = true)
	{
		if ($this->started) {
			return;
		}

		$this->started = true;

		do {
            //TODO: implementation main loop workflow.
		    usleep($tickInterval);
        } while ($loop);
	}

	/**
	 * Attach trader to event channel.
	 *
	 * @param SplObserver $trader
	 * @param $event
	 * @return bool
	 */
	public function attach(SplObserver $trader, Currency $currency, $event)
	{
		/** @var Channel $channel */
		foreach ($this->channels as $channel) {
			if ($channel->getEvent() === $event) {
				$channel->attach($trader);

				return true;
			}
		}

		return false;
	}
}