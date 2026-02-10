@extends('adm.html_base')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-4">Encurtador de Links</h4>
                <a class="btn btn-secondary" href="{{route('encurtar_link')}}">Novo link</a>
            </div>
        </div>
        
        <table id="table_link_encurtado" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>Link encurtado</th>
                    <th>Link de destino</th>
                    <th>Número de Cliques</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($links as $link)
                <tr>
                    <td><a target="_blank" href="{{"$link->dominio"}}">{{"$link->dominio"}}</a></td>
                    <td><a target="_blank" href="{{$link->url_longa}}">{{$link->url_longa}}</a></td>
                    <td>{{$link->click_count}}</td>
                    <td>
                        <a href="{{route('encurtar_link_editar_mostrar', ['id'=> $link->id])}}" class="action-icon"> <i class="mdi mdi-pencil"></i></a>
                        <a href="{{route('encurtar_link_excluir', ['id'=> $link->id])}}" class="action-icon"> <i class="mdi mdi-delete"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
                                                        
    </div>
</div>
@section('head')

    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
   
  
@endsection
@endsection

@section('scripts')
<script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
    
    <script src="{{asset('Hyper_v5.4/Admin/dist/saas/assets/vendor/datatables.net-select/js/dataTables.select.min.js')}}"></script>
<script>
    $('#table_link_encurtado').DataTable( {
        paging: false,
        order: false,
                
    } );
</script>
@endsection