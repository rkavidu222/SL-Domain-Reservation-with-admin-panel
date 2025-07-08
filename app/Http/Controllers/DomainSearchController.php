<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DomainPrice;

class DomainSearchController extends Controller
{
    private $companyKey = 'OFQnVNJe8XAqh3sxSYiWrTDLZR6Jfw';

    private function callDomainAPI(string $domainName): ?array
    {
        $ch = curl_init("https://api.domains.lk/searchdomain");
        $payload = json_encode(["domainname" => $domainName]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->companyKey,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) : null;
    }

    public function index()
    {
        $allPrices = DomainPrice::all()->keyBy('category');
        return view('domain.search', compact('allPrices'));
    }

    public function search(Request $request)
    {
        $request->validate(['domainname' => 'required|string|max:255']);

        $inputDomain = strtolower(trim($request->input('domainname')));
        if (!str_ends_with($inputDomain, '.lk')) {
            $inputDomain .= '.lk';
        }

        $baseDomain = preg_replace('/\.lk$/', '', $inputDomain);
        $apiResponse = $this->callDomainAPI($inputDomain);

        if (!$apiResponse || !isset($apiResponse['Message'])) {
            return response()->json(['success' => false, 'error' => 'Invalid API response']);
        }

        $mainCategory = $apiResponse['DomainCategory'] ?? $this->classifyDomainCategory($baseDomain);

        // Always show CAT1 and CAT2 unless domain is CAT4 or CAT5
        $showCat1 = !in_array($mainCategory, ['CAT4', 'CAT5']);
        $showCat2 = !in_array($mainCategory, ['CAT4', 'CAT5']);

        $allPrices = DomainPrice::all()->keyBy('category')->map(function ($item) {
            return [
                'old_price' => $item->old_price,
                'new_price' => $item->new_price,
            ];
        })->toArray();

        $reservedSLDs = ['edu', 'com', 'hotel', 'org', 'web'];
        $secondLevelDomains = [];
        foreach ($reservedSLDs as $sld) {
            $secondLevelDomains[] = $baseDomain . '.' . $sld . '.lk';
        }

        $suggested = [];
        if (!empty($apiResponse['AlsoAvailable'])) {
			foreach ($apiResponse['AlsoAvailable'] as $suggestion) {
				if (isset($suggestion['DomainName'])) {
					$suggested[] = $suggestion['DomainName'] . '.lk';
				}
			}
		}


        return response()->json([
            'success' => true,
            'data' => $apiResponse,
            'category' => $mainCategory,
            'baseDomain' => $baseDomain,
            'domainFullName' => $inputDomain,
            'secondLevelDomains' => $secondLevelDomains,
            'suggestedDomains' => $suggested,
            'prices' => $allPrices,
            'showCat1' => $showCat1,
            'showCat2' => $showCat2,
            'showCat3' => true, // always show for SLD options
        ]);
    }

    private function classifyDomainCategory(string $base): string
    {
        $len = strlen($base);
        $isNumeric = ctype_digit($base);

        if ($len == 2 || ($isNumeric && $len >= 2 && $len <= 7)) return 'CAT4';
        if ($len == 3 || ($isNumeric && $len >= 8 && $len <= 10)) return 'CAT5';

        return 'CAT1';
    }
}
