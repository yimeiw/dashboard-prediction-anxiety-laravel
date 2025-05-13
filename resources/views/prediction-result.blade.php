<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Anxiety Prediction Result') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">Prediction Result</h3>
                <p class="mt-4 text-xl text-blue-700">Your predicted anxiety level is: <strong>{{ $prediction }}</strong></p>
            </div>
        </div>
    </div>
</x-app-layout>
