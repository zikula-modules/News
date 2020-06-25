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

declare(strict_types=1);

namespace MU\NewsModule\HookSubscriber\Base;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\HookBundle\Category\FilterHooksCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;

/**
 * Base class for filter hooks subscriber.
 */
abstract class AbstractMessageFilterHooksSubscriber implements HookSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getOwner(): string
    {
        return 'MUNewsModule';
    }
    
    public function getCategory(): string
    {
        return FilterHooksCategory::NAME;
    }
    
    public function getTitle(): string
    {
        return $this->translator->trans('Message filter hooks subscriber', [], 'hooks');
    }
    
    public function getAreaName(): string
    {
        return 'subscriber.munewsmodule.filter_hooks.messages';
    }

    public function getEvents(): array
    {
        return [
            FilterHooksCategory::TYPE_FILTER => 'munewsmodule.filter_hooks.messages.filter'
        ];
    }
}