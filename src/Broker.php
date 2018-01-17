<?php

namespace Xoptov\INDXBroker;

use SplObserver;
use RuntimeException;
use SplDoublyLinkedList;
use Xoptov\TradingCore\Channel;
use Xoptov\TradingCore\OrderBook;
use Xoptov\INDXConnector\Connector;
use Xoptov\INDXConnector\Credential;
use Xoptov\TradingCore\Model\Currency;
use Xoptov\TradingCore\Model\CurrencyPair;

class Broker
{
    /** @var Credential */
    private $brokerCredential;

	/** @var Connector */
	private $connector;

	/** @var SplDoublyLinkedList */
	private $currencyPairs;

	/** @var SplDoublyLinkedList */
	private $orderBooks;

	/** @var SplDoublyLinkedList */
	private $channels;

	/** @var bool */
	private $started = false;

	/**
	 * Broker constructor.
	 *
     * @param Credential $credential
	 * @param Connector $connector
	 */
	public function __construct(Credential $credential, Connector $connector)
	{
	    $this->brokerCredential = $credential;
		$this->connector = $connector;

        // Internal properties.
        $this->currencyPairs = new SplDoublyLinkedList();
        $this->orderBooks = new SplDoublyLinkedList();
		$this->channels = new SplDoublyLinkedList();
	}

	/**
	 * Main loop
	 *
	 * @param int $tickInterval
     * @param bool $loop
	 */
	public function start($tickInterval = 1000, $loop = false)
	{
		if ($this->started) {
			return;
		}

		$this->started = true;

		$this->loadCurrencyPairs();

		do {
            //TODO: implementation main loop workflow.
		    usleep($tickInterval);
        } while ($loop);
	}

	/**
	 * Attach trader to event channel.
	 *
	 * @param SplObserver $trader
	 * @param CurrencyPair $currencyPair
	 * @param string $event
	 */
	public function attach(SplObserver $trader, CurrencyPair $currencyPair, $event)
	{
		$channel = $this->getChannel($currencyPair, $event);

		if (!$channel) {
			$channel = new Channel($currencyPair, $event);
			$this->channels->push($channel);
		}

		$channel->attach($trader);

		$this->observeCurrencyPair($currencyPair);
	}

	/**
	 * @param SplObserver $trader
	 */
	public function detach(SplObserver $trader, CurrencyPair $currencyPair)
	{
		/** @var Channel $channel */
		foreach ($this->channels as $key => $channel) {
			if ($channel->getCurrencyPairId() === $currencyPair->getId()) {
				$channel->detach($trader);

				// Remove channel if there is last observer on channel.
				if (!$channel->getSubscribersCount()) {
					$this->channels->offsetUnset($key);
				}
			}
		}

		// Checking live currency pair observers.
		foreach ($this->channels as $channel) {
			if ($channel->getCurrencyPairId() === $currencyPair->getId()) {
				return;
			}
		}

		/** @var OrderBook $orderBook */
		foreach ($this->orderBooks as $key => $orderBook) {
			if ($orderBook->getCurrencyPairId() === $currencyPair->getId()) {
				$this->orderBooks->offsetUnset($key);
			}
		}
	}

	/**
	 * @param CurrencyPair $currencyPair
	 */
	private function observeCurrencyPair(CurrencyPair $currencyPair)
	{
		/** @var OrderBook $orderBook */
		foreach ($this->orderBooks as $orderBook) {
			if ($orderBook->getCurrencyPairId() === $currencyPair->getId()) {
				return;
			}
		}

		$orderBook = new OrderBook($currencyPair);
		$this->orderBooks->push($orderBook);
	}

	/**
	 * @param CurrencyPair $currencyPair
	 * @param string $event
	 *
	 * @return Channel|null
	 */
	private function getChannel(CurrencyPair $currencyPair, $event)
	{
		/** @var Channel $channel */
		foreach ($this->channels as $channel) {
			if ($channel->getCurrencyPairId() === $currencyPair->getId() && $channel->getEvent() === $event) {
				return $channel;
			}
		}

		return null;
	}

	private function loadCurrencyPairs()
	{
		$quote = new Currency(null, "WMZ", "WebMoney US Dollar");

		$result = $this->connector->getSymbolList();
	}
}