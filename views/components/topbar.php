<?php

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'properties';
}

?>
<!-- Topbar -->
<div class="w-full border-b py-3 px-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800"><a href="?page=properties">Property Listing</a></h1>
    <div class="flex flex-wrap gap-2">
        <span onclick="filterProperties('ALL')" class="cursor-pointer hover:shadow-xl hover:scale-95 transition-all text-xs font-semibold py-1 px-3 bg-blue-600/80 text-white rounded-full" title="Total Listings" id="totalCount">Total: 0</span>
        <span onclick="filterProperties('PUBLISHED')" class="cursor-pointer hover:shadow-xl hover:scale-95 transition-all text-xs font-semibold py-1 px-3 bg-green-600/80 text-white rounded-full" title="Published Listings" id="publishedCount">Published: 0</span>
        <span onclick="filterProperties('LIVE')" class="cursor-pointer hover:shadow-xl hover:scale-95 transition-all text-xs font-semibold py-1 px-3 bg-cyan-600/80 text-white rounded-full" title="Live Listings" id="liveCount">Live: 0</span>
        <span onclick="filterProperties('DRAFT')" class="cursor-pointer hover:shadow-xl hover:scale-95 transition-all text-xs font-semibold py-1 px-3 bg-yellow-600/80 text-white rounded-full" title="Draft Listings" id="draftCount">Draft: 0</span>
        <span onclick="filterProperties('ARCHIVED')" class="cursor-pointer hover:shadow-xl hover:scale-95 transition-all text-xs font-semibold py-1 px-3 bg-gray-600/80 text-white rounded-full" title="Archived Listings" id="archivedCount">Archived: 0</span>
        <span onclick="filterProperties('PF')" class="cursor-pointer hover:shadow-xl hover:scale-95 transition-all text-xs font-semibold py-1 px-3 bg-purple-600/80 text-white rounded-full" title="PF Enabled" id="pfCount">PF: 0</span>
        <span onclick="filterProperties('POCKET')" class="cursor-pointer hover:shadow-xl hover:scale-95 transition-all text-xs font-semibold py-1 px-3 bg-red-600/80 text-white rounded-full" title="Pocket Listings" id="pocketCount">Pocket: 0</span>
    </div>

    <div>
        <!-- Dropdown -->
        <div class="dropdown">
            <button class="btn btn-lg btn-light flex items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-gear"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item <?= $page == 'properties' ? 'active' : '' ?>" href="?page=properties"><i class="fa fa-home me-2"></i> Properties</a></li>
                <li><a class="dropdown-item <?= $page == 'pocket' ? 'active' : '' ?>" href="?page=pocket"><i class="fa fa-home me-2"></i> Pocket Listings</a></li>
                <li><a class="dropdown-item <?= $page == 'agents' ? 'active' : '' ?>" href="?page=agents"><i class="fa fa-user-group me-2"></i> Agents</a></li>
                <li><a class="dropdown-item <?= $page == 'pf-locations' ? 'active' : '' ?>" href="?page=pf-locations"><i class="fa fa-map me-2"></i> PF Locations</a></li>
                <li><a class="dropdown-item <?= $page == 'bayut-locations' ? 'active' : '' ?>" href="?page=bayut-locations"><i class="fa fa-map-pin me-2"></i> Bayut Locations</a></li>
                <li><a class="dropdown-item <?= $page == 'developers' ? 'active' : '' ?>" href="?page=developers"><i class="fa fa-helmet-safety me-2"></i> Developers</a></li>
                <li><a class="dropdown-item <?= $page == 'reports' ? 'active' : '' ?>" href="?page=reports"><i class="fa fa-chart-line me-2"></i> Reports</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#"><i class="fa fa-sign-out-alt me-2"></i> Exit</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
    async function fetchAllProperties() {
        let allProperties = [];
        let start = 0; // Start from 0
        let hasNextPage = true;

        while (hasNextPage) {
            const apiUrl = `https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.list?entityTypeId=1036&select[]=id&select[]=ufCrm5Status&select[]=ufCrm5PfEnable&select[]=ufCrm5BayutEnable&select[]=ufCrm5DubizzleEnable&select[]=ufCrm5WebsiteEnable&start=${start}`;

            try {
                const response = await fetch(apiUrl, {
                    method: 'GET'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();

                // Append new items to the list
                allProperties = allProperties.concat(data.result.items);

                // Update the next start position
                if (data.next !== undefined) {
                    start = data.next;
                } else {
                    hasNextPage = false; // No more pages
                }
            } catch (error) {
                console.error('Error fetching properties:', error);
                hasNextPage = false; // Stop on error
            }
        }

        return allProperties;
    }

    // Usage example:
    fetchAllProperties().then((properties) => {
        console.log("Total Properties Fetched:", properties.length);
        console.log(properties);
        let totalCount = properties.length;
        let publishedCount = 0;
        let liveCount = 0;
        let draftCount = 0;
        let archivedCount = 0;
        let pfCount = 0;
        let pocketCount = 0;

        properties.forEach((property) => {
            if (property.ufCrm5Status === "PUBLISHED") publishedCount++;
            if (property.ufCrm5Status === "LIVE") liveCount++;
            if (property.ufCrm5Status === "DRAFT") draftCount++;
            if (property.ufCrm5Status === "ARCHIVED") archivedCount++;
            if (property.ufCrm5PfEnable === "Y" && property.ufCrm5Status === "PUBLISHED") pfCount++;
            if (property.ufCrm5Status === "POCKET") pocketCount++;
        })
        document.getElementById('totalCount').textContent = "Total: " + totalCount;
        document.getElementById('publishedCount').textContent = "Published: " + publishedCount;
        document.getElementById('liveCount').textContent = "Live: " + liveCount;
        document.getElementById('draftCount').textContent = "Draft: " + draftCount;
        document.getElementById('archivedCount').textContent = "Archived: " + archivedCount;
        document.getElementById('pfCount').textContent = "PF: " + pfCount;
        document.getElementById('pocketCount').textContent = "Pocket: " + pocketCount;
    });

    document.querySelectorAll('.dropdown-item').forEach(item => {
        if (item.innerText === document.querySelector('.btn').innerText) {
            item.classList.add('active');
        }
    });

    function filterProperties(filterKey) {
        localStorage.setItem('listingFilter', filterKey);

        const filterLabels = {
            'ALL': 'All Listings',
            'PUBLISHED': 'Published',
            'POCKET': 'Pocket Listings',
            'DRAFT': 'Draft',
            'ARCHIVED': 'Archived',
            'PF': 'PF',
            'POCKET': 'Pocket',
        };

        document.querySelector('.btn.btn-filter').innerText = filterLabels[filterKey] || 'Select Filter';

        document.querySelectorAll('.dropdown-item.filter-item').forEach(item => {
            if (item.innerText === filterLabels[filterKey]) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        if (filterKey === 'ALL') {
            fetchProperties(currentPage);
            return;
        }

        if(filterKey === 'PF') {
            fetchProperties(currentPage, {
                '!ufCrm5PfEnable': 'N'
            });
            return;
        }

        fetchProperties(currentPage, {
            'ufCrm5Status': filterKey
        });

        document.querySelector('#clearFiltersBtn').classList.remove('d-none');
    }
</script>