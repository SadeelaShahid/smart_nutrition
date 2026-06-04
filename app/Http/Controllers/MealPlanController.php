<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MealPlan;
use Illuminate\Support\Facades\Http;
class MealPlanController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'age' => 'required|integer|min:1|max:120',
            'height' => 'required|numeric|min:50|max:300',
            'weight' => 'required|numeric|min:10|max:500',
        ]);
        $height_m = $request->height / 100;
        $bmi = round($request->weight / ($height_m * $height_m), 2);
        if ($bmi < 18.5) {
            $category = 'Underweight';
        } elseif ($bmi < 25) {
            $category = 'Normal';
        } elseif ($bmi < 30) {
            $category = 'Overweight';
        } else {
            $category = 'Obese';
        }
        $meal_plan = $this->getAIMealPlan($category, $request->age, $bmi);
        $plan = MealPlan::create([
            'user_id' => auth()->id(),
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'bmi' => $bmi,
            'bmi_category' => $category,
            'meal_plan' => $meal_plan,
        ]);
        return redirect()->route('meal.result', $plan->id);
    }
    public function result($id)
    {
        $plan = MealPlan::where('user_id', auth()->id())
                        ->findOrFail($id);
        return view('meal-result', compact('plan'));
    }
    private function getAIMealPlan($category, $age, $bmi)
    {
        $apiKey = env('GEMINI_API_KEY');
        $prompt = "Create a 7-day meal plan for a person with the following details:
- Age: {$age} years
- BMI: {$bmi}
- BMI Category: {$category}

Format the response EXACTLY like this, no extra text:
Day 1
Breakfast - [meal]
Lunch - [meal]
Dinner - [meal]
Day 2
Breakfast - [meal]
Lunch - [meal]
Dinner - [meal]
Day 3
Breakfast - [meal]
Lunch - [meal]
Dinner - [meal]
Day 4
Breakfast - [meal]
Lunch - [meal]
Dinner - [meal]
Day 5
Breakfast - [meal]
Lunch - [meal]
Dinner - [meal]
Day 6
Breakfast - [meal]
Lunch - [meal]
Dinner - [meal]
Day 7
Breakfast - [meal]
Lunch - [meal]
Dinner - [meal]";

        try {
            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]
            );
            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($text) {
                return trim($text);
            }
        } catch (\Exception $e) {
            // fallback
        }
        return $this->getFallbackPlan($category);
    }
    private function getFallbackPlan($category)
    {
        $plans = [
            'Underweight' => "Day 1\nBreakfast - Oatmeal with banana\nLunch - Rice with chicken\nDinner - Pasta with vegetables\nDay 2\nBreakfast - Eggs with toast\nLunch - Beef with rice\nDinner - Soup with bread\nDay 3\nBreakfast - Pancakes with honey\nLunch - Fish with vegetables\nDinner - Rice with lentils\nDay 4\nBreakfast - Yogurt with granola\nLunch - Chicken sandwich\nDinner - Noodles with beef\nDay 5\nBreakfast - Smoothie with nuts\nLunch - Biryani\nDinner - Daal with roti\nDay 6\nBreakfast - French toast\nLunch - Grilled chicken\nDinner - Vegetable curry with rice\nDay 7\nBreakfast - Cereal with milk\nLunch - Lamb chops\nDinner - Pasta with chicken",
            'Normal' => "Day 1\nBreakfast - Eggs with fruit\nLunch - Grilled chicken salad\nDinner - Fish with vegetables\nDay 2\nBreakfast - Oatmeal\nLunch - Turkey sandwich\nDinner - Stir fry with rice\nDay 3\nBreakfast - Yogurt parfait\nLunch - Lentil soup\nDinner - Grilled salmon\nDay 4\nBreakfast - Smoothie bowl\nLunch - Chicken wrap\nDinner - Vegetable curry\nDay 5\nBreakfast - Whole grain toast\nLunch - Tuna salad\nDinner - Daal chawal\nDay 6\nBreakfast - Fruit salad\nLunch - Grilled fish\nDinner - Chicken with vegetables\nDay 7\nBreakfast - Boiled eggs\nLunch - Mixed salad\nDinner - Rice with beans",
            'Overweight' => "Day 1\nBreakfast - Green smoothie\nLunch - Salad with grilled chicken\nDinner - Steamed fish\nDay 2\nBreakfast - Oatmeal no sugar\nLunch - Vegetable soup\nDinner - Grilled chicken\nDay 3\nBreakfast - Boiled eggs\nLunch - Tuna salad\nDinner - Stir fried vegetables\nDay 4\nBreakfast - Fruit bowl\nLunch - Lentil soup\nDinner - Baked fish\nDay 5\nBreakfast - Greek yogurt\nLunch - Chicken salad\nDinner - Vegetable curry no rice\nDay 6\nBreakfast - Smoothie\nLunch - Grilled vegetables\nDinner - Boiled chicken\nDay 7\nBreakfast - Apple with nuts\nLunch - Salad\nDinner - Steamed vegetables",
            'Obese' => "Day 1\nBreakfast - Boiled eggs only\nLunch - Green salad\nDinner - Steamed chicken\nDay 2\nBreakfast - Oatmeal water\nLunch - Vegetable soup\nDinner - Grilled fish\nDay 3\nBreakfast - Fruit small\nLunch - Salad no dressing\nDinner - Boiled vegetables\nDay 4\nBreakfast - Green tea with egg\nLunch - Lentil soup small\nDinner - Steamed fish\nDay 5\nBreakfast - Cucumber with yogurt\nLunch - Grilled chicken small\nDinner - Vegetable stir fry\nDay 6\nBreakfast - Smoothie no sugar\nLunch - Tuna salad\nDinner - Boiled chicken\nDay 7\nBreakfast - Boiled egg with fruit\nLunch - Green salad\nDinner - Steamed vegetables",
        ];
        return $plans[$category];
    }
}