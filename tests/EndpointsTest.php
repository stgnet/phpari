<?php
	use Ratchet\Server\IoServer;
	use Ratchet\MessageComponentInterface;
	use Ratchet\ConnectionInterface;

	require 'vendor/autoload.php';

	class AriSimulator implements MessageComponentInterface {
		public function onOpen(ConnectionInterface $conn) {
			echo "New connection\n";
		}
		public function onMessage(ConnectionInterface $from, $msg) {
			echo "Have message $msg\n";
			$from->send("{error: unimplemented}\n");
		}
		public function onClose(ConnectionInterface $conn) {
		}
		public function onError(ConnectionInterface $conn, \Exception $e) {
			echo "Error: ".$e->getMessage()."\n";
		}
	}

	class EndpointsTest extends PHPUnit_Framework_TestCase
	{
		protected $loop;
		protected $server;
		protected $ari;

		protected function setUp()
		{
			// create a websocket that simulates the ARI interface
			$this->server = IoServer::factory(new AriSimulator(), 8088);

			// make absolutely sure we dump out if things go wrongly
			$this->server->loop->addTimer(2, function(){
				$this->server->loop->stop();
			});

			/* ### this will not work because the pest library is blocking (doesn't use react)
					only option is to start separate ari simulator process */
			// start the client connection
//			$this->ari = new phpari('nobody', 'secret', 'hello', 'localhost', 8088, '/ari', $this->server->loop);
		}
		public function testEndpoint()
		{
			$this->server->run();
			$endpoints = new endpoints($this->ari);

			$response = $endpoints->endpoints_list();

			print_r($response);

			$this->assertEquals(1, 1);
		}
	}

	/*
	$server = IoServer::factory(new AriSimulator(), 8088);
	$server->run();
	*/
