<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\API\V1\Transformer;

use Bitter\SimpleSupportSystem\Entity\Project;
use League\Fractal\TransformerAbstract;

class ProjectTransformer extends TransformerAbstract
{

    public function transform(Project $project)
    {
        return [
            "id" => $project->getProjectId(),
            "name" => $project->getProjectName(),
            "handle" => $project->getProjectHandle()
        ];
    }

}