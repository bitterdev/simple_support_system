<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Events;

use Bitter\SimpleSupportSystem\Entity\TicketComment;
use Symfony\Component\EventDispatcher\GenericEvent;

class TicketCommentCreate extends GenericEvent
{
    /** @var TicketComment */
    protected $ticketComment;

    /**
     * @return TicketComment
     */
    public function getTicketComment()
    {
        return $this->ticketComment;
    }

    /**
     * @param TicketComment $ticketComment
     * @return TicketCommentCreate
     */
    public function setTicketComment(TicketComment $ticketComment)
    {
        $this->ticketComment = $ticketComment;
        return $this;
    }

}
