<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Entity\Search;

use Concrete\Core\Entity\Search\SavedSearch;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="`SavedProjectSearchQueries`")
 */
class SavedProjectSearch extends SavedSearch
{
    /**
    * @var integer
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(name="`id`", type="integer", nullable=true)
    */
    protected $id;
    
}
