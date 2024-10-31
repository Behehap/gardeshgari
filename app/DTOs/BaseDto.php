<?php

namespace App\DTOs;

class BaseDto implements \JsonSerializable
{


    public BaseDtoStatusEnum|null $status;
    public string|null $message;
    public array|string|\JsonSerializable|\stdClass|null $data;

    /**
     * @param BaseDtoStatusEnum|null $status
     * @param string|null $message
     * @param array|\JsonSerializable|\stdClass|string|null $data
     */
    public function __construct(?BaseDtoStatusEnum $status = null, ?string $message = null, array|string|\stdClass|\JsonSerializable|null $data = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return [
            'status' => $this->status->value,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
}

enum BaseDtoStatusEnum: string
{
    case OK = 'SUCCESS';
    case ERROR = 'ERROR';
}
