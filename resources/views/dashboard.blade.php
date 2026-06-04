<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🥗 Smart Nutrition System
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Your Profile</h3>
                <p class="text-sm text-gray-500 mb-6">Enter your details to compute BMI and get a 7-day meal plan.</p>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <strong>⚠️ Invalid Input!</strong>
                        <ul class="mt-1 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/generate-plan">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Age</label>
                            <input type="number" name="age" placeholder="e.g. 22" min="1" max="120"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" required />
                            @error('age')
                                <p class="text-red-500 text-xs mt-1">⚠️ {{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Height (cm)</label>
                            <input type="number" name="height" placeholder="e.g. 165" min="50" max="300"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" required />
                            @error('height')
                                <p class="text-red-500 text-xs mt-1">⚠️ {{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Weight (kg)</label>
                            <input type="number" name="weight" placeholder="e.g. 60" min="10" max="500"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" required />
                            @error('weight')
                                <p class="text-red-500 text-xs mt-1">⚠️ {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-base tracking-wide shadow-md transition duration-200">
                        🍽️ Generate Weekly Plan
                    </button>
                </form>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">How it works</h3>
                <ol class="list-decimal list-inside text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <li>BMI is calculated from your height and weight.</li>
                    <li>You are grouped by BMI category and age band.</li>
                    <li>A personalized weekly meal plan is generated using AI.</li>
                </ol>
                <p class="text-xs text-gray-400 mt-4">This tool is for demonstration only. Consult a qualified professional for medical or dietary guidance.</p>
            </div>
        </div>
    </div>
</x-app-layout>