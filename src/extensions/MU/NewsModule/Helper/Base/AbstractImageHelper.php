<?php

/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 *
 * @see https://homepages-mit-zikula.de
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

declare(strict_types=1);

namespace MU\NewsModule\Helper\Base;

use Imagine\Image\ImageInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * Helper base class for image methods.
 */
abstract class AbstractImageHelper
{
    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;
    
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * Name of the application.
     *
     * @var string
     */
    protected $name;
    
    public function __construct(
        ZikulaHttpKernelInterface $kernel,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        VariableApiInterface $variableApi
    ) {
        $this->kernel = $kernel;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->variableApi = $variableApi;
        $this->name = 'MUNewsModule';
    }
    
    /**
     * This method returns an Imagine runtime options array for the given arguments.
     */
    public function getRuntimeOptions(string $objectType = '', string $fieldName = '', string $context = '', array $args = []): array
    {
        $this->checkIfImagineCacheDirectoryExists();
    
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'])) {
            $context = 'controllerAction';
        }
    
        $contextName = '';
        if ('controllerAction' === $context) {
            if (!isset($args['controller'])) {
                $args['controller'] = 'user';
            }
            if (!isset($args['action'])) {
                $args['action'] = 'index';
            }
    
            $contextName = $this->name . '_' . $args['controller'] . '_' . $args['action'];
        }
        if (empty($contextName)) {
            $contextName = $this->name . '_default';
        }
    
        return $this->getCustomRuntimeOptions($objectType, $fieldName, $contextName, $context, $args);
    }
    
    /**
     * This method returns an Imagine runtime options array for the given arguments.
     */
    public function getCustomRuntimeOptions(
        string $objectType = '',
        string $fieldName = '',
        string $contextName = '',
        string $context = '',
        array $args = []
    ): array {
        $options = [
            'thumbnail' => [
                'size' => [100, 100], // thumbnail width and height in pixels
                'mode' => $this->variableApi->get(
                    'MUNewsModule',
                    'thumbnailMode' . ucfirst($objectType) . ucfirst($fieldName),
                    ImageInterface::THUMBNAIL_INSET
                ),
                'extension' => null, // file extension for thumbnails (jpg, png, gif; null for original file type)
            ],
        ];
    
        if ($this->name . '_relateditem' === $contextName) {
            $options['thumbnail']['size'] = [100, 75];
        } elseif ('controllerAction' === $context) {
            if (in_array($args['action'], ['view', 'display', 'edit'])) {
                $fieldSuffix = ucfirst($objectType) . ucfirst($fieldName) . ucfirst($args['action']);
                $defaultWidth = 'view' === $args['action'] ? 32 : 240;
                $defaultHeight = 'view' === $args['action'] ? 24 : 180;
                $options['thumbnail']['size'] = [
                    $this->variableApi->get('MUNewsModule', 'thumbnailWidth' . $fieldSuffix, $defaultWidth),
                    $this->variableApi->get('MUNewsModule', 'thumbnailHeight' . $fieldSuffix, $defaultHeight),
                ];
            }
        }
    
        return $options;
    }
    
    /**
     * Check if cache directory exists and create it if needed.
     */
    protected function checkIfImagineCacheDirectoryExists(): void
    {
        $cacheDirectory = $this->kernel->getProjectDir() . '/public/media/cache';
        $fs = new Filesystem();
        if ($fs->exists($cacheDirectory)) {
            return;
        }
        try {
            $parentDirectory = mb_substr($cacheDirectory, 0, -6);
            if (!$fs->exists($parentDirectory)) {
                $fs->mkdir($parentDirectory);
            }
            $fs->mkdir($cacheDirectory);
        } catch (IOExceptionInterface $exception) {
            $request = $this->requestStack->getCurrentRequest();
            if ($request->hasSession() && $session = $request->getSession()) {
                $session->getFlashBag()->add(
                    'warning',
                    $this->translator->trans(
                        'The cache directory "%directory%" does not exist. Please create it and make it writable for the webserver.',
                        ['%directory%' => $cacheDirectory],
                        'config'
                    )
                );
            }
        }
    }
}
