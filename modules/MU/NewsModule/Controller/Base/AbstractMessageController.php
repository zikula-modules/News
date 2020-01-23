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

namespace MU\NewsModule\Controller\Base;

use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\FormExtensionBundle\Form\Type\DeletionType;
use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\Response\PlainResponse;
use Zikula\Core\RouteUrl;
use MU\NewsModule\Entity\MessageEntity;

/**
 * Message controller base class.
 */
abstract class AbstractMessageController extends AbstractController
{
    
    /**
     * This is the default action handling the index area called without defining arguments.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function indexInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'message';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        $permissionHelper = $this->get('mu_news_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        return $this->redirectToRoute('munewsmodule_message_' . $templateParameters['routeArea'] . 'view');
    }
    
    
    /**
     * This action provides an item list overview.
     *
     * @param Request $request
     * @param string $sort Sorting field
     * @param string $sortdir Sorting direction
     * @param int $pos Current pager position
     * @param int $num Amount of entries to display
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws Exception
     */
    protected function viewInternal(
        Request $request,
        $sort,
        $sortdir,
        $pos,
        $num,
        $isAdmin = false
    ) {
        $objectType = 'message';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        $permissionHelper = $this->get('mu_news_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        $controllerHelper = $this->get('mu_news_module.controller_helper');
        $viewHelper = $this->get('mu_news_module.view_helper');
        
        $request->query->set('sort', $sort);
        $request->query->set('sortdir', $sortdir);
        $request->query->set('pos', $pos);
        
        /** @var RouterInterface $router */
        $router = $this->get('router');
        $routeName = 'munewsmodule_message_' . ($isAdmin ? 'admin' : '') . 'view';
        $sortableColumns = new SortableColumns($router, $routeName, 'sort', 'sortdir');
        
        $sortableColumns->addColumns([
            new Column('workflowState'),
            new Column('title'),
            new Column('imageUpload1'),
            new Column('displayOnIndex'),
            new Column('createdBy'),
            new Column('createdDate'),
            new Column('updatedBy'),
            new Column('updatedDate'),
        ]);
        
        $templateParameters = $controllerHelper->processViewActionParameters(
            $objectType,
            $sortableColumns,
            $templateParameters,
            true
        );
        
        // filter by permissions
        $templateParameters['items'] = $permissionHelper->filterCollection(
            $objectType,
            $templateParameters['items'],
            $permLevel
        );
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($objectType, 'view', $templateParameters);
    }
    
    
    /**
     * This action provides a item detail view.
     *
     * @param Request $request
     * @param string $slug Slug of treated message instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if message to be displayed isn't found
     */
    protected function displayInternal(
        Request $request,
        MessageEntity $message = null,
        $slug = '',
        $isAdmin = false
    ) {
        if (null === $message) {
            $message = $this->get('mu_news_module.entity_factory')->getRepository('message')->selectBySlug($slug);
        }
        if (null === $message) {
            throw new NotFoundHttpException(
                $this->__(
                    'No such message found.'
                )
            );
        }
        
        $objectType = 'message';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        $permissionHelper = $this->get('mu_news_module.permission_helper');
        if (!$permissionHelper->hasEntityPermission($message, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        if (
            'approved' !== $message->getWorkflowState()
            && !$permissionHelper->hasEntityPermission($message, ACCESS_EDIT)
        ) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
            $objectType => $message
        ];
        
        $controllerHelper = $this->get('mu_news_module.controller_helper');
        $templateParameters = $controllerHelper->processDisplayActionParameters(
            $objectType,
            $templateParameters,
            $message->supportsHookSubscribers()
        );
        
        // fetch and return the appropriate template
        $response = $this->get('mu_news_module.view_helper')->processTemplate($objectType, 'display', $templateParameters);
        
        if ('ics' === $request->getRequestFormat()) {
            $fileName = $objectType . '_' .
                (property_exists($message, 'slug')
                    ? $message['slug']
                    : $this->get('mu_news_module.entity_display_helper')->getFormattedTitle($message)
                ) . '.ics'
            ;
            $response->headers->set('Content-Disposition', 'attachment; filename=' . $fileName);
        }
        
        return $response;
    }
    
    
    /**
     * This action provides a handling of edit requests.
     *
     * @param Request $request
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws RuntimeException Thrown if another critical error occurs (e.g. workflow actions not available)
     * @throws Exception
     */
    protected function editInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'message';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_COMMENT;
        $permissionHelper = $this->get('mu_news_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        $controllerHelper = $this->get('mu_news_module.controller_helper');
        $templateParameters = $controllerHelper->processEditActionParameters($objectType, $templateParameters);
        
        // delegate form processing to the form handler
        $formHandler = $this->get('mu_news_module.form.handler.message');
        $result = $formHandler->processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        
        $templateParameters = $formHandler->getTemplateParameters();
        
        // fetch and return the appropriate template
        return $this->get('mu_news_module.view_helper')->processTemplate($objectType, 'edit', $templateParameters);
    }
    
    
    /**
     * This action provides a handling of simple delete requests.
     *
     * @param Request $request
     * @param string $slug Slug of treated message instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if message to be deleted isn't found
     * @throws RuntimeException Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    protected function deleteInternal(
        Request $request,
        $slug,
        $isAdmin = false
    ) {
        if (null === $message) {
            $message = $this->get('mu_news_module.entity_factory')->getRepository('message')->selectBySlug($slug);
        }
        if (null === $message) {
            throw new NotFoundHttpException(
                $this->__(
                    'No such message found.'
                )
            );
        }
        
        $objectType = 'message';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_DELETE;
        $permissionHelper = $this->get('mu_news_module.permission_helper');
        if (!$permissionHelper->hasEntityPermission($message, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $logger = $this->get('logger');
        $logArgs = ['app' => 'MUNewsModule', 'user' => $this->get('zikula_users_module.current_user')->get('uname'), 'entity' => 'message', 'id' => $message->getKey()];
        
        // determine available workflow actions
        $workflowHelper = $this->get('mu_news_module.workflow_helper');
        $actions = $workflowHelper->getActionsForObject($message);
        if (false === $actions || !is_array($actions)) {
            $this->addFlash('error', $this->__('Error! Could not determine workflow actions.'));
            $logger->error('{app}: User {user} tried to delete the {entity} with id {id}, but failed to determine available workflow actions.', $logArgs);
            throw new RuntimeException($this->__('Error! Could not determine workflow actions.'));
        }
        
        // redirect to the list of messages
        $redirectRoute = 'munewsmodule_message_' . ($isAdmin ? 'admin' : '') . 'view';
        
        // check whether deletion is allowed
        $deleteActionId = 'delete';
        $deleteAllowed = false;
        foreach ($actions as $actionId => $action) {
            if ($actionId != $deleteActionId) {
                continue;
            }
            $deleteAllowed = true;
            break;
        }
        if (!$deleteAllowed) {
            $this->addFlash(
                'error',
                $this->__(
                    'Error! It is not allowed to delete this message.'
                )
            );
            $logger->error('{app}: User {user} tried to delete the {entity} with id {id}, but this action was not allowed.', $logArgs);
        
            return $this->redirectToRoute($redirectRoute);
        }
        
        $form = $this->createForm(DeletionType::class, $message);
        if ($message->supportsHookSubscribers()) {
            $hookHelper = $this->get('mu_news_module.hook_helper');
        
            // call form aware display hooks
            $formHook = $hookHelper->callFormDisplayHooks($form, $message, FormAwareCategory::TYPE_DELETE);
        }
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                if ($message->supportsHookSubscribers()) {
                    // Let any ui hooks perform additional validation actions
                    $validationErrors = $hookHelper->callValidationHooks($message, UiHooksCategory::TYPE_VALIDATE_DELETE);
                    if (0 < count($validationErrors)) {
                        foreach ($validationErrors as $message) {
                            $this->addFlash('error', $message);
                        }
                    } else {
                        // execute the workflow action
                        $success = $workflowHelper->executeAction($message, $deleteActionId);
                        if ($success) {
                            $this->addFlash(
                                'status',
                                $this->__(
                                    'Done! Message deleted.'
                                )
                            );
                            $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', $logArgs);
                        }
                        
                        if ($message->supportsHookSubscribers()) {
                            // Call form aware processing hooks
                            $hookHelper->callFormProcessHooks($form, $message, FormAwareCategory::TYPE_PROCESS_DELETE);
                        
                            // Let any ui hooks know that we have deleted the message
                            $hookHelper->callProcessHooks($message, UiHooksCategory::TYPE_PROCESS_DELETE);
                        }
                        
                        return $this->redirectToRoute($redirectRoute);
                    }
                } else {
                    // execute the workflow action
                    $success = $workflowHelper->executeAction($message, $deleteActionId);
                    if ($success) {
                        $this->addFlash(
                            'status',
                            $this->__(
                                'Done! Message deleted.'
                            )
                        );
                        $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', $logArgs);
                    }
                    
                    if ($message->supportsHookSubscribers()) {
                        // Call form aware processing hooks
                        $hookHelper->callFormProcessHooks($form, $message, FormAwareCategory::TYPE_PROCESS_DELETE);
                    
                        // Let any ui hooks know that we have deleted the message
                        $hookHelper->callProcessHooks($message, UiHooksCategory::TYPE_PROCESS_DELETE);
                    }
                    
                    return $this->redirectToRoute($redirectRoute);
                }
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->__('Operation cancelled.'));
        
                return $this->redirectToRoute($redirectRoute);
            }
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
            'deleteForm' => $form->createView(),
            $objectType => $message
        ];
        if ($message->supportsHookSubscribers()) {
            $templateParameters['formHookTemplates'] = $formHook->getTemplates();
        }
        
        $controllerHelper = $this->get('mu_news_module.controller_helper');
        $templateParameters = $controllerHelper->processDeleteActionParameters($objectType, $templateParameters, true);
        
        // fetch and return the appropriate template
        return $this->get('mu_news_module.view_helper')->processTemplate($objectType, 'delete', $templateParameters);
    }
    
    
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request
     * @param boolean $isAdmin Whether the admin area is used or not
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    protected function handleSelectedEntriesActionInternal(
        Request $request,
        $isAdmin = false
    ) {
        $objectType = 'message';
        
        // Get parameters
        $action = $request->request->get('action');
        $items = $request->request->get('items');
        if (!is_array($items) || !count($items)) {
            return $this->redirectToRoute('munewsmodule_message_' . ($isAdmin ? 'admin' : '') . 'index');
        }
        
        $action = strtolower($action);
        
        $repository = $this->get('mu_news_module.entity_factory')->getRepository($objectType);
        $workflowHelper = $this->get('mu_news_module.workflow_helper');
        $hookHelper = $this->get('mu_news_module.hook_helper');
        $logger = $this->get('logger');
        $userName = $this->get('zikula_users_module.current_user')->get('uname');
        
        // process each item
        foreach ($items as $itemId) {
            // check if item exists, and get record instance
            $entity = $repository->selectById($itemId, false);
            if (null === $entity) {
                continue;
            }
        
            // check if $action can be applied to this entity (may depend on it's current workflow state)
            $allowedActions = $workflowHelper->getActionsForObject($entity);
            $actionIds = array_keys($allowedActions);
            if (!in_array($action, $actionIds, true)) {
                // action not allowed, skip this object
                continue;
            }
        
            if ($entity->supportsHookSubscribers()) {
                // Let any ui hooks perform additional validation actions
                $hookType = 'delete' === $action
                    ? UiHooksCategory::TYPE_VALIDATE_DELETE
                    : UiHooksCategory::TYPE_VALIDATE_EDIT
                ;
                $validationErrors = $hookHelper->callValidationHooks($entity, $hookType);
                if (count($validationErrors) > 0) {
                    foreach ($validationErrors as $message) {
                        $this->addFlash('error', $message);
                    }
                    continue;
                }
            }
        
            $success = false;
            try {
                // execute the workflow action
                $success = $workflowHelper->executeAction($entity, $action);
            } catch (Exception $exception) {
                $this->addFlash(
                    'error',
                    $this->__f(
                        'Sorry, but an error occured during the %action% action.',
                        ['%action%' => $action]
                    ) . '  ' . $exception->getMessage()
                );
                $logger->error(
                    '{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id},'
                        . ' but failed. Error details: {errorMessage}.',
                    [
                        'app' => 'MUNewsModule',
                        'user' => $userName,
                        'action' => $action,
                        'entity' => 'message',
                        'id' => $itemId,
                        'errorMessage' => $exception->getMessage()
                    ]
                );
            }
        
            if (!$success) {
                continue;
            }
        
            if ('delete' === $action) {
                $this->addFlash(
                    'status',
                    $this->__(
                        'Done! Message deleted.'
                    )
                );
                $logger->notice(
                    '{app}: User {user} deleted the {entity} with id {id}.',
                    [
                        'app' => 'MUNewsModule',
                        'user' => $userName,
                        'entity' => 'message',
                        'id' => $itemId
                    ]
                );
            } else {
                $this->addFlash(
                    'status',
                    $this->__(
                        'Done! Message updated.'
                    )
                );
                $logger->notice(
                    '{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.',
                    [
                        'app' => 'MUNewsModule',
                        'user' => $userName,
                        'action' => $action,
                        'entity' => 'message',
                        'id' => $itemId
                    ]
                );
            }
        
            if ($entity->supportsHookSubscribers()) {
                // Let any ui hooks know that we have updated or deleted an item
                $hookType = 'delete' === $action
                    ? UiHooksCategory::TYPE_PROCESS_DELETE
                    : UiHooksCategory::TYPE_PROCESS_EDIT
                ;
                $url = null;
                if ('delete' !== $action) {
                    $urlArgs = $entity->createUrlArgs();
                    $urlArgs['_locale'] = $request->getLocale();
                    $url = new RouteUrl('munewsmodule_message_display', $urlArgs);
                }
                $hookHelper->callProcessHooks($entity, $hookType, $url);
            }
        }
        
        return $this->redirectToRoute('munewsmodule_message_' . ($isAdmin ? 'admin' : '') . 'index');
    }
    
    /**
     * This method cares for a redirect within an inline frame.
     *
     * @param string $idPrefix Prefix for inline window element identifier
     * @param string $commandName Name of action to be performed (create or edit)
     * @param int $id Identifier of created message (used for activating auto completion after closing the modal window)
     *
     * @return Response
     */
    public function handleInlineRedirectAction(
        $idPrefix,
        $commandName,
        $id = 0
    )
     {
        if (empty($idPrefix)) {
            return false;
        }
        
        $formattedTitle = '';
        $searchTerm = '';
        if (!empty($id)) {
            $repository = $this->get('mu_news_module.entity_factory')->getRepository('message');
            $message = null;
            if (!is_numeric($id)) {
                $message = $repository->selectBySlug($id);
            }
            if (null === $message && is_numeric($id)) {
                $message = $repository->selectById($id);
            }
            if (null !== $message) {
                $formattedTitle = $this->get('mu_news_module.entity_display_helper')->getFormattedTitle($message);
                $searchTerm = $message->getTitle();
            }
        }
        
        $templateParameters = [
            'itemId' => $id,
            'formattedTitle' => $formattedTitle,
            'searchTerm' => $searchTerm,
            'idPrefix' => $idPrefix,
            'commandName' => $commandName
        ];
        
        return new PlainResponse(
            $this->get('twig')->render('@MUNewsModule/Message/inlineRedirectHandler.html.twig', $templateParameters)
        );
    }
    
}
