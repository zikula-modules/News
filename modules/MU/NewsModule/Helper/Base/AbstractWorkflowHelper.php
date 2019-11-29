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

use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Workflow\Registry;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Helper\ListEntriesHelper;
use MU\NewsModule\Helper\PermissionHelper;

/**
 * Helper base class for workflow methods.
 */
abstract class AbstractWorkflowHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var Registry
     */
    protected $workflowRegistry;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var ListEntriesHelper
     */
    protected $listEntriesHelper;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        Registry $registry,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi,
        EntityFactory $entityFactory,
        ListEntriesHelper $listEntriesHelper,
        PermissionHelper $permissionHelper
    ) {
        $this->translator = $translator;
        $this->workflowRegistry = $registry;
        $this->logger = $logger;
        $this->currentUserApi = $currentUserApi;
        $this->entityFactory = $entityFactory;
        $this->listEntriesHelper = $listEntriesHelper;
        $this->permissionHelper = $permissionHelper;
    }
    
    /**
     * This method returns a list of possible object states.
     *
     * @return array List of collected state information
     */
    public function getObjectStates()
    {
        $states = [];
        $states[] = [
            'value' => 'initial',
            'text' => $this->translator->__('Initial'),
            'ui' => 'danger'
        ];
        $states[] = [
            'value' => 'waiting',
            'text' => $this->translator->__('Waiting'),
            'ui' => 'warning'
        ];
        $states[] = [
            'value' => 'approved',
            'text' => $this->translator->__('Approved'),
            'ui' => 'success'
        ];
        $states[] = [
            'value' => 'suspended',
            'text' => $this->translator->__('Suspended'),
            'ui' => 'primary'
        ];
        $states[] = [
            'value' => 'archived',
            'text' => $this->translator->__('Archived'),
            'ui' => 'info'
        ];
        $states[] = [
            'value' => 'trashed',
            'text' => $this->translator->__('Trashed'),
            'ui' => 'danger'
        ];
        $states[] = [
            'value' => 'deleted',
            'text' => $this->translator->__('Deleted'),
            'ui' => 'danger'
        ];
    
        return $states;
    }
    
    /**
     * This method returns information about a certain state.
     *
     * @param string $state The given state value
     *
     * @return array|null The corresponding state information
     */
    public function getStateInfo($state = 'initial')
    {
        $result = null;
        $stateList = $this->getObjectStates();
        foreach ($stateList as $singleState) {
            if ($singleState['value'] !== $state) {
                continue;
            }
            $result = $singleState;
            break;
        }
    
        return $result;
    }
    
    /**
     * Retrieve the available actions for a given entity object.
     *
     * @param EntityAccess $entity The given entity instance
     *
     * @return array List of available workflow actions
     */
    public function getActionsForObject(EntityAccess $entity)
    {
        $workflow = $this->workflowRegistry->get($entity);
        $wfActions = $workflow->getEnabledTransitions($entity);
        $currentState = $entity->getWorkflowState();
    
        $actions = [];
        foreach ($wfActions as $action) {
            $actionId = $action->getName();
            $actions[$actionId] = [
                'id' => $actionId,
                'title' => $this->getTitleForAction($currentState, $actionId),
                'buttonClass' => $this->getButtonClassForAction($actionId)
            ];
        }
    
        return $actions;
    }
    
    /**
     * Returns a translatable title for a certain action.
     *
     * @param string $currentState Current state of the entity
     * @param string $actionId Id of the treated action
     *
     * @return string The action title
     */
    protected function getTitleForAction($currentState, $actionId)
    {
        $title = '';
        switch ($actionId) {
            case 'submit':
                $title = $this->translator->__('Submit');
                break;
            case 'approve':
                $title = 'initial' === $currentState ? $this->translator->__('Submit and approve') : $this->translator->__('Approve');
                break;
            case 'unpublish':
                $title = $this->translator->__('Unpublish');
                break;
            case 'publish':
                $title = $this->translator->__('Publish');
                break;
            case 'archive':
                $title = $this->translator->__('Archive');
                break;
            case 'unarchive':
                $title = $this->translator->__('Unarchive');
                break;
            case 'trash':
                $title = $this->translator->__('Trash');
                break;
            case 'recover':
                $title = $this->translator->__('Recover');
                break;
            case 'delete':
                $title = $this->translator->__('Delete');
                break;
        }
    
        if ('' === $title) {
            if ('update' === $actionId) {
                $title = $this->translator->__('Update');
            } elseif ('trash' === $actionId) {
                $title = $this->translator->__('Trash');
            } elseif ('recover' === $actionId) {
                $title = $this->translator->__('Recover');
            }
        }
    
        return $title;
    }
    
    /**
     * Returns a button class for a certain action.
     *
     * @param string $actionId Id of the treated action
     *
     * @return string The button class
     */
    protected function getButtonClassForAction($actionId)
    {
        $buttonClass = '';
        switch ($actionId) {
            case 'submit':
                $buttonClass = 'success';
                break;
            case 'approve':
                $buttonClass = 'success';
                break;
            case 'unpublish':
                $buttonClass = '';
                break;
            case 'publish':
                $buttonClass = '';
                break;
            case 'archive':
                $buttonClass = '';
                break;
            case 'unarchive':
                $buttonClass = '';
                break;
            case 'trash':
                $buttonClass = '';
                break;
            case 'recover':
                $buttonClass = '';
                break;
            case 'delete':
                $buttonClass = 'danger';
                break;
        }
    
        if ('' === $buttonClass && 'update' === $actionId) {
            $buttonClass = 'success';
        }
    
        if (empty($buttonClass)) {
            $buttonClass = 'default';
        }
    
        return 'btn btn-' . $buttonClass;
    }
    
    /**
     * Executes a certain workflow action for a given entity object.
     *
     * @param EntityAccess $entity The given entity instance
     * @param string $actionId  Name of action to be executed
     * @param bool $recursive True if the function called itself
     *
     * @return bool Whether everything worked well or not
     */
    public function executeAction(EntityAccess $entity, $actionId = '', $recursive = false)
    {
        $workflow = $this->workflowRegistry->get($entity);
        if (!$workflow->can($entity, $actionId)) {
            return false;
        }
    
        // get entity manager
        $entityManager = $this->entityFactory->getEntityManager();
        $logArgs = ['app' => 'MUNewsModule', 'user' => $this->currentUserApi->get('uname')];
    
        $result = false;
        if (!$workflow->can($entity, $actionId)) {
            return $result;
        }
    
        try {
            if ('delete' === $actionId) {
                $entityManager->remove($entity);
            } else {
                $entityManager->persist($entity);
            }
            // we flush two times on purpose to avoid a hen-egg problem with workflow post-processing
            // first we flush to ensure that the entity gets an identifier
            $entityManager->flush();
            // then we apply the workflow which causes additional actions, like notifications
            $workflow->apply($entity, $actionId);
            // then we flush again to save the new workflow state of the entity
            $entityManager->flush();
    
            $result = true;
            if ('delete' === $actionId) {
                $this->logger->notice('{app}: User {user} deleted an entity.', $logArgs);
            } else {
                $this->logger->notice('{app}: User {user} updated an entity.', $logArgs);
            }
        } catch (Exception $exception) {
            if ('delete' === $actionId) {
                $this->logger->error('{app}: User {user} tried to delete an entity, but failed.', $logArgs);
            } else {
                $this->logger->error('{app}: User {user} tried to update an entity, but failed.', $logArgs);
            }
            throw new RuntimeException($exception->getMessage());
        }
    
        if (false !== $result && !$recursive) {
            $entities = $entity->getRelatedObjectsToPersist();
            foreach ($entities as $rel) {
                if ('initial' === $rel->getWorkflowState()) {
                    $this->executeAction($rel, $actionId, true);
                }
            }
        }
    
        return false !== $result;
    }
    
    /**
     * Collects amount of moderation items foreach object type.
     *
     * @return array List of collected amounts
     */
    public function collectAmountOfModerationItems()
    {
        $amounts = [];
    
    
        // check if objects are waiting for approval
        $state = 'waiting';
        $objectType = 'message';
        if ($this->permissionHelper->hasComponentPermission($objectType, ACCESS_ADD)) {
            $amount = $this->getAmountOfModerationItems($objectType, $state);
            if (0 < $amount) {
                $amounts[] = [
                    'aggregateType' => 'messagesApproval',
                    'description' => $this->translator->__('Messages pending approval'),
                    'amount' => $amount,
                    'objectType' => $objectType,
                    'state' => $state,
                    'message' => $this->translator->transChoice('One message is waiting for approval.|%count% messages are waiting for approval.', $amount, ['%count%' => $amount], 'munewsmodule')
                ];
        
                $this->logger->info('{app}: There are {amount} {entities} waiting for approval.', ['app' => 'MUNewsModule', 'amount' => $amount, 'entities' => 'messages']);
            }
        }
    
        return $amounts;
    }
    
    /**
     * Retrieves the amount of moderation items for a given object type
     * and a certain workflow state.
     *
     * @param string $objectType Name of treated object type
     * @param string $state The given state value
     *
     * @return int The affected amount of objects
     */
    public function getAmountOfModerationItems($objectType = '', $state = '')
    {
        $repository = $this->entityFactory->getRepository($objectType);
        $collectionFilterHelper = $repository->getCollectionFilterHelper();
        $repository->setCollectionFilterHelper(null);
    
        $where = 'tbl.workflowState = \'' . $state . '\'';
        $parameters = ['workflowState' => $state];
    
        $result = $repository->selectCount($where, false, $parameters);
        $repository->setCollectionFilterHelper($collectionFilterHelper);
    
        return $result;
    }
}
