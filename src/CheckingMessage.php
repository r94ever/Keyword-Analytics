<?php

namespace Qmas\KeywordAnalytics;

class CheckingMessage
{
    protected $type;

    protected $field;

    protected $msgId;

    protected $msg;

    protected $validatorName;

    protected $data;

    const ERROR_TYPE = 'error';

    const SUCCESS_TYPE = 'success';

    const WARNING_TYPE = 'warning';

    const IGNORED_TYPE = 'ignored';

    const KEYWORD_FIELD = 'keyword';

    const TITLE_FIELD = 'headline';

    const DESCRIPTION_FIELD = 'metaDescription';

    const HTML_FIELD = 'html';

    const URL_FIELD = 'url';

    const SUCCESS_MSG_ID = 'success';

    const TOO_LONG_MSG_ID = 'tooLong';

    const TOO_SHORT_MSG_ID = 'tooShort';

    const IGNORE_MSG_ID = 'ignore';

    const KEYWORD_NOT_FOUND_MSG_ID = 'keywordNotFound';

    const KEYWORD_TOO_LOW_MSG_ID = 'keywordTooLow';

    const KEYWORD_TOO_OFTEN_MSG_ID = 'keywordToOften';

    const KEYWORD_DENSITY_TOO_HIGH_MSG_ID = 'densityTooHigh';

    const KEYWORD_DENSITY_TOO_LOW_MSG_ID = 'densityTooLow';

    const NO_IMAGE_MSG_ID = 'noImagesFound';

    const TOO_FEW_IMAGES_MSG_ID = 'tooFewImage';

    const NO_LINKS_FOUND_MSG_ID = 'outboundLinks';

    const LENGTH_VALIDATOR = 'length';

    const KEYWORD_COUNT_VALIDATOR = 'keywordCount';

    const KEYWORD_DENSITY_VALIDATOR = 'keywordDensity';

    const WORD_COUNT_VALIDATOR = 'wordCount';

    const HEADING_VALIDATOR = 'heading';

    const IMAGE_COUNT_VALIDATOR = 'imageCount';

    const OUTBOUND_LINKS_VALIDATOR = 'outboundLinks';

    /**
     * CheckingMessage constructor.
     *
     * @param null $type
     * @param null $field
     * @param null $msgId
     * @param null $msg
     * @param null $validatorName
     * @param array $data
     */
    public function __construct($type = null, $field = null, $msgId = null, $msg = null, $validatorName = null, $data = [])
    {
        $this->type             = $type;
        $this->field            = $field;
        $this->msgId            = $msgId;
        $this->msg              = $msg;
        $this->validatorName    = $validatorName;
        $this->data             = $data;

        return $this;
    }

    public function build()
    {
        return [
            "type"          => $this->type,
            "field"         => $this->field,
            "msgId"         => $this->msgId,
            "msg"           => $this->msg,
            "validatorName" => $this->validatorName,
            "data"          => $this->data
        ];
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function setField(string $field)
    {
        $this->field = $field;
    }

    public function setMsgId(string $msgId)
    {
        $this->msgId = $msgId;
    }

    public function setMsg(string $msg)
    {
        $this->msg = $msg;
    }

    public function setValidatorName(string $validatorName)
    {
        $this->validatorName = $validatorName;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
}
