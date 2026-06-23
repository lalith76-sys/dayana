<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $items = Item::with('category')->latest();

            return DataTables::of($items)
                ->editColumn('stock_quantity', function ($item) {
                    $badge = 'success';
                    if ($item->stock_quantity <= 0) $badge = 'danger';
                    else if ($item->stock_quantity <= $item->reorder_level) $badge = 'warning';
                    
                    return '<span class="badge badge-'.$badge.' p-2">'.$item->stock_quantity.'</span>';
                })
                ->editColumn('defective_quantity', function ($item) {
                    return '<span class="badge badge-danger p-2">'.$item->defective_quantity.'</span>';
                })
                ->addColumn('stock_value', function ($item) {
                    return 'Rs. '.number_format($item->stock_value, 2);
                })
                ->addColumn('action', function ($item) {
                    return '<a href="'.route('items.stock-card', $item->id).'" class="btn btn-sm btn-info">
                        <i class="fas fa-archive"></i> Card
                    </a>';
                })
                ->rawColumns(['stock_quantity', 'defective_quantity', 'action'])
                ->make(true);
        }

        return view('stock.index');
    }
}