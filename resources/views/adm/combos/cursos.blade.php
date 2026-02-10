@extends('adm.html_base')

@section('content')
<div class="container">
    <h1>Adicionar Cursos ao Combo: {{ $combo->titulo }}</h1>
    <form action="{{ route('combo.cursos.salvar', $combo->id) }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Selecionar</th>
                    <th>ID</th>
                    <th>Nome do Curso</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cursos as $curso)
                <tr>
                    <td>
                        <input type="checkbox" name="cursos[]" value="{{ $curso->id }}"
                        {{ in_array($curso->id, $selected) ? 'checked' : '' }}>
                    </td>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->titulo }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Salvar Cursos</button>
    </form>
</div>
@endsection
