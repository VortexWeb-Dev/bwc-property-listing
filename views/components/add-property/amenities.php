<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-6">Amenities</h2>

    <div class="my-4 flex justify-between gap-6">
        <div class="w-full md:w-1/2 border rounded-lg p-4" style="max-height: 20rem; overflow-y: auto;">
            <label class="block text-sm font-medium mb-2">Available Amenities</label>
            <div id="availableAmenities" class="space-y-2">
                <!-- Grouped amenities as accordions will be displayed here -->
            </div>
        </div>

        <div class="w-full md:w-1/2 border rounded-lg p-4" style="max-height: 20rem; overflow-y: auto;">
            <label class="block text-sm font-medium mb-2">Selected Amenities</label>
            <ul id="selectedAmenities" class="list-none p-0 space-y-2">
                <!-- Selected amenities will be displayed here -->
            </ul>
        </div>
    </div>

    <input type="hidden" name="amenities" id="amenitiesInput">
</div>

<script>
    const groupedAmenities = {
        "Residential Building Amenities": [{
                id: "CS",
                label: "24/7 Concierge"
            },
            {
                id: "SE",
                label: "24/7 Security"
            },
            {
                id: "",
                label: "Valet Parking"
            },
            {
                id: "",
                label: "Smart Home Automation Systems"
            },
            {
                id: "",
                label: "High-speed Elevators"
            },
            {
                id: "AC",
                label: "Centralized Air Conditioning"
            },
        ],
        "Leisure & Wellness Amenities": [
            {
                id: "SP",
                label: "Swimming Pools (Shared)"
            },
            {
                id: "CO",
                label: "Children's Pools"
            },
            {
                id: "PP",
                label: "Infinity Pools / Sky Pools"
            },
            {
                id: "PJ",
                label: "Jacuzzi"
            },
            {
                id: "SS",
                label: "Spa Facilities"
            },
            {
                id: "SA",
                label: "Sauna"
            },
            {
                id: "SR",
                label: "Steam Rooms"
            },
            {
                id: "PY",
                label: "Fully Equipped Gyms / Fitness Centers"
            },
        ],
        "Family & Community-Oriented Amenities": [{
                id: "PR",
                label: "Kids’ Play Areas"
            },
            {
                id: "CD",
                label: "Daycare Centers"
            },
            {
                id: "BB",
                label: "BBQ & Picnic Areas"
            },
        ],
        "Outdoor & Recreational Amenities": [{
                id: "PG",
                label: "Landscaped Gardens"
            },
            {
                id: "BC",
                label: "Private Beach Access"
            },
        ],
        "Security & Convenience": [{
            id: "MT",
            label: "Maintenance Services On-Demand"
        }, ],
        "Water Views": [{
            id: "VW",
            label: "Full Sea View"
        }, ],
        "City & Skyline Views": [{
                id: "BL",
                label: "Burj Khalifa View"
            },
            {
                id: "CW",
                label: "City View"
            },
        ],
        "Palm & Island Views": [{
            id: "BL",
            label: "Palm Jumeirah View"
        }, ],
        "Green & Leisure Views": [{
                id: "GV",
                label: "Golf Course View"
            },
            {
                id: "GR",
                label: "Garden / Courtyard View"
            },
            {
                id: "CV",
                label: "Community View"
            },
        ]
    };

    let selectedAmenities = [];

    function renderAmenities() {
        const container = document.getElementById("availableAmenities");
        container.innerHTML = "";

        for (const group in groupedAmenities) {
            const groupDiv = document.createElement("div");
            groupDiv.classList.add("mb-2");

            const header = document.createElement("div");
            header.classList.add("text-xl", "font-semibold", "cursor-pointer", "p-2", "bg-gray-200", "rounded");
            header.textContent = group;
            header.onclick = () => toggleAccordion(header);

            const list = document.createElement("ul");
            list.classList.add("pl-4", "hidden");

            groupedAmenities[group].forEach(amenity => {
                const li = document.createElement("li");
                li.classList.add("text-gray-700", "p-1");

                li.innerHTML = `
                    <input type="checkbox" id="${amenity.id}" onclick="toggleAmenity('${amenity.id}', '${amenity.label}')" />
                    <label for="${amenity.id}" class="ml-2">${amenity.label}</label>
                `;

                list.appendChild(li);
            });

            groupDiv.appendChild(header);
            groupDiv.appendChild(list);
            container.appendChild(groupDiv);
        }
    }

    function toggleAccordion(header) {
        const nextSibling = header.nextElementSibling;
        nextSibling.classList.toggle("hidden");
    }

    function toggleAmenity(id, label) {
        const isChecked = document.getElementById(id).checked;

        if (isChecked) {
            selectedAmenities.push({
                id,
                label
            });
        } else {
            selectedAmenities = selectedAmenities.filter(a => a.id !== id);
        }

        updateSelectedAmenities();
        updateAmenitiesInput();
    }

    function updateSelectedAmenities() {
        const container = document.getElementById("selectedAmenities");
        container.innerHTML = "";

        selectedAmenities.forEach(amenity => {
            const li = document.createElement("li");
            li.classList.add("text-gray-700", "p-2", "bg-gray-100", "rounded-md", "flex", "justify-between", "items-center");
            li.textContent = amenity.label;

            const removeBtn = document.createElement("button");
            removeBtn.classList.add("text-red-500", "hover:text-red-700");
            removeBtn.textContent = "×";
            removeBtn.onclick = () => toggleAmenity(amenity.id, amenity.label);

            li.appendChild(removeBtn);
            container.appendChild(li);
        });
    }

    function updateAmenitiesInput() {
        const selectedIds = selectedAmenities.map(a => a.id).filter(id => id);
        document.getElementById("amenitiesInput").value = JSON.stringify(selectedIds);
    }

    renderAmenities();
</script>