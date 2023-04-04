<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Entity;

use Concrete\Core\Entity\User\User;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Punic\Exception;
use Punic\Exception\BadArgumentType;

/**
 * @ORM\Entity
 */
class TicketComment
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $ticketCommentId;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $author;

    /**
     * @var Ticket
     * @ORM\ManyToOne(targetEntity="\Bitter\SimpleSupportSystem\Entity\Ticket", inversedBy="comments")
     * @ORM\JoinColumn(name="ticketId", referencedColumnName="ticketId")
     */
    private $ticket;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $approved = false;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content = null;

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
     * @return int
     */
    public function getTicketCommentId()
    {
        return $this->ticketCommentId;
    }

    /**
     * @param int $ticketCommentId
     * @return TicketComment
     */
    public function setTicketCommentId(int $ticketCommentId)
    {
        $this->ticketCommentId = $ticketCommentId;
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
     * @return TicketComment
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param Ticket $ticket
     * @return TicketComment
     */
    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;
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
     * @return TicketComment
     */
    public function setContent(string $content)
    {
        $this->content = $content;
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
     * @return TicketComment
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
     * @return TicketComment
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
     * @return TicketComment
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
        return $this;
    }
}