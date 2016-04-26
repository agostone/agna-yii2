<?php
namespace Agna\Yii2\Autoloader\Composer;

/**
 * Test class for 'Agna\Yii2\Autoloader\Composer\Component' autoloader component.
 *
 * @author Agoston Nagy
 */
class ComponentTest extends \PHPUnit_Framework_TestCase
{
    var $application;

    var $psr0ClassNames;
    var $psr4ClassNames;

    var $psr0Namespaces;
    var $psr4Namespaces;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->psr0Namespaces = [
            'Psr01_' => __DIR__ . '/assets',
            'Psr02_' => __DIR__ . '/assets'
        ];

        $this->psr0ClassNames = [
            'Psr01_NamespaceA_Class1',
            'Psr01_NamespaceA_Class2',
            'Psr02_NamespaceB_Class3',
            'Psr02_NamespaceB_Class4'
        ];

        $this->psr4ClassNames = [
            'Psr41\NamespaceC\Class5',
            'Psr41\NamespaceC\Class6',
            'Psr42\NamespaceD\Class7',
            'Psr42\NamespaceD\Class8'
        ];

        $this->psr4Namespaces = [
            'Psr41\\' => __DIR__ . '/assets/Psr41',
            'Psr42\\' => __DIR__ . '/assets/Psr42'
        ];

        // Application config
        $config = [
            'id' => 'unit-testing',
            'basePath' => __DIR__,
            'components' => [
                'autoloader' => [
                    'class' => 'Agna\Yii2\Autoloader\Composer\Component'
                ]
            ]
        ];

        // Creating yii console application. Registers itself to \Yii::$app
        new \yii\console\Application($config);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        // Removing application instance
        \Yii::$app = null;
    }

    /**
     * Testing setPsr0 function of the loader component
     */
    public function testSetPsr0()
    {
        /* @var $autoloader \Agna\Yii2\Autoloader\Composer\Component */
        $autoloader = \Yii::$app->autoloader;
        $autoloader->setPsr0($this->psr0Namespaces);

        foreach ($this->psr0ClassNames as $index => $className) {
            $instance = new $className();
            $this->assertInstanceOf($this->psr0ClassNames[$index], $instance, "->setPsr0 failed, the created class is not an instance of '{$this->psr0ClassNames[$index]}'!");
            $this->assertEquals($this->psr0ClassNames[$index], $instance->getClassName(), "->setPsr0 failed, calling getClassName should return with '{$this->psr0ClassNames[$index]}'!");
        }
    }

    /**
     * Testing setPsr4 function of the loader component
     */
    public function testSetPsr4()
    {
        /* @var $autoloader \Agna\Yii2\Autoloader\Composer\Component */
        $autoloader = \Yii::$app->autoloader;
        $autoloader->setPsr4($this->psr4Namespaces);

        foreach ($this->psr4ClassNames as $index => $className) {
            $instance = new $className();
            $this->assertInstanceOf($this->psr4ClassNames[$index], $instance, "->setPsr4 failed, the created class is not an instance of '{$this->psr4ClassNames[$index]}'!");
            $this->assertEquals($this->psr4ClassNames[$index], $instance->getClassName(), "->setPsr4 failed, calling getClassName should return with '{$this->psr4ClassNames[$index]}'!");
        }
    }

    /**
     * Testing addPsr0 function of the loader component
     */
    public function testAddPsr0()
    {
        /* @var $autoloader \Agna\Yii2\Autoloader\Composer\Component */
        $autoloader = \Yii::$app->autoloader;

        foreach ($this->psr0Namespaces as $namespace => $paths) {
            $autoloader->addPsr0($namespace, $paths);
        }

        foreach ($this->psr0ClassNames as $index => $className) {
            $instance = new $className();
            $this->assertInstanceOf($this->psr0ClassNames[$index], $instance, "->addPsr0 failed, the created class is not an instance of '{$this->psr0ClassNames[$index]}'!");
            $this->assertEquals($this->psr0ClassNames[$index], $instance->getClassName(), "->addPsr0 failed, calling getClassName should return with '{$this->psr0ClassNames[$index]}'!");
        }
    }

    /**
     * Testing addPsr4 function of the loader component
     */
    public function testAddPsr4()
    {
        /* @var $autoloader \Agna\Yii2\Autoloader\Composer\Component */
        $autoloader = \Yii::$app->autoloader;

        foreach ($this->psr4Namespaces as $namespace => $paths) {
            $autoloader->addPsr4($namespace, $paths);
        }

        foreach ($this->psr4ClassNames as $index => $className) {
            $instance = new $className();
            $this->assertInstanceOf($this->psr4ClassNames[$index], $instance, "->addPsr4 failed, the created class is not an instance of '{$this->psr4ClassNames[$index]}'!");
            $this->assertEquals($this->psr4ClassNames[$index], $instance->getClassName(), "->addPsr4 failed, calling getClassName should return with '{$this->psr4ClassNames[$index]}'!");
        }
    }
}
