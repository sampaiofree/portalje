@props(['label', 'name', 'checked', 'id'])

<div x-data="{ on: {{ $checked ? 'true' : 'false' }} }" class="flex flex-col items-center">
    <label class="text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
    <div 
        @click="on = !on; $dispatch('status-changed', { id: {{ $id }}, field: '{{ $name }}', value: on })"
        class="relative inline-flex items-center cursor-pointer"
    >
        <input type="checkbox" class="sr-only peer" :checked="on">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
    </div>
</div>