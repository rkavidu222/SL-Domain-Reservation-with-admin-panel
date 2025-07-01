<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainPrice;

class DomainSearchController extends Controller
{
    // Show search page with prices for initial load
    public function index()
    {
        $allPrices = DomainPrice::all()->keyBy('category');
        return view('domain.search', compact('allPrices'));
    }

    // API endpoint to check domain availability & send prices
    public function search(Request $request)
    {
        $request->validate([
            'domainname' => 'required|string|max:255',
        ]);

        $domainName = $request->input('domainname');
        $apiKey = 'OFQnVNJe8XAqh3sxSYiWrTDLZR6Jfw';

        $ch = curl_init("https://api.domains.lk/searchdomain");
        $payload = json_encode(['domainname' => $domainName]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return response()->json(['success' => false, 'error' => 'API connection failed'], 500);
        }

        curl_close($ch);

        $decoded = json_decode($response, true);

        if (!$decoded) {
            return response()->json(['success' => false, 'error' => 'Invalid API response'], 500);
        }

        $allPrices = DomainPrice::all()->keyBy('category')->map(function ($item) {
            return [
                'old_price' => $item->old_price,
                'new_price' => $item->new_price,
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => $decoded,
            'prices' => $allPrices,
        ]);
    }
}
