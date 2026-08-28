<?php

namespace App\Enums;

enum FiscalEmissionType: int
{
    case Normal = 1;
    case FsIa = 2;
    case Scan = 3;
    case Dpec = 4;
    case FsDa = 5;
    case SvcAn = 6;
    case SvcRs = 7;
    case OfflineNfce = 9;
}
