<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Parser\Event;

use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Parser\Event\PreFileEvent
 * @covers ::__construct
 
 */
class PreFileEventTest extends TestCase
{
    /** @var PreFileEvent $fixture */
    protected $fixture;

    /**
     * Sets up a fixture.
     */
    protected function setUp(): void
    {
        $this->fixture = new PreFileEvent(new stdClass());
    }

    /**
     * @covers ::createInstance
     * @covers ::getSubject
     */
    public function testCreatingAnInstance(): void
    {
        $subject = new stdClass();
        $this->fixture = PreFileEvent::createInstance($subject);
        $this->assertSame($subject, $this->fixture->getSubject());
    }

    /**
     * @covers \phpDocumentor\Parser\Event\PreFileEvent::getFile
     * @covers \phpDocumentor\Parser\Event\PreFileEvent::setFile
     */
    public function testRemembersFileThatTriggersIt(): void
    {
        $filename = 'myfile.txt';

        $this->assertEmpty($this->fixture->getFile());

        $this->fixture->setFile($filename);

        $this->assertSame($filename, $this->fixture->getFile());
    }
}
