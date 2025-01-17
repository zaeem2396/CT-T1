<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $filePath = 'products.json';

    public function index()
    {
        $data['products'] = $this->getProducts();
        return view('index', $data);
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'prodName' => 'required|string',
            'qtyInStock' => 'required|integer|min:0',
            'pricePerItem' => 'required|numeric|min:0'
        ]);

        $products = $this->getProducts();

        $totalValue = $request->qtyInStock * $request->pricePerItem;

        $newProd = [
            'prodName' => $request->prodName,
            'qtyInStock' => $request->qtyInStock,
            'pricePerItem' => $request->pricePerItem,
            'dateTimeSumitted' => Carbon::now()->toDateTimeString(),
            'totalValue' => $totalValue
        ];

        $products[] = $newProd;

        Storage::put($this->filePath, json_encode($products, JSON_PRETTY_PRINT));
        return response()->json([
            'status' => 200,
            'products' => $products
        ]);
    }

    public function editProduct(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
            'prodName' => 'required|string',
            'qtyInStock' => 'required|integer|min:0',
            'pricePerItem' => 'required|numberic|min:0'
        ]);

        $products = $this->getProducts();
        $index = $request->index;

        if (!isset($products[$index])) {
            return response()->json([
                'status' => 500,
                'message' => 'Invalid index'
            ]);
        }

        $products[$index] = [
            'prodName' => $request->prodName,
            'qtyInStock' => $request->qtyInStock,
            'pricePerItem' => $request->pricePerItem,
            'dateTimeSumitted' => Carbon::now()->toDateTimeString(),
            'totalValue' => $request->qtyInStock * $request->pricePerItem
        ];

        Storage::put($this->filePath, json_encode($products, JSON_PRETTY_PRINT));
        return response()->json([
            'status' => 200,
            'products' => $products
        ]);
    }

    private function getProducts()
    {
        return Storage::exists($this->filePath) ? json_decode(Storage::get($this->filePath), true) : [];
    }
}
