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
        return view('admin.layouts.management.domainPrice', compact('prices'));

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

	public function updateSingle(Request $request, $id)
{
    $request->validate([
        'old_price' => 'required|numeric',
        'new_price' => 'required|numeric',
    ]);

    $price = DomainPrice::findOrFail($id);
    $price->update([
        'old_price' => $request->old_price,
        'new_price' => $request->new_price,
    ]);

    return redirect()->back()->with('success', "{$price->category} price updated successfully.");
}

}
