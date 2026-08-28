<?php

namespace App\Enums;

enum NFeTransmissionState: string
{
    case Authorized = 'authorized';
    case Rejected = 'rejected';
    case Denied = 'denied';
    case Processing = 'processing';
    case Indeterminate = 'indeterminate';
    case Failed = 'failed';
}
