<?php

namespace Xoptov\INDXBroker;

use SplObserver;
use SplDoublyLinkedList;
use Xoptov\TradingCore\Channel;
use Xoptov\TradingCore\OrderBook;
use Xoptov\INDXConnector\Connector;
use Xoptov\INDXConnector\Credential;

class Broker
{
	/** @var Credential */
	private $credential;

	/** @var Connector */
	private $connector;

	/** @var OrderBook */
	private $orderBook;

	/** @var SplDoublyLinkedList */
	private $currencies;

	/** @var SplDoublyLinkedList */
	private $currencyPairs;

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
	 * @param array $events
	 */
	public function __construct(Credential $credential, Connector $connector, OrderBook $orderBook, array $events)
	{
		$this->credential = $credential;
		$this->connector = $connector;
		$this->orderBook = $orderBook;
		$this->currencies = new SplDoublyLinkedList();
		$this->currencyPairs = new SplDoublyLinkedList();
		$this->channels = new SplDoublyLinkedList();

		$this->setupChannels($events);
	}

	/**
	 * Main loop
	 *
	 * @param int $tickInterval
	 */
	public function start($tickInterval = 1000)
	{
		if ($this->started) {
			return;
		}

		$this->started = true;

		$this->loadCurrencies();
		$this->loadCurrencyPairs();

		while (true) {
			//TODO: implementation main loop workflow.
			usleep($tickInterval);
		}
	}

	/**
	 * Attach trader to event channel.
	 *
	 * @param SplObserver $trader
	 * @param $event
	 * @return bool
	 */
	public function attach(SplObserver $trader, $event)
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

	/**
	 * @param array $events
	 */
	private function setupChannels(array $events)
	{
		foreach ($events as $event) {
			$this->channels->push(new Channel($event));
		}
	}

	private function loadCurrencies()
	{

	}

	private function loadCurrencyPairs()
	{

	}
}