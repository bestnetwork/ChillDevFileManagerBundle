<?php

/**
 * This file is part of the ChillDev FileManager bundle.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2012 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.0.1
 * @since 0.0.1
 * @package ChillDev\Bundle\FileManagerBundle
 */

namespace ChillDev\Bundle\FileManagerBundle\Tests\Controller;

use PHPUnit_Framework_TestCase;

use ChillDev\Bundle\FileManagerBundle\Controller\DisksController;
use ChillDev\Bundle\FileManagerBundle\Filesystem\Disk;
use ChillDev\Bundle\FileManagerBundle\Filesystem\Manager;

use Symfony\Component\DependencyInjection\Container;

/**
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2012 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.0.1
 * @since 0.0.1
 * @package ChillDev\Bundle\FileManagerBundle
 */
class DisksControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * DI container.
     *
     * @var Container
     * @version 0.0.1
     * @since 0.0.1
     */
    protected $container;

    /**
     * Disks manager.
     *
     * @var Manager
     * @version 0.0.1
     * @since 0.0.1
     */
    protected $manager;

    /**
     * @version 0.0.1
     * @since 0.0.1
     */
    protected function setUp()
    {
        $this->container = new Container();
        $this->manager = new Manager();
        $this->manager->createDisk('id', 'Test', \realpath(__DIR__ . '/../fixtures/fs') . '/');

        $this->container->set('chilldev.filemanager.disks.manager', $this->manager);
    }

    /**
     * Check list action.
     *
     * @test
     * @version 0.0.1
     * @since 0.0.1
     */
    public function listAction()
    {
        $controller = new DisksController();
        $controller->setContainer($this->container);

        $this->assertSame($this->manager, $controller->listAction()['disks'], 'DisksController::listAction() should return disks manager under key "disks".');
    }

    /**
     * Check directory listing.
     *
     * @test
     * @version 0.0.1
     * @since 0.0.1
     */
    public function browseAction()
    {
        $disk = $this->manager['id'];

        $return = (new DisksController())->browseAction($disk, '//./bar/../bar/.///');

        $this->assertSame($disk, $return['disk'], 'DisksController::browseAction() should return disk scope object under key "disk".');
        $this->assertEquals('bar', $return['path'], 'DisksController::browseAction() should resolve all "./" and "../" references, replace multiple "/" with single one and return computed path under key "path".');

        $this->assertCount(2, $return['list'], 'DisksController::browseAction() should return list of all files in given directory under key "list".');

        $file = $return['list']['baz'];
        $this->assertFalse($file['isDirectory'], 'DisksController::browseAction() should set "isDirectory" flag to false for file entries.');
        $this->assertEquals(4, $file['size'], 'DisksController::browseAction() should set "size" field for file entries to value of the size of given file.');
        $this->assertEquals('bar/baz', $file['path'], 'DisksController::browseAction() should set "path" field as the relative path to given file from disk.');

        $file = $return['list']['quux'];
        $this->assertTrue($file['isDirectory'], 'DisksController::browseAction() should set "isDirectory" flag to true for directory entries.');
        $this->assertArrayNotHasKey('size', $file, 'DisksController::browseAction() should not set "size" field for directory entries.');
    }

    /**
     * Check scope-escaping path.
     *
     * @test
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Directory path contains invalid reference that exceeds disk scope.
     * @version 0.0.1
     * @since 0.0.1
     */
    public function browseInvalidPath()
    {
        (new DisksController())->browseAction(new Disk('', '', ''), '/foo/../../');
    }

    /**
     * Check non-existing path.
     *
     * @test
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Directory "[Foo]/test" does not exist.
     * @version 0.0.1
     * @since 0.0.1
     */
    public function browseNonexistingPath()
    {
        (new DisksController())->browseAction(new Disk('', 'Foo', ''), 'test');
    }

    /**
     * Check non-directory path.
     *
     * @test
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage "[Test]/foo" is not a directory.
     * @version 0.0.1
     * @since 0.0.1
     */
    public function browseNondirectoryPath()
    {
        (new DisksController())->browseAction($this->manager['id'], 'foo');
    }

    /**
     * Check default path parameter.
     *
     * @test
     * @version 0.0.1
     * @since 0.0.1
     */
    public function browseDefaultPath()
    {
        $return = (new DisksController())->browseAction($this->manager['id']);

        $this->assertEquals('', $return['path'], 'DisksController::browseAction() should list root path of disk scope by default.');
    }
}