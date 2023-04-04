<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem;

namespace Bitter\SimpleSupportSystem;

use Bitter\SimpleSupportSystem\Entity\Project;
use Bitter\SimpleSupportSystem\Search\ItemList\Pager\Manager\ProjectListPagerManager;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Closure;
use DateTime;

class ProjectList extends ItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $isFulltextSearch = false;
    protected $autoSortColumns = ['t0.projectId', 't0.projectName', 't0.projectHandle', 't0.createdAt', 't0.updatedAt'];
    protected $permissionsChecker = -1;

    public function createQuery()
    {
        $this->query->select('t0.*')
            ->from("Project", "t0");
    }

    public function finalizeQuery(QueryBuilder $query)
    {
        return $query;
    }

    /**
     * @param string $keywords
     */
    public function filterByKeywords($keywords)
    {
        $this->query->andWhere('(t0.`projectId` LIKE :keywords OR t0.`projectName` LIKE :keywords OR t0.`projectHandle` LIKE :keywords)');
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    /**
     * @param string $projectName
     */
    public function filterByProjectName($projectName)
    {
        $this->query->andWhere('t0.`projectName` LIKE :projectName');
        $this->query->setParameter('projectName', '%' . $projectName . '%');
    }

    /**
     * @param string $projectHandle
     */
    public function filterByProjectHandle($projectHandle)
    {
        $this->query->andWhere('t0.`projectHandle` LIKE :projectHandle');
        $this->query->setParameter('projectHandle', '%' . $projectHandle . '%');
    }

    /**
     * @param DateTime $date
     * @param string $comparison
     */
    public function filterByCreatedAt($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('t0.createdAt', $comparison, $this->query->createNamedParameter($date)));
    }

    /**
     * @param DateTime $date
     * @param string $comparison
     */
    public function filterByUpdatedAt($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison('t0.updatedAt', $comparison, $this->query->createNamedParameter($date)));
    }

    /**
     * @param array $queryRow
     * @return Project
     */
    public function getResult($queryRow)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $entityManager->getRepository(Project::class)->findOneBy(["projectId" => $queryRow["projectId"]]);
    }

    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t0.projectId)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
        }

        return -1; // unknown
    }

    public function getPagerManager()
    {
        return new ProjectListPagerManager($this);
    }

    public function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }

    public function getPaginationAdapter()
    {
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct t0.projectId)')
                ->setMaxResults(1);
        });
    }

    public function checkPermissions($mixed)
    {
        if (isset($this->permissionsChecker)) {
            if ($this->permissionsChecker === -1) {
                return true;
            }

            /** @noinspection PhpParamsInspection */
            return call_user_func_array($this->permissionsChecker, [$mixed]);
        }

        return true;
    }

    public function setPermissionsChecker(Closure $checker = null)
    {
        $this->permissionsChecker = $checker;
    }

    public function ignorePermissions()
    {
        $this->permissionsChecker = -1;
    }

    public function getPermissionsChecker()
    {
        return $this->permissionsChecker;
    }

    public function enablePermissions()
    {
        unset($this->permissionsChecker);
    }

    public function isFulltextSearch()
    {
        return $this->isFulltextSearch;
    }
}
