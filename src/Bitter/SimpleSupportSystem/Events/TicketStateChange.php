<?php /** @noinspection PhpUnused */

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Events;

use Bitter\SimpleSupportSystem\Entity\Ticket;
use Symfony\Component\EventDispatcher\GenericEvent;

class TicketStateChange extends GenericEvent
{
    /** @var Ticket */
    protected $ticket;

    /**
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param Ticket $ticket
     * @return TicketCreate
     */
    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;
        return $this;
    }

}
