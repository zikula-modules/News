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

namespace MU\NewsModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Core\CoreEvents;
use Zikula\Core\Event\ModuleStateEvent;

/**
 * Event handler base class for module installer events.
 */
abstract class AbstractInstallerListener implements EventSubscriberInterface
{
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::MODULE_INSTALL             => ['moduleInstalled', 5],
            CoreEvents::MODULE_POSTINSTALL         => ['modulePostInstalled', 5],
            CoreEvents::MODULE_UPGRADE             => ['moduleUpgraded', 5],
            CoreEvents::MODULE_ENABLE              => ['moduleEnabled', 5],
            CoreEvents::MODULE_DISABLE             => ['moduleDisabled', 5],
            CoreEvents::MODULE_REMOVE              => ['moduleRemoved', 5]
        ];
    }
    
    /**
     * Listener for the `module.install` event.
     *
     * Called after a module has been successfully installed.
     * The event allows accessing the module bundle and the extension
     * information array using `$event->getModule()` and `$event->getModInfo()`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param ModuleStateEvent $event The event instance
     */
    public function moduleInstalled(ModuleStateEvent $event)
    {
    }
    
    /**
     * Listener for the `module.postinstall` event.
     *
     * Called after a module has been installed (on reload of the extensions view).
     * The event allows accessing the module bundle and the extension
     * information array using `$event->getModule()` and `$event->getModInfo()`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param ModuleStateEvent $event The event instance
     */
    public function modulePostInstalled(ModuleStateEvent $event)
    {
    }
    
    /**
     * Listener for the `module.upgrade` event.
     *
     * Called after a module has been successfully upgraded.
     * The event allows accessing the module bundle and the extension
     * information array using `$event->getModule()` and `$event->getModInfo()`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param ModuleStateEvent $event The event instance
     */
    public function moduleUpgraded(ModuleStateEvent $event)
    {
    }
    
    /**
     * Listener for the `module.enable` event.
     *
     * Called after a module has been successfully enabled.
     * The event allows accessing the module bundle and the extension
     * information array using `$event->getModule()` and `$event->getModInfo()`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param ModuleStateEvent $event The event instance
     */
    public function moduleEnabled(ModuleStateEvent $event)
    {
    }
    
    /**
     * Listener for the `module.disable` event.
     *
     * Called after a module has been successfully disabled.
     * The event allows accessing the module bundle and the extension
     * information array using `$event->getModule()` and `$event->getModInfo()`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param ModuleStateEvent $event The event instance
     */
    public function moduleDisabled(ModuleStateEvent $event)
    {
    }
    
    /**
     * Listener for the `module.remove` event.
     *
     * Called after a module has been successfully removed.
     * The event allows accessing the module bundle and the extension
     * information array using `$event->getModule()` and `$event->getModInfo()`.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * The current request's type: `MASTER_REQUEST` or `SUB_REQUEST`.
     * If a listener should only be active for the master request,
     * be sure to check that at the beginning of your method.
     *     `if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
     *         return;
     *     }`
     *
     * The kernel instance handling the current request:
     *     `$kernel = $event->getKernel();`
     *
     * The currently handled request:
     *     `$request = $event->getRequest();`
     *
     * @param ModuleStateEvent $event The event instance
     */
    public function moduleRemoved(ModuleStateEvent $event)
    {
    }
}
