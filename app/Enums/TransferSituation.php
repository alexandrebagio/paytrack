<?php

namespace App\Enums;

enum TransferSituation: string
{
    case Pending = 'P';
    case Error = 'E';
    case Finish = 'F';
}
