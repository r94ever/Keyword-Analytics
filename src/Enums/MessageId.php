<?php

namespace Qmas\KeywordAnalytics\Enums;

enum MessageId: string
{
    case SUCCESS = 'success';

    case TOO_LONG = 'tooLong';

    case TOO_SHORT = 'tooShort';

    case IGNORE = 'ignore';

    case KEYWORD_NOT_FOUND = 'keywordNotFound';

    case KEYWORD_TOO_LOW = 'keywordTooLow';

    case KEYWORD_TOO_OFTEN = 'keywordToOften';

    case KEYWORD_DENSITY_TOO_HIGH = 'densityTooHigh';

    case KEYWORD_DENSITY_TOO_LOW = 'densityTooLow';

    case NO_IMAGE = 'noImagesFound';

    case TOO_FEW_IMAGES = 'tooFewImage';

    case NO_LINKS_FOUND = 'outboundLinks';
}
