@props([
    'code'  => null,
    'size'  => 'md',
    'emoji' => null,
    'alt'   => null,
    'style' => '',
])

@php
    if (!$code) { echo $emoji ?? ''; return; }
    $px  = match((string)$size) {
        'xs'  => 18,
        'sm'  => 24,
        'md'  => 32,
        'lg'  => 48,
        'xl'  => 64,
        '2xl' => 96,
        default => (int)$size,
    };
    $h   = (int)round($px * 0.67);
    $cdn = $px <= 22 ? 'w20' : ($px <= 44 ? 'w40' : 'w80');
    $src = "https://flagcdn.com/{$cdn}/" . strtolower($code) . ".png";
    $alt = $alt ?? $code;
@endphp

<img src="{{ $src }}"
     width="{{ $px }}"
     height="{{ $h }}"
     alt="{{ $alt }}"
     loading="lazy"
     style="border-radius:3px; object-fit:cover; flex-shrink:0; display:inline-block; vertical-align:middle; {{ $style }}"
     onerror="this.style.display='none'">
