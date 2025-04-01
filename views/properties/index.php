<?php include 'views/components/index-buttons.php'; ?>

<div class="mx-auto mb-8 px-4 overflow-hidden">
    <!-- Loading -->
    <?php include_once('views/components/loading.php'); ?>

    <div id="property-table">
        <div class="table-container">
            <table class="min-w-full divide-y divide-gray-200 table-responsive">
                <thead>
                    <tr>
                        <th scope="col" class="px-4 py-3 text-start">
                            <label for="hs-at-with-checkboxes-main" class="flex">
                                <input id="select-all" onclick="toggleCheckboxes(this)" type="checkbox" class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" id="hs-at-with-checkboxes-main">
                                <span class="sr-only">Checkbox</span>
                                <span class="ml-2 text-xs font-medium text-gray-500" id="selected-count"></span>
                            </label>
                        </th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Actions</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase max-w-[200px]">Property Details</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Unit Type</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Size</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Unit Status</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Listing Agent</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Listing Owner</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase min-w-[200px]">Published Portals</th>
                        <th scope="col" class="px-3 py-3 text-start text-xs font-medium text-gray-500 uppercase">Created On</th>
                    </tr>
                </thead>
                <tbody id="property-list" class="divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
    <?php include 'views/components/pagination.php'; ?>
</div>


<!-- Modals -->
<?php include 'views/modals/filter.php'; ?>
<?php include 'views/modals/refresh-listing.php'; ?>
<?php
if ($isAdmin) {
    include 'views/modals/transfer-to-agent.php';
    include 'views/modals/transfer-to-owner.php';
}
?>
</div>


<script>
    let currentPage = 1;
    const pageSize = 50;
    let totalPages = 0;

    const isAdmin = <?php echo json_encode($isAdmin); ?>;

    async function fetchProperties(page = 1, filters = null) {
        const baseUrl = 'https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r';
        const entityTypeId = 1036;
        const fields = [
            'id', 'ufCrm5ReferenceNumber', 'ufCrm5OfferingType', 'ufCrm5PropertyType', 'ufCrm5Price', 'ufCrm5TitleEn', 'ufCrm5DescriptionEn', 'ufCrm5Size', 'ufCrm5Bedroom', 'ufCrm5Bathroom', 'ufCrm5PhotoLinks', 'ufCrm5AgentName', 'ufCrm5City', 'ufCrm5Community', 'ufCrm5SubCommunity', 'ufCrm5Tower', 'ufCrm5PfEnable', 'ufCrm5BayutEnable', 'ufCrm5DubizzleEnable', 'ufCrm5WebsiteEnable', 'ufCrm5ListingOwner', 'ufCrm5Status', 'ufCrm5RentalPeriod', 'createdTime'
        ];
        const orderBy = {
            id: 'desc'
        };
        const start = (page - 1) * pageSize;

        function buildApiUrl(baseUrl, entityTypeId, fields, orderBy, start, filters) {
            const selectParams = fields.map((field, index) => `select[${index}]=${field}`).join('&');

            const orderParams = Object.entries(orderBy)
                .map(([key, value]) => `order[${key}]=${value}`)
                .join('&');

            if (filters) {
                const filterParams = Object.entries(filters)
                    .map(([key, value]) => `filter[${key}]=${value}`)
                    .join('&');

                return `${baseUrl}/crm.item.list?entityTypeId=${entityTypeId}&${selectParams}&${orderParams}&start=${start}&${filterParams}`;
            }

            return `${baseUrl}/crm.item.list?entityTypeId=${entityTypeId}&${selectParams}&${orderParams}&start=${start}`;
        }

        // Generate the API URL
        const apiUrl = buildApiUrl(baseUrl, entityTypeId, fields, orderBy, start, filters);

        const loading = document.getElementById('loading');
        const propertyTable = document.getElementById('property-table');
        const propertyList = document.getElementById('property-list');
        const pagination = document.getElementById('pagination');
        const prevPage = document.getElementById('prevPage');
        const nextPage = document.getElementById('nextPage');
        const pageInfo = document.getElementById('pageInfo');

        try {
            loading.classList.remove('hidden');
            propertyTable.classList.add('hidden');
            pagination.classList.add('hidden');


            const response = await fetch(apiUrl, {
                method: 'GET'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            const properties = data.result?.items || [];
            const totalCount = data.total || 0;

            totalPages = Math.ceil(totalCount / pageSize);

            prevPage.disabled = page === 1;
            nextPage.disabled = page === totalPages || totalPages === 0;
            pageInfo.textContent = `Page ${page} of ${totalPages}`;

            propertyList.innerHTML = properties
                .map(
                    (property) => `
                <tr>
                    <td class="size-sm whitespace-nowrap">
                        <div class="ps-6 py-3">
                            <label for="hs-at-with-checkboxes-1" class="flex">
                            <input type="checkbox" name="property_ids[]" value="${property.id}" class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" id="hs-at-with-checkboxes-1">
                            <span class="sr-only">Checkbox</span>
                            </label>
                        </div>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-transparent dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu shadow absolute z-10" style="max-height: 50vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #6B7280 #f9fafb; font-size:medium;">
                                <li><a class="dropdown-item" href="?page=edit-property&id=${property.id}"><i class="fa-solid fa-edit me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="?page=view-property&id=${property.id}"><i class="fa-solid fa-eye me-2"></i>View Details</a></li>
                                <li><button class="dropdown-item" onclick="handleAction('duplicate', ${property.id})"><i class="fa-solid fa-copy me-2"></i>Duplicate Listing</button></li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#referenceModal" data-property-id="${property.id}" data-reference="${property.ufCrm5ReferenceNumber}">
                                        <i class="fa-solid fa-sync me-2"></i>Refresh Listing
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" target="_blank" href="download-jpg.php?type=agent&id=${property.id}"><i class="fa-solid fa-image me-2"></i>Download JPG as Listing Agent</a></li>
                                <li><a class="dropdown-item" target="_blank" href="download-pdf.php?type=logged&id=${property.id}"><i class="fa-solid fa-print me-2"></i>Download PDF as Logged-In Agent</a></li>
                                <li><a class="dropdown-item" target="_blank" href="download-pdf.php?type=agent&id=${property.id}"><i class="fa-solid fa-print me-2"></i>Download PDF as Listing Agent</a></li>
                                <li><a class="dropdown-item" target="_blank" href="download-pdf.php?type=owner&id=${property.id}"><i class="fa-solid fa-print me-2"></i>Download PDF as Listing Owner</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="downloadImages(${property.id}, event)"><i class="fa-solid fa-folder me-2"></i>Download Images (Zip)</a></li>
                                ${isAdmin ? `
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item" onclick="handleAction('publish', ${property.id})"><i class="fa-solid fa-bullhorn me-2"></i>Publish to all</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('publish', ${property.id}, 'pf')"><i class="fa-solid fa-search me-2"></i>Publish to PF</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('publish', ${property.id}, 'bayut')"><i class="fa-solid fa-building me-2"></i>Publish to Bayut</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('publish', ${property.id}, 'dubizzle')"><i class="fa-solid fa-home me-2"></i>Publish to Dubizzle</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('publish', ${property.id}, 'website')"><i class="fa-solid fa-globe me-2"></i>Publish to Website</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('live', ${property.id})"><i class="fa-solid fa-arrow-trend-up me-2"></i>Make Live</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item" onclick="handleAction('unpublish', ${property.id})"><i class="fa-solid fa-archive me-2"></i>Unpublish from all</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('unpublish', ${property.id}, 'pf')"><i class="fa-solid fa-search me-2"></i>Unpublish from PF</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('unpublish', ${property.id}, 'bayut')"><i class="fa-solid fa-building me-2"></i>Unpublish from Bayut</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('unpublish', ${property.id}, 'dubizzle')"><i class="fa-solid fa-home me-2"></i>Unpublish from Dubizzle</button></li>
                                <li><button class="dropdown-item" onclick="handleAction('unpublish', ${property.id}, 'website')"><i class="fa-solid fa-globe me-2"></i>Unpublish from Website</button></li>
                                ` : ''}
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-danger" onclick="handleAction('archive', ${property.id})"><i class="fa-solid fa-archive me-2"></i>Archive</button></li>
                                <li><button class="dropdown-item text-danger" onclick="handleAction('delete', ${property.id})"><i class="fa-solid fa-trash me-2"></i>Delete</button></li>
                            </ul>
                        </div>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800 text-wrap">${property.ufCrm5ReferenceNumber || 'N/A'}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <div class="flex">
                            <img class="w-20 h-20 rounded object-cover mr-4" src="${property.ufCrm5PhotoLinks[0] || 'https://placehold.jp/150x150.png'}" alt="${property.ufCrm5TitleEn || 'N/A'}">
                            <div class="text-sm">
                                <a href="?page=view-property&id=${property.id}" class="text-gray-800 font-semibold hover:underline hover:text-blue-600 text-decoration-none">${property.ufCrm5TitleEn || 'N/A'}</a>
                                <p class="text-gray-400 text-wrap max-w-full truncate">${property.ufCrm5DescriptionEn.slice(0, 60) + '...' || 'N/A'}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-sm text-muted" title="Bathrooms"><i class="fa-solid fa-bath mr-1"></i>${property.ufCrm5Bathroom || 'N/A'}</span>
                            <span class="text-sm text-muted" title="Bedrooms"><i class="fa-solid fa-bed mr-1"></i>${property.ufCrm5Bedroom === 0 ? 'Studio' : property.ufCrm5Bedroom === 11 ? '10+' : property.ufCrm5Bedroom || 'N/A'}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-sm text-muted" title="Bathrooms"><i class="fa-solid fa-ruler-combined mr-1"></i>${property.ufCrm5Size + ' sqft' || 'N/A'}</span>
                            <span class="text-sm text-muted" title="Bedrooms"><i class="fa-solid fa-ruler-horizontal mr-1"></i>${sqftToSqm(property.ufCrm5Size) + ' sqm' || 'N/A'}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        ${
                            property.ufCrm5Price 
                                ? `${formatPrice(property.ufCrm5Price)}${property.ufCrm5OfferingType === 'RR' || property.ufCrm5OfferingType === 'CR' 
                                    ? `/${property.ufCrm5RentalPeriod === 'Y' ? 'Year' : property.ufCrm5RentalPeriod === 'M' ? 'Month' : property.ufCrm5RentalPeriod === 'W' ? 'Week' : property.ufCrm5RentalPeriod === 'D' ? 'Day' : ''} - Rent`
                                    : (property.ufCrm5OfferingType === 'CS' || property.ufCrm5OfferingType === 'RS' ? ' - Sale' : '')}`
                                : ''
                        }
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        ${getStatusBadge(property.ufCrm5Status)}
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <p>
                            ${[
                                property.ufCrm5City,
                                property.ufCrm5Community,
                            ]
                            .filter(Boolean)
                            .join(' - ') || ''}
                        </p>
                        <p>
                            ${[
                                property.ufCrm5SubCommunity,
                                property.ufCrm5Tower
                            ]
                            .filter(Boolean)
                            .join(' - ') || ''}
                        </p>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <p class="">${property.ufCrm5AgentName || ''}</p> 
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <p class="">${property.ufCrm5ListingOwner || ''}</p> 
                    </td>
                   <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <div class="flex gap-1">
                            ${property.ufCrm5PfEnable === "Y" ? '<img class="w-8 h-8 rounded-full object-cover" src="assets/images/pf.png" alt="Property Finder" title="Property Finder">' : ''}
                            ${property.ufCrm5BayutEnable === "Y" ? '<img class="w-8 h-8 rounded-full object-cover" src="assets/images/bayut.png" alt="Bayut" title="Bayut">' : ''}
                            ${property.ufCrm5DubizzleEnable === "Y" ? '<img class="w-8 h-8 rounded-full object-cover" src="assets/images/dubizzle.png" alt="Dubizzle" title="Dubizzle">' : ''}
                            ${property.ufCrm5WebsiteEnable === "Y" ? '<img class="w-8 h-8 rounded-full object-cover" src="assets/images/company-logo.png" alt="BWC" title="BWC">' : ''}
                        </div>
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-xs font-medium text-gray-800">
                        <p class="">${formatDate(property.createdTime) || ''}</p> 
                    </td>

                </tr>`
                )
                .join('');

            return properties;
        } catch (error) {
            console.error('Error fetching properties:', error);
            return [];
        } finally {
            loading.classList.add('hidden');
            propertyTable.classList.remove('hidden');
            pagination.classList.remove('hidden');

        }
    }

    function changePage(direction) {
        if (direction === 'prev' && currentPage > 1) {
            currentPage--;
        } else if (direction === 'next' && currentPage < totalPages) {
            currentPage++;
        }
        fetchProperties(currentPage, activeFilters);
    }

    function formatPrice(amount, locale = 'en-US', currency = 'AED') {
        if (isNaN(amount)) {
            return 'Invalid amount';
        }

        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    function getStatusBadge(status) {
        switch (status) {
            case 'PUBLISHED':
                return '<span class="inline-flex items-center gap-x-1.5 py-1.5 px-2 border rounded-full text-xs font-medium bg-green-50 text-green-800">Published</span>';
            case 'UNPUBLISHED':
                return '<span class="inline-flex items-center gap-x-1.5 py-1.5 px-2 border rounded-full text-xs font-medium bg-red-50 text-red-800">Unpublished</span>';
            case 'LIVE':
                return '<span class="inline-flex items-center gap-x-1.5 py-1.5 px-2 border rounded-full text-xs font-medium bg-blue-50 text-blue-800">Live</span>';
            case 'DRAFT':
                return '<span class="inline-flex items-center gap-x-1.5 py-1.5 px-2 border rounded-full text-xs font-medium bg-gray-50 text-gray-800">Draft</span>';
            case 'ARCHIVED':
                return '<span class="inline-flex items-center gap-x-1.5 py-1.5 px-2 border rounded-full text-xs font-medium bg-gray-50 text-gray-800">Archived</span>';
            default:
                return '<span class="inline-flex items-center gap-x-1.5 py-1.5 px-2 border rounded-full text-xs font-medium bg-gray-50 text-gray-800">' + status + '</span>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Load persisted filters
        const savedFilters = JSON.parse(localStorage.getItem('activeFilters')) || {};

        // Apply default published filter if empty
        if (!Object.keys(savedFilters).length) {
            savedFilters[encodeURIComponent('ufCrm5Status')] = 'PUBLISHED';
            localStorage.setItem('activeFilters', JSON.stringify(savedFilters));
        }

        fetchProperties(currentPage, savedFilters);
    });
</script>