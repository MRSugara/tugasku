@props(['priority'])

{{-- Badge prioritas tugas. Warna & teks diambil dari enum TaskPriority. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$priority->color()]) }}>
    {{ $priority->label() }}
</span>
