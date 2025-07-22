<div class="app-content">
  <div class="container-fluid">
    <div class="row">

      <!-- Box 1: Admins -->
      <div class="col-lg-3 col-12 mb-3">
        <div class="small-box text-bg-primary">
          <div class="inner">
            <h3>{{ $adminCount }}</h3>
            <p>Admins</p>
          </div>
          <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"/>
          </svg>
        </div>
      </div>

      <!-- Box 2: All Orders -->
      <div class="col-lg-3 col-12 mb-3">
        <div class="small-box text-bg-success">
          <div class="inner">
            <h3>{{ $allOrdersCount }}</h3>
            <p>All Orders</p>
          </div>
          <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2zm0 4h18v2H3v-2zm0 4h18v2H3v-2z"/>
          </svg>
        </div>
      </div>

      <!-- Box 3: Paid Orders -->
      <div class="col-lg-3 col-12 mb-3">
        <div class="small-box text-bg-info">
          <div class="inner">
            <h3>{{ $paidOrdersCount }}</h3>
            <p>Paid Orders</p>
          </div>
          <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.477 2 2 6.484 2 12s4.477 10 10 10 10-4.484 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
          </svg>
        </div>
      </div>

      <!-- Box 4: Pending Orders -->
      <div class="col-lg-3 col-12 mb-3">
        <div class="small-box text-bg-warning">
          <div class="inner">
            <h3>{{ $pendingOrdersCount }}</h3>
            <p>Pending Orders</p>
          </div>
          <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-.75 5a.75.75 0 011.5 0v4.25l3.25 1.95a.75.75 0 11-.75 1.3L11.25 12V7z"/>
          </svg>
        </div>
      </div>

      <!-- Box 5: Trashed Orders -->
      <div class="col-lg-3 col-12 mb-3">
        <div class="small-box text-bg-danger">
          <div class="inner">
            <h3>{{ $trashedOrdersCount }}</h3>
            <p>Trashed Orders</p>
          </div>
          <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M6 7V5a2 2 0 012-2h8a2 2 0 012 2v2h3a1 1 0 110 2h-1v11a2 2 0 01-2 2H6a2 2 0 01-2-2V9H3a1 1 0 110-2h3zm2-2v2h8V5H8zm-2 4v11h12V9H6z" clip-rule="evenodd"/>
          </svg>
        </div>
      </div>

      <!-- Box 6: All Templates -->
        <div class="col-lg-3 col-12 mb-3">
        <div class="small-box text-bg-secondary">
            <div class="inner">
            <h3>{{ $smsTemplatesCount }}</h3>
            <p>All Templates</p>
            </div>
            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v14a1 1 0 01-1 1H6.414L3 21V4z"/>
            </svg>
        </div>
        </div>

      <!-- Box 7: Success SMS -->
        <div class="col-lg-3 col-12 mb-3">
        <div class="small-box text-bg-success">
            <div class="inner">
            <h3>{{ $successSmsCount }}</h3>
            <p>Success SMS</p>
            </div>
            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 4h16v2H4V4zm0 4h10v2H4V8zm0 4h7v2H4v-2zm14.293 2.293L16 17.586V20h2.414l2.293-2.293a1 1 0 000-1.414l-2-2a1 1 0 00-1.414 0z"/>
            </svg>
        </div>
        </div>

        <!-- Box 8: Failed SMS -->
        <div class="col-lg-3 col-12 mb-3">
        <div class="small-box text-bg-danger">
            <div class="inner">
            <h3>{{ $failedSmsCount }}</h3>
            <p>Failed SMS</p>
            </div>
            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14h2v2h-2v-2zm0-8h2v6h-2V8z"/>
            </svg>
        </div>
        </div>




    </div>
  </div>
</div>
