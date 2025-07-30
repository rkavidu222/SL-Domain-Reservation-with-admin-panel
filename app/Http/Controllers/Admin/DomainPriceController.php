<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainPrice;
use App\Models\ActivityLog;

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

        $admin = auth()->guard('admin')->user();

        foreach ($request->prices as $id => $data) {
            $price = DomainPrice::find($id);
            if ($price) {
                // Log activity with admin_id instead of user_id
                ActivityLog::create([
                    'admin_id' => $admin->id,
                    'task' => "DomainPrice updated by admin_id={$admin->id}, category={$price->category}, old_price from {$price->old_price} to {$data['old_price']}, new_price from {$price->new_price} to {$data['new_price']}"
                ]);

                $price->update([
                    'old_price' => $data['old_price'],
                    'new_price' => $data['new_price'],
                ]);
            }
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
        $admin = auth()->guard('admin')->user();

        ActivityLog::create([
            'admin_id' => $admin->id,
            'task' => "DomainPrice single update by admin_id={$admin->id}, category={$price->category}, old_price from {$price->old_price} to {$request->old_price}, new_price from {$price->new_price} to {$request->new_price}"
        ]);

        $price->update([
            'old_price' => $request->old_price,
            'new_price' => $request->new_price,
        ]);

        return redirect()->back()->with('success', "{$price->category} price updated successfully.");
    }
}
