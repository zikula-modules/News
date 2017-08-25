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

namespace MU\NewsModule\HookProvider\Base;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\FormAwareHook\FormAwareHook;
use Zikula\Bundle\HookBundle\FormAwareHook\FormAwareResponse;
use Zikula\Bundle\HookBundle\HookProviderInterface;
use Zikula\Bundle\HookBundle\ServiceIdTrait;
use Zikula\Common\Translator\TranslatorInterface;
use MU\NewsModule\Form\Type\Hook\DeleteMessageType;
use MU\NewsModule\Form\Type\Hook\EditMessageType;

/**
 * Base class for form aware hook provider.
 */
abstract class AbstractMessageFormAwareHookProvider implements HookProviderInterface
{
    use ServiceIdTrait;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * MessageFormAwareHookProvider constructor.
     *
     * @param TranslatorInterface  $translator
     * @param SessionInterface     $session
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        TranslatorInterface $translator,
        SessionInterface $session,
        FormFactoryInterface $formFactory
    ) {
        $this->translator = $translator;
        $this->session = $session;
        $this->formFactory = $formFactory;
    }

    /**
     * @inheritDoc
     */
    public function getOwner()
    {
        return 'MUNewsModule';
    }
    
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return FormAwareCategory::NAME;
    }
    
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->translator->__('Message form aware provider');
    }

    /**
     * @inheritDoc
     */
    public function getProviderTypes()
    {
        return [
            FormAwareCategory::TYPE_EDIT => 'edit',
            FormAwareCategory::TYPE_PROCESS_EDIT => 'processEdit',
            FormAwareCategory::TYPE_DELETE => 'delete',
            FormAwareCategory::TYPE_PROCESS_DELETE => 'processDelete'
        ];
    }

    /**
     * Provide the inner editing form.
     *
     * @param FormAwareHook $hook
     */
    public function edit(FormAwareHook $hook)
    {
        $innerForm = $this->formFactory->create(EditMessageType::class, null, [
            'auto_initialize' => false,
            'mapped' => false
        ]);
        $hook
            ->formAdd($innerForm)
            ->addTemplate('@MUNewsModule/Hook/editMessageForm.html.twig')
        ;
    }

    /**
     * Process the inner editing form.
     *
     * @param FormAwareResponse $hook
     */
    public function processEdit(FormAwareResponse $hook)
    {
        $innerForm = $hook->getFormData('munewsmodule_hook_editmessageform');
        $dummyOutput = $innerForm['dummyName'] . ' (Option ' . $innerForm['dummyChoice'] . ')';
        $this->session->getFlashBag()->add('success', sprintf('The MessageFormAwareHookProvider edit form was processed and the answer was %s', $dummyOutput));
    }

    /**
     * Provide the inner deletion form.
     *
     * @param FormAwareHook $hook
     */
    public function delete(FormAwareHook $hook)
    {
        $innerForm = $this->formFactory->create(DeleteMessageType::class, null, [
            'auto_initialize' => false,
            'mapped' => false
        ]);
        $hook
            ->formAdd($innerForm)
            ->addTemplate('@MUNewsModule/Hook/deleteMessageForm.html.twig')
        ;
    }

    /**
     * Process the inner deletion form.
     *
     * @param FormAwareResponse $hook
     */
    public function processDelete(FormAwareResponse $hook)
    {
        $innerForm = $hook->getFormData('munewsmodule_hook_deletemessageform');
        $dummyOutput = $innerForm['dummyName'] . ' (Option ' . $innerForm['dummyChoice'] . ')';
        $this->session->getFlashBag()->add('success', sprintf('The MessageFormAwareHookProvider delete form was processed and the answer was %s', $dummyOutput));
    }
}