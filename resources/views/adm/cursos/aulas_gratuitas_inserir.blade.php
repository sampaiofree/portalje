@extends('adm.html_base')


@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('aulas_gratuitas_cadastrar_post') }}" method="POST">
            @csrf
        
            <div class="form-group">
                <label for="id_curso">Curso</label>
                <select name="id_curso" id="id_curso" class="form-control">
                    <option value="">Selecione um curso</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id }}" {{ old('id_curso') == $curso->id ? 'selected' : '' }}>
                            {{ $curso->titulo }} <!-- Supondo que a tabela "curso" tem um campo "nome" -->
                        </option>
                    @endforeach
                </select>
                @error('id_curso')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        
            <div class="mt-3 form-group">
                <label for="aula_titulo">Título da Aula</label>
                <input type="text" name="aula_titulo" id="aula_titulo" class="form-control" value="{{ old('aula_titulo') }}">
                @error('aula_titulo')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        
            <div class="mt-3 form-group">
                <label for="aula_id_youtube">ID do Vídeo no YouTube</label>
                <input type="text" name="aula_id_youtube" id="aula_id_youtube" class="form-control" value="{{ old('aula_id_youtube') }}">
                @error('aula_id_youtube')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        
            <button type="submit" class="mt-3 btn btn-primary">Criar Aula</button>
        </form>
    </div>
</div>

@endsection

@section('head')
   
@endsection

@section('scripts')


@endsection