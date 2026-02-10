<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dados_portal;

class Alunos extends Controller
{
   function suporte_alunos(){
    $telefones = Dados_portal::pluck('telefone_suporte_alunos');
    $telefones = $telefones->toArray();
    $telefone = $telefones[0];
   
    return redirect("https://api.whatsapp.com/send/?phone=$telefone&text=Olá, sou aluno e preciso de suporte");
   }
}
