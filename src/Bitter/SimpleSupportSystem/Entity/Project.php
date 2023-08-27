<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Entity;

use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Punic\Exception;
use Punic\Exception\BadArgumentType;

/**
 * @ORM\Entity
 */
class Project
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $projectId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $projectName = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $projectHandle = '';

    /**
     * @var ArrayCollection|Ticket[]
     * @ORM\OneToMany(targetEntity="\Bitter\SimpleSupportSystem\Entity\Ticket", mappedBy="project", orphanRemoval=true)
     */
    protected $tickets;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt = null;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID", onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\Site\Site|null
     */
    protected $site = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     * @return Project
     */
    public function setProjectId(int $projectId)
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * @param string $projectName
     * @return Project
     */
    public function setProjectName(string $projectName)
    {
        $this->projectName = $projectName;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjectHandle()
    {
        return $this->projectHandle;
    }

    /**
     * @param string $projectHandle
     * @return Project
     */
    public function setProjectHandle(string $projectHandle)
    {
        $this->projectHandle = $projectHandle;
        return $this;
    }

    /**
     * @return Ticket[]|ArrayCollection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * @param Ticket[]|ArrayCollection $tickets
     * @return Project
     */
    public function setTickets($tickets)
    {
        $this->tickets = $tickets;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return Project
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return Project
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAtDisplayValue()
    {
        $app = Application::getFacadeApplication();
        /** @var Date $date */
        $date = $app->make(Date::class);
        try {
            return $date->formatDateTime($this->updatedAt);
        } catch (BadArgumentType $e) {
            return t("Invalid Date");
        } catch (Exception $e) {
            return t("Invalid Date");
        }
    }

    /**
     * @return \Concrete\Core\Entity\Site\Site|null
     */
    public function getSite(): ?\Concrete\Core\Entity\Site\Site
    {
        return $this->site;
    }

    /**
     * @param \Concrete\Core\Entity\Site\Site|null $site
     * @return Project
     */
    public function setSite(?\Concrete\Core\Entity\Site\Site $site): Project
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAtDisplayValue()
    {
        $app = Application::getFacadeApplication();
        /** @var Date $date */
        $date = $app->make(Date::class);
        try {
            return $date->formatDateTime($this->createdAt);
        } catch (BadArgumentType $e) {
            return t("Invalid Date");
        } catch (Exception $e) {
            return t("Invalid Date");
        }
    }
}