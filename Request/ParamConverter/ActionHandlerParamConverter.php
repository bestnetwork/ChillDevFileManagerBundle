<?php

/**
 * This file is part of the ChillDev FileManager bundle.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2014 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.1.3
 * @since 0.1.3
 * @package ChillDev\Bundle\FileManagerBundle
 */

namespace ChillDev\Bundle\FileManagerBundle\Request\ParamConverter;

use ChillDev\Bundle\FileManagerBundle\Action\ActionsManager;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Action handler parameter converter.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @copyright 2014 © by Rafał Wrzeszcz - Wrzasq.pl.
 * @version 0.1.3
 * @since 0.1.3
 * @package ChillDev\Bundle\FileManagerBundle
 */
class ActionHandlerParamConverter implements ParamConverterInterface
{
    /**
     * Actions handlers manager.
     *
     * @var ActionsManager
     * @version 0.1.3
     * @since 0.1.3
     */
    protected $manager;

    /**
     * Initializes param converter.
     *
     * @param ActionsManager $manager Actions manager.
     * @version 0.1.3
     * @since 0.1.3
     */
    public function __construct(ActionsManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Performs action lookup.
     *
     * @param Request $request Current request.
     * @param ParamConverter $configuration Conversion configuration.
     * @return bool Whether conversion took place.
     * @version 0.1.3
     * @since 0.1.3
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $options = $configuration->getOptions();

        // default atribute name is the same as action parameter name
        $param = isset($options['param']) ? $options['param'] : $name;
        $action = $request->attributes->get($param);

        if (!isset($this->manager[$action])) {
            throw new NotFoundHttpException(
                \sprintf('Action specified by request as "%s" has no handler registred.', $action)
            );
        }

        $request->attributes->set($name, $this->manager[$action]);

        return true;
    }

    /**
     * Checks if this converter can handle given conversion.
     *
     * @param ParamConverter $configuration Conversion configuration.
     * @return bool Whether given conversion can be handled or not.
     * @version 0.1.3
     * @since 0.1.3
     */
    public function supports(ParamConverter $configuration)
    {
        return 'ChillDev\\Bundle\\FileManagerBundle\\Action\\Handler\\HandlerInterface' === $configuration->getClass();
    }
}
