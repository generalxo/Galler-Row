@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pages" class="mt-12 flex flex-wrap items-center justify-between gap-4">
        @if ($paginator->onFirstPage())
            <span></span>
        @else
            <x-button variant="ghost" size="sm" wire:click="previousPage('{{ $paginator->getPageName() }}')" class="underline underline-offset-4">Previous</x-button>
        @endif

        <span class="type-body">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <x-button variant="ghost" size="sm" wire:click="nextPage('{{ $paginator->getPageName() }}')" class="underline underline-offset-4">Next</x-button>
        @else
            <span></span>
        @endif
    </nav>
@endif
