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
    let activeFilters = {};

    // Call this function whenever you want to update (or add/remove) a single filter.
    function updateFilter(key, value) {
        // Remove filter if value is empty, null, or 'ALL'
        if (value === '' || value === null || value === 'ALL') {
            delete activeFilters[key];
        } else {
            activeFilters[key] = value;
        }
        // Call fetchProperties with all active filters
        fetchProperties(currentPage, activeFilters);
        // Show/hide the clear filters button based on active filters
        if (Object.keys(activeFilters).length > 0) {
            document.querySelector('#clearFiltersBtn').classList.remove('d-none');
        } else {
            document.querySelector('#clearFiltersBtn').classList.add('d-none');
        }
    }

    // Merge multiple filters at once (used by the modal)
    function setFilters(newFilters) {
        activeFilters = Object.assign({}, activeFilters, newFilters);
        // Remove any keys that have empty values
        for (let key in activeFilters) {
            if (activeFilters[key] === '' || activeFilters[key] === null || activeFilters[key] === 'ALL') {
                delete activeFilters[key];
            }
        }
        fetchProperties(currentPage, activeFilters);
        if (Object.keys(activeFilters).length > 0) {
            document.querySelector('#clearFiltersBtn').classList.remove('d-none');
        } else {
            document.querySelector('#clearFiltersBtn').classList.add('d-none');
        }
    }

    // Clear all active filters
    function clearAllFilters() {
        activeFilters = {};
        fetchProperties(currentPage);
        document.querySelector('#clearFiltersBtn').classList.add('d-none');
        // Optionally: reset UI elements (dropdowns, form fields, etc.)
    }
</script>

<?php
include __DIR__ . '/views/footer.php';
?>