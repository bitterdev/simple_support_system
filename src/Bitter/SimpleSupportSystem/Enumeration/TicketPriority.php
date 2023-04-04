<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Enumeration;

abstract class TicketPriority
{
    const TICKET_STATE_TRIVIAL = 'trivial';
    const TICKET_STATE_MINOR = 'minor';
    const TICKET_STATE_MAJOR = 'major';
    const TICKET_STATE_CRITICAL = 'critical';
    const TICKET_STATE_BLOCKER = 'blocker';
}