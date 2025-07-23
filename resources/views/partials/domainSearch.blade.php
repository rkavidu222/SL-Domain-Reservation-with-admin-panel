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
    body {
      background-color: #f3f6fa;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .card-animate {
      background: #fff;
      border-radius: 1rem;
      padding: 1.5rem;
      box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
      animation: fadeIn 0.3s ease-in-out;
      margin-bottom: 1.5rem;
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

    .result-row {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 0.5rem;
      padding: 0.75rem 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .line1, .line2 {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
      white-space: nowrap;
    }

    .line1 .domain-name {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #0d6efd;
      font-weight: 600;
      font-size: 1rem;
    }

    .line1 .old-price {
      text-decoration: line-through;
      color: #6c757d;
      font-size: 0.95rem;
    }

    .line2 .new-price {
      color: #dc3545;
      font-weight: 700;
      font-size: 1rem;
    }

    .line2 .btn-buy {
      padding: 0.35rem 0.75rem;
      font-size: 0.9rem;
      min-width: 100px;
    }

    .input-group {
      max-width: 600px;
      margin: 0 auto;
      display: flex;
      gap: 0.5rem;
    }

    #domainInput {
      flex-grow: 1;
    }

    #domainExtension {
      max-width: 100px;
      flex-shrink: 0;
    }

    #searchBtn {
      flex-shrink: 0;
      min-width: 100px;
    }

    @media (max-width: 575.98px) {
		  .line1,
		  .line2 {
			flex-direction: column;
			align-items: center;
			justify-content: center;
			gap: 0.25rem;
			white-space: normal;
			text-align: center;
		  }
		  .line2 .btn-buy {
			width: 100%;
			text-align: center;
		  }


		  .input-group {
			flex-direction: column !important;
			gap: 0.5rem;
			max-width: 100%;
		  }


		  #domainInput,
		  #domainExtension,
		  #searchBtn {
			width: 100% !important;
			max-width: 100% !important;
		  }
		}
  </style>
</head>
<body>

<div id="step1" class="my-4">
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
      <div class="mb-4 d-flex justify-content-center">
        <div class="input-group w-100">
          <input type="text" id="domainInput" class="form-control" placeholder="Enter your domain name" />
          <select id="domainExtension" class="form-select">
            <option value=".lk">.lk</option>
            <option value=".com.lk">.com.lk</option>
            <option value=".org.lk">.org.lk</option>
            <option value=".edu.lk">.edu.lk</option>
            <option value=".hotel.lk">.hotel.lk</option>
            <option value=".web.lk">.web.lk</option>
          </select>
          <button id="searchBtn" class="btn btn-primary">Search</button>
        </div>
      </div>
      <small class="text-muted">Please enter a domain name without "www", symbols, or spaces</small>
    </div>

    <div id="resultContainer"></div>
  </div>
</div>

<script>
  const prices = @json($allPrices);

  function buyNowUrl(domainName, newPrice, category, oldPrice = 0) {
    return "{{ route('domain.contact.info') }}" +
      `?domain_name=${encodeURIComponent(domainName)}` +
      `&price=${encodeURIComponent(newPrice)}` +
      `&category=${encodeURIComponent(category)}` +
      `&old_price=${encodeURIComponent(oldPrice)}`;
  }

  function getBaseDomain(input) {
    const knownExts = ['.lk', '.com.lk', '.org.lk', '.edu.lk', '.hotel.lk', '.web.lk'];
    let cleaned = input.trim().toLowerCase();

    if (cleaned.startsWith('www.')) cleaned = cleaned.slice(4);
    cleaned = cleaned.replace(/^\.+|\.+$/g, '');

    for (const ext of knownExts) {
      if (cleaned.endsWith(ext)) {
        cleaned = cleaned.slice(0, -ext.length);
        break;
      }
    }

    cleaned = cleaned.replace(/\.(com|org|hotel|edu|web)$/, '');
    cleaned = cleaned.replace(/[^a-z0-9\-]/g, '');

    return cleaned;
  }

  function renderRow(domain, cat) {
    const oldPrice = prices[cat]?.old_price || 0;
    const newPrice = prices[cat]?.new_price || 0;

    return `
      <div class="result-row">
        <div class="line1">
          <span class="domain-name"><i class="bi bi-globe2"></i> ${domain}</span>
          <span class="old-price">LKR ${Number(oldPrice).toFixed(2)}</span>
        </div>
        <div class="line2">
          <span class="new-price">LKR ${Number(newPrice).toFixed(2)}</span>
          <a href="${buyNowUrl(domain, newPrice, cat, oldPrice)}" class="btn btn-sm btn-primary btn-buy">
            <i class="bi bi-cart3 me-1"></i> Buy Now
          </a>
        </div>
      </div>
    `;
  }

  document.getElementById('searchBtn').addEventListener('click', async function () {
    const domainInputRaw = document.getElementById('domainInput').value.trim().toLowerCase();
    const extension = document.getElementById('domainExtension').value.toLowerCase();
    const resultContainer = document.getElementById('resultContainer');
    const btn = this;

    if (!domainInputRaw || domainInputRaw.length < 2) {
      alert('Please enter at least 2 characters. Single-letter domains are not allowed.');
      return;
    }

    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...`;
    resultContainer.innerHTML = '';

    try {
      const baseDomain = getBaseDomain(domainInputRaw);
      const fullDomain = `${baseDomain}${extension}`;

      const response = await fetch("{{ route('domain.search.api') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ domainname: fullDomain })
      });

      const json = await response.json();
      btn.disabled = false;
      btn.textContent = 'Search';

      const apiData = json.data;
      const category = json.category;
      const message = (apiData?.Message || '').toLowerCase();
      const isAvailable = message.includes('available');

      const reservedSLDs = ['edu', 'com', 'hotel', 'org', 'web'];
      const cat3Domains = ['.edu.lk', '.com.lk', '.hotel.lk', '.org.lk', '.web.lk'];

      let html = `
        <div class="alert ${isAvailable ? 'alert-success' : 'alert-danger'}">
          <strong>${fullDomain}</strong> Domain is ${isAvailable ? 'available' : 'not available'} for registration.
        </div>
      `;

      if (!isAvailable) {
        resultContainer.innerHTML = html;
        return;
      }

      if (cat3Domains.includes(extension)) {
        html += `
          <div class="card-animate">
            <div class="section-header">CAT3 - Second Level Domain Only</div>
            ${renderRow(fullDomain, 'CAT3')}
          </div>
        `;
      } else if (category === 'CAT4') {
        html += `
          <div class="card-animate">
            <div class="section-header">CAT4 - Premium Domain</div>
            ${renderRow(fullDomain, 'CAT4')}
            <div class="text-center text-muted small mt-3">
              <p class="mb-2">You automatically reserve the following names</p>
              <ul class="list-unstyled text-center d-inline-block">
                ${reservedSLDs.map(sld => `<li>${baseDomain}.${sld}.lk</li>`).join('')}
              </ul>
            </div>
          </div>
        `;
      } else if (category === 'CAT5') {
        html += `
          <div class="card-animate">
            <div class="section-header">CAT5 - Special Domain</div>
            ${renderRow(fullDomain, 'CAT5')}
            <div class="text-center text-muted small mt-3">
              <p class="mb-2">You automatically reserve the following names</p>
              <ul class="list-unstyled text-center d-inline-block">
                ${reservedSLDs.map(sld => `<li>${baseDomain}.${sld}.lk</li>`).join('')}
              </ul>
            </div>
          </div>
        `;
      } else {
        html += `
          <div class="card-animate">
            <div class="section-header">CAT2 - Top Level Domain Only</div>
            ${renderRow(fullDomain, 'CAT2')}
          </div>
          <div class="card-animate">
            <div class="section-header">CAT1 - Full Package</div>
            ${renderRow(fullDomain, 'CAT1')}
          </div>
          <div class="card-animate">
            <div class="section-header">Other Options - Second Level Domains</div>
            ${cat3Domains.map(ext => renderRow(`${baseDomain}${ext}`, 'CAT3')).join('')}
          </div>
        `;

        if (json.suggestedDomains?.length) {
          html += `
            <div class="card-animate">
              <div class="section-header">Suggested Domains</div>
              ${json.suggestedDomains.map(s => renderRow(s, 'CAT2')).join('')}
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

  // Input Sanitizer
  document.getElementById('domainInput').addEventListener('input', function () {
    this.value = this.value
      .replace(/[^a-z0-9\-]/gi, '')
      .replace(/--+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '');
  });
</script>

</body>
</html>
