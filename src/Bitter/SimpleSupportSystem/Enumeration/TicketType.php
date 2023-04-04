<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Enumeration;

abstract class TicketType
{
    const TICKET_TYPE_BUG = 'bug';
    const TICKET_TYPE_ENHANCEMENT = 'enhancement';
    const TICKET_TYPE_PROPOSAL = 'proposal';
    const TICKET_TYPE_TASK = 'task';
}