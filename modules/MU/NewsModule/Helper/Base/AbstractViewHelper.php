<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Helper\Base;

use Dompdf\Dompdf;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Zikula\Core\Response\PlainResponse;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ThemeModule\Engine\AssetFilter;
use Zikula\ThemeModule\Engine\ParameterBag;
use MU\NewsModule\Helper\ControllerHelper;
use MU\NewsModule\Helper\PermissionHelper;

/**
 * Helper base class for view layer methods.
 */
abstract class AbstractViewHelper
{
    /**
     * @var Twig_Environment
     */
    protected $twig;
    
    /**
     * @var FilesystemLoader
     */
    protected $twigLoader;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var AssetFilter
     */
    protected $assetFilter;
    
    /**
     * @var ParameterBag
     */
    protected $pageVars;
    
    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    public function __construct(
        Twig_Environment $twig,
        FilesystemLoader $twigLoader,
        RequestStack $requestStack,
        VariableApiInterface $variableApi,
        AssetFilter $assetFilter,
        ParameterBag $pageVars,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper
    ) {
        $this->twig = $twig;
        $this->twigLoader = $twigLoader;
        $this->requestStack = $requestStack;
        $this->variableApi = $variableApi;
        $this->assetFilter = $assetFilter;
        $this->pageVars = $pageVars;
        $this->controllerHelper = $controllerHelper;
        $this->permissionHelper = $permissionHelper;
    }
    
    /**
     * Determines the view template for a certain method with given parameters.
     *
     * @param string $type Current controller (name of currently treated entity)
     * @param string $func Current function (index, view, ...)
     * @param bool $isAdmin Whether an admin template is desired or not
     *
     * @return string name of template file
     */
    public function getViewTemplate($type, $func, $isAdmin = false)
    {
        // create the base template name
        $template = '@MUNewsModule/' . ucfirst($type) . '/' . ($isAdmin ? 'Admin/' : '') . $func;
    
        // check for template extension
        $templateExtension = '.' . $this->determineExtension($type, $func);
    
        // check whether a special template is used
        $request = $this->requestStack->getCurrentRequest();
        $tpl = null !== $request ? $request->query->getAlnum('tpl') : '';
        if (!empty($tpl)) {
            // check if custom template exists
            $customTemplate = $template . ucfirst($tpl);
            if ($this->twigLoader->exists($customTemplate . $templateExtension)) {
                $template = $customTemplate;
            }
        }
    
        $template .= $templateExtension;
    
        return $template;
    }
    
    /**
     * Helper method for managing view templates.
     *
     * @param string $type Current controller (name of currently treated entity)
     * @param string $func Current function (index, view, ...)
     * @param array $templateParameters Template data
     * @param string $template Optional assignment of precalculated template file
     *
     * @return Response
     */
    public function processTemplate(
        $type,
        $func,
        array $templateParameters = [],
        $template = ''
    ) {
        $templateExtension = $this->determineExtension($type, $func);
        if (empty($template)) {
            $isAdmin = isset($templateParameters['routeArea']) && $templateParameters['routeArea'] == 'admin';
            $template = $this->getViewTemplate($type, $func, $isAdmin);
        }
    
        if ('pdf.twig' === $templateExtension) {
            $template = str_replace('.pdf', '.html', $template);
    
            return $this->processPdf($templateParameters, $template);
        }
    
        // look whether we need output with or without the theme
        $request = $this->requestStack->getCurrentRequest();
        $raw = null !== $request ? $request->query->getBoolean('raw') : false;
        if (!$raw && 'html.twig' !== $templateExtension) {
            $raw = true;
        }
    
        $output = $this->twig->render($template, $templateParameters);
        $response = null;
        if (true === $raw) {
            // standalone output
            if ('csv.twig' === $templateExtension) {
                // convert to UTF-16 for improved excel compatibility
                // see http://stackoverflow.com/questions/4348802/how-can-i-output-a-utf-8-csv-in-php-that-excel-will-read-properly
                $output = chr(255) . chr(254) . mb_convert_encoding($output, 'UTF-16LE', 'UTF-8');
            }
            $output = $this->injectAssetsIntoRawOutput($output);
    
            $response = new PlainResponse($output);
        } else {
            // normal output
            $response = new Response($output);
        }
    
        // check if we need to set any custom headers
        switch ($templateExtension) {
            case 'csv.twig':
                $response->headers->set('Content-Encoding', 'UTF-8');
                $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
                $response->headers->set('Content-Disposition', 'attachment; filename=' . $type . '-list.csv');
                break;
            case 'ics.twig':
                $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
                break;
            case 'json.twig':
                $response->headers->set('Content-Type', 'application/json');
                break;
            case 'xml.twig':
                $response->headers->set('Content-Type', 'text/xml');
                break;
            case 'atom.twig':
                $response->headers->set('Content-Type', 'application/atom+xml');
                break;
            case 'rss.twig':
                $response->headers->set('Content-Type', 'application/rss+xml');
                break;
        }
    
        return $response;
    }
    
    /**
     * Adds assets to a raw page which is not processed by the Theme engine.
     *
     * @param string $output The output to be enhanced
     *
     * @return string Output including additional assets
     */
    protected function injectAssetsIntoRawOutput($output = '')
    {
        return $this->assetFilter->filter($output);
    }
    
    /**
     * Get extension of the currently treated template.
     *
     * @param string $type Current controller (name of currently treated entity)
     * @param string $func Current function (index, view, ...)
     *
     * @return string Template extension
     */
    protected function determineExtension($type, $func)
    {
        $templateExtension = 'html.twig';
        if (!in_array($func, ['view', 'display'])) {
            return $templateExtension;
        }
    
        $extensions = $this->availableExtensions($type, $func);
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $templateExtension;
        }
    
        $format = $request->getRequestFormat();
        if ('html' !== $format && in_array($format, $extensions, true)) {
            $templateExtension = $format . '.twig';
        }
    
        return $templateExtension;
    }
    
    /**
     * Get list of available template extensions.
     *
     * @param string $type Current controller (name of currently treated entity)
     * @param string $func Current function (index, view, ...)
     *
     * @return string[] List of allowed template extensions
     */
    protected function availableExtensions($type, $func)
    {
        $extensions = [];
        $hasAdminAccess = $this->permissionHelper->hasComponentPermission($type, ACCESS_ADMIN);
        if ('view' === $func) {
            if ($hasAdminAccess) {
                $extensions = ['csv', 'rss', 'atom', 'xml', 'json', 'pdf'];
            } else {
                $extensions = ['rss', 'atom', 'pdf'];
            }
        } elseif ('display' === $func) {
            if ($hasAdminAccess) {
                $extensions = ['xml', 'json', 'ics', 'pdf'];
            } else {
                $extensions = ['ics', 'pdf'];
            }
        }
    
        return $extensions;
    }
    
    /**
     * Processes a template file using dompdf (LGPL).
     *
     * @param array $templateParameters Template data
     * @param string $template Name of template to use
     *
     * @return Response
     */
    protected function processPdf(array $templateParameters = [], $template = '')
    {
        // first the content, to set page vars
        $output = $this->twig->render($template, $templateParameters);
    
        // make local images absolute
        $request = $this->requestStack->getCurrentRequest();
        $output = str_replace(
            ['img src="' . $request->getSchemeAndHttpHost() . $request->getBasePath() . '/', 'img src="/'],
            ['img src="/', 'img src="' . $request->server->get('DOCUMENT_ROOT') . '/'],
            $output
        );
    
        // then the surrounding
        $output = $this->twig->render('@MUNewsModule/includePdfHeader.html.twig') . $output . '</body></html>';
    
        // create name of the pdf output file
        $siteName = $this->variableApi->getSystemVar('sitename');
        $pageTitle = iconv('UTF-8', 'ASCII//TRANSLIT', $this->pageVars->get('title'));
        $fileTitle = iconv('UTF-8', 'ASCII//TRANSLIT', $siteName)
           . '-'
           . ('' !== $pageTitle ? $pageTitle . '-' : '')
           . date('Ymd') . '.pdf'
        ;
        $fileTitle = str_replace(' ', '_', $fileTitle);
    
        /*
        if (true === $request->query->getBoolean('dbg', false)) {
            die($output);
        }
        */
    
        // instantiate pdf object
        $pdf = new Dompdf();
        // define page properties
        $pdf->setPaper('A4', 'portrait');
        // load html input data
        $pdf->loadHtml($output);
        // create the actual pdf file
        $pdf->render();
        // stream output to browser
        $pdf->stream($fileTitle);
    
        return new Response();
    }
}
