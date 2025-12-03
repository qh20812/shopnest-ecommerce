<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case COD = 'cod';
    case CREDIT_CARD = 'credit_card';
    case E_WALLET = 'e_wallet';
    case BANK_TRANSFER = 'bank_transfer';

    private const LABELS = [
        self::COD->value => 'Thanh toán khi nhận hàng',
        self::CREDIT_CARD->value => 'Thẻ tín dụng',
        self::E_WALLET->value => 'Ví điện tử',
        self::BANK_TRANSFER->value => 'Chuyển khoản ngân hàng',
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
