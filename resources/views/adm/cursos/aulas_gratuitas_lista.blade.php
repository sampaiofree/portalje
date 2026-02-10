@extends('adm.html_base')


@section('content')
<div class="row">
    <div class="col-12">
        <h1>Lista de Aulas Demonstrativas</h1>
        <a href="{{route('aulas_gratuitas_cadastrar')}}" class="btn btn-primary">Adicionar aula</a>

    <!-- Exibindo mensagens de sucesso -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

        <!-- Tabela para listar as aulas -->
        <table class="mt-5 table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título da Aula</th>
                    <th>Curso</th>
                    <th>Vídeo YouTube</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($aulas as $aula)
                    <tr>
                        <td>{{ $aula->id }}</td>
                        <td>{{ $aula->aula_titulo }}</td>
                        <td>{{ $aula->curso->titulo }}</td> <!-- Exibindo o nome do curso relacionado -->
                        <td><a href="https://www.youtube.com/watch?v={{ $aula->aula_id_youtube }}" target="_blank">Assistir</a></td>
                        <td>
                            <!-- Formulário de exclusão -->
                            <form action="{{ route('aulas_gratuitas_destroy', $aula->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('head')
   
@endsection

@section('scripts')


@endsection