<?php

declare(strict_types=1);

namespace Rechtlogisch\Evatr\Enum;

use Rechtlogisch\Evatr\StatusMessages;

enum Status: string
{
    case EVATR_0000 = 'evatr-0000';
    case EVATR_0001 = 'evatr-0001';
    case EVATR_0002 = 'evatr-0002';
    case EVATR_0003 = 'evatr-0003';
    case EVATR_0004 = 'evatr-0004';
    case EVATR_0005 = 'evatr-0005';
    case EVATR_0006 = 'evatr-0006';
    case EVATR_0007 = 'evatr-0007';
    case EVATR_0008 = 'evatr-0008';
    case EVATR_0011 = 'evatr-0011';
    case EVATR_0012 = 'evatr-0012';
    case EVATR_0013 = 'evatr-0013';
    case EVATR_1001 = 'evatr-1001';
    case EVATR_1002 = 'evatr-1002';
    case EVATR_1003 = 'evatr-1003';
    case EVATR_1004 = 'evatr-1004';
    case EVATR_2001 = 'evatr-2001';
    case EVATR_2002 = 'evatr-2002';
    case EVATR_2003 = 'evatr-2003';
    case EVATR_2004 = 'evatr-2004';
    case EVATR_2005 = 'evatr-2005';
    case EVATR_2006 = 'evatr-2006';
    case EVATR_2007 = 'evatr-2007';
    case EVATR_2008 = 'evatr-2008';
    case EVATR_2011 = 'evatr-2011';
    case EVATR_3011 = 'evatr-3011';

    public function description(): string
    {
        return StatusMessages::messageFor($this->value) ?? $this->value;
    }
}
