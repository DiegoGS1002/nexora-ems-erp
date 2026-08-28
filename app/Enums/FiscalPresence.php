<?php

namespace App\Enums;

enum FiscalPresence: int
{
    case NotApplicable = 0;
    case InPerson = 1;
    case Internet = 2;
    case TeleService = 3;
    case HomeDeliveryNfce = 4;
    case OutsideEstablishment = 5;
    case Other = 9;
}
