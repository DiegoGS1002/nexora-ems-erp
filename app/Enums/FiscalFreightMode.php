<?php

namespace App\Enums;

enum FiscalFreightMode: int
{
    case Sender = 0;
    case Recipient = 1;
    case ThirdParty = 2;
    case SenderOwn = 3;
    case RecipientOwn = 4;
    case None = 9;
}
