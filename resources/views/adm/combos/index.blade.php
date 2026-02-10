@extends('adm.html_base')

@section('content')
<div class="container">
    <h1>Lista de Combos</h1>
    <div class="accordion" id="accordionCombos">
        @foreach($combos as $combo)
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading{{ $combo->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $combo->id }}" aria-expanded="false" aria-controls="collapse{{ $combo->id }}">
                    {{ $combo->titulo }} - {{ $combo->headline }}
                </button>
            </h2>
            <div id="collapse{{ $combo->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $combo->id }}" data-bs-parent="#accordionCombos">
                <div class="accordion-body">
                    <p><strong>ID:</strong> {{ $combo->id }}</p>
                    <p><strong>URL:</strong> {{ $combo->url }}</p>
                    
                    <h5>Cursos inclusos:</h5>
                    @if($combo->cursos->isNotEmpty())
                        <ul>
                            @foreach($combo->cursos as $curso)
                                <li class="my-1">
                                    {{ $curso->titulo }}
                                    <form action="{{ route('combo.curso.excluir', [$combo->id, $curso->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Nenhum curso adicionado.</p>
                    @endif
                    
                    <a href="{{ route('combo.editar_form', $combo->id) }}" class="btn btn-primary btn-sm">Editar</a>
                    <a href="{{ route('combo.cursos.form', $combo->id) }}" class="btn btn-secondary btn-sm">Adicionar Cursos</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
