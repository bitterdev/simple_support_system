<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Enumeration;

abstract class TicketState
{
    const TICKET_STATE_NEW = 'new';
    const TICKET_STATE_OPEN = 'open';
    const TICKET_STATE_ON_HOLD = 'on_hold';
    const TICKET_STATE_RESOLVED = 'resolved';
    const TICKET_STATE_DUPLICATE = 'duplicate';
    const TICKET_STATE_INVALID = 'invalid';
    const TICKET_STATE_WONT_FIX = 'wont_fix';
    const TICKET_STATE_CLOSED = 'closed';
}