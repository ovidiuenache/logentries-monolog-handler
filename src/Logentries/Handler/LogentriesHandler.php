<?php

namespace Logentries\Handler;

use Logentries\Socket;
use Monolog\Level;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class LogentriesHandler extends AbstractProcessingHandler
{
	protected string $token;

	protected Socket $socket;

	public function __construct(
        string $token,
        int|string|Level $level = Level::Debug,
        bool $bubble = true,
        Socket $socket = null
    ) {
		$this->token  = $token;
		$this->socket = $socket ?? new Socket('data.logentries.com', 80);

		parent::__construct($level, $bubble);
	}

    /**
     * @inheritDoc
     */
	protected function write(LogRecord $record): void
	{
		$data = $this->generateDataStream($record);

		$this->socket->write($data);
	}

    /**
     * @inheritDoc
     */
	public function close(): void
	{
		$this->socket->close();
	}

	private function generateDataStream(LogRecord $record): string
	{
		return \sprintf("%s %s\n", $this->token, $record['formatted']);
	}
}
