<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🥗 Your Weekly Meal Plan
        </h2>
    </x-slot>

    <style>
        @media print {
            body * { visibility: hidden; }
            #printable, #printable * { visibility: visible; }
            #printable {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print { display: none !important; }
            #printable .day-card {
                page-break-inside: avoid;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Results Card --}}
            <div class="no-print bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Your Results</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500">Age</p>
                        <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $plan->age }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500">Height</p>
                        <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $plan->height }} cm</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500">Weight</p>
                        <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $plan->weight }} kg</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500">BMI</p>
                        <p class="text-2xl font-semibold text-green-500">{{ $plan->bmi }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="bg-green-100 text-green-800 text-sm font-semibold px-4 py-1 rounded-full">
                        {{ $plan->bmi_category }}
                    </span>
                </div>
            </div>

            {{-- Meal Plan Card --}}
            <div class="no-print bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-6">7-Day Meal Plan</h3>
                <div class="space-y-4">
                    @php
                        $dayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                        $allLines = explode("\n", $plan->meal_plan);
                        $days = [];
                        $currentDay = null;
                        foreach($allLines as $line) {
                            $line = trim($line);
                            if(strpos($line, 'Day ') === 0) {
                                $currentDay = $line;
                                $days[$currentDay] = [];
                            } elseif($currentDay && $line != '') {
                                $days[$currentDay][] = $line;
                            }
                        }
                        $dayIndex = 0;
                    @endphp
                    @foreach($days as $dayTitle => $meals)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-semibold text-green-600 dark:text-green-400 mb-3">
                            {{ $dayNames[$dayIndex] ?? $dayTitle }}
                        </h4>
                        <div class="space-y-2">
                            @foreach($meals as $meal)
                                @php
                                    $parts = explode(' - ', $meal, 2);
                                    $mealType = $parts[0] ?? '';
                                    $mealDesc = $parts[1] ?? '';
                                @endphp
                                <div class="flex items-start gap-2">
                                    <span class="min-w-24 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        {{ $mealType }}:
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $mealDesc }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @php $dayIndex++; @endphp
                    @endforeach
                </div>
            </div>

            {{-- Buttons --}}
            <div class="no-print flex gap-4 mb-8">
                <a href="/dashboard" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg text-sm">
                    ← Back to Dashboard
                </a>
                <button onclick="printPamphlet()" class="inline-block bg-white hover:bg-gray-50 border border-green-500 text-green-600 font-semibold px-6 py-2 rounded-lg text-sm">
                    🖨️ Print Meal Plan
                </button>
            </div>

            {{-- PRINTABLE PAMPHLET --}}
            <div id="printable" style="display:none; font-family: Georgia, serif; background: white; padding: 30px; max-width: 800px; margin: auto;">

                {{-- Header --}}
                <div style="text-align:center; border-bottom: 3px solid #2d6a4f; padding-bottom: 16px; margin-bottom: 24px;">
                    <h1 style="color: #2d6a4f; font-size: 26px; margin: 0;">🥗 Smart Nutrition System</h1>
                    <p style="color: #555; font-size: 13px; margin: 6px 0 0;">Personalized Weekly Meal Plan</p>
                </div>

                {{-- User Info --}}
                <div style="background: #f1f8f4; border: 1px solid #a8d5ba; border-radius: 10px; padding: 14px; margin-bottom: 24px; display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                    <div style="text-align:center; padding: 0 16px;">
                        <p style="color: #888; font-size: 11px; margin: 0;">AGE</p>
                        <p style="color: #2d6a4f; font-size: 20px; font-weight: bold; margin: 4px 0;">{{ $plan->age }}</p>
                    </div>
                    <div style="text-align:center; padding: 0 16px; border-left: 1px solid #a8d5ba; border-right: 1px solid #a8d5ba;">
                        <p style="color: #888; font-size: 11px; margin: 0;">HEIGHT</p>
                        <p style="color: #2d6a4f; font-size: 20px; font-weight: bold; margin: 4px 0;">{{ $plan->height }} cm</p>
                    </div>
                    <div style="text-align:center; padding: 0 16px; border-right: 1px solid #a8d5ba;">
                        <p style="color: #888; font-size: 11px; margin: 0;">WEIGHT</p>
                        <p style="color: #2d6a4f; font-size: 20px; font-weight: bold; margin: 4px 0;">{{ $plan->weight }} kg</p>
                    </div>
                    <div style="text-align:center; padding: 0 16px;">
                        <p style="color: #888; font-size: 11px; margin: 0;">BMI</p>
                        <p style="color: #2d6a4f; font-size: 20px; font-weight: bold; margin: 4px 0;">{{ $plan->bmi }} <span style="font-size:12px; color:#555;">({{ $plan->bmi_category }})</span></p>
                    </div>
                </div>

                {{-- 7 Day Plan --}}
                <h2 style="color: #2d6a4f; font-size: 17px; margin-bottom: 16px; text-align:center;">7-Day Meal Plan</h2>

                @php
                    $printDayNames = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                    $printDayIndex = 0;
                @endphp

                @foreach($days as $dayTitle => $meals)
                <div class="day-card" style="margin-bottom: 14px; border: 1px solid #a8d5ba; border-radius: 8px; overflow: hidden; page-break-inside: avoid;">
                    <div style="background: #2d6a4f; padding: 7px 14px;">
                        <h3 style="color: white; margin: 0; font-size: 14px;">{{ $printDayNames[$printDayIndex] ?? $dayTitle }}</h3>
                    </div>
                    <div style="padding: 10px 14px; background: white;">
                        @foreach($meals as $meal)
                            @php
                                $parts = explode(' - ', $meal, 2);
                                $mealType = $parts[0] ?? '';
                                $mealDesc = $parts[1] ?? '';
                            @endphp
                            <div style="display: flex; gap: 12px; padding: 4px 0; border-bottom: 1px solid #f1f8f4;">
                                <span style="min-width: 75px; font-size: 12px; font-weight: bold; color: #2d6a4f;">{{ $mealType }}:</span>
                                <span style="font-size: 12px; color: #333;">{{ $mealDesc }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @php $printDayIndex++; @endphp
                @endforeach

                {{-- Footer --}}
                <div style="text-align:center; margin-top: 20px; padding-top: 12px; border-top: 2px solid #2d6a4f;">
                    <p style="color: #888; font-size: 10px;">This meal plan is generated by Smart Nutrition System. Consult a professional for medical advice.</p>
                    <p style="color: #2d6a4f; font-size: 11px; font-weight: bold;">🥗 Smart Nutrition System — {{ now()->format('d M Y') }}</p>
                </div>

            </div>
        </div>
    </div>

    <script>
        function printPamphlet() {
            document.getElementById('printable').style.display = 'block';
            setTimeout(function() {
                window.print();
                setTimeout(function() {
                    document.getElementById('printable').style.display = 'none';
                }, 1500);
            }, 400);
        }
    </script>

</x-app-layout>