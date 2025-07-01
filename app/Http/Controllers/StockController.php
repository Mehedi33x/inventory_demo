<?php
namespace App\Http\Controllers;

use App\Models\Stock;
use App\Services\DataHandleService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $dataHandleService;

    public function __construct(DataHandleService $dataHandleService)
    {
        $this->dataHandleService = $dataHandleService;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $stocks = Stock::with('product')
            ->select('id', 'product_id', 'quantity')
            ->paginate($perPage);
        return response()->json([
            'data'        => $stocks->items(),
            'currentPage' => $stocks->currentPage(),
            'totalPages'  => $stocks->lastPage(),
            'total'       => $stocks->total(),
        ]);
    }

    public function store(Request $request)
    {
        $this->doValidation($request);
        $data          = $request->only(['product_id', 'quantity']);
        $existingStock = Stock::where('product_id', $data['product_id'])->first();
        if ($existingStock) {
            $existingStock->quantity += $data['quantity'];
            $existingStock->save();
            return response()->json([
                'message' => 'Stock quantity updated successfully',
                'stock'   => $existingStock->fresh(),
            ], 200);
        }
        $stock = $this->dataHandleService->store(new Stock(), $data);
        return response()->json([
            'message' => 'Stock created successfully',
            'stock'   => $stock,
        ], 201);
    }

    public function show($id)
    {
        $stock = Stock::find($id);
        if ($stock) {
            return response()->json($stock);
        }
        return response()->json(['error' => 'Stock not found'], 404);
    }
    public function edit($id)
    {
        $stock = Stock::find($id);
        if ($stock) {
            return response()->json($stock);
        }
        return response()->json(['error' => 'Stock not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $this->doValidation($request);
        $stock = Stock::find($id);
        if (! $stock) {
            return response()->json(['error' => 'Stock not found'], 404);
        }
        $data = $request->all();
        $this->dataHandleService->update($stock, $data);
        return response()->json([
            'message' => 'Stock updated successfully',
            'stock'   => $stock->fresh(),
        ], 200);
    }

    public function destroy($id)
    {
        $stock = Stock::find($id);
        if (! $stock) {
            return response()->json(['error' => 'Stock not found'], 404);
        }
        $stock->delete();
        return response()->json(['message' => 'Stock deleted successfully'], 200);
    }

    private function doValidation(Request $request)
    {
        return $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:0',
        ]);
    }
}
