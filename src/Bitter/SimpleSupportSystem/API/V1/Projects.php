<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\API\V1;

use Bitter\SimpleSupportSystem\API\V1\Transformer\ProjectTransformer;
use Bitter\SimpleSupportSystem\Entity\Project;
use Doctrine\ORM\EntityManager;
use League\Fractal\Resource\Collection;

class Projects
{
    protected $entityManager;
    protected $projectRepository;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
        $this->projectRepository = $this->entityManager->getRepository(Project::class);
    }

    public function list()
    {
        return new Collection($this->projectRepository->findAll(), new ProjectTransformer());
    }
}