<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class GoogleDriveException extends Exception
{
    public const NOT_CONFIGURED = 'NOT_CONFIGURED';
    public const NOT_CONNECTED = 'NOT_CONNECTED';
    public const TOKEN_EXPIRED = 'TOKEN_EXPIRED';
    public const ROOT_FOLDER_MISSING = 'ROOT_FOLDER_MISSING';
    public const FOLDER_FAILED = 'FOLDER_FAILED';
    public const UPLOAD_FAILED = 'UPLOAD_FAILED';
    public const PERMISSION_FAILED = 'PERMISSION_FAILED';
    public const DELETE_FAILED = 'DELETE_FAILED';
    public const NETWORK_ERROR = 'NETWORK_ERROR';

    protected string $errorCode;
    protected string $userMessage;
    protected ?string $errorId;

    public function __construct(
        string $errorCode,
        string $userMessage,
        string $technicalMessage = '',
        ?string $errorId = null,
        int $httpStatus = 422,
        ?Throwable $previous = null
    ) {
        parent::__construct($technicalMessage ?: $userMessage, $httpStatus, $previous);
        $this->errorCode = $errorCode;
        $this->userMessage = $userMessage;
        $this->errorId = $errorId;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    public function getErrorId(): ?string
    {
        return $this->errorId;
    }

    public function getFormattedUserMessage(): string
    {
        return $this->errorId
            ? "{$this->userMessage} (Kode Error: {$this->errorId})"
            : $this->userMessage;
    }
}
