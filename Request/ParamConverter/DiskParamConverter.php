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

namespace ChillDev\Bundle\FileManagerBundle\Request\ParamConverter;

use ChillDev\Bundle\FileManagerBundle\Filesystem\Manager;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Disk parameter converter.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2012 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.0.1
 * @since 0.0.1
 * @package ChillDev\Bundle\FileManagerBundle
 */
class DiskParamConverter implements ParamConverterInterface
{
    /**
     * Disks manager.
     *
     * @var Manager
     * @version 0.0.1
     * @since 0.0.1
     */
    protected $manager;

    /**
     * Initializes param converter.
     *
     * @param Manager $manager Disks manager.
     * @version 0.0.1
     * @since 0.0.1
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Performs disk lookup.
     *
     * @param Request $request Current request.
     * @param ParamConverter $configuration Conversion configuration.
     * @return bool Whether conversion took place.
     * @version 0.0.1
     * @since 0.0.1
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $options = $configuration->getOptions();

        // default atribute name is the same as action parameter name
        $param = isset($options['param']) ? $options['param'] : $name;
        $disk = $request->attributes->get($param);

        if (!isset($this->manager[$disk])) {
            throw new NotFoundHttpException(\sprintf('Disk specified by request as "%s" is not configured.', $disk));
        }

        $request->attributes->set($name, $this->manager[$disk]);

        return true;
    }

    /**
     * Checks if this converter can handle given conversion.
     *
     * @param ParamConverter $configuration Conversion configuration.
     * @return bool Whether given conversion can be handled or not.
     * @version 0.0.1
     * @since 0.0.1
     */
    public function supports(ParamConverter $configuration)
    {
        return 'ChillDev\\Bundle\\FileManagerBundle\\Filesystem\\Disk' === $configuration->getClass();
    }
}
