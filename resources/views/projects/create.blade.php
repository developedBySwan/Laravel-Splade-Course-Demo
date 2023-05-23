<x-layout>
    <x-slot name="header">
        {{ __('Add new project') }}
    </x-slot>

    <x-panel class="flex flex-col pt-16 pb-16">
        <x-splade-form :for="$form" />
    </x-panel>
</x-layout>
