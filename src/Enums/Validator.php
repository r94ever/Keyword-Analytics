<?php

namespace Qmas\KeywordAnalytics\Enums;

enum Validator: string
{
    case LENGTH = 'length';

    case KEYWORD_COUNT = 'keywordCount';

    case KEYWORD_DENSITY = 'keywordDensity';

    case WORD_COUNT = 'wordCount';

    case HEADING = 'heading';

    case IMAGE_COUNT = 'imageCount';

    case OUTBOUND_LINKS = 'outboundLinks';
}