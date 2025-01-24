<?php

namespace App\Enums;

enum LeaveRequestStatus : string {
    case REJECTED = "rejected";
    case APPROVED = "approved";
    case PENDING = "pending";
}


