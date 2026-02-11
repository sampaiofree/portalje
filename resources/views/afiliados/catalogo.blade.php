<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>Catalogo de Cursos - {{ $host }}</title>
        <link>{{ $baseUrl }}</link>
        <description>Feed XML para catalogo Meta Ads</description>
@foreach($itens as $item)
        <item>
            <g:id>{{ $item['id'] }}</g:id>
            <g:title><![CDATA[{{ $item['title'] }}]]></g:title>
            <g:description><![CDATA[{{ $item['description'] }}]]></g:description>
            <g:availability>{{ $item['availability'] }}</g:availability>
            <g:condition>{{ $item['condition'] }}</g:condition>
            <g:price>{{ $item['price'] }}</g:price>
            <g:link>{{ $item['link'] }}</g:link>
            <g:image_link>{{ $item['image_link'] }}</g:image_link>
            <g:brand>{{ $item['brand'] }}</g:brand>
        </item>
@endforeach
    </channel>
</rss>

