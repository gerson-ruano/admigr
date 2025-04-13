<?php

return [

    'required' => 'El campo :attribute es obligatorio.',
    'numeric' => 'El campo :attribute debe ser numérico.',
    
    'distinct' => [
        'must_be_selected' => 'Se debe seleccionar al menos un campo :attribute.',
        'only_one_must_be_selected' => 'Se debe seleccionar solamente un campo :attribute.',
    ],

    'items' => [
        'product_id' => [
            'duplicate' => 'Este producto ya fue seleccionado. Por favor, elige uno diferente.',
        ],
        'quantity' => [
            'min' => 'La cantidad debe ser al menos :min.',
        ],
    ],

];
