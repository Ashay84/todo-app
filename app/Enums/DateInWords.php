<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class DateInWords extends Enum
{
    const TODAY = "Today";
    const THIS_WEEK = "This Week";
    const NEXT_WEEK = "Next Week";
    const OVERDUE = "Overdue";
}
