<?php

namespace App\Services\Asaas\Enums;

enum BillingTypeEnum: string
{
    case BOLETO         = 'BOLETO';
    case CREDIT_CARD    = 'CREDIT_CARD';
    case PIX            = 'PIX';
    case BANK_TRANSFER  = 'BANK_TRANSFER';
    case DEPOSIT        = 'DEPOSIT';
    case UNDEFINED      = 'UNDEFINED';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::BOLETO        => 'Boleto Bancário',
            self::CREDIT_CARD   => 'Cartão de Crédito',
            self::PIX           => 'PIX',
            self::BANK_TRANSFER => 'Transferência Bancária',
            self::DEPOSIT       => 'Depósito',
            self::UNDEFINED     => 'Indefinido',
        };
    }
}
