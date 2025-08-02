<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dispatch;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function show(Dispatch $dispatch)
    {
        $dispatch->load(['items.product', 'customer', 'seller']); // Asegura relaciones

        $pdf = Pdf::loadView('pdf.dispatch', ['dispatch' => $dispatch]);

        // Puedes cambiar a ->download() si prefieres
        return $pdf->stream('factura-' . $dispatch->order_number . '.pdf');
    }
}
