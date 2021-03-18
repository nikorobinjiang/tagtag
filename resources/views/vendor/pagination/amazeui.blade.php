<div class="am-fl">
    <ul class="am-pagination">
        <li class="am-disabled"><span>每页{{ $paginator->perPage() }}条/共{{ $paginator->total() }}条</span></li>
    </ul>
</div>
<div class="am-fr">
    <ul class="am-pagination">
        <li class="am-disabled"><span>第{{ $paginator->currentPage() }}页/共{{ $paginator->lastPage() }}页</span></li>
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="am-disabled"><span>&laquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="am-disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="am-active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="am-disabled"><span>&raquo;</span></li>
        @endif
    </ul>
</div>
