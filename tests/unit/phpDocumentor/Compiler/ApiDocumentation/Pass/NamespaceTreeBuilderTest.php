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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function sort;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\ApiDocumentation\Pass\NamespaceTreeBuilder
 
 
 */
final class NamespaceTreeBuilderTest extends TestCase
{
    use Faker;

    private NamespaceTreeBuilder $fixture;
    private ApiSetDescriptor $apiSet;

    protected function setUp(): void
    {
        $this->fixture = new NamespaceTreeBuilder();

        $this->apiSet = self::faker()->apiSetDescriptor();
        $this->apiSet->getFiles()->add(new FileDescriptor('hash'));
    }

    /** @covers ::getDescription */
    public function testGetDescription(): void
    {
        $this->assertSame(
            'Build "namespaces" index and add namespaces to "elements"',
            $this->fixture->getDescription(),
        );
    }

    /**
     * @covers ::__invoke
     * @covers ::addElementsOfTypeToNamespace
     */
    public function testNamespaceStringIsConvertedToTreeAndAddedToElements(): void
    {
        $class = new ClassDescriptor();
        $class->setNamespace('\My\Space\Deeper');
        $class->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Deeper\Class1'));
        $this->apiSet->getFiles()->get(0)->getClasses()->add($class);

        // assert that namespaces are not created in duplicate by processing two classes
        $class2 = new ClassDescriptor();
        $class2->setNamespace('\My\Space\Deeper2');
        $class2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Deeper2\Class2'));
        $this->apiSet->getFiles()->get(0)->getClasses()->add($class2);

        $this->fixture->__invoke($this->apiSet);

        $elements = $this->apiSet->getIndexes()->get('elements')->getAll();
        $elementNames = array_keys($elements);
        sort($elementNames);
        $this->assertSame(
            ['~\\', '~\\My', '~\\My\\Space', '~\\My\\Space\\Deeper', '~\\My\\Space\\Deeper2'],
            $elementNames,
        );
        $this->assertInstanceOf(
            NamespaceDescriptor::class,
            $this->apiSet->getNamespace()->getChildren()->get('My'),
        );
        $this->assertInstanceOf(
            NamespaceDescriptor::class,
            $this->apiSet->getNamespace()->getChildren()->get('My')->getChildren()->get('Space'),
        );
        $this->assertSame($elements['~\\My'], $this->apiSet->getNamespace()->getChildren()->get('My'));
    }

    /**
     * @covers ::__invoke
     * @covers ::addElementsOfTypeToNamespace
     */
    public function testAddClassToNamespace(): void
    {
        $class = new ClassDescriptor();
        $class->setNamespace('\My\Space');
        $class->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class1'));
        $this->apiSet->getFiles()->get(0)->getClasses()->add($class);

        // double check if a second class in the same deep namespace ends up at the right location
        $class2 = new ClassDescriptor();
        $class2->setNamespace('\My\Space');
        $class2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class2'));
        $this->apiSet->getFiles()->get(0)->getClasses()->add($class2);

        $this->fixture->__invoke($this->apiSet);

        $this->assertSame(
            [$class, $class2],
            $this->apiSet
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getClasses()->getAll(),
        );
    }

    /**
     * @covers ::__invoke
     * @covers ::addElementsOfTypeToNamespace
     */
    public function testAddInterfaceToNamespace(): void
    {
        $interface = new InterfaceDescriptor();
        $interface->setNamespace('\My\Space');
        $interface->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Interface1'));
        $this->apiSet->getFiles()->get(0)->getInterfaces()->add($interface);

        // double check if a second interface in the same deep namespace ends up at the right location
        $interface2 = new InterfaceDescriptor();
        $interface2->setNamespace('\My\Space');
        $interface2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Interface2'));
        $this->apiSet->getFiles()->get(0)->getInterfaces()->add($interface2);

        $this->fixture->__invoke($this->apiSet);

        $this->assertSame(
            [$interface, $interface2],
            $this->apiSet
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getInterfaces()->getAll(),
        );
    }

    /**
     * @covers ::__invoke
     * @covers ::addElementsOfTypeToNamespace
     */
    public function testAddTraitToNamespace(): void
    {
        $trait = new TraitDescriptor();
        $trait->setNamespace('\My\Space');
        $trait->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Trait1'));
        $this->apiSet->getFiles()->get(0)->getTraits()->add($trait);

        // double check if a second trait in the same deep namespace ends up at the right location
        $trait2 = new TraitDescriptor();
        $trait2->setNamespace('\My\Space');
        $trait2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Trait2'));
        $this->apiSet->getFiles()->get(0)->getTraits()->add($trait2);

        $this->fixture->__invoke($this->apiSet);

        $this->assertSame(
            [$trait, $trait2],
            $this->apiSet
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getTraits()->getAll(),
        );
    }

    /**
     * @covers ::__invoke
     * @covers ::addElementsOfTypeToNamespace
     */
    public function testAddConstantToNamespace(): void
    {
        $constant = new ConstantDescriptor();
        $constant->setNamespace('\My\Space');
        $constant->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Constant1'));
        $this->apiSet->getFiles()->get(0)->getConstants()->add($constant);

        // double check if a second constant in the same deep namespace ends up at the right location
        $constant2 = new ConstantDescriptor();
        $constant2->setNamespace('\My\Space');
        $constant2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Constant2'));
        $this->apiSet->getFiles()->get(0)->getConstants()->add($constant2);

        $this->fixture->__invoke($this->apiSet);

        $this->assertSame(
            [$constant, $constant2],
            $this->apiSet
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getConstants()->getAll(),
        );
    }

    /**
     * @covers ::__invoke
     * @covers ::addElementsOfTypeToNamespace
     */
    public function testAddFunctionToNamespace(): void
    {
        $function = new FunctionDescriptor();
        $function->setNamespace('\My\Space');
        $function->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Function1'));
        $this->apiSet->getFiles()->get(0)->getFunctions()->add($function);

        // double check if a second function in the same deep namespace ends up at the right location
        $function2 = new FunctionDescriptor();
        $function2->setNamespace('\My\Space');
        $function2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Function2'));
        $this->apiSet->getFiles()->get(0)->getFunctions()->add($function2);

        $this->fixture->__invoke($this->apiSet);

        $this->assertSame(
            [$function, $function2],
            $this->apiSet
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getFunctions()->getAll(),
        );
    }
}
