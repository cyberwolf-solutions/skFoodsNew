<?php

namespace App\Http\Controllers;

use PgSql\Lob;
use App\Models\Stock;
use App\Models\DailyStock;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DailyStockController extends Controller
{
    //
    public function index()
    {
        $title = 'Kitchen Products';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        //$data = Stock::all();
        $data = DailyStock::all();
        return view('daily-stock.index', compact('title', 'breadcrumbs', 'data'));
    }

    public function create()
    {
        $title = 'Consumption ';

        $is_edit = false;
        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        //$data = Stock::all();
        $data = Stock::all();
        return view('daily-stock.create-edit', compact('title', 'breadcrumbs', 'data', 'is_edit'));
    }



    // public function store(Request $request)
    // {
    //     // Validate the request data
    //     $validator = Validator::make($request->all(), [
    //         'ingredient' => 'required|exists:ingredients,id', // Validate that the ingredient exists
    //         'quanity' => 'required|numeric|min:0', // Ensure that the quantity is numeric and non-negative
    //         'date' => 'required|date', // Ensure the date is valid
    //     ]);

    //     // If validation fails, return errors
    //     if ($validator->fails()) {
    //         $all_errors = null;

    //         foreach ($validator->errors()->messages() as $errors) {
    //             foreach ($errors as $error) {
    //                 $all_errors .= $error . "<br>";
    //             }
    //         }

    //         return response()->json(['success' => false, 'message' => $all_errors]);
    //     }

    //     try {
    //         // Find the ingredient from the daily stock selection
    //         $ingredient = Ingredient::find($request->ingredient);

    //         // Check if the stock exists for the selected ingredient
    //         $stock = Stock::where('name', $ingredient->name)->first();

    //         if (!$stock) {
    //             return response()->json(['success' => false, 'message' => 'Stock not found for this ingredient']);
    //         }

    //         // Ensure the kitchen consumption quantity is available in the stock
    //         if ($stock->quantity < $request->quanity) {
    //             return response()->json(['success' => false, 'message' => 'Not enough stock available']);
    //         }

    //         // Subtract the consumption quantity from the stock quantity
    //         $stock->quantity -= $request->quanity;

    //         // Save the updated stock quantity in the stock table
    //         $stock->save();

    //         // Create the daily stock record
    //         $dailyStock = DailyStock::create([
    //             'name' => $ingredient->name,
    //             'quantity' => $request->quanity,
    //             'products' => $request->product, // assuming `product` is from the form
    //             'created_by' => Auth::user()->id,
    //             'date' => $request->date
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Daily stock record created successfully',
    //             'url' => route('daily-stock.index')
    //         ]);
    //     } catch (\Throwable $th) {
    //         // Handle errors
    //         return response()->json(['success' => false, 'message' => 'Something went wrong! ' . $th->getMessage()]);
    //     }
    // }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'ingredients' => 'required|array', // Ensure ingredients are provided as an array
            'ingredients.*' => 'exists:ingredients,id', // Validate each ingredient exists
            'kitchen_quantity' => 'required|array', // Ensure kitchen consumption quantities are provided
            'kitchen_quantity.*' => 'numeric|min:0', // Ensure each quantity is numeric and non-negative
            'products_made' => 'required|array', // Ensure products made are provided
            'products_made.*' => 'string', // Validate products made
            'date' => 'required|date', // Ensure the date is valid
        ]);

        Log::info('data', $request->all());

        // If validation fails, return errors
        if ($validator->fails()) {
            $all_errors = null;
            foreach ($validator->errors()->messages() as $errors) {
                foreach ($errors as $error) {
                    $all_errors .= $error . "<br>";
                }
            }
            return response()->json(['success' => false, 'message' => $all_errors]);
        }

        try {
            foreach ($request->ingredients as $index => $ingredientId) {
                $ingredient = Ingredient::find($ingredientId);
                $stock = Stock::where('name', $ingredient->name)->first();

                if (!$stock) {
                    return response()->json(['success' => false, 'message' => 'Stock not found for ingredient: ' . $ingredient->name]);
                }

                $consumedQuantity = $request->kitchen_quantity[$index];

                if ($stock->quantity < $consumedQuantity) {
                    return response()->json(['success' => false, 'message' => 'Not enough stock available for ingredient: ' . $ingredient->name]);
                }

                // Subtract the consumed quantity from stock
                $stock->quantity -= $consumedQuantity;
                $stock->save();

                // Create the daily stock record
                DailyStock::create([
                    'name' => $ingredient->name,
                    'quantity' => $consumedQuantity,
                    'products' => $request->products_made[$index],
                    'created_by' => Auth::user()->id,
                    'date' => $request->date
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Daily stock records created successfully',
                'url' => route('daily-stock.index')
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong! ' . $th->getMessage()]);
        }
    }
}
