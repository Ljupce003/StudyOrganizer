@props(['text' => ''])

<div {{ $attributes->merge(['class' => 'prose max-w-none']) }}>
    {!! $html !!}
</div>
