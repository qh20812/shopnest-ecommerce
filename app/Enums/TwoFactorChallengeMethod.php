<?php

namespace App\Enums;

enum TwoFactorChallengeMethod: string
{
    case AUTHENTICATOR = 'authenticator';
    case SMS = 'sms';
    case EMAIL = 'email';
    case BACKUP_CODE = 'backup_code';

    private const LABELS = [
        self::AUTHENTICATOR->value => 'Ứng dụng xác thực',
        self::SMS->value => 'Tin nhắn SMS',
        self::EMAIL->value => 'Email',
        self::BACKUP_CODE->value => 'Mã dự phòng',
    ];

    /**
     * Get the label for the enum case
     */
    public function label(): string
    {
        return self::LABELS[$this->value];
    }

    /**
     * Get all enum values with their labels
     */
    public static function options(): array
    {
        return array_map(
            fn(self $enum) => ['value' => $enum->value, 'label' => $enum->label()],
            self::cases()
        );
    }
}
