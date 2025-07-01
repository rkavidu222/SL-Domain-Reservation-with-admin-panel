<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainPrice;

class DomainPriceController extends Controller
{
    public function index()
    {
        $prices = DomainPrice::all();
        return view('admin.domain_prices.index', compact('prices'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.old_price' => 'required|numeric',
            'prices.*.new_price' => 'required|numeric',
        ]);

        foreach ($request->prices as $id => $data) {
            DomainPrice::where('id', $id)->update([
                'old_price' => $data['old_price'],
                'new_price' => $data['new_price'],
            ]);
        }

        return redirect()->back()->with('success', 'Prices updated successfully.');
    }
}
