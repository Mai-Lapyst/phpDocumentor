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

namespace phpDocumentor\Pipeline\Stage;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @uses \phpDocumentor\Pipeline\Stage\Payload
 *
 * @coversDefaultClass \phpDocumentor\Pipeline\Stage\TransformToPayload
 */
final class TransformToPayloadTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_converts_the_configuration_to_an_payload(): void
    {
        $config = ['config' => 'yes'];
        $builder = $this->prophesize(ProjectDescriptorBuilder::class)->reveal();

        $payload = (new TransformToPayload($builder))($config);

        $this->assertSame($builder, $payload->getBuilder());
        $this->assertSame($config, $payload->getConfig());
    }
}
