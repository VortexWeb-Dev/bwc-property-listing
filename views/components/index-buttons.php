<div class="px-6 my-4 flex justify-between">
  <div class="mb-3 mb-lg-0 flex gap-2">
    <div class="flex gap-2 items-center">
      <!-- Filter Dropdown -->
      <div class="dropdown">
        <?php
        $filterLabels = [
          'ALL' => 'All Listings',
          'POCKET' => 'Pocket Listings',
          'DRAFT' => 'Draft',
          'PUBLISHED' => 'Published',
          'LIVE' => 'Live',
          'PENDING' => 'Pending',
          'ARCHIVED' => 'Archived',
          'DUPLICATE' => 'Duplicate',
        ];
        // $currentFilter = $filter ?? 'PUBLISHED'; // Default to PUBLISHED
        // $currentFilterLabel = $filterLabels[$currentFilter] ?? 'Select Filter';
        ?>
        <button class="btn btn-filter btn-outline-primary dropdown-toggle w-100"
          type="button"
          id="filterDropdown"
          data-bs-toggle="dropdown"
          aria-expanded="false"
          style="background-color: white; color: var(--bs-primary); border-color: var(--bs-primary);">
          <!-- Text will be updated by JavaScript -->
          Select Filter
        </button>
        <ul class="dropdown-menu w-100" aria-labelledby="filterDropdown">
          <?php foreach ($filterLabels as $key => $label): ?>
            <li>
              <button
                class="dropdown-item"
                type="button"
                onclick="filterProperties('<?= $key ?>')">
                <?= $label ?>
              </button>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Filter Modal Button -->
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
        <i class="fas fa-filter me-2"></i>Filters
      </button>

      <a href="javascript:void(0)" onclick="clearAllFilters()" id="clearFiltersBtn" class="btn btn-secondary py-1.5 px-4 rounded-md d-none">
        <i class="fas fa-eraser me-2"></i> Clear Filters
      </a>
    </div>
  </div>

  <div class="flex flex-wrap justify-end items-center gap-2">
    <!-- Create Listing Button -->
    <a href="?page=add-property" class="btn btn-primary py-1.5 px-4 rounded-md"><i class="fas fa-plus me-2"></i>Create Listing</a>

    <!-- Bulk Actions Dropdown -->
    <div class="relative">
      <button class="btn btn-secondary py-1.5 px-4 rounded-md bg-secondary text-white dropdown-toggle"
        type="button"
        id="bulkActionsDropdown"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="fas fa-cog me-2"></i>Bulk Actions
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-md absolute w-76 mt-2 border border-gray-300 bg-white text-sm" aria-labelledby="bulkActionsDropdown">
        <li class="admin-only">
          <h6 class="dropdown-header">Transfer</h6>
        </li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="selectAndAddPropertiesToAgentTransfer()"><i class="fas fa-user-tie me-2"></i>Transfer to Agent</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="selectAndAddPropertiesToOwnerTransfer()"><i class="fas fa-user me-2"></i>Transfer to Owner</button></li>
        <li class="admin-only">
          <hr class="dropdown-divider">
        </li>
        <li class="admin-only">
          <h6 class="dropdown-header">Publish</h6>
        </li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish')"><i class="fas fa-bullhorn me-2"></i>Publish All</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish', 'pf')"><i class="fas fa-search me-2"></i>Publish To PF</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish', 'bayut')"><i class="fas fa-building me-2"></i>Publish To Bayut</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish', 'dubizzle')"><i class="fas fa-home me-2"></i>Publish To Dubizzle</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('publish', 'website')"><i class="fas fa-globe me-2"></i>Publish To Website</button></li>
        <li class="admin-only">
          <hr class="dropdown-divider">
        </li>

        <li class="admin-only">
          <h6 class="dropdown-header">Unpublish</h6>
        </li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish')"><i class="fas fa-eye-slash me-2"></i>Unpublish</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish', 'pf')"><i class="fas fa-search me-2"></i>Unpublish from PF</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish', 'bayut')"><i class="fas fa-building me-2"></i>Unpublish from Bayut</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish', 'dubizzle')"><i class="fas fa-home me-2"></i>Unpublish from Dubizzle</button></li>
        <li class="admin-only"><button class="dropdown-item px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('unpublish', 'website')"><i class="fas fa-globe me-2"></i>Unpublish from Website</button></li>
        <li class="admin-only">
          <hr class="dropdown-divider">
        </li>
        <li><button class="dropdown-item text-danger px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('archive')"><i class="fas fa-archive me-2"></i>Archive</button></li>
        <li><button class="dropdown-item text-danger px-4 py-2 w-full text-left truncate" type="button" onclick="handleBulkAction('delete')"><i class="fas fa-trash-alt me-2"></i>Delete</button></li>
      </ul>
    </div>

  </div>
</div>

<script>
   // Add this function to check filter state
   function checkFilterState() {
    updateDropdownText();
    toggleClearButton();
  }

  // Updated function to toggle clear button
  function toggleClearButton() {
    const clearBtn = document.getElementById('clearFiltersBtn');
    
    // Check if there are any keys besides ufCrm5Status
    const hasAdditionalFilters = Object.keys(activeFilters).some(
      key => key !== 'ufCrm5Status'
    );

    clearBtn.classList.toggle('d-none', !hasAdditionalFilters);
  }

  // Modified filterProperties function
  function filterProperties(filterKey) {
    const newFilters = {};

    // Clear existing portal filters when changing status
    delete activeFilters['ufCrm5PfEnable'];
    delete activeFilters['ufCrm5BayutEnable'];
    delete activeFilters['ufCrm5DubizzleEnable'];
    delete activeFilters['ufCrm5WebsiteEnable'];

    if (filterKey === 'ALL') {
      delete activeFilters['ufCrm5Status'];
    } else if (filterKey === 'PF') {
      newFilters['ufCrm5PfEnable'] = 'Y';
    } else {
      newFilters['ufCrm5Status'] = filterKey;
    }

    setFilters(newFilters);
    localStorage.setItem('listingFilter', filterKey);
    checkFilterState();
  }

  // Update your clearAllFilters function
  function clearAllFilters() {
    // Keep only the status filter if it exists
    const statusValue = activeFilters['ufCrm5Status'];
    activeFilters = statusValue ? { 'ufCrm5Status': statusValue } : {};
    setFilters(activeFilters);
    checkFilterState();
  }

  // Update event listener to check filters on load
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize activeFilters from localStorage
    activeFilters = JSON.parse(localStorage.getItem('activeFilters') || '{}');
    checkFilterState();
  });

  function updateDropdownText() {
    const statusKey = encodeURIComponent('ufCrm5Status');
    const pfKey = encodeURIComponent('ufCrm5PfEnable');

    // Determine current filter state
    let currentFilter = 'PUBLISHED'; // Default

    if (activeFilters[pfKey] === 'Y') {
      currentFilter = 'PF';
    } else if (!activeFilters[statusKey] && Object.keys(activeFilters).length === 0) {
      currentFilter = 'ALL';
    } else if (activeFilters[statusKey]) {
      currentFilter = activeFilters[statusKey];
    }

    const filterLabels = {
      'ALL': 'All Listings',
      'PF': 'PF Listings',
      'POCKET': 'Pocket Listings',
      'DRAFT': 'Draft',
      'PUBLISHED': 'Published',
      'LIVE': 'Live',
      'PENDING': 'Pending',
      'ARCHIVED': 'Archived',
      'DUPLICATE': 'Duplicate',
    };

    const button = document.querySelector('.btn.btn-filter');
    button.innerText = filterLabels[currentFilter] || 'Select Filter';
  }

  // Initialize dropdown text on load and after filter changes
  document.addEventListener('DOMContentLoaded', updateDropdownText);
</script>