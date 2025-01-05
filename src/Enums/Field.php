<?php

namespace Qmas\KeywordAnalytics\Enums;

enum Field: string
{
    case KEYWORD = 'keyword';

    case TITLE = 'headline';

    case DESCRIPTION = 'metaDescription';

    case HTML = 'html';

    case URL = 'url';
}
