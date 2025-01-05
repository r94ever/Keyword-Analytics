<?php

namespace Qmas\KeywordAnalytics;

use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;

class CheckingMessage
{
    protected ?CheckResultType $type;

    protected ?Field $field;

    protected ?MessageId $msgId;

    protected ?Validator $validatorName;

    protected ?string $msg = '';

    protected ?array $data = [];

    /**
     * CheckingMessage constructor.
     *
     * @param CheckResultType|null $type
     * @param MessageId|null $msgId
     * @param Validator|null $validatorName
     * @param Field|null $field
     * @param string|null $msg
     * @param array|null $data
     */
    public function __construct(
        ?CheckResultType $type = null,
        ?MessageId $msgId = null,
        ?Validator $validatorName = null,
        ?Field $field = null,
        ?string $msg = '',
        ?array $data = []
    ) {
        $this->setType($type);
        $this->setField($field);
        $this->setMsgId($msgId);
        $this->setMsg($msg);
        $this->setValidatorName($validatorName);
        $this->setData($data);

        return $this;
    }

    public static function make(
        ?CheckResultType $type = null,
        ?MessageId $msgId = null,
        ?Validator $validatorName = null,
        ?Field $field = null,
        ?string $msg = null,
        ?array $data = []
    ): self
    {
        return new self(type: $type, msgId: $msgId, validatorName: $validatorName, field: $field, msg: $msg, data: $data);
    }

    public function build(): array
    {
        return [
            "type"          => $this->type->value,
            "field"         => $this->field->value,
            "msgId"         => $this->msgId->value,
            "msg"           => $this->msg,
            "validatorName" => $this->validatorName->value,
            "data"          => $this->data
        ];
    }

    public function setType(?CheckResultType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setField(?Field $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function setMsgId(?MessageId $msgId): self
    {
        $this->msgId = $msgId;

        return $this;
    }

    public function setMsg(?string $msg = ''): self
    {
        $this->msg = $msg;

        return $this;
    }

    public function setValidatorName(?Validator $validatorName): self
    {
        $this->validatorName = $validatorName;

        return $this;
    }

    public function setData(?array $data = []): self
    {
        $this->data = $data;

        return $this;
    }
}
