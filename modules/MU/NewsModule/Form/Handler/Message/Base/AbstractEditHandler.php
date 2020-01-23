<?php

/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @see https://homepages-mit-zikula.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Form\Handler\Message\Base;

use MU\NewsModule\Form\Handler\Common\EditHandler;
use MU\NewsModule\Form\Type\MessageType;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MU\NewsModule\Entity\MessageEntity;
use MU\NewsModule\Helper\FeatureActivationHelper;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the message object type.
 */
abstract class AbstractEditHandler extends EditHandler
{
    public function processForm(array $templateParameters = [])
    {
        $this->objectType = 'message';
        $this->objectTypeCapital = 'Message';
        $this->objectTypeLower = 'message';
        
        $this->hasPageLockSupport = true;
        $this->hasAttributes = true;
        $this->hasTranslatableFields = true;
    
        $result = parent::processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
    
        if ('create' === $this->templateParameters['mode'] && !$this->modelHelper->canBeCreated($this->objectType)) {
            $request = $this->requestStack->getCurrentRequest();
            if ($request->hasSession() && ($session = $request->getSession())) {
                $session->getFlashBag()->add(
                    'error',
                    $this->__(
                        'Sorry, but you can not create the message yet as other items are required which must be created before!'
                    )
                );
            }
            $logArgs = [
                'app' => 'MUNewsModule',
                'user' => $this->currentUserApi->get('uname'),
                'entity' => $this->objectType
            ];
            $this->logger->notice(
                '{app}: User {user} tried to create a new {entity}, but failed'
                    . ' as other items are required which must be created before.',
                $logArgs
            );
    
            return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
        }
    
        $entityData = $this->entityRef->toArray();
    
        // assign data to template as array (for additions like standard fields)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
        $this->templateParameters['supportsHookSubscribers'] = $this->entityRef->supportsHookSubscribers();
    
        return $result;
    }
    
    protected function createForm()
    {
        return $this->formFactory->create(MessageType::class, $this->entityRef, $this->getFormOptions());
    }
    
    protected function getFormOptions()
    {
        $options = [
            'entity' => $this->entityRef,
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'has_moderate_permission' => $this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADMIN),
            'allow_moderation_specific_creator' => (bool)$this->variableApi->get(
                'MUNewsModule',
                'allowModerationSpecificCreatorFor' . $this->objectTypeCapital,
                false
            ),
            'allow_moderation_specific_creation_date' => (bool)$this->variableApi->get(
                'MUNewsModule',
                'allowModerationSpecificCreationDateFor' . $this->objectTypeCapital,
                false
            ),
            'filter_by_ownership' => !$this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADD),
            'inline_usage' => $this->templateParameters['inlineUsage']
        ];
        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::ATTRIBUTES, $this->objectType)) {
            $options['attributes'] = $this->templateParameters['attributes'];
        }
    
        $workflowRoles = $this->prepareWorkflowAdditions(false);
        $options = array_merge($options, $workflowRoles);
    
        $options['translations'] = [];
        foreach ($this->templateParameters['supportedLanguages'] as $language) {
            $translationKey = $this->objectTypeLower . $language;
            $options['translations'][$language] = isset($this->templateParameters[$translationKey]) ? $this->templateParameters[$translationKey] : [];
        }
    
        return $options;
    }

    protected function getRedirectCodes()
    {
        $codes = parent::getRedirectCodes();
    
        // user index page of message area
        $codes[] = 'userIndex';
        // admin index page of message area
        $codes[] = 'adminIndex';
        // user list of messages
        $codes[] = 'userView';
        // admin list of messages
        $codes[] = 'adminView';
        // user list of own messages
        $codes[] = 'userOwnView';
        // admin list of own messages
        $codes[] = 'adminOwnView';
        // user detail page of treated message
        $codes[] = 'userDisplay';
        // admin detail page of treated message
        $codes[] = 'adminDisplay';
    
    
        return $codes;
    }

    /**
     * Get the default redirect url. Required if no returnTo parameter has been supplied.
     * This method is called in handleCommand so we know which command has been performed.
     *
     * @param array $args List of arguments
     *
     * @return string The default redirect url
     */
    protected function getDefaultReturnUrl(array $args = [])
    {
        $objectIsPersisted = 'delete' !== $args['commandName']
            && !('create' === $this->templateParameters['mode'] && 'cancel' === $args['commandName']
        );
        if (null !== $this->returnTo && $objectIsPersisted) {
            // return to referer
            return $this->returnTo;
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters)
            ? $this->templateParameters['routeArea']
            : ''
        ;
        $routePrefix = 'munewsmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // redirect to the list of messages
        $url = $this->router->generate($routePrefix . 'view');
    
        if ($objectIsPersisted) {
            // redirect to the detail page of treated message
            $url = $this->router->generate($routePrefix . 'display', $this->entityRef->createUrlArgs());
        }
    
        return $url;
    }

    public function handleCommand(array $args = [])
    {
        $result = parent::handleCommand($args);
        if (false === $result) {
            return $result;
        }
    
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if (
            'create' === $this->templateParameters['mode']
            && $this->form->has('submitrepeat')
            && $this->form->get('submitrepeat')->isClicked()
        ) {
            $args['commandName'] = 'submit';
            $this->repeatCreateAction = true;
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    protected function getDefaultMessage(array $args = [], $success = false)
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        switch ($args['commandName']) {
            case 'submit':
                if ('create' === $this->templateParameters['mode']) {
                    $message = $this->__('Done! Message created.');
                } else {
                    $message = $this->__('Done! Message updated.');
                }
                if ('waiting' === $this->entityRef->getWorkflowState()) {
                    $message .= ' ' . $this->__('It is now waiting for approval by our moderators.');
                }
                break;
            case 'delete':
                $message = $this->__('Done! Message deleted.');
                break;
            default:
                $message = $this->__('Done! Message updated.');
                break;
        }
    
        return $message;
    }

    /**
     * @throws RuntimeException Thrown if concurrent editing is recognised or another error occurs
     */
    public function applyAction(array $args = [])
    {
        // get treated entity reference from persisted member var
        /** @var MessageEntity $entity */
        $entity = $this->entityRef;
    
        $action = $args['commandName'];
    
        $success = false;
        try {
            // execute the workflow action
            $success = $this->workflowHelper->executeAction($entity, $action);
        } catch (Exception $exception) {
            $request = $this->requestStack->getCurrentRequest();
            if ($request->hasSession() && ($session = $request->getSession())) {
                $session->getFlashBag()->add(
                    'error',
                    $this->__f(
                        'Sorry, but an error occured during the %action% action. Please apply the changes again!',
                        ['%action%' => $action]
                    ) . ' ' . $exception->getMessage()
                );
            }
            $logArgs = [
                'app' => 'MUNewsModule',
                'user' => $this->currentUserApi->get('uname'),
                'entity' => 'message',
                'id' => $entity->getKey(),
                'errorMessage' => $exception->getMessage()
            ];
            $this->logger->error(
                '{app}: User {user} tried to edit the {entity} with id {id},'
                    . ' but failed. Error details: {errorMessage}.',
                $logArgs
            );
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && 'create' === $this->templateParameters['mode']) {
            // store new identifier
            $this->idValue = $entity->getKey();
        }
    
        return $success;
    }

    /**
     * Get URL to redirect to.
     *
     * @param array $args List of arguments
     *
     * @return string The redirect url
     */
    protected function getRedirectUrl(array $args = [])
    {
        if (isset($this->templateParameters['inlineUsage']) && true === $this->templateParameters['inlineUsage']) {
            $commandName = 'submit' === substr($args['commandName'], 0, 6) ? 'create' : $args['commandName'];
            $urlArgs = [
                'idPrefix' => $this->idPrefix,
                'commandName' => $commandName,
                'id' => $this->idValue
            ];
    
            // inline usage, return to special function for closing the modal window instance
            return $this->router->generate('munewsmodule_' . $this->objectTypeLower . '_handleinlineredirect', $urlArgs);
        }
    
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        $request = $this->requestStack->getCurrentRequest();
        if ($request->hasSession() && ($session = $request->getSession())) {
            if ($session->has('munewsmodule' . $this->objectTypeCapital . 'Referer')) {
                $this->returnTo = $session->get('munewsmodule' . $this->objectTypeCapital . 'Referer');
                $session->remove('munewsmodule' . $this->objectTypeCapital . 'Referer');
            }
        }
    
        if ('create' !== $this->templateParameters['mode']) {
            // force refresh because slugs may have changed (e.g. by translatable)
            $this->entityFactory->getEntityManager()->clear();
            $this->entityRef = $this->initEntityForEditing();
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes(), true)) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        $routeArea = 0 === strpos($this->returnTo, 'admin') ? 'admin' : '';
        $routePrefix = 'munewsmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'userIndex':
            case 'adminIndex':
                return $this->router->generate($routePrefix . 'index');
            case 'userView':
            case 'adminView':
                return $this->router->generate($routePrefix . 'view');
            case 'userOwnView':
            case 'adminOwnView':
                return $this->router->generate($routePrefix . 'view', [ 'own' => 1 ]);
            case 'userDisplay':
            case 'adminDisplay':
                if (
                    'delete' !== $args['commandName']
                    && !('create' === $this->templateParameters['mode'] && 'cancel' === $args['commandName'])
                ) {
                    return $this->router->generate($routePrefix . 'display', $this->entityRef->createUrlArgs());
                }
    
                return $this->getDefaultReturnUrl($args);
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
