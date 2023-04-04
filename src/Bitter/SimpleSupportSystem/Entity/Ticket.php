<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Entity;

use Bitter\SimpleSupportSystem\Enumeration\TicketPriority;
use Bitter\SimpleSupportSystem\Enumeration\TicketState;
use Bitter\SimpleSupportSystem\Enumeration\TicketType;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Punic\Exception;
use Punic\Exception\BadArgumentType;

/**
 * @ORM\Entity
 */
class Ticket
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $ticketId = null;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, length=9)
     */
    protected $ticketState = TicketState::TICKET_STATE_NEW;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, length=11)
     */
    protected $ticketType = TicketType::TICKET_TYPE_BUG;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, length=8)
     */
    protected $ticketPriority = TicketPriority::TICKET_STATE_TRIVIAL;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User", inversedBy="alerts")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $author = null;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email = null;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $approved = false;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title = null;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content = null;

    /**
     * @var ArrayCollection|File[]
     * @ORM\ManyToMany(targetEntity="\Concrete\Core\Entity\File\File", cascade={"persist"})
     * @ORM\JoinTable(name="TicketAttachment",
     *   joinColumns={@ORM\JoinColumn(name="ticketId", referencedColumnName="ticketId")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="fID", referencedColumnName="fID")}
     * )
     */
    protected $attachments = null;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="\Bitter\SimpleSupportSystem\Entity\Project", inversedBy="tickets")
     * @ORM\JoinColumn(name="projectId", referencedColumnName="projectId")
     */
    private $project;

    /**
     * @var ArrayCollection|TicketComment[]
     * @ORM\OneToMany(targetEntity="\Bitter\SimpleSupportSystem\Entity\TicketComment", mappedBy="ticket", orphanRemoval=true)
     */
    protected $comments;

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

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTicketId()
    {
        return $this->ticketId;
    }

    /**
     * @param int $ticketId
     * @return Ticket
     */
    public function setTicketId(int $ticketId)
    {
        $this->ticketId = $ticketId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTicketState()
    {
        return $this->ticketState;
    }

    /**
     * @return string
     */
    public function getTicketStateDisplayValue()
    {
        $ticketStates = [
            TicketState::TICKET_STATE_NEW => t("New"),
            TicketState::TICKET_STATE_OPEN => t("Open"),
            TicketState::TICKET_STATE_ON_HOLD => t("On Hold"),
            TicketState::TICKET_STATE_RESOLVED => t("Resolved"),
            TicketState::TICKET_STATE_DUPLICATE => t("Duplicate"),
            TicketState::TICKET_STATE_INVALID => t("Invalid"),
            TicketState::TICKET_STATE_WONT_FIX => t("Won't fixed"),
            TicketState::TICKET_STATE_CLOSED => t("Closed"),
        ];

        return $ticketStates[$this->ticketState];
    }

    /**
     * @param string $ticketState
     * @return Ticket
     */
    public function setTicketState(string $ticketState)
    {
        $this->ticketState = $ticketState;
        return $this;
    }

    /**
     * @return string
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * @return string
     */
    public function getTicketTypeDisplayValue()
    {
        $ticketTypes = [
            TicketType::TICKET_TYPE_BUG => t("Bug"),
            TicketType::TICKET_TYPE_ENHANCEMENT => t("Enhancement"),
            TicketType::TICKET_TYPE_PROPOSAL => t("Proposal"),
            TicketType::TICKET_TYPE_TASK => t("Task")
        ];

        return $ticketTypes[$this->ticketType];
    }

    /**
     * @param string $ticketType
     * @return Ticket
     */
    public function setTicketType(string $ticketType)
    {
        $this->ticketType = $ticketType;
        return $this;
    }

    /**
     * @return string
     */
    public function getTicketPriority()
    {
        return $this->ticketPriority;
    }

    /**
     * @return string
     */
    public function getTicketPriorityDisplayValue()
    {
        $ticketPriorities = [
            TicketPriority::TICKET_STATE_TRIVIAL => t("Trivial"),
            TicketPriority::TICKET_STATE_MINOR => t("Minor"),
            TicketPriority::TICKET_STATE_MAJOR => t("Major"),
            TicketPriority::TICKET_STATE_CRITICAL => t("Critical"),
            TicketPriority::TICKET_STATE_BLOCKER => t("Blocker")
        ];

        return $ticketPriorities[$this->ticketPriority];
    }

    /**
     * @param string $ticketPriority
     * @return Ticket
     */
    public function setTicketPriority(string $ticketPriority)
    {
        $this->ticketPriority = $ticketPriority;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getAuthorDisplayValue()
    {
        if ($this->author instanceof User) {
            return $this->author->getUserName();
        } else {
            return t("Former User");
        }
    }

    /**
     * @param User $author
     * @return Ticket
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Ticket
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Ticket
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Ticket
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return File[]|ArrayCollection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param File[]|ArrayCollection $attachments
     * @return Ticket
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Project $project
     * @return Ticket
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return TicketComment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return TicketComment[]|object[]
     */
    public function getApprovedComments()
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(TicketComment::class)->findBy(["ticket" => $this, "approved" => true]);
    }

    /**
     * @param TicketComment[]|ArrayCollection $comments
     * @return Ticket
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
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
     * @return Ticket
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

    /**
     * @param DateTime $updatedAt
     * @return Ticket
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function hasPermissions()
    {
        $user = new \Concrete\Core\User\User();

        if ($user->isSuperUser()) {
            return true;
        } else if ($this->getAuthor() instanceof User) {
            if ($user->isRegistered()) {
                return $user->getUserID() == $this->author->getUserID();
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     * @return Ticket
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
        return $this;
    }

}