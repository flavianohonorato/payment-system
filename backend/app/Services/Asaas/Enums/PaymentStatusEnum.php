<?php

namespace App\Services\Asaas\Enums;

enum PaymentStatusEnum: string
{
    case PENDING    = 'PENDING';
    case CONFIRMED  = 'CONFIRMED';
    case RECEIVED   = 'RECEIVED';
    case OVERDUE    = 'OVERDUE';
    case REFUNDED   = 'REFUNDED';
    case CANCELED   = 'CANCELED';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::PENDING   => 'Pendente',
            self::CONFIRMED => 'Confirmado',
            self::RECEIVED  => 'Recebido',
            self::OVERDUE   => 'Atrasado',
            self::REFUNDED  => 'Reembolsado',
            self::CANCELED  => 'Cancelado',
        };
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return match($this) {
            self::PENDING   => 'yellow',
            self::CONFIRMED => 'blue',
            self::RECEIVED  => 'green',
            self::OVERDUE   => 'red',
            self::REFUNDED  => 'orange',
            self::CANCELED  => 'gray',
        };
    }
}
