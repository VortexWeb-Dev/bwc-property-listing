<?php
require __DIR__ . '/crest/settings.php';
require __DIR__ . '/controllers/SpaController.php';
require __DIR__ . '/utils/index.php';

include __DIR__ . '/views/header.php';

$currentUser = fetchCurrentUser();
$currentUserId = $currentUser['ID'];
$isAdmin = isAdmin($currentUserId);

include 'views/components/toast.php';
include 'views/components/topbar.php';

$pages = [
    'properties' => 'views/properties/index.php',
    'add-property' => 'views/properties/add.php',
    'edit-property' => 'views/properties/edit.php',
    'view-property' => 'views/properties/view.php',

    'pocket' => 'views/pocket/index.php',
    'agents' => 'views/agents/index.php',
    'developers' => 'views/developers/index.php',
    'pf-locations' => 'views/pf-locations/index.php',
    'bayut-locations' => 'views/bayut-locations/index.php',
    'settings' => 'views/settings/index.php',
    'reports' => 'views/reports/index.php',
];

$page = isset($_GET['page']) && array_key_exists($_GET['page'], $pages) ? $_GET['page'] : 'properties';

require $pages[$page];

if (!array_key_exists($page, $pages)) {
    header("Location: index.php?page=properties';");
    exit;
}
?>

<script>
    // Store isAdmin in localStorage
    // localStorage.setItem('isAdmin', <?php echo json_encode($isAdmin); ?>);
</script>

<script>
    // Global filter state – holds all active filters
    let activeFilters = JSON.parse(localStorage.getItem('activeFilters')) || {};

    // Initialize with default published filter if empty
    if (Object.keys(activeFilters).length === 0) {
        activeFilters = {
            [encodeURIComponent('ufCrm5Status')]: 'PUBLISHED'
        };
        localStorage.setItem('activeFilters', JSON.stringify(activeFilters));
    }

    function updateFilter(key, value) {
        const encodedKey = encodeURIComponent(key);
        if (value === '' || value === null || value === 'ALL') {
            delete activeFilters[encodedKey];
        } else {
            activeFilters[encodedKey] = value;
        }
        localStorage.setItem('activeFilters', JSON.stringify(activeFilters));
        fetchProperties(currentPage, activeFilters);
        toggleClearFiltersButton();
    }

    function setFilters(newFilters) {
        // Merge filters with proper encoding
        const encodedFilters = Object.fromEntries(
            Object.entries(newFilters).map(([k, v]) => [encodeURIComponent(k), v])
        );

        activeFilters = {
            ...activeFilters,
            ...encodedFilters
        };

        // Clean empty values
        Object.keys(activeFilters).forEach(key => {
            if (!activeFilters[key] || activeFilters[key] === 'ALL') {
                delete activeFilters[key];
            }
        });

        localStorage.setItem('activeFilters', JSON.stringify(activeFilters));
        fetchProperties(currentPage, activeFilters);
        toggleClearFiltersButton();
    }

    function clearAllFilters() {
        // Completely reset filters
        activeFilters = {
            [encodeURIComponent('ufCrm5Status')]: 'PUBLISHED'
        };

        // Clear localStorage properly
        localStorage.setItem('activeFilters', JSON.stringify(activeFilters));
        localStorage.removeItem('listingFilter');

        // Force reset to default state
        fetchProperties(1, activeFilters);
        document.querySelector('#clearFiltersBtn').classList.add('d-none');
        updateDropdownText();
    }

    function toggleClearFiltersButton() {
        const statusKey = encodeURIComponent('ufCrm5Status');
        const hasNonDefaultFilters = Object.keys(activeFilters).length > 1 ||
            (Object.keys(activeFilters).length === 1 && !activeFilters.hasOwnProperty(statusKey));

        document.querySelector('#clearFiltersBtn').classList.toggle('d-none', !hasNonDefaultFilters);
    }
</script>

<?php
include __DIR__ . '/views/footer.php';
?>