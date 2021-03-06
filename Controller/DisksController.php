<?php

/**
 * This file is part of the ChillDev FileManager bundle.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2012 - 2014 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.1.3
 * @since 0.0.2
 * @package ChillDev\Bundle\FileManagerBundle
 */

namespace ChillDev\Bundle\FileManagerBundle\Controller;

use ChillDev\Bundle\FileManagerBundle\Filesystem\Disk;
use ChillDev\Bundle\FileManagerBundle\Utils\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Disks controller.
 *
 * @Route("/disks")
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2012 - 2014 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.1.3
 * @since 0.0.1
 * @package ChillDev\Bundle\FileManagerBundle
 */
class DisksController extends SymfonyBaseController
{
    /**
     * Disks listing page.
     *
     * @Route("/", name="chilldev_filemanager_disks_list")
     * @Template(engine="default")
     * @return array Template data.
     * @version 0.1.1
     * @since 0.0.1
     */
    public function listAction()
    {
        return ['disks' => $this->get('chilldev.filemanager.disks.manager')];
    }

    /**
     * Directory listing action.
     *
     * @Route(
     *      "/{disk}/{path}",
     *      name="chilldev_filemanager_disks_browse",
     *      requirements={"path"=".*"},
     *      defaults={"path"=""}
     *  )
     * @Template(engine="default")
     * @param Request $request Current request.
     * @param Disk $disk Disk scope.
     * @param string $path Destination directory.
     * @return array Template data.
     * @throws HttpException When requested path is invalid or is not a directory.
     * @throws NotFoundHttpException When requested path does not exist.
     * @version 0.1.3
     * @since 0.0.1
     */
    public function browseAction(Request $request, Disk $disk, $path = '')
    {
        $path = Controller::resolvePath($path);

        $list = [];

        // get filesystem from given disk
        $filesystem = $disk->getFilesystem();

        Controller::ensureExist($disk, $filesystem, $path);
        Controller::ensureDirectoryFlag($disk, $filesystem, $path);

        // file information object
        $info = $filesystem->getFileInfo($path);

        foreach ($filesystem->createDirectoryIterator($path) as $file => $info) {
            $data = [
                'isDirectory' => $info->isDir(),
                'path' => $path . '/' . $file,
                'mimeType' => $info->getMimeType(),
            ];

            // directories doesn't have size
            if (!$info->isDir()) {
                $data['size'] = $info->getSize();
            }

            $list[$file] = $data;
        }

        $by = $request->query->get('by', 'path');

        // select only allowed sorting parameters
        if (!\in_array($by, ['path', 'size', 'mimeType'])) {
            $by = 'path';
        }

        // perform sorting
        \uasort($list, Controller::getSorter($by, $request->query->get('order', 1)));

        return [
            'disk' => $disk,
            'path' => $path,
            'list' => $list,
            'actions' => $this->get('chilldev.filemanager.actions.actions_manager'),
        ];
    }
}
