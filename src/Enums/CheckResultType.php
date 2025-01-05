<?php

namespace Qmas\KeywordAnalytics\Enums;

enum CheckResultType: string
{
    case ERROR = 'error';

    case SUCCESS = 'success';

    case WARNING = 'warning';

    case IGNORED = 'ignored';
}
