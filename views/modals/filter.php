<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-white rounded-lg shadow-lg">

            <div class="modal-header px-6 py-4 border-b">
                <h5 class="text-xl font-semibold" id="filterModalLabel">Filters</h5>
                <button type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none" data-bs-dismiss="modal" aria-label="Close">
                    X
                </button>
            </div>

            <div class="modal-body px-6 py-4">
                <form id="filterForm" method="GET" action="index.php" onsubmit="prepareFilters(event);">
                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div>
                            <label for="city" class="block text-sm font-medium mb-2">City</label>
                            <input type="text" id="city" name="city" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="<?= isset($_GET['city']) ? htmlspecialchars($_GET['city']) : 'Search City ...' ?>" />
                            <?php include './views/modals/search-city-popup.php' ?>
                        </div>
                        <div>
                            <label for="community" class="block text-sm font-medium mb-2">Community</label>
                            <input type="text" id="community" name="community" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="<?= isset($_GET['community']) ? htmlspecialchars($_GET['community']) : 'Search Community ...' ?>" />
                            <?php include './views/modals/search-community-popup.php' ?>
                        </div>
                        <div>
                            <label for="subCommunity" class="block text-sm font-medium mb-2">Sub Community</label>
                            <input type="text" id="subCommunity" name="subCommunity" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="<?= isset($_GET['subCommunity']) ? htmlspecialchars($_GET['subCommunity']) : 'Search Sub Community ...' ?>" />
                            <?php include './views/modals/search-subCommunity-popup.php' ?>
                        </div>
                        <div>
                            <label for="building" class="block text-sm font-medium mb-2">Building</label>
                            <input type="text" id="building" name="building" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="<?= isset($_GET['building']) ? htmlspecialchars($_GET['building']) : 'Search Building ...' ?>" />
                            <?php include './views/modals/search-building-popup.php' ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div>
                            <label for="reference" class="block text-sm font-medium mb-2">Ref. ID</label>
                            <input type="text" id="reference" name="reference" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="<?= isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : '' ?>">
                        </div>
                        <div>
                            <label for="permit" class="block text-sm font-medium mb-2">Permit # or DMTC #</label>
                            <input type="text" id="permit" name="permit" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        </div>
                        <div>
                            <label for="listing_title" class="block text-sm font-medium mb-2">Listing Title</label>
                            <input type="text" id="listing_title" name="listing_title" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="<?= isset($_GET['listing_title']) ? htmlspecialchars($_GET['listing_title']) : '' ?>">
                        </div>
                        <div>
                            <label for="property_type" class="block text-sm font-medium mb-2">Property Type</label>
                            <select id="property_type" name="property_type" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Please select</option>
                                <option value="AP">Apartment / Flat</option>
                                <option value="TH">Townhouse</option>
                                <option value="VH">Villa / House</option>
                                <option value="PH">Penthouse</option>
                                <option value="LP">Residential Land</option>
                                <option value="FF">Full Floor</option>
                                <option value="BU">Bulk Units</option>
                                <option value="CD">Compound</option>
                                <option value="DX">Duplex</option>
                                <option value="FA">Factory</option>
                                <option value="FA">Farm</option>
                                <option value="HA">Hotel Apartment</option>
                                <option value="HF">Half Floor</option>
                                <option value="LC">Labor Camp</option>
                                <option value="LP">Land / Plot</option>
                                <option value="OF">Office Space</option>
                                <option value="OF">Business Centre</option>
                                <option value="RE">Retail</option>
                                <option value="RE">Restaurant</option>
                                <option value="SA">Staff Accommodation</option>
                                <option value="WB">Whole Building</option>
                                <option value="SH">Shop</option>
                                <option value="SR">Show Room</option>
                                <option value="OF">Co-working Space</option>
                                <option value="WH">Storage</option>
                                <option value="WH">Warehouse</option>
                                <option value="LP">Commercial Land</option>
                                <option value="FF">Commercial Floor</option>
                                <option value="WB">Commercial Building</option>
                                <option value="FF">Residential Floor</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div>
                            <label for="portal" class="block text-sm font-medium mb-2">Portal</label>
                            <select name="portal" id="portal" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Please select</option>
                                <option value="PF">Property Finder</option>
                                <option value="BAYUT">Bayut</option>
                                <option value="DUBIZZLE">Dubizzle</option>
                                <option value="WEBSITE">Website</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium mb-2">Status</label>
                            <select name="status" id="status" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Please select</option>
                                <option value="PUBLISHED">Published</option>
                                <option value="UNPUBLISHED">Unpublished</option>
                                <option value="LIVE">Live</option>
                                <option value="DRAFT">Draft</option>
                                <option value="ARCHIVED">Archived</option>
                                <option value="POCKET">Pocket</option>
                            </select>
                        </div>
                        <div>
                            <label for="saleRent" class="block text-sm font-medium mb-2">Sale/Rent</label>
                            <select name="saleRent" id="saleRent" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Please select</option>
                                <option value="RS">Sale</option>
                                <option value="RR">Rent</option>
                            </select>
                        </div>
                        <div>
                            <label for="listing_agent" class="block text-sm font-medium mb-2">Listing Agent</label>
                            <select id="listing_agent" name="listing_agent" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Please select</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div>
                            <label for="listing_owner" class="block text-sm font-medium mb-2">Listing Owner</label>
                            <select id="listing_owner" name="listing_owner" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Please select</option>
                            </select>
                        </div>
                        <div>
                            <label for="landlordEmail" class="block text-sm font-medium mb-2">Landlord Email</label>
                            <input type="email" id="landlordEmail" name="landlordEmail" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="<?= isset($_GET['landlordEmail']) ? htmlspecialchars($_GET['landlordEmail']) : '' ?>">
                        </div>
                        <div>
                            <label for="landlordPhone" class="block text-sm font-medium mb-2">Landlord Phone</label>
                            <input type="text" id="landlordPhone" name="landlordPhone" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="<?= isset($_GET['landlordPhone']) ? htmlspecialchars($_GET['landlordPhone']) : '' ?>">
                        </div>
                        <div>
                            <label for="bedrooms" class="block text-sm font-medium mb-2">Bedrooms</label>
                            <select id="bedrooms" name="bedrooms" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Please select</option>
                                <option value="0">Studio</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">10+</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div class="max-w-sm">
                            <label for="bathrooms" class="block text-sm font-medium mb-2">No. of Bathrooms</label>
                            <input type="number" id="bathrooms" name="bathrooms" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium mb-2">Price</label>
                            <input type="number" step="0.01" id="price" name="price" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        </div>
                        <div>
                            <label for="min_price" class="block text-sm font-medium mb-2">Min Price</label>
                            <input type="number" step="0.01" id="min_price" name="min_price"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                placeholder="Min price">
                        </div>
                        <div>
                            <label for="max_price" class="block text-sm font-medium mb-2">Max Price</label>
                            <input type="number" step="0.01" id="max_price" name="max_price"
                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                placeholder="Max price">
                        </div>
                        <div>
                            <label for="developer" class="block text-sm font-medium mb-2">Developer</label>
                            <select id="developer" name="developer" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                                <option value="">Please select</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer flex gap-2 px-6 py-4 border-t">
                <button type="reset" form="filterForm" class="px-4 py-2 border rounded-md text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200">Reset</button>
                <button type="submit" form="filterForm" class="px-4 py-2 border rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">Apply</button>
            </div>
        </div>
    </div>
</div>


<script>
    function prepareFilters(e) {
        e.preventDefault();
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const formValues = Object.fromEntries([...formData.entries()].filter(([_, v]) => v));

        // Handle price conflicts
        if (formValues.price && (formValues.min_price || formValues.max_price)) {
            alert('Please use either exact price OR price range, not both');
            return;
        }

        const newFilters = {};
        const fieldMappings = {
            'min_price': '>ufCrm5Price',
            'max_price': '<ufCrm5Price',
            'city': '%ufCrm5City',
            'community': '%ufCrm5Community',
            'subCommunity': '%ufCrm5SubCommunity',
            'building': '%ufCrm5Tower',
            'reference': 'ufCrm5ReferenceNumber',
            'permit': 'ufCrm5ReraPermitNumber',
            'listing_title': '%ufCrm5TitleEn',
            'property_type': 'ufCrm5PropertyType',
            'saleRent': 'ufCrm5OfferingType',
            'listing_agent': '%ufCrm5AgentName',
            'developer': '%ufCrm5Developers',
            'listing_owner': '%ufCrm5ListingOwner',
            'landlordEmail': '%ufCrm5LandlordEmail',
            'landlordPhone': '%ufCrm5LandlordContact',
            'bedrooms': 'ufCrm5Bedroom',
            'bathrooms': 'ufCrm5Bathroom',
            'price': 'ufCrm5Price',
            'portal': 'portal',
            'status': 'status'
        };

        // Process price range
        if (formValues.min_price) {
            newFilters[encodeURIComponent('>ufCrm5Price')] = formValues.min_price;
        }
        if (formValues.max_price) {
            newFilters[encodeURIComponent('<ufCrm5Price')] = formValues.max_price;
        }

        // Process other filters
        Object.entries(formValues).forEach(([key, value]) => {
            if (fieldMappings[key]) {
                const encodedKey = encodeURIComponent(fieldMappings[key]);
                newFilters[encodedKey] = value;
            }
        });

        // Handle portal filters
        if (formValues.portal) {
            const portalMap = {
                'PF': 'ufCrm5PfEnable',
                'BAYUT': 'ufCrm5BayutEnable',
                'DUBIZZLE': 'ufCrm5DubizzleEnable',
                'WEBSITE': 'ufCrm5WebsiteEnable'
            };
            // Clear existing portal filters
            Object.values(portalMap).forEach(p => delete newFilters[encodeURIComponent(p)]);
            newFilters[encodeURIComponent(portalMap[formValues.portal])] = '1';
        }

        setFilters(newFilters);
        document.querySelector('[data-bs-dismiss="modal"]').click();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const listingAgentSelect = document.getElementById('listing_agent');
        const listingOwnerSelect = document.getElementById('listing_owner');
        const developerSelect = document.getElementById('developer');
        const baseUrl = 'https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r';

        const createSelectOptions = (data, selectElement, key, value) => {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[key];
                option.textContent = item[value];
                selectElement.appendChild(option);
            });
        };

        const fetchAgents = async () => {
            try {
                const response = await fetch(`${baseUrl}/crm.item.list?entityTypeId=1040&select[0]=id&select[1]=ufCrm7AgentName&order[ufCrm7AgentName]=asc`);
                const data = await response.json();
                return data.result.items;
            } catch (error) {
                console.error('Error fetching agents:', error);
                return;
            }
        };

        const fetchDevelopers = async () => {
            try {
                const response = await fetch(`${baseUrl}/crm.item.list?entityTypeId=1052&select[0]=id&select[1]=ufCrm13DeveloperName&order[ufCrm13DeveloperName]=asc`);
                const data = await response.json();
                const totalDevelopers = data.total;
                const developers = [];

                for (let i = 0; i < Math.ceil(totalDevelopers / 50); i++) {
                    const paginatedResponse = await fetch(`${baseUrl}/crm.item.list?entityTypeId=1052&select[0]=id&select[1]=ufCrm13DeveloperName&order[ufCrm13DeveloperName]=asc&start=${i * 50}`);
                    const paginatedData = await paginatedResponse.json();
                    developers.push(...paginatedData.result.items);
                }
                return developers;
            } catch (error) {
                console.error('Error fetching developers:', error);
                return;
            }
        };

        const fetchOwners = async () => {
            try {
                const response = await fetch(`${baseUrl}/user.get?select[0]=NAME&select[1]=LAST_NAME&order[NAME]=asc`);
                const data = await response.json();
                const totalOwners = data.total;
                const owners = [];

                for (let i = 0; i < Math.ceil(totalOwners / 50); i++) {
                    const paginatedResponse = await fetch(`${baseUrl}/user.get?select[0]=NAME&select[1]=LAST_NAME&order[NAME]=asc&start=${i * 50}`);
                    const paginatedData = await paginatedResponse.json();
                    owners.push(...paginatedData.result.map(owner => ({
                        NAME: `${owner.NAME} ${owner.LAST_NAME}`.trim()
                    })));
                }


                const agents = await fetchAgents();
                agents.forEach(agent => {
                    owners.push({
                        NAME: agent.ufCrm7AgentName
                    });
                });


                return [...new Set(owners.map(owner => owner.NAME))]
                    .map(NAME => owners.find(owner => owner.NAME === NAME))
                    .sort((a, b) => a.NAME.localeCompare(b.NAME));
            } catch (error) {
                console.error('Error fetching owners:', error);
                return;
            }
        };

        const fetchAndDisplayOptions = async () => {
            try {

                const [agents, developers, owners] = await Promise.all([
                    fetchAgents(),
                    fetchDevelopers(),
                    fetchOwners()
                ]);


                createSelectOptions(agents, listingAgentSelect, 'ufCrm7AgentName', 'ufCrm7AgentName');
                createSelectOptions(developers, developerSelect, 'ufCrm13DeveloperName', 'ufCrm13DeveloperName');
                createSelectOptions(owners, listingOwnerSelect, 'NAME', 'NAME');
            } catch (error) {
                console.error('Error fetching listing data:', error);
            }
        };

        fetchAndDisplayOptions();
    });
</script>