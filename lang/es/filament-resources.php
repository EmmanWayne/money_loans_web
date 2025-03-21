<?php

return [
    'loan' => [
        'single' => 'Préstamo',
        'plural' => 'Préstamos',
        'navigation_label' => 'Préstamos',
        
        'fields' => [
            'client_id' => 'Cliente',
            'amount' => 'Monto',
            'interest_rate' => 'Tasa de Interés',
            'term_months' => 'Plazo (Meses)',
            'payment_frequency' => 'Frecuencia de Pago',
            'status' => 'Estado',
            'total_amount' => 'Monto Total',
            'monthly_payment' => 'Pago Mensual',
            'approved_at' => 'Fecha de Aprobación',
            'rejection_reason' => 'Motivo de Rechazo',
        ],

        'status_options' => [
            'PENDING' => 'Pendiente',
            'APPROVED' => 'Aprobado',
            'REJECTED' => 'Rechazado',
            'ACTIVE' => 'Activo',
            'COMPLETED' => 'Completado',
            'DEFAULTED' => 'En Mora',
        ],

        'frequency_options' => [
            'WEEKLY' => 'Semanal',
            'BIWEEKLY' => 'Quincenal',
            'MONTHLY' => 'Mensual',
        ],
    ],

    'payment' => [
        'single' => 'Pago',
        'plural' => 'Pagos',
        'navigation_label' => 'Pagos',
        
        'fields' => [
            'loan_id' => 'Préstamo',
            'amount' => 'Monto',
            'payment_method' => 'Método de Pago',
            'reference_number' => 'Número de Referencia',
            'payment_date' => 'Fecha de Pago',
            'notes' => 'Notas',
        ],

        'payment_methods' => [
            'CASH' => 'Efectivo',
            'TRANSFER' => 'Transferencia',
            'CARD' => 'Tarjeta',
            'OTHER' => 'Otro',
        ],
    ],
];
