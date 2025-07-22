<?php
namespace App\Http\Controllers;

use App\Models\AccountJournal;
use App\Models\Sale;
use App\Services\DataHandleService;
use DB;
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
        $sales   = Sale::with('product')
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
        DB::beginTransaction();
        try {
            $sale            = $this->dataHandleService->store(new Sale(), $data);
            $available_stock = \App\Models\Stock::find($data['product_id']);
            if ($available_stock) {
                if ($available_stock->quantity < $data['quantity']) {
                    \DB::rollBack();
                    return response()->json(['error' => 'Insufficient stock'], 400);
                }
                $available_stock->quantity -= $data['quantity'];
                $available_stock->save();
            }
            $this->createJournalEntries($sale, $data);
            \DB::commit();

            return response()->json([
                'message' => 'Sale recorded successfully',
                'sale'    => $sale,
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'error'   => 'Something went wrong',
                'details' => $e->getMessage(),
            ], 500);
        }
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

    private function createJournalEntries(Sale $sale, array $data)
    {
        $netSales = $data['total_price'] - $data['vat'] + $data['discount'];
        AccountJournal::create([
            'sale_id' => $sale->id,
            'account' => $data['due'] > 0 ? 'Accounts Receivable' : 'Cash',
            'type'    => 'debit',
            'amount'  => $data['total_price'],
        ]);
        AccountJournal::create([
            'sale_id' => $sale->id,
            'account' => 'Sales Revenue',
            'type'    => 'credit',
            'amount'  => $netSales,
        ]);
        if ($data['vat'] > 0) {
            AccountJournal::create([
                'sale_id' => $sale->id,
                'account' => 'VAT Payable',
                'type'    => 'credit',
                'amount'  => $data['vat'],
            ]);
        }
        if ($data['discount'] > 0) {
            AccountJournal::create([
                'sale_id' => $sale->id,
                'account' => 'Discount Given',
                'type'    => 'debit',
                'amount'  => $data['discount'],
            ]);
        }
    }

    public function getJournalEntries()
    {
        $journals = AccountJournal::with('sale.product')
            ->latest()
            ->get();

        return response()->json($journals);
    }

}
