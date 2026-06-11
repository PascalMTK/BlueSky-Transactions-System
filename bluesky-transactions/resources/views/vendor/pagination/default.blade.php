@if ($paginator->hasPages())
<nav style="display:flex; justify-content:center; margin-top:16px; gap:4px; flex-wrap:wrap;">
    @if ($paginator->onFirstPage())
        <span style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; color:#94A3B8; background:#F8FAFC; border:1.5px solid #E2E8F0; cursor:not-allowed;">← Précédent</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; color:#0284C7; background:white; border:1.5px solid #E2E8F0; text-decoration:none;">← Précédent</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:8px 6px; font-size:13px; color:#94A3B8;">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:700; color:white; background:#0284C7; border:1.5px solid #0284C7;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; color:#0284C7; background:white; border:1.5px solid #E2E8F0; text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; color:#0284C7; background:white; border:1.5px solid #E2E8F0; text-decoration:none;">Suivant →</a>
    @else
        <span style="padding:8px 14px; border-radius:8px; font-size:13px; font-weight:600; color:#94A3B8; background:#F8FAFC; border:1.5px solid #E2E8F0; cursor:not-allowed;">Suivant →</span>
    @endif
</nav>
@endif
