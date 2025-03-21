<?php

return [
    'navigation' => [
        'groups' => [
            'administration' => 'Administración',
            'operations' => 'Operaciones',
        ],
    ],

    'resources' => [
        'labels' => [
            'loans' => 'Préstamos',
            'clients' => 'Clientes',
            'payments' => 'Pagos',
            'payment_schedules' => 'Calendario de Pagos',
        ],
    ],

    'widgets' => [
        'stats' => [
            'active_loans' => 'Préstamos Activos',
            'pending_payments' => 'Pagos Pendientes',
            'defaulted_loans' => 'Préstamos en Mora',
            'total_portfolio' => 'Cartera Total',
        ],
    ],

    'common' => [
        'actions' => [
            'create' => 'Crear',
            'edit' => 'Editar',
            'delete' => 'Eliminar',
            'save' => 'Guardar',
            'cancel' => 'Cancelar',
            'confirm' => 'Confirmar',
            'back' => 'Volver',
        ],
        
        'messages' => [
            'created' => 'Registro creado exitosamente',
            'updated' => 'Registro actualizado exitosamente',
            'deleted' => 'Registro eliminado exitosamente',
        ],
    ],
];
