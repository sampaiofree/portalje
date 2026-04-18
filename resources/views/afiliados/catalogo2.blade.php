# Catálogo de Cursos - {!! $host !!}

- Base URL: {!! $baseUrl !!}
- Formato: markdown
- Total de cursos: {!! (string) $itens->count() !!}

@foreach($itens as $item)
## {!! $item['title'] !== '' ? $item['title'] : $item['id'] !!}

- ID: {!! $item['id'] !!}
- Descrição: {!! $item['description'] !== '' ? $item['description'] : 'Não informada' !!}
- Disponibilidade: {!! $item['availability'] !!}
- Condição: {!! $item['condition'] !!}
- Preço: {!! $item['price'] !!}
- Link da página: {!! $item['link'] !== '' ? $item['link'] : 'Não disponível' !!}
- Link do checkout: {!! $item['checkout_link'] !== '' ? $item['checkout_link'] : 'Não disponível' !!}
- Carga horária: {!! $item['workload'] !== '' ? $item['workload'] : 'Não informada' !!}
- Professor: {!! $item['teacher_name'] !== '' ? $item['teacher_name'] : 'Não informado' !!}
- Imagem: {!! $item['image_link'] !== '' ? $item['image_link'] : 'Sem imagem' !!}
- Marca: {!! $item['brand'] !!}

@endforeach
