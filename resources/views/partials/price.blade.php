<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Domain Search - .LK Domains</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body { background-color: #f3f6fa; }
    .card-animate {
      background: #fff;
      border-radius: 1rem;
      padding: 1.5rem;
      box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
      animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .section-header {
      border-left: 6px solid #2563EB;
      padding-left: 12px;
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: #1F2937;
      background-color: #F3F4F6;
      padding: 10px;
      border-radius: 0.25rem;
    }
    .price-badge { font-size: 0.95rem; }
  </style>
</head>
<body>

<div id="step1" class="my-4">

  <div class="container">
    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if (session('warning'))
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
  </div>

  <h2 class="mb-2 text-center text-primary fs-3 fw-semibold">Search .LK Domain</h2><br>

  <div class="mb-4 text-center">
    <input type="text" id="domainInput" class="form-control" placeholder="Enter your domain" />

    {{-- Show CAT2 price initially --}}
    @php $cat2Price = $allPrices->get('CAT2'); @endphp
    @if($cat2Price)
      <div class="price-info text-center mt-2">
        <del>Rs. {{ number_format($cat2Price->old_price, 2) }}/=</del>
        <span class="text-danger fw-bold">Now Rs. {{ number_format($cat2Price->new_price, 2) }}/=</span>
      </div>
    @endif

    <button id="searchBtn" class="btn btn-primary mt-3">Search</button>
  </div>

  <div id="resultContainer"></div>
</div>

<script>
  // Pass allPrices from PHP to JS as an object for dynamic pricing
  const prices = {
    CAT1: {
      old: {{ $allPrices->get('CAT1') ? $allPrices->get('CAT1')->old_price : 0 }},
      new: {{ $allPrices->get('CAT1') ? $allPrices->get('CAT1')->new_price : 0 }}
    },
    CAT2: {
      old: {{ $allPrices->get('CAT2') ? $allPrices->get('CAT2')->old_price : 0 }},
      new: {{ $allPrices->get('CAT2') ? $allPrices->get('CAT2')->new_price : 0 }}
    },
    CAT3: {
      old: {{ $allPrices->get('CAT3') ? $allPrices->get('CAT3')->old_price : 0 }},
      new: {{ $allPrices->get('CAT3') ? $allPrices->get('CAT3')->new_price : 0 }}
    },
    SUGGESTED: {
      old: {{ $allPrices->get('SUGGESTED') ? $allPrices->get('SUGGESTED')->old_price : 0 }},
      new: {{ $allPrices->get('SUGGESTED') ? $allPrices->get('SUGGESTED')->new_price : 0 }}
    }
  };

  document.getElementById('searchBtn').addEventListener('click', async function() {
    const domainInput = document.getElementById('domainInput');
    const btn = this;
    const resultContainer = document.getElementById('resultContainer');
    const domain = domainInput.value.trim().toLowerCase();

    if (!domain) {
      alert('Please enter a domain name.');
      return;
    }

    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...`;
    resultContainer.innerHTML = '';

    try {
      const response = await fetch("{{ route('domain.search.api') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
        body: JSON.stringify({ domainname: domain + '.lk' })
      });

      const data = await response.json();

      btn.disabled = false;
      btn.textContent = 'Search';

      if (!data.success || !data.data) {
        resultContainer.innerHTML = `<div class="alert alert-warning">No data received from API.</div>`;
        return;
      }

      const domainData = data.data;
      const fullDomain = domainData.DomainNameFull || domain + '.lk';
      const baseDomain = domainData.DomainName || domain;

      const isAvailable = domainData.Message && domainData.Message.toLowerCase().includes('available');

      const availabilityMsg = isAvailable
        ? `<div class="alert alert-success">${fullDomain} is available for registration.</div>`
        : `<div class="alert alert-danger">${fullDomain} is NOT available for registration.</div>`;

      if (!isAvailable) {
        resultContainer.innerHTML = availabilityMsg;
        return;
      }

      // Prepare URLs with domain_name, price, and category for Buy Now buttons
      function buyNowUrl(domainName, newPrice, category, oldPrice = 0) {
		  return "{{ route('domain.contact.info') }}" +
			`?domain_name=${encodeURIComponent(domainName)}` +
			`&price=${encodeURIComponent(newPrice)}` +
			`&category=${encodeURIComponent(category)}` +
			`&old_price=${encodeURIComponent(oldPrice)}`;
		}

      // Build detailed domain offers HTML dynamically from DB prices
      const cat2Html = `
        <div class="mb-4 p-4 rounded border bg-white">
          <div class="section-header">CAT2 - Top Level Domain Only</div>
          <div class="text-center my-3 d-flex justify-content-center align-items-center gap-2">
            <i class="bi bi-globe2 text-primary"></i>
            <span class="fw-medium">${fullDomain}</span>
            <span class="text-decoration-line-through text-muted ms-3">LKR ${Number(prices.CAT2.old).toFixed(2)}</span>
            <span class="text-danger fw-bold ms-3">LKR ${Number(prices.CAT2.new).toFixed(2)}</span>
          </div>
          <div class="text-center">
            <a href="${buyNowUrl(fullDomain, Number(prices.CAT2.new).toFixed(2), 'CAT2', Number(prices.CAT2.old).toFixed(2))}" class="btn btn-primary">
              <i class="bi bi-cart3 me-2"></i> Buy Now
            </a>
          </div>
        </div>`;

      const cat1Html = `
        <div class="mb-4 p-4 rounded border bg-white">
          <div class="section-header">CAT1 - Full Domain Package</div>
          <div class="text-center my-3 d-flex justify-content-center align-items-center gap-2">
            <i class="bi bi-globe2 text-primary"></i>
            <span class="fw-medium">${fullDomain}</span>
            <span class="text-decoration-line-through text-muted ms-3">LKR ${Number(prices.CAT1.old).toFixed(2)}</span>
            <span class="text-danger fw-bold ms-3">LKR ${Number(prices.CAT1.new).toFixed(2)}</span>
          </div>
          <div class="text-center text-muted small mt-3">
            <p class="mb-2">You automatically reserve the following names:</p>
            <ul class="list-unstyled text-start d-inline-block">
              ${['edu.lk', 'com.lk', 'hotel.lk', 'org.lk', 'web.lk'].map(d => `<li>${baseDomain}.${d}</li>`).join('')}
            </ul>
          </div>
          <div class="text-center mt-3">
           <a href="${buyNowUrl(fullDomain, Number(prices.CAT1.new).toFixed(2), 'CAT1', Number(prices.CAT1.old).toFixed(2))}" class="btn btn-primary">
            <i class="bi bi-cart3 me-2"></i> Buy Now
            </a>
          </div>
        </div>`;

      const cat3Html = `
        <div class="mb-4 p-4 rounded border bg-white">
          <div class="section-header">Other Options Related to Your Domain Search</div>
          <div class="fw-bold text-primary mb-3">CAT3 - Second Level Domain Only</div>
          ${['edu.lk', 'com.lk', 'hotel.lk', 'org.lk', 'web.lk'].map(d => `
            <div class="mb-3 p-3 border rounded bg-light">
              <div class="d-flex justify-content-between">
                <span><i class="bi bi-globe2 text-primary"></i> ${baseDomain}.${d}</span>
                <span class="text-muted text-decoration-line-through">LKR ${Number(prices.CAT3.old).toFixed(2)}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="text-danger fw-bold">LKR ${Number(prices.CAT3.new).toFixed(2)}</span>
                <a <a href="${buyNowUrl(baseDomain + '.' + d, Number(prices.CAT3.new).toFixed(2), 'CAT3', Number(prices.CAT3.old).toFixed(2))}" class="btn btn-sm btn-primary">
                  <i class="bi bi-cart3 me-1"></i> Buy Now
                </a>
              </div>
            </div>`).join('')}
        </div>`;

      const suggestions = Array.isArray(domainData.AlsoAvailable) ? domainData.AlsoAvailable : [];
      let suggestedHtml = `<div class="mb-4 p-4 rounded border bg-white"><div class="section-header">Suggested Top Level Domains</div>`;
      if (suggestions.length > 0) {
        suggestedHtml += suggestions.map(s => `
          <div class="mb-3 p-3 border rounded bg-light">
            <div class="d-flex justify-content-between">
              <span><i class="bi bi-globe2 text-primary"></i> ${s.DomainName}</span>
              <span class="text-muted text-decoration-line-through">LKR ${Number(prices.SUGGESTED.old).toFixed(2)}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <span class="text-danger fw-bold">LKR ${Number(prices.SUGGESTED.new).toFixed(2)}</span>
              <a href="${buyNowUrl(s.DomainName, Number(prices.SUGGESTED.new).toFixed(2), 'SUGGESTED', Number(prices.SUGGESTED.old).toFixed(2))}" class="btn btn-sm btn-primary">
                <i class="bi bi-cart3 me-1"></i> Buy Now
              </a>
            </div>
          </div>`).join('');
      } else {
        suggestedHtml += `<div>No suggestions available.</div>`;
      }
      suggestedHtml += '</div>';

      resultContainer.innerHTML = availabilityMsg + cat2Html + cat1Html + cat3Html + suggestedHtml;

    } catch (error) {
      btn.disabled = false;
      btn.textContent = 'Search';
      resultContainer.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
