# Catálogo de Cursos - {!! $host !!}

- Base URL: {!! $baseUrl !!}
- Formato: markdown
- Total de cursos: {!! (string) $itens->count() !!}

@foreach($itens as $item)
## {!! $item['title'] !== '' ? $item['title'] : $item['id'] !!}

- ID: {!! $item['id'] !!}
- Descrição: {!! $item['description'] !== '' ? $item['description'] : 'Não informada' !!}
- Link da página: {!! $item['link'] !== '' ? $item['link'] : 'Não disponível' !!}
- Carga horária: {!! $item['workload'] !== '' ? $item['workload'] : 'Não informada' !!}
- Professor: {!! $item['teacher_name'] !== '' ? $item['teacher_name'] : 'Não informado' !!}

@foreach(($item['offers'] ?? []) as $offer)
- {!! $offer['label'] !!}: {!! $offer['price_value'] !!}
@if(!empty($offer['show_cash_line']))
- À vista ({!! strtolower($offer['label']) !!}): {!! $offer['cash_value'] !!}
@endif
- Checkout ({!! strtolower($offer['label']) !!}): {!! $offer['checkout_url'] !!}
@endforeach

@endforeach
