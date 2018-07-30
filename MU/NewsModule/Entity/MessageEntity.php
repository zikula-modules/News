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

namespace MU\NewsModule\Entity;

use MU\NewsModule\Entity\Base\AbstractMessageEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for message entities.
 * @Gedmo\TranslationEntity(class="MU\NewsModule\Entity\MessageTranslationEntity")
 * @ORM\Entity(repositoryClass="MU\NewsModule\Entity\Repository\MessageRepository")
 * @ORM\Table(name="mu_news_message",
 *     indexes={
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 * @UniqueEntity(fields="slug", ignoreNull="false")
 */
class MessageEntity extends BaseEntity
{
/*
    protected $articleImages = [];

    public function getArticleImages() {
            return $this->articleImages;
    }

    public function setArticleImages(array $articleImages) {
            $this->articleImages = $articleImages;	
    }
*/
}
