@props(['label', 'field' => null, 'sortBy' => null, 'sortDir' => 'asc'])

@if ($field)
    @php
        $isActive = $sortBy === $field;
        $nextDir = $isActive && $sortDir === 'asc' ? 'desc' : 'asc';
        $url = request()->fullUrlWithQuery(['sort_by' => $field, 'sort_dir' => $nextDir, 'page' => null]);
    @endphp
    <th style="white-space:nowrap;">
        <a href="{{ $url }}" class="text-white d-flex align-items-center justify-content-between" style="text-decoration:none;">
            <span>{{ $label }}</span>
            <i class="fas {{ $isActive ? ($sortDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-muted' }} ml-1"></i>
        </a>
    </th>
@else
    <th>{{ $label }}</th>
@endif
