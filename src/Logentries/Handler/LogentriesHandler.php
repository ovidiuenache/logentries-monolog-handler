<?php namespace Logentries\Handler;

use Logentries\Socket;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
*  VERSION: 2.0
*/
class LogentriesHandler extends AbstractProcessingHandler
{
	protected string $token;

	protected Socket $socket;

	/**
	 * @param string  $token  Token UUID for Logentries logfile
	 * @param integer $level  The minimum logging level at which this handler will be triggered
	 * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
	 */
	public function __construct($token, $level = Logger::DEBUG, $bubble = true, Socket $socket = null)
	{
		$this->token  = $token;
		$this->socket = $socket ?? new Socket('data.logentries.com', 80);

		parent::__construct($level, $bubble);
	}

    /**
     * {@inheritDoc}
     */
	protected function write(array $record): void
	{
		$data = $this->generateDataStream($record);
		$this->socket->write($data);
	}

    /**
     * {@inheritDoc}
     */
	public function close(): void
	{
		$this->socket->close();
	}

	private function generateDataStream(array $record): string
	{
		return \sprintf("%s %s\n", $this->token, $record['formatted']);
	}
}
