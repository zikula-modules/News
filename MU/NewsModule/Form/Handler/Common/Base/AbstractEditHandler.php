<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Form\Handler\Common\Base;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\RouteUrl;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\GroupsModule\Constant as GroupsConstant;
use Zikula\GroupsModule\Entity\Repository\GroupApplicationRepository;
use Zikula\PageLockModule\Api\ApiInterface\LockingApiInterface;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Helper\FeatureActivationHelper;
use MU\NewsModule\Helper\ControllerHelper;
use MU\NewsModule\Helper\HookHelper;
use MU\NewsModule\Helper\ModelHelper;
use MU\NewsModule\Helper\TranslatableHelper;
use MU\NewsModule\Helper\WorkflowHelper;

/**
 * This handler class handles the page events of editing forms.
 * It collects common functionality required by different object types.
 */
abstract class AbstractEditHandler
{
    use TranslatorTrait;

    /**
     * Name of treated object type.
     *
     * @var string
     */
    protected $objectType;

    /**
     * Name of treated object type starting with upper case.
     *
     * @var string
     */
    protected $objectTypeCapital;

    /**
     * Lower case version.
     *
     * @var string
     */
    protected $objectTypeLower;

    /**
     * Permission component based on object type.
     *
     * @var string
     */
    protected $permissionComponent;

    /**
     * Reference to treated entity instance.
     *
     * @var EntityAccess
     */
    protected $entityRef = null;

    /**
     * Name of primary identifier field.
     *
     * @var string
     */
    protected $idField = null;

    /**
     * Identifier of treated entity.
     *
     * @var integer
     */
    protected $idValue = 0;

    /**
     * Code defining the redirect goal after command handling.
     *
     * @var string
     */
    protected $returnTo = null;

    /**
     * Whether a create action is going to be repeated or not.
     *
     * @var boolean
     */
    protected $repeatCreateAction = false;

    /**
     * Url of current form with all parameters for multiple creations.
     *
     * @var string
     */
    protected $repeatReturnUrl = null;

    /**
     * Whether the PageLock extension is used for this entity type or not.
     *
     * @var boolean
     */
    protected $hasPageLockSupport = false;

    /**
     * Whether the entity has attributes or not.
     *
     * @var boolean
     */
    protected $hasAttributes = false;

    /**
     * Whether the entity has an editable slug or not.
     *
     * @var boolean
     */
    protected $hasSlugUpdatableField = false;

    /**
     * Whether the entity has translatable fields or not.
     *
     * @var boolean
     */
    protected $hasTranslatableFields = false;

    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * The current request.
     *
     * @var Request
     */
    protected $request;

    /**
     * The router.
     *
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PermissionApiInterface
     */
    protected $permissionApi;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;

    /**
     * @var GroupApplicationRepository
     */
    protected $groupApplicationRepository;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * @var HookHelper
     */
    protected $hookHelper;

    /**
     * @var ModelHelper
     */
    protected $modelHelper;

    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;

    /**
     * @var TranslatableHelper
     */
    protected $translatableHelper;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    /**
     * Reference to optional locking api.
     *
     * @var LockingApiInterface
     */
    protected $lockingApi = null;

    /**
     * The handled form type.
     *
     * @var AbstractType
     */
    protected $form;

    /**
     * Template parameters.
     *
     * @var array
     */
    protected $templateParameters = [];

    /**
     * EditHandler constructor.
     *
     * @param ZikulaHttpKernelInterface $kernel           Kernel service instance
     * @param TranslatorInterface       $translator       Translator service instance
     * @param FormFactoryInterface      $formFactory      FormFactory service instance
     * @param RequestStack              $requestStack     RequestStack service instance
     * @param RouterInterface           $router           Router service instance
     * @param LoggerInterface           $logger           Logger service instance
     * @param PermissionApiInterface    $permissionApi    PermissionApi service instance
     * @param VariableApiInterface      $variableApi      VariableApi service instance
     * @param CurrentUserApiInterface   $currentUserApi   CurrentUserApi service instance
     * @param GroupApplicationRepository $groupApplicationRepository GroupApplicationRepository service instance.
     * @param EntityFactory             $entityFactory    EntityFactory service instance
     * @param ControllerHelper          $controllerHelper ControllerHelper service instance
     * @param ModelHelper               $modelHelper      ModelHelper service instance
     * @param WorkflowHelper            $workflowHelper   WorkflowHelper service instance
     * @param HookHelper                $hookHelper       HookHelper service instance
     * @param TranslatableHelper        $translatableHelper TranslatableHelper service instance
     * @param FeatureActivationHelper   $featureActivationHelper FeatureActivationHelper service instance
     */
    public function __construct(
        ZikulaHttpKernelInterface $kernel,
        TranslatorInterface $translator,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        RouterInterface $router,
        LoggerInterface $logger,
        PermissionApiInterface $permissionApi,
        VariableApiInterface $variableApi,
        CurrentUserApiInterface $currentUserApi,
        GroupApplicationRepository $groupApplicationRepository,
        EntityFactory $entityFactory,
        ControllerHelper $controllerHelper,
        ModelHelper $modelHelper,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        TranslatableHelper $translatableHelper,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->kernel = $kernel;
        $this->setTranslator($translator);
        $this->formFactory = $formFactory;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->logger = $logger;
        $this->permissionApi = $permissionApi;
        $this->variableApi = $variableApi;
        $this->currentUserApi = $currentUserApi;
        $this->groupApplicationRepository = $groupApplicationRepository;
        $this->entityFactory = $entityFactory;
        $this->controllerHelper = $controllerHelper;
        $this->modelHelper = $modelHelper;
        $this->workflowHelper = $workflowHelper;
        $this->hookHelper = $hookHelper;
        $this->translatableHelper = $translatableHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * Initialise form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param array $templateParameters List of preassigned template variables
     *
     * @return boolean False in case of initialisation errors, otherwise true
     *
     * @throws RuntimeException Thrown if the workflow actions can not be determined
     */
    public function processForm(array $templateParameters)
    {
        $this->templateParameters = $templateParameters;
    
        // initialise redirect goal
        $this->returnTo = $this->request->query->get('returnTo', null);
        // default to referer
        $refererSessionVar = 'munewsmodule' . $this->objectTypeCapital . 'Referer';
        if (null === $this->returnTo && $this->request->headers->has('referer')) {
            $currentReferer = $this->request->headers->get('referer');
            if ($currentReferer != $this->request->getUri()) {
                $this->returnTo = $currentReferer;
                $this->request->getSession()->set($refererSessionVar, $this->returnTo);
            }
        }
        if (null === $this->returnTo && $this->request->getSession()->has($refererSessionVar)) {
            $this->returnTo = $this->request->getSession()->get($refererSessionVar);
        }
        // store current uri for repeated creations
        $this->repeatReturnUrl = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() . $this->request->getPathInfo();
    
        $this->permissionComponent = 'MUNewsModule:' . $this->objectTypeCapital . ':';
    
        $this->idField = $this->entityFactory->getIdField($this->objectType);
    
        // retrieve identifier of the object we wish to edit
        $routeParams = $this->request->get('_route_params', []);
        if (empty($this->idValue)) {
            if (array_key_exists($this->idField, $routeParams)) {
                $this->idValue = (int) !empty($routeParams[$this->idField]) ? $routeParams[$this->idField] : 0;
            }
            if (0 === $this->idValue) {
                $this->idValue = $this->request->query->getInt($this->idField, 0);
            }
            if (0 === $this->idValue && $this->idField != 'id') {
                $this->idValue = $this->request->query->getInt('id', 0);
            }
        }
    
        $entity = null;
        $this->templateParameters['mode'] = !empty($this->idValue) ? 'edit' : 'create';
    
        if ($this->templateParameters['mode'] == 'edit') {
            if (!$this->permissionApi->hasPermission($this->permissionComponent, $this->idValue . '::', ACCESS_EDIT)) {
                throw new AccessDeniedException();
            }
    
            $entity = $this->initEntityForEditing();
            if (null !== $entity) {
                if (true === $this->hasPageLockSupport && $this->kernel->isBundle('ZikulaPageLockModule') && null !== $this->lockingApi) {
                    // try to guarantee that only one person at a time can be editing this entity
                    $lockName = 'MUNewsModule' . $this->objectTypeCapital . $entity->getKey();
                    $this->lockingApi->addLock($lockName, $this->getRedirectUrl(null));
                    // reload entity as the addLock call above has triggered the preUpdate event
                    $this->entityFactory->getObjectManager()->refresh($entity);
                }
            }
        } else {
            $permissionLevel = in_array($this->objectType, ['message']) ? ACCESS_COMMENT : ACCESS_EDIT;
            if (!$this->permissionApi->hasPermission($this->permissionComponent, '::', $permissionLevel)) {
                throw new AccessDeniedException();
            }
    
            $entity = $this->initEntityForCreation();
    
            // set default values from request parameters
            foreach ($this->request->query->all() as $key => $value) {
                if (strlen($key) < 5 || substr($key, 0, 4) != 'set_') {
                    continue;
                }
                $fieldName = str_replace('set_', '', $key);
                $setterName = 'set' . ucfirst($fieldName);
                if (!method_exists($entity, $setterName)) {
                    continue;
                }
                $entity[$fieldName] = $value;
            }
        }
    
        if (null === $entity) {
            $this->request->getSession()->getFlashBag()->add('error', $this->__('No such item found.'));
    
            return new RedirectResponse($this->getRedirectUrl(['commandName' => 'cancel']), 302);
        }
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    
        
        if (true === $this->hasAttributes) {
            if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::ATTRIBUTES, $this->objectType)) {
                $this->initAttributesForEditing();
            }
        }
        
        if (true === $this->hasTranslatableFields) {
            $this->initTranslationsForEditing();
        }
    
        $actions = $this->workflowHelper->getActionsForObject($entity);
        if (false === $actions || !is_array($actions)) {
            $this->request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not determine workflow actions.'));
            $logArgs = ['app' => 'MUNewsModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $this->objectType, 'id' => $entity->getKey()];
            $this->logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed to determine available workflow actions.', $logArgs);
            throw new \RuntimeException($this->__('Error! Could not determine workflow actions.'));
        }
    
        $this->templateParameters['actions'] = $actions;
    
        $this->form = $this->createForm();
        if (!is_object($this->form)) {
            return false;
        }
    
        if ($entity->supportsHookSubscribers()) {
            // Call form aware display hooks
            $formHook = $this->hookHelper->callFormDisplayHooks($this->form, $entity, FormAwareCategory::TYPE_EDIT);
            $this->templateParameters['formHookTemplates'] = $formHook->getTemplates();
        }
    
        // handle form request and check validity constraints of edited entity
        if ($this->form->handleRequest($this->request) && $this->form->isSubmitted()) {
            if ($this->form->isValid()) {
                $result = $this->handleCommand();
                if (false === $result) {
                    $this->templateParameters['form'] = $this->form->createView();
                }
    
                return $result;
            }
            if ($this->form->get('cancel')->isClicked()) {
                return new RedirectResponse($this->getRedirectUrl(['commandName' => 'cancel']), 302);
            }
        }
    
        $this->templateParameters['form'] = $this->form->createView();
    
        // everything okay, no initialisation errors occured
        return true;
    }
    
    /**
     * Creates the form type.
     */
    protected function createForm()
    {
        // to be customised in sub classes
        return null;
    }
    
    /**
     * Returns the template parameters.
     *
     * @return array
     */
    public function getTemplateParameters()
    {
        return $this->templateParameters;
    }
    
    
    /**
     * Initialise existing entity for editing.
     *
     * @return EntityAccess|null Desired entity instance or null
     */
    protected function initEntityForEditing()
    {
        return $this->entityFactory->getRepository($this->objectType)->selectById($this->idValue);
    }
    
    /**
     * Initialise new entity for creation.
     *
     * @return EntityAccess|null Desired entity instance or null
     */
    protected function initEntityForCreation()
    {
        $templateId = $this->request->query->getInt('astemplate', '');
        $entity = null;
    
        if (!empty($templateId)) {
            // reuse existing entity
            $entityT = $this->entityFactory->getRepository($this->objectType)->selectById($templateId);
            if (null === $entityT) {
                return null;
            }
            $entity = clone $entityT;
        }
    
        if (null === $entity) {
            $createMethod = 'create' . ucfirst($this->objectType);
            $entity = $this->entityFactory->$createMethod();
        }
    
        return $entity;
    }
    
    /**
     * Initialise translations.
     */
    protected function initTranslationsForEditing()
    {
        $translationsEnabled = $this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, $this->objectType);
        $this->templateParameters['translationsEnabled'] = $translationsEnabled;
    
        $supportedLanguages = $this->translatableHelper->getSupportedLanguages($this->objectType);
        // assign list of installed languages for translatable extension
        $this->templateParameters['supportedLanguages'] = $supportedLanguages;
    
        if (!$translationsEnabled) {
            return;
        }
    
        if ($this->variableApi->getSystemVar('multilingual') != 1) {
            $this->templateParameters['translationsEnabled'] = false;
    
            return;
        }
        if (count($supportedLanguages) < 2) {
            $this->templateParameters['translationsEnabled'] = false;
    
            return;
        }
    
        $mandatoryFieldsPerLocale = $this->translatableHelper->getMandatoryFields($this->objectType);
        $localesWithMandatoryFields = [];
        foreach ($mandatoryFieldsPerLocale as $locale => $fields) {
            if (count($fields) > 0) {
                $localesWithMandatoryFields[] = $locale;
            }
        }
        if (!in_array($this->translatableHelper->getCurrentLanguage(), $localesWithMandatoryFields)) {
            $localesWithMandatoryFields[] = $this->translatableHelper->getCurrentLanguage();
        }
        $this->templateParameters['localesWithMandatoryFields'] = $localesWithMandatoryFields;
    
        // retrieve and assign translated fields
        $translations = $this->translatableHelper->prepareEntityForEditing($this->entityRef);
        foreach ($translations as $language => $translationData) {
            $this->templateParameters[$this->objectTypeLower . $language] = $translationData;
        }
    }
    
    /**
     * Initialise attributes.
     */
    protected function initAttributesForEditing()
    {
        $entity = $this->entityRef;
    
        $entityData = [];
    
        // overwrite attributes array entry with a form compatible format
        $attributes = [];
        foreach ($this->getAttributeFieldNames() as $fieldName) {
            $attributes[$fieldName] = $entity->getAttributes()->get($fieldName) ? $entity->getAttributes()->get($fieldName)->getValue() : '';
        }
        $entityData['attributes'] = $attributes;
    
        $this->templateParameters['attributes'] = $attributes;
    }
    
    /**
     * Return list of attribute field names.
     * To be customised in sub classes as needed.
     *
     * @return array list of attribute names
     */
    protected function getAttributeFieldNames()
    {
        return [
            'field1', 'field2', 'field3'
        ];
    }

    /**
     * Get list of allowed redirect codes.
     *
     * @return string[] list of possible redirect codes
     */
    protected function getRedirectCodes()
    {
        $codes = [];
    
        // to be filled by subclasses
    
        return $codes;
    }

    /**
     * Command event handler.
     *
     * @param array $args List of arguments
     *
     * @return mixed Redirect or false on errors
     */
    public function handleCommand(array $args = [])
    {
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if ($this->form->get('cancel')->isClicked()) {
            $args['commandName'] = 'cancel';
        }
    
        $action = $args['commandName'];
        $isRegularAction = !in_array($action, ['delete', 'cancel']);
    
        if ($isRegularAction || $action == 'delete') {
            $this->fetchInputData($args);
        }
    
        // get treated entity reference from persisted member var
        $entity = $this->entityRef;
    
        if ($entity->supportsHookSubscribers() && $action != 'cancel') {
            // Let any ui hooks perform additional validation actions
            $hookType = $action == 'delete' ? UiHooksCategory::TYPE_VALIDATE_DELETE : UiHooksCategory::TYPE_VALIDATE_EDIT;
            $validationErrors = $this->hookHelper->callValidationHooks($entity, $hookType);
            if (count($validationErrors) > 0) {
                $flashBag = $this->request->getSession()->getFlashBag();
                foreach ($validationErrors as $message) {
                    $flashBag->add('error', $message);
                }
    
                return false;
            }
        }
    
        if ($isRegularAction || $action == 'delete') {
            $success = $this->applyAction($args);
            if (!$success) {
                // the workflow operation failed
                return false;
            }
    
            if ($isRegularAction && true === $this->hasTranslatableFields) {
                if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, $this->objectType)) {
                    $this->processTranslationsForUpdate();
                }
            }
    
            if ($entity->supportsHookSubscribers()) {
                $routeUrl = null;
                if ($action != 'delete') {
                    $urlArgs = $entity->createUrlArgs();
                    $urlArgs['_locale'] = $this->request->getLocale();
                    $routeUrl = new RouteUrl('munewsmodule_' . $this->objectTypeLower . '_display', $urlArgs);
                }
    
                // Call form aware processing hooks
                $hookType = $action == 'delete' ? FormAwareCategory::TYPE_PROCESS_DELETE : FormAwareCategory::TYPE_PROCESS_EDIT;
                $this->hookHelper->callFormProcessHooks($this->form, $entity, $hookType, $routeUrl);
    
                // Let any ui hooks know that we have created, updated or deleted an item
                $hookType = $action == 'delete' ? UiHooksCategory::TYPE_PROCESS_DELETE : UiHooksCategory::TYPE_PROCESS_EDIT;
                $this->hookHelper->callProcessHooks($entity, $hookType, $routeUrl);
            }
        }
    
        if (true === $this->hasPageLockSupport && $this->templateParameters['mode'] == 'edit' && $this->kernel->isBundle('ZikulaPageLockModule') && null !== $this->lockingApi) {
            $lockName = 'MUNewsModule' . $this->objectTypeCapital . $entity->getKey();
            $this->lockingApi->releaseLock($lockName);
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * Prepare update of attributes.
     */
    protected function processAttributesForUpdate()
    {
        $entity = $this->entityRef;
        foreach ($this->getAttributeFieldNames() as $fieldName) {
            $value = $this->form['attributes' . $fieldName]->getData();
            $entity->setAttribute($fieldName, $value);
        }
        
    }
    
    /**
     * Prepare update of translations.
     */
    protected function processTranslationsForUpdate()
    {
        if (!$this->templateParameters['translationsEnabled']) {
            return;
        }
    
        // persist translated fields
        $this->translatableHelper->processEntityAfterEditing($this->entityRef, $this->form, $this->entityFactory->getObjectManager());
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param array   $args    arguments from handleCommand method
     * @param Boolean $success true if this is a success, false for default error
     *
     * @return String desired status or error message
     */
    protected function getDefaultMessage(array $args = [], $success = false)
    {
        $message = '';
        switch ($args['commandName']) {
            case 'create':
                if (true === $success) {
                    $message = $this->__('Done! Item created.');
                } else {
                    $message = $this->__('Error! Creation attempt failed.');
                }
                break;
            case 'update':
                if (true === $success) {
                    $message = $this->__('Done! Item updated.');
                } else {
                    $message = $this->__('Error! Update attempt failed.');
                }
                break;
            case 'delete':
                if (true === $success) {
                    $message = $this->__('Done! Item deleted.');
                } else {
                    $message = $this->__('Error! Deletion attempt failed.');
                }
                break;
        }
    
        return $message;
    }
    
    /**
     * Add success or error message to session.
     *
     * @param array   $args    arguments from handleCommand method
     * @param Boolean $success true if this is a success, false for default error
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    protected function addDefaultMessage(array $args = [], $success = false)
    {
        $message = $this->getDefaultMessage($args, $success);
        if (empty($message)) {
            return;
        }
    
        $flashType = true === $success ? 'status' : 'error';
        $this->request->getSession()->getFlashBag()->add($flashType, $message);
        $logArgs = ['app' => 'MUNewsModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $this->objectType, 'id' => $this->entityRef->getKey()];
        if (true === $success) {
            $this->logger->notice('{app}: User {user} updated the {entity} with id {id}.', $logArgs);
        } else {
            $this->logger->error('{app}: User {user} tried to update the {entity} with id {id}, but failed.', $logArgs);
        }
    }

    /**
     * Input data processing called by handleCommand method.
     *
     * @param array $args Additional arguments
     */
    public function fetchInputData(array $args = [])
    {
        // fetch posted data input values as an associative array
        $formData = $this->form->getData();
    
        if ($args['commandName'] != 'cancel') {
            if (true === $this->hasSlugUpdatableField && isset($entityData['slug'])) {
                $entityData['slug'] = iconv('UTF-8', 'ASCII//TRANSLIT', $entityData['slug']);
            }
        }
    
        if ($this->templateParameters['mode'] == 'create' && isset($this->form['repeatCreation']) && $this->form['repeatCreation']->getData() == 1) {
            $this->repeatCreateAction = true;
        }
    
        if (method_exists($this->entityRef, 'getCreatedBy')) {
            if (isset($this->form['moderationSpecificCreator']) && null !== $this->form['moderationSpecificCreator']->getData()) {
                $this->entityRef->setCreatedBy($this->form['moderationSpecificCreator']->getData());
            }
            if (isset($this->form['moderationSpecificCreationDate']) && $this->form['moderationSpecificCreationDate']->getData() != '') {
                $this->entityRef->setCreatedDate($this->form['moderationSpecificCreationDate']->getData());
            }
        }
    
        if (isset($this->form['additionalNotificationRemarks']) && $this->form['additionalNotificationRemarks']->getData() != '') {
            $this->request->getSession()->set('MUNewsModuleAdditionalNotificationRemarks', $this->form['additionalNotificationRemarks']->getData());
        }
    
        if (true === $this->hasAttributes) {
            if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::ATTRIBUTES, $this->objectType)) {
                $this->processAttributesForUpdate();
            }
        }
    
        // return remaining form data
        return $formData;
    }

    /**
     * This method executes a certain workflow action.
     *
     * @param array $args Arguments from handleCommand method
     *
     * @return bool Whether everything worked well or not
     */
    public function applyAction(array $args = [])
    {
        // stub for subclasses
        return false;
    }

    /**
     * Prepares properties related to advanced workflows.
     *
     * @param bool $enterprise Whether the enterprise workflow is used instead of the standard workflow
     *
     * @return array List of additional form options
     */
    protected function prepareWorkflowAdditions($enterprise = false)
    {
        $roles = [];
        $currentUserId = $this->currentUserApi->isLoggedIn() ? $this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        $roles['is_creator'] = $this->templateParameters['mode'] == 'create'
            || (method_exists($this->entityRef, 'getCreatedBy') && $this->entityRef->getCreatedBy()->getUid() == $currentUserId);
    
        $groupApplicationArgs = [
            'user' => $currentUserId,
            'group' => $this->variableApi->get('MUNewsModule', 'moderationGroupFor' . $this->objectTypeCapital, GroupsConstant::GROUP_ID_ADMIN)
        ];
        $roles['is_moderator'] = count($this->groupApplicationRepository->findBy($groupApplicationArgs)) > 0;
    
        if (true === $enterprise) {
            $groupApplicationArgs = [
                'user' => $currentUserId,
                'group' => $this->variableApi->get('MUNewsModule', 'superModerationGroupFor' . $this->objectTypeCapital, GroupsConstant::GROUP_ID_ADMIN)
            ];
            $roles['is_super_moderator'] = count($this->groupApplicationRepository->findBy($groupApplicationArgs)) > 0;
        }
    
        return $roles;
    }

    /**
     * Sets optional locking api reference.
     *
     * @param LockingApiInterface $lockingApi
     */
    public function setLockingApi(LockingApiInterface $lockingApi)
    {
        $this->lockingApi = $lockingApi;
    }
}
