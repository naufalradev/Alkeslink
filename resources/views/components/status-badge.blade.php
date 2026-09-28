{{--
  Component: status-badge
  Props: $status (string), $label (string)
--}}
@props(['status', 'label'])

@php
$colors = [
    'draft'     => 'bg-slate-100 text-slate-500',
    'active'    => 'bg-teal-100 text-teal-700',
    'matched'   => 'bg-blue-100 text-blue-700',
    'delivered' => 'bg-green-100 text-green-700',
    'closed'    => 'bg-red-100 text-red-600',
    'pending'   => 'bg-yellow-100 text-yellow-700',
    'approved'  => 'bg-blue-100 text-blue-700',
    'rejected'  => 'bg-red-100 text-red-600',
    'handover'  => 'bg-purple-100 text-purple-700',
    'completed' => 'bg-green-100 text-green-700',
];
$class = $colors[$status] ?? 'bg-slate-100 text-slate-500';
@endphp

<span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide {{ $class }}">
    {{ $label }}
</span>
