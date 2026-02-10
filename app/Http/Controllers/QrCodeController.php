<?php

// QrCodeController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function index()
    {
        return view('scan'); // Sua view de escaneamento
    }

    public function processScan(Request $request)
    {
        $data = $request->input('qr_code_data');
        // Processar os dados do QR code, por exemplo, salvar no banco, etc.
        return response()->json(['success' => true]);
    }
}
