<?php

namespace App\Enums;

enum RecipientManifestationType: int
{
    case OperationConfirmation = 210200;
    case OperationAcknowledgement = 210210;
    case OperationUnknown = 210220;
    case OperationNotPerformed = 210240;
}
