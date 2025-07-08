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

	{{-- Show CAT2 price initially --}}
    @php $cat2Price = $allPrices->get('CAT2'); @endphp
    @if($cat2Price)
      <div class="price-info text-center mt-2">
        <del>Rs. {{ number_format($cat2Price->old_price, 2) }}/=</del>
        <span class="text-danger fw-bold">Now Rs. {{ number_format($cat2Price->new_price, 2) }}/=</span>
      </div>
    @endif

  <div class="container">
    <h2 class="mb-2 text-center text-primary fs-3 fw-semibold">Search .LK Domain</h2><br>

    <div class="mb-4 text-center">
      <input type="text" id="domainInput" class="form-control" placeholder="Enter your domain" />
      <button id="searchBtn" class="btn btn-primary mt-3">Search</button>
    </div>

    <div id="resultContainer"></div>
  </div>
</div>

<script>
  // Prices passed from backend
  const prices = @json($allPrices);

  function buyNowUrl(domainName, newPrice, category, oldPrice = 0) {
    return "{{ route('domain.contact.info') }}" +
      `?domain_name=${encodeURIComponent(domainName)}` +
      `&price=${encodeURIComponent(newPrice)}` +
      `&category=${encodeURIComponent(category)}` +
      `&old_price=${encodeURIComponent(oldPrice)}`;
  }

  document.getElementById('searchBtn').addEventListener('click', async function () {
    const domainInput = document.getElementById('domainInput');
    const btn = this;
    const resultContainer = document.getElementById('resultContainer');
    const inputDomain = domainInput.value.trim().toLowerCase();

    if (!inputDomain) {
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
        body: JSON.stringify({ domainname: inputDomain })
      });

      const json = await response.json();

      btn.disabled = false;
      btn.textContent = 'Search';

      if (!json.success) {
        resultContainer.innerHTML = `<div class="alert alert-danger">${json.error || 'Error searching domain'}</div>`;
        return;
      }

      const baseDomain = json.baseDomain;
      const apiData = json.data;
      const category = json.category;
      const domainFullName = apiData.DomainNameFull || (baseDomain + '.lk');
      const message = apiData.Message || '';

      const isAvailable = message.toLowerCase().includes('available');

      let html = `<div class="alert ${isAvailable ? 'alert-success' : 'alert-danger'}">
        ${domainFullName} Domain is ${isAvailable ? 'available' : 'not available'} for registration.
      </div>`;

      if (!isAvailable) {
        resultContainer.innerHTML = html;
        return;
      }

      // Reserved Second Level Domains (SLDs)
      const reservedSLDs = ['edu', 'com', 'hotel', 'org', 'web'];

      // Helper to render a domain price line with Buy Now button
      function renderDomainLine(domainName, categoryKey) {
        const oldPrice = prices[categoryKey]?.old_price || 0;
        const newPrice = prices[categoryKey]?.new_price || 0;
        return `
          <div class="mb-3 p-3 border rounded bg-light d-flex justify-content-between align-items-center">
            <div><i class="bi bi-globe2 text-primary"></i> ${domainName}</div>
            <div>
              <span class="text-decoration-line-through text-muted me-2">LKR ${Number(oldPrice).toFixed(2)}</span>
              <span class="text-danger fw-bold me-3">LKR ${Number(newPrice).toFixed(2)}</span>
              <a href="${buyNowUrl(domainName, newPrice, categoryKey, oldPrice)}" class="btn btn-sm btn-primary">
                <i class="bi bi-cart3 me-1"></i> Buy Now
              </a>
            </div>
          </div>
        `;
      }

      // Render by category logic
      if (category === 'CAT4') {
        html += `
          <div class="mb-4 p-4 rounded border bg-white">
            <div class="section-header">CAT4 - Premium Domain</div>
            <div class="text-center my-3 d-flex justify-content-center align-items-center gap-2">
              <i class="bi bi-globe2 text-primary"></i>
              <span class="fw-medium">${domainFullName}</span>
              <span class="text-decoration-line-through text-muted ms-3">LKR ${Number(prices.CAT4.old_price).toFixed(2)}</span>
              <span class="text-danger fw-bold ms-3">LKR ${Number(prices.CAT4.new_price).toFixed(2)}</span>
            </div>
            <div class="text-center text-muted small mt-3">
              <p class="mb-2">You automatically reserve following names</p>
              <ul class="list-unstyled text-start d-inline-block">
                ${reservedSLDs.map(sld => `<li>${baseDomain}.${sld}.lk</li>`).join('')}
              </ul>
            </div>
            <div class="text-center mt-3">
              <a href="${buyNowUrl(domainFullName, prices.CAT4.new_price, 'CAT4', prices.CAT4.old_price)}" class="btn btn-primary">
                <i class="bi bi-cart3 me-2"></i> Buy Now
              </a>
            </div>
          </div>
        `;
      }
      else if (category === 'CAT5') {
        html += `
          <div class="mb-4 p-4 rounded border bg-white">
            <div class="section-header">CAT5 - Special Domain</div>
            <div class="text-center my-3 d-flex justify-content-center align-items-center gap-2">
              <i class="bi bi-globe2 text-primary"></i>
              <span class="fw-medium">${domainFullName}</span>
              <span class="text-decoration-line-through text-muted ms-3">LKR ${Number(prices.CAT5.old_price).toFixed(2)}</span>
              <span class="text-danger fw-bold ms-3">LKR ${Number(prices.CAT5.new_price).toFixed(2)}</span>
            </div>
            <div class="text-center text-muted small mt-3">
              <p class="mb-2">You automatically reserve following names</p>
              <ul class="list-unstyled text-start d-inline-block">
                ${reservedSLDs.map(sld => `<li>${baseDomain}.${sld}.lk</li>`).join('')}
              </ul>
            </div>
            <div class="text-center mt-3">
              <a href="${buyNowUrl(domainFullName, prices.CAT5.new_price, 'CAT5', prices.CAT5.old_price)}" class="btn btn-primary">
                <i class="bi bi-cart3 me-2"></i> Buy Now
              </a>
            </div>
          </div>
        `;
      }
      else {
        // Show CAT2 - Top Level Domain Only
        html += `
          <div class="mb-4 p-4 rounded border bg-white">
            <div class="section-header">CAT2 - Top Level Domain Only</div>
            <div class="text-center my-3 d-flex justify-content-center align-items-center gap-2">
              <i class="bi bi-globe2 text-primary"></i>
              <span class="fw-medium">${domainFullName}</span>
              <span class="text-decoration-line-through text-muted ms-3">LKR ${Number(prices.CAT2.old_price).toFixed(2)}</span>
              <span class="text-danger fw-bold ms-3">LKR ${Number(prices.CAT2.new_price).toFixed(2)}</span>
            </div>
            <div class="text-center">
              <a href="${buyNowUrl(domainFullName, prices.CAT2.new_price, 'CAT2', prices.CAT2.old_price)}" class="btn btn-primary">
                <i class="bi bi-cart3 me-2"></i> Buy Now
              </a>
            </div>
          </div>
        `;

        // Show CAT1 - Full Package
        html += `
          <div class="mb-4 p-4 rounded border bg-white">
            <div class="section-header">CAT1 - Full Domain Package</div>
            <div class="text-center my-3 d-flex justify-content-center align-items-center gap-2">
              <i class="bi bi-globe2 text-primary"></i>
              <span class="fw-medium">${domainFullName}</span>
              <span class="text-decoration-line-through text-muted ms-3">LKR ${Number(prices.CAT1.old_price).toFixed(2)}</span>
              <span class="text-danger fw-bold ms-3">LKR ${Number(prices.CAT1.new_price).toFixed(2)}</span>
            </div>
            <div class="text-center text-muted small mt-3">
              <p class="mb-2">You automatically reserve the following names:</p>
              <ul class="list-unstyled text-start d-inline-block">
                ${reservedSLDs.map(sld => `<li>${baseDomain}.${sld}.lk</li>`).join('')}
              </ul>
            </div>
            <div class="text-center mt-3">
              <a href="${buyNowUrl(domainFullName, prices.CAT1.new_price, 'CAT1', prices.CAT1.old_price)}" class="btn btn-primary">
                <i class="bi bi-cart3 me-2"></i> Buy Now
              </a>
            </div>
          </div>
        `;

        // Show CAT3 - Second Level Domains only
		html += `
		  <div class="mb-4 p-4 rounded border bg-white">
			<div class="section-header">Other Options Related to Your Domain Search</div>
			<div class="fw-bold text-primary mb-3">CAT3 - Second Level Domain Only</div>
			${['edu.lk', 'com.lk', 'hotel.lk', 'org.lk', 'web.lk'].map(d => `
			  <div class="mb-3 p-3 border rounded bg-light">
				<div class="d-flex justify-content-between">
				  <span><i class="bi bi-globe2 text-primary"></i> ${baseDomain}.${d}</span>
				  <span class="text-muted text-decoration-line-through">LKR ${Number(prices.CAT3.old_price).toFixed(2)}</span>
				</div>
				<div class="d-flex justify-content-between align-items-center mt-1">
				  <span class="text-danger fw-bold">LKR ${Number(prices.CAT3.new_price).toFixed(2)}</span>
				  <a href="${buyNowUrl(baseDomain + '.' + d, prices.CAT3.new_price, 'CAT3', prices.CAT3.old_price)}" class="btn btn-sm btn-primary">
					<i class="bi bi-cart3 me-1"></i> Buy Now
				  </a>
				</div>
			  </div>
			`).join('')}
		  </div>
		`;


        // Suggested Top Level Domains if any
		if (json.suggestedDomains && json.suggestedDomains.length > 0) {
		  html += `
			<div class="mb-4 p-4 rounded border bg-white">
			  <div class="section-header">Suggested Top Level Domains</div>
			  <div class="fw-bold text-primary mb-3">CAT2 - Top Level Domain Suggestions</div>
			  ${json.suggestedDomains.map(suggestedDomain => `
				<div class="mb-3 p-3 border rounded bg-light">
				  <div class="d-flex justify-content-between">
					<span><i class="bi bi-globe2 text-primary"></i> ${suggestedDomain}</span>
					<span class="text-muted text-decoration-line-through">LKR ${Number(prices.CAT2.old_price).toFixed(2)}</span>
				  </div>
				  <div class="d-flex justify-content-between align-items-center mt-1">
					<span class="text-danger fw-bold">LKR ${Number(prices.CAT2.new_price).toFixed(2)}</span>
					<a href="${buyNowUrl(suggestedDomain, prices.CAT2.new_price, 'CAT2', prices.CAT2.old_price)}" class="btn btn-sm btn-primary">
					  <i class="bi bi-cart3 me-1"></i> Buy Now
					</a>
				  </div>
				</div>
			  `).join('')}
			</div>
		  `;
		}

			  }

      resultContainer.innerHTML = html;

    } catch (error) {
      btn.disabled = false;
      btn.textContent = 'Search';
      resultContainer.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    }
  });
</script>

</body>
</html>
