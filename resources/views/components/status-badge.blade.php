@props(['status'])

{{-- Badge status tugas. Warna & teks diambil dari enum TaskStatus. --}}
<span data-status-badge {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$status->color()]) }}>
    {{ $status->label() }}
</span>
