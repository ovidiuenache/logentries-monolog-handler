<?php 

use Mockery as m;
use Monolog\Logger;
use Logentries\Handler\LogentriesHandler;

final class LogentriesHandlerTest extends \PHPUnit_Framework_TestCase
{
	private Logger $log;
	private m\MockInterface $socketMock;

    /**
     * {@inheritDoc}
     */
	public function setUp(): void
	{
		$this->socketMock = m::mock('Logentries\Socket');

		$this->log = new Logger('TestLog');
		$this->log->pushHandler(new LogentriesHandler('testToken', Logger::DEBUG, true, $this->socketMock));
	}

	public function tearDown(): void
	{
		m::close();
	}

	public function testWarning(): void
	{
		$this->socketMock->shouldReceive('write')
						 ->once()
						 ->with('/testToken\s\[(\d){4}-(\d){2}-(\d){2}\s(\d){2}:(\d){2}:(\d){2}\]\sTestLog.WARNING:\sFoo\s\[\]\s\[\]/');

		$this->log->addWarning('Foo');
	}
}
