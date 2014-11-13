<?php

/**
 * This file is part of the ChillDev FileManager bundle.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2013 - 2014 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.1.4
 * @since 0.0.2
 * @package ChillDev\Bundle\FileManagerBundle
 */

namespace ChillDev\Bundle\FileManagerBundle\Filesystem;

use FilesystemIterator;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as FsUtils;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Abstract filesystem management.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2013 - 2014 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.1.3
 * @since 0.0.2
 * @package ChillDev\Bundle\FileManagerBundle
 */
class Filesystem
{
    /**
     * Root path for disk scope.
     *
     * @var string
     * @version 0.0.3
     * @since 0.0.3
     */
    protected $root;

    /**
     * Symfony's filesystem helper utility.
     *
     * @var FsUtils
     * @version 0.0.3
     * @since 0.0.3
     */
    protected $filesystem;

    /**
     * Initializes filesystem wrapper.
     *
     * @param string $root Root path for this disk.
     * @throws IOException When given path does not exist.
     * @version 0.0.3
     * @since 0.0.3
     */
    public function __construct($root)
    {
        if (!\file_exists($root)) {
            throw new IOException(\sprintf('Specified filesystem root path "%s" does not exist.', $root));
        }

        $this->root = $root;
        $this->filesystem = new FsUtils();
    }

    /**
     * Checks if file exists.
     *
     * @param string $path File path.
     * @return bool File state.
     * @version 0.0.3
     * @since 0.0.2
     */
    public function exists($path)
    {
        return \file_exists($this->root . DIRECTORY_SEPARATOR . $path);
    }

    /**
     * Removes file or directory.
     *
     * @param string $path File path.
     * @version 0.0.3
     * @since 0.0.3
     */
    public function remove($path)
    {
        $this->filesystem->remove($this->root . $path);
    }

    /**
     * Creates new directory.
     *
     * @param string $path New directory path.
     * @version 0.0.3
     * @since 0.0.2
     */
    public function mkdir($path)
    {
        \mkdir($this->root . $path);
    }

    /**
     * Moves/renames file.
     *
     * @param string $source Old location.
     * @param string $destination New location.
     * @version 0.0.3
     * @since 0.0.3
     */
    public function move($source, $destination)
    {
        \rename($this->root . $source, $this->root . $destination);
    }

    /**
     * Copies file.
     *
     * @param string $source Source.
     * @param string $destination Destination.
     * @version 0.0.3
     * @since 0.0.3
     */
    public function copy($source, $destination)
    {
        if (\is_dir($this->root . $source)) {
            // copy entire directory tree
            $this->mkdir($destination);
            $this->filesystem->mirror($this->root . $source, $this->root . $destination);
        } else {
            // copy regular file
            \copy($this->root . $source, $this->root . $destination);
        }
    }

    /**
     * Uploads file.
     *
     * @param string $path Destination directory.
     * @param UploadedFile $file Uploaded file.
     * @param string $name Destination filename.
     * @version 0.0.3
     * @since 0.0.3
     */
    public function upload($path, UploadedFile $file, $name)
    {
        $file->move($this->root . $path, $name);
    }

    /**
     * Initializes directory iterator.
     *
     * @param string $path Subject directory path.
     * @return FilesystemIterator Directory listing iterator.
     * @version 0.1.3
     * @since 0.0.2
     */
    public function createDirectoryIterator($path)
    {
        $iterator = new FilesystemIterator(
            $this->root . DIRECTORY_SEPARATOR . $path,
            FilesystemIterator::KEY_AS_FILENAME
            | FilesystemIterator::CURRENT_AS_FILEINFO
            | FilesystemIterator::SKIP_DOTS
            | FilesystemIterator::UNIX_PATHS
        );
        $iterator->setInfoClass('ChillDev\\Bundle\\FileManagerBundle\\Filesystem\\FileInfo');
        return $iterator;
    }

    /**
     * Creates fileinfo object.
     *
     * @param string $path Subject path.
     * @return FileInfo File information object.
     * @version 0.1.3
     * @since 0.0.3
     */
    public function getFileInfo($path)
    {
        return new FileInfo($this->root . DIRECTORY_SEPARATOR . $path);
    }
}
