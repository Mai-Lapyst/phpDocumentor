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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\EventAbstract;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;

/** @coversDefaultClass \phpDocumentor\Transformer\Event\PreTransformEvent */
final class PreTransformEventTest extends TestCase
{
    use ProphecyTrait;

    private PreTransformEvent|EventAbstract $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new PreTransformEvent(new stdClass());
    }

    public function testCreatingAnInstance(): void
    {
        $subject = new stdClass();
        $this->fixture = PreTransformEvent::createInstance($subject);
        $this->assertSame($subject, $this->fixture->getSubject());
    }

    public function testSetAndGetProject(): void
    {
        $project = $this->prophesize(ProjectDescriptor::class);
        $this->assertNull($this->fixture->getProject());

        $this->fixture->setProject($project->reveal());

        $this->assertSame($project->reveal(), $this->fixture->getProject());
    }
}
