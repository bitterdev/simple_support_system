<?php /** @noinspection PhpUnused */

/**
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project;

use Bitter\SimpleSupportSystem\Entity\Project;
use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class Menu extends DropdownMenu
{
    protected $menuAttributes = ['class' => 'ccm-popover-page-menu'];

    public function __construct(Project $project)
    {
        parent::__construct();

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_support_system/projects/edit", $project->getProjectId()),
                t('Details')
            )
        );

        $this->addItem(
            new LinkItem(
                (string)Url::to("/dashboard/simple_support_system/projects/remove", $project->getProjectId()),
                t('Remove'),
                [
                    "class" => "ccm-delete-item"
                ]
            )
        );
    }
}
