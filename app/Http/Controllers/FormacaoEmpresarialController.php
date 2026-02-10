<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormacaoEmpresarialController extends Controller
{
    /**
     * Handle the incoming request and store form data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function form(Request $request)
    {        
        $prepopulado = "&name=$request->nome&email=$request->email&phonenumber=$request->telefone";

        if($request->preco=='2'){
            return redirect('https://pay.hotmart.com/N92504101N?off=4bjw0p0q&checkoutMode=10'.$prepopulado);
        }elseif($request->preco=='3'){
            return redirect('https://pay.hotmart.com/N92504101N?off=z8eaiqou&checkoutMode=10'.$prepopulado);
        }elseif($request->preco=='4'){
            return redirect('https://pay.hotmart.com/N92504101N?off=lc34kpu7&checkoutMode=10'.$prepopulado);
        }elseif($request->preco=='5'){
            return redirect('https://pay.hotmart.com/N92504101N?off=duw5c7f6&checkoutMode=10'.$prepopulado);
        }else{
            return redirect('https://pay.hotmart.com/N92504101N?off=fs8crkzx&checkoutMode=10'.$prepopulado);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'success' => true,
            'message' => 'Dados enviados com sucesso!',
        ]);
    }
}
