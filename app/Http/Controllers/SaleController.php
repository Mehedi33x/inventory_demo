<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\DataHandleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected $dataHandleService;

    public function __construct(DataHandleService $dataHandleService)
    {
        $this->dataHandleService = $dataHandleService;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);

        $sales = Sale::with('product')
            ->select('id', 'product_id', 'quantity', 'price', 'total_price', 'paid', 'due')
            ->paginate($perPage);

        return response()->json([
            'data'        => $sales->items(),
            'currentPage' => $sales->currentPage(),
            'totalPages'  => $sales->lastPage(),
            'total'       => $sales->total(),
        ]);
    }

    public function store(Request $request)
    {
        $this->doValidation($request);

        $data = $request->only([
            'product_id',
            'invoice_number',
            'quantity',
            'price',
            'vat',
            'discount',
            'total_price',
            'paid',
            'due',
        ]);

        $sale = $this->dataHandleService->store(new Sale(), $data);

        return response()->json([
            'message' => 'Sale recorded successfully',
            'sale'    => $sale,
        ], 201);
    }

    public function show($id)
    {
        $sale = Sale::find($id);

        if ($sale) {
            return response()->json($sale);
        }

        return response()->json(['error' => 'Sale not found'], 404);
    }

    public function edit($id)
    {
        $sale = Sale::find($id);

        if ($sale) {
            return response()->json($sale);
        }

        return response()->json(['error' => 'Sale not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $this->doValidation($request);

        $sale = Sale::find($id);

        if (! $sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        $data = $request->only([
            'product_id',
            'quantity',
            'price',
            'vat',
            'discount',
            'total_price',
            'paid',
            'due',
        ]);

        $this->dataHandleService->update($sale, $data);

        return response()->json([
            'message' => 'Sale updated successfully',
            'sale'    => $sale->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $sale = Sale::find($id);

        if (! $sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        $sale->delete();

        return response()->json(['message' => 'Sale deleted successfully'], 200);
    }

    private function doValidation(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'invoice_number' => 'required',
            'quantity'       => 'required|integer|min:1',
            'price'          => 'required|numeric|min:0',
            'vat'            => 'nullable|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0',
            'total_price'    => 'required|numeric|min:0',
            'paid'           => 'required|numeric|min:0',
            'due'            => 'required|numeric|min:0',
        ]);
    }

    public function generateSalesInvoice(Request $request)
    {
        $id = $request->query('id', null);
        if ($id) {
            $item = Sale::find($id);
            if ($item) {
                return response()->json([
                    'invoice_number' => $item->invoice_number,
                ]);
            }
        }
        $latestItem = Sale::orderBy('invoice_number', 'desc')->first();
        if ($latestItem && is_numeric($latestItem->invoice_number)) {
            $nextNumber = str_pad($latestItem->invoice_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        return response()->json([
            'invoice_number' => $nextNumber,
        ]);
    }
}
