<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./node_modules/lodash/lodash.min.js"></script>
<script src="./node_modules/dropzone/dist/dropzone-min.js"></script>
<script src="./node_modules/preline/dist/preline.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="./node_modules/lodash/lodash.min.js"></script>
<script src="./node_modules/apexcharts/dist/apexcharts.min.js"></script>
<script src="./node_modules/preline/dist/helper-apexcharts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fabric@latest/dist/index.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const isAdmin = <?php echo json_encode($isAdmin); ?>;

        if (!isAdmin) {
            document.querySelectorAll(".admin-only").forEach(el => el.style.display = "none");
        }
    });

    // Toggle Bayut and Dubizzle
    document.getElementById('toggle_bayut_dubizzle') && document.getElementById('toggle_bayut_dubizzle').addEventListener('change', function() {
        const isChecked = this.checked;
        document.getElementById('bayut_enable').checked = isChecked;
        document.getElementById('dubizzle_enable').checked = isChecked;
    });

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        return date.toLocaleDateString('en-US', options);
    }

    // Update character count
    function updateCharCount(countElement, length, maxLength) {
        titleCount = document.getElementById(countElement);
        titleCount.textContent = length;

        if (length >= maxLength) {
            titleCount.parentElement.classList.add('text-danger');
        } else {
            titleCount.parentElement.classList.remove('text-danger');
        }
    }

    // Parse and update location fields
    function updateLocationFields(location, type) {
        const locationParts = location.split('-');

        const city = locationParts[0].trim();
        const community = locationParts[1].trim();
        const subcommunity = locationParts[2].trim() || null;
        const building = locationParts[3].trim() || null;

        document.getElementById(`${type}_city`).value = city;
        document.getElementById(`${type}_community`).value = community;
        document.getElementById(`${type}_subcommunity`).value = subcommunity;
        document.getElementById(`${type}_building`).value = building;
    }

    // Update reference
    async function handleUpdateReference(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const propertyId = formData.get('propertyId');
        const newReference = formData.get('newReference');

        try {
            const response = await fetch('https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.update?entityTypeId=1036&id=' + propertyId + '&fields[ufCrm5ReferenceNumber]=' + newReference);
            const data = await response.json();
            location.reload();
        } catch (error) {
            console.error('Error updating reference:', error);
        }
    }

    // Format input date
    function formatInputDate(dateInput) {
        if (!dateInput) return null;

        const date = new Date(dateInput);

        if (isNaN(date.getTime())) return null;

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    // Get agent
    async function getAgent(agentId) {
        const response = await fetch(`https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.list?entityTypeId=1040&filter[ufCrm7AgentId]=${agentId}`);
        const data = await response.json();
        return data.result.items[0] || null;
    }

    // Handle action
    async function handleAction(action, propertyId, platform = null) {
        const baseUrl = 'https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r';
        let apiUrl = '';
        let reloadRequired = true;

        switch (action) {
            case 'copyLink':
                const link = `https://lightgray-kudu-834713.hostingersite.com/property-listing-bwc/index.php?page=view-property&id=${propertyId}`;
                navigator.clipboard.writeText(link);
                alert('Link copied to clipboard.');
                reloadRequired = false;
                break;

            case 'downloadPDF':
                window.location.href = `download-pdf.php?id=${propertyId}`;
                reloadRequired = false;
                break;
            case 'downloadPDFAgent':
                window.location.href = `download-pdf-agent.php?id=${propertyId}`;
                reloadRequired = false;
                break;

            case 'duplicate':
                try {
                    const getUrl = `${baseUrl}/crm.item.get?entityTypeId=1036&id=${propertyId}&select[0]=id&select[1]=uf_*`;
                    const response = await fetch(getUrl, {
                        method: 'GET'
                    });
                    const data = await response.json();
                    const property = data.result.item;

                    let addUrl = `${baseUrl}/crm.item.add?entityTypeId=1036`;
                    for (const field in property) {
                        if (
                            field.startsWith('ufCrm5') &&
                            !['ufCrm5ReferenceNumber', 'ufCrm5TitleEn', 'ufCrm5Status', 'ufCrm5PhotoLinks', 'ufCrm5Documents', 'ufCrm5Notes'].includes(field)
                        ) {
                            addUrl += `&fields[${field}]=${encodeURIComponent(property[field])}`;
                        }
                    }

                    if (property['ufCrm5PhotoLinks']) {
                        property['ufCrm5PhotoLinks'].forEach((photoLink, index) => {
                            addUrl += `&fields[ufCrm5PhotoLinks][${index}]=${encodeURIComponent(photoLink)}`;
                        });
                    }

                    if (property['ufCrm5Documents']) {
                        property['ufCrm5Documents'].forEach((document, index) => {
                            addUrl += `&fields[ufCrm5Documents][${index}]=${encodeURIComponent(document)}`;
                        });
                    }

                    if (property['ufCrm5Notes']) {
                        property['ufCrm5Notes'].forEach((note, index) => {
                            addUrl += `&fields[ufCrm5Notes][${index}]=${encodeURIComponent(note)}`;
                        });
                    }

                    addUrl += `&fields[ufCrm5TitleEn]=${encodeURIComponent(property.ufCrm5TitleEn + ' (Duplicate)')}`;
                    addUrl += `&fields[ufCrm5ReferenceNumber]=${await getNewReference(property.ufCrm5OfferingType)}`;
                    addUrl += `&fields[ufCrm5Status]=DRAFT`;

                    await fetch(addUrl, {
                        method: 'GET'
                    });
                } catch (error) {
                    console.error('Error duplicating property:', error);
                }
                break;

            case 'publish':
                apiUrl = `${baseUrl}/crm.item.update?entityTypeId=1036&id=${propertyId}&fields[ufCrm5Status]=PUBLISHED`;
                if (platform) {
                    apiUrl += `&fields[ufCrm5${platform.charAt(0).toUpperCase() + platform.slice(1)}Enable]=Y`;
                } else {
                    apiUrl += `&fields[ufCrm5PfEnable]=Y&fields[ufCrm5BayutEnable]=Y&fields[ufCrm5DubizzleEnable]=Y&fields[ufCrm5WebsiteEnable]=Y&fields[ufCrm5Status]=PUBLISHED`;
                }
                break;

            case 'unpublish':
                apiUrl = `${baseUrl}/crm.item.update?entityTypeId=1036&id=${propertyId}`;
                if (platform) {
                    apiUrl += `&fields[ufCrm5${platform.charAt(0).toUpperCase() + platform.slice(1)}Enable]=N`;
                } else {
                    apiUrl += `&fields[ufCrm5PfEnable]=N&fields[ufCrm5BayutEnable]=N&fields[ufCrm5DubizzleEnable]=N&fields[ufCrm5WebsiteEnable]=N&fields[ufCrm5Status]=UNPUBLISHED`;
                }
                break;

            case 'archive':
                if (confirm('Are you sure you want to archive this property?')) {
                    apiUrl = `${baseUrl}/crm.item.update?entityTypeId=1036&id=${propertyId}&fields[ufCrm5Status]=ARCHIVED`;
                } else {
                    reloadRequired = false;
                }
                break;

            case 'delete':
                if (confirm('Are you sure you want to delete this property?')) {
                    try {
                        // First get property details to find image URLs
                        const getPropertyUrl = `${baseUrl}/crm.item.get?entityTypeId=1036&id=${propertyId}`;
                        const propertyResponse = await fetch(getPropertyUrl);
                        const propertyData = await propertyResponse.json();

                        if (propertyData.result && propertyData.result.item) {
                            const property = propertyData.result.item;
                            console.log('Property data for deletion:', property);

                            // Delete images from S3
                            if (property.ufCrm5PhotoLinks && Array.isArray(property.ufCrm5PhotoLinks)) {
                                console.log('Found photo links:', property.ufCrm5PhotoLinks);
                                for (const imageUrl of property.ufCrm5PhotoLinks) {
                                    try {
                                        console.log('Attempting to delete image:', imageUrl);
                                        const response = await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: imageUrl
                                            })
                                        });
                                        const result = await response.json();
                                        console.log('Delete response:', result);
                                        if (!result.success) {
                                            console.error(`Failed to delete image: ${result.error}`);
                                        }
                                    } catch (error) {
                                        console.error(`Error deleting S3 object: ${imageUrl}`, error);
                                    }
                                }
                            }

                            // Delete floorplan from S3 if exists
                            if (property.ufCrm5FloorPlan) {
                                try {
                                    console.log('Attempting to delete floorplan:', property.ufCrm5FloorPlan);
                                    const response = await fetch('./delete-s3-object.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            fileUrl: property.ufCrm5FloorPlan
                                        })
                                    });
                                    const result = await response.json();
                                    console.log('Floorplan delete response:', result);
                                    if (!result.success) {
                                        console.error(`Failed to delete floorplan: ${result.error}`);
                                    }
                                } catch (error) {
                                    console.error(`Error deleting S3 floorplan: ${property.ufCrm5FloorPlan}`, error);
                                }
                            }

                            // Delete documents from S3
                            if (property.ufCrm5Documents && Array.isArray(property.ufCrm5Documents)) {
                                console.log('Found documents:', property.ufCrm5Documents);
                                for (const docUrl of property.ufCrm5Documents) {
                                    try {
                                        console.log('Attempting to delete document:', docUrl);
                                        const response = await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: docUrl
                                            })
                                        });
                                        const result = await response.json();
                                        console.log('Delete response:', result);
                                        if (!result.success) {
                                            console.error(`Failed to delete document: ${result.error}`);
                                        }
                                    } catch (error) {
                                        console.error(`Error deleting S3 document: ${docUrl}`, error);
                                    }
                                }
                            }
                        }

                        // Now delete the property from CRM
                        apiUrl = `${baseUrl}/crm.item.delete?entityTypeId=1036&id=${propertyId}`;
                    } catch (error) {
                        console.error('Error in delete process:', error);
                        reloadRequired = false;
                    }
                } else {
                    reloadRequired = false;
                }
                break;

            default:
                console.error('Invalid action:', action);
                reloadRequired = false;
        }

        if (apiUrl) {
            try {
                await fetch(apiUrl, {
                    method: 'GET'
                });
            } catch (error) {
                console.error(`Error executing ${action}:`, error);
            }
        }

        if (reloadRequired) {
            location.reload();
        }
    }

    // Bulk action
    async function handleBulkAction(action, platform) {
        const checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        const propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (propertyIds.length === 0) {
            alert('Please select at least one property.');
            return;
        }

        if (confirm(`Are you sure you want to ${action} the selected properties?`)) {
            try {
                const baseUrl = 'https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r';
                const apiUrl = `${baseUrl}/crm.item.${action === 'delete' ? 'delete' : 'update'}?entityTypeId=1036`;

                const platformFieldMapping = {
                    pf: 'ufCrm5PfEnable',
                    bayut: 'ufCrm5BayutEnable',
                    dubizzle: 'ufCrm5DubizzleEnable',
                    website: 'ufCrm5WebsiteEnable'
                };

                // If action is delete, first get all property details to find image URLs
                if (action === 'delete') {
                    for (const propertyId of propertyIds) {
                        try {
                            // Get property details to find image URLs
                            const getPropertyUrl = `${baseUrl}/crm.item.get?entityTypeId=1036&id=${propertyId}`;
                            const propertyResponse = await fetch(getPropertyUrl);
                            const propertyData = await propertyResponse.json();

                            if (propertyData.result && propertyData.result.item && !propertyData.result.item.ufCrm5TitleEn.includes('(Duplicate)')) {
                                const property = propertyData.result.item;

                                // Delete images from S3
                                if (property.ufCrm5PhotoLinks && Array.isArray(property.ufCrm5PhotoLinks)) {
                                    for (const imageUrl of property.ufCrm5PhotoLinks) {
                                        try {
                                            await fetch('./delete-s3-object.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify({
                                                    fileUrl: imageUrl
                                                })
                                            });
                                        } catch (error) {
                                            console.error(`Error deleting S3 object: ${imageUrl}`, error);
                                        }
                                    }
                                }

                                // Delete floorplan from S3 if exists
                                if (property.ufCrm5FloorPlan) {
                                    try {
                                        await fetch('./delete-s3-object.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                fileUrl: property.ufCrm5FloorPlan
                                            })
                                        });
                                    } catch (error) {
                                        console.error(`Error deleting S3 floorplan: ${property.ufCrm5FloorPlan}`, error);
                                    }
                                }

                                // Delete documents from S3
                                if (property.ufCrm5Documents && Array.isArray(property.ufCrm5Documents)) {
                                    for (const docUrl of property.ufCrm5Documents) {
                                        try {
                                            await fetch('./delete-s3-object.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify({
                                                    fileUrl: docUrl
                                                })
                                            });
                                        } catch (error) {
                                            console.error(`Error deleting S3 document: ${docUrl}`, error);
                                        }
                                    }
                                }
                            }
                        } catch (error) {
                            console.error(`Error getting property details for deletion: ${propertyId}`, error);
                        }
                    }
                }

                const requests = propertyIds.map(propertyId => {
                    let url = `${apiUrl}&id=${propertyId}`;

                    if (action === 'publish') {
                        url += '&fields[ufCrm5Status]=PUBLISHED';

                        if (platformFieldMapping[platform]) {
                            url += `&fields[${platformFieldMapping[platform]}]=Y`;
                        } else {
                            url += `&fields[ufCrm5PfEnable]=Y&fields[ufCrm5BayutEnable]=Y&fields[ufCrm5DubizzleEnable]=Y&fields[ufCrm5WebsiteEnable]=Y`;
                        }
                    } else if (action === 'unpublish') {
                        if (platformFieldMapping[platform]) {
                            url += `&fields[${platformFieldMapping[platform]}]=N`;
                        } else {
                            url += `&fields[ufCrm5PfEnable]=N&fields[ufCrm5BayutEnable]=N&fields[ufCrm5DubizzleEnable]=N&fields[ufCrm5WebsiteEnable]=N&fields[ufCrm5Status]=UNPUBLISHED`;
                        }
                    } else if (action === 'archive') {
                        url += '&fields[ufCrm5Status]=ARCHIVED';
                    }

                    return fetch(url, {
                            method: 'GET'
                        })
                        .then(response => response.json())
                        .then(data => {})
                        .catch(error => {
                            console.error(`Error updating property ${propertyId}:`, error);
                        });
                });

                // Wait for all requests to finish
                await Promise.all(requests);

                location.reload();
            } catch (error) {
                console.error('Error handling bulk action:', error);
            }
        }
    }

    // Function to add watermark to the image
    function addWatermark(imageElement, watermarkImagePath) {
        return new Promise((resolve, reject) => {
            const watermarkImage = new Image();
            watermarkImage.src = watermarkImagePath;

            watermarkImage.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const width = imageElement.width;
                const height = imageElement.height;

                canvas.width = width;
                canvas.height = height;

                ctx.drawImage(imageElement, 0, 0, width, height);

                const watermarkAspect = watermarkImage.width / watermarkImage.height;
                const imageAspect = width / height;

                let watermarkWidth, watermarkHeight;

                if (watermarkAspect > imageAspect) {
                    watermarkWidth = width * 0.2;
                    watermarkHeight = watermarkWidth / watermarkAspect;
                } else {
                    watermarkHeight = height * 0.2;
                    watermarkWidth = watermarkHeight * watermarkAspect;
                }

                const xPosition = (width - watermarkWidth) / 2;
                const yPosition = (height - watermarkHeight) / 2;

                ctx.drawImage(watermarkImage, xPosition, yPosition, watermarkWidth, watermarkHeight);
                const watermarkedImage = canvas.toDataURL('image/jpeg', 0.8);
                resolve(watermarkedImage);
            };

            watermarkImage.onerror = function() {
                reject('Failed to load watermark image.');
            };
        });
    }

    // Function to add watermark text to the image
    function addWatermarkText(imageElement, watermarkText) {
        return new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const width = imageElement.width;
            const height = imageElement.height;

            canvas.width = width;
            canvas.height = height;

            ctx.drawImage(imageElement, 0, 0, width, height);

            // Set the watermark text properties
            ctx.font = '360px Arial'; // You can adjust the font size here
            ctx.fillStyle = 'rgba(255, 255, 255, 0.6)'; // White color with 50% transparency
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Add the watermark text to the image (centered)
            ctx.fillText(watermarkText, width / 2, height / 2);

            // Convert the image to JPEG with reduced quality (optional)
            const watermarkedImage = canvas.toDataURL('image/jpeg', 0.7); // Adjust quality as needed
            resolve(watermarkedImage);
        });
    }

    // Function to upload a file
    function uploadFile(file, isDocument = false) {
        const formData = new FormData();
        formData.append('file', file);

        if (isDocument) {
            formData.append('isDocument', 'true');
        }

        return fetch('upload-file.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    return data.url;
                } else {
                    console.error('Error uploading file (PHP backend):', data.error);
                    return null;
                }
            })
            .catch((error) => {
                console.error("Error uploading file:", error);
                return null;
            });
    }

    // Process base64 images
    async function processBase64Images(base64Images, watermarkPath) {
        const photoPaths = [];

        for (const base64Image of base64Images) {
            const regex = /^data:image\/(\w+);base64,/;
            const matches = base64Image.match(regex);

            if (matches) {
                const base64Data = base64Image.replace(regex, '');
                const imageData = atob(base64Data);

                const blob = new Blob([new Uint8Array(imageData.split('').map(c => c.charCodeAt(0)))], {
                    type: `image/${matches[1]}`,
                });
                const imageUrl = URL.createObjectURL(blob);

                const imageElement = new Image();
                imageElement.src = imageUrl;

                await new Promise((resolve, reject) => {
                    imageElement.onload = async () => {
                        try {
                            // Add watermark to the image
                            const watermarkedDataUrl = await addWatermark(imageElement, watermarkPath);
                            // const watermarkedDataUrl = await addWatermarkText(imageElement, 'LIVRICHY');
                            // const watermarkedDataUrl = await addWatermarkWithFabric(imageElement, watermarkPath);

                            // Convert the data URL to a Blob
                            const watermarkedBlob = dataURLToBlob(watermarkedDataUrl);

                            // Upload the watermarked Blob
                            const uploadedUrl = await uploadFile(watermarkedBlob);

                            if (uploadedUrl) {
                                photoPaths.push(uploadedUrl); // Add the uploaded URL to the photoPaths array
                            } else {
                                console.error('Error uploading photo from base64 data');
                            }

                            resolve();
                        } catch (error) {
                            console.error('Error processing watermarking or uploading:', error);
                            reject(error);
                        } finally {
                            URL.revokeObjectURL(imageUrl); // Clean up the object URL
                        }
                    };

                    imageElement.onerror = (error) => {
                        console.error('Failed to load image from URL:', error);
                        reject(error);
                    };
                });
            } else {
                console.error('Invalid base64 image data');
            }
        }

        return photoPaths;
    }

    function getAmenityName(amenityId) {
        const amenities = [{
                id: 'GV',
                label: 'Golf view'
            },
            {
                id: 'CW',
                label: 'City view'
            },
            {
                id: 'NO',
                label: 'North orientation'
            },
            {
                id: 'SO',
                label: 'South orientation'
            },
            {
                id: 'EO',
                label: 'East orientation'
            },
            {
                id: 'WO',
                label: 'West orientation'
            },
            {
                id: 'NS',
                label: 'Near school'
            },
            {
                id: 'HO',
                label: 'Near hospital'
            },
            {
                id: 'TR',
                label: 'Terrace'
            },
            {
                id: 'NM',
                label: 'Near mosque'
            },
            {
                id: 'SM',
                label: 'Near supermarket'
            },
            {
                id: 'ML',
                label: 'Near mall'
            },
            {
                id: 'PT',
                label: 'Near public transportation'
            },
            {
                id: 'MO',
                label: 'Near metro'
            },
            {
                id: 'VT',
                label: 'Near veterinary'
            },
            {
                id: 'BC',
                label: 'Beach access'
            },
            {
                id: 'PK',
                label: 'Public parks'
            },
            {
                id: 'RT',
                label: 'Near restaurants'
            },
            {
                id: 'NG',
                label: 'Near Golf'
            },
            {
                id: 'AP',
                label: 'Near airport'
            },
            {
                id: 'CS',
                label: 'Concierge Service'
            },
            {
                id: 'SS',
                label: 'Spa'
            },
            {
                id: 'SY',
                label: 'Shared Gym'
            },
            {
                id: 'MS',
                label: 'Maid Service'
            },
            {
                id: 'WC',
                label: 'Walk-in Closet'
            },
            {
                id: 'HT',
                label: 'Heating'
            },
            {
                id: 'GF',
                label: 'Ground floor'
            },
            {
                id: 'SV',
                label: 'Server room'
            },
            {
                id: 'DN',
                label: 'Pantry'
            },
            {
                id: 'RA',
                label: 'Reception area'
            },
            {
                id: 'VP',
                label: 'Visitors parking'
            },
            {
                id: 'OP',
                label: 'Office partitions'
            },
            {
                id: 'SH',
                label: 'Core and Shell'
            },
            {
                id: 'CD',
                label: 'Children daycare'
            },
            {
                id: 'CL',
                label: 'Cleaning services'
            },
            {
                id: 'NH',
                label: 'Near Hotel'
            },
            {
                id: 'CR',
                label: 'Conference room'
            },
            {
                id: 'BL',
                label: 'View of Landmark'
            },
            {
                id: 'PR',
                label: 'Children Play Area'
            },
            {
                id: 'BH',
                label: 'Beach Access'
            }
        ];

        return amenities.find(amenity => amenity.id === amenityId)?.label || amenityId;
    }

    function getAmenityId(amenityName) {
        console.log(amenityName);

        const amenities = [{
                id: 'GV',
                label: 'Golf view'
            },
            {
                id: 'CW',
                label: 'City view'
            },
            {
                id: 'NO',
                label: 'North orientation'
            },
            {
                id: 'SO',
                label: 'South orientation'
            },
            {
                id: 'EO',
                label: 'East orientation'
            },
            {
                id: 'WO',
                label: 'West orientation'
            },
            {
                id: 'NS',
                label: 'Near school'
            },
            {
                id: 'HO',
                label: 'Near hospital'
            },
            {
                id: 'TR',
                label: 'Terrace'
            },
            {
                id: 'NM',
                label: 'Near mosque'
            },
            {
                id: 'SM',
                label: 'Near supermarket'
            },
            {
                id: 'ML',
                label: 'Near mall'
            },
            {
                id: 'PT',
                label: 'Near public transportation'
            },
            {
                id: 'MO',
                label: 'Near metro'
            },
            {
                id: 'VT',
                label: 'Near veterinary'
            },
            {
                id: 'BC',
                label: 'Beach access'
            },
            {
                id: 'PK',
                label: 'Public parks'
            },
            {
                id: 'RT',
                label: 'Near restaurants'
            },
            {
                id: 'NG',
                label: 'Near Golf'
            },
            {
                id: 'AP',
                label: 'Near airport'
            },
            {
                id: 'CS',
                label: 'Concierge Service'
            },
            {
                id: 'SS',
                label: 'Spa'
            },
            {
                id: 'SY',
                label: 'Shared Gym'
            },
            {
                id: 'MS',
                label: 'Maid Service'
            },
            {
                id: 'WC',
                label: 'Walk-in Closet'
            },
            {
                id: 'HT',
                label: 'Heating'
            },
            {
                id: 'GF',
                label: 'Ground floor'
            },
            {
                id: 'SV',
                label: 'Server room'
            },
            {
                id: 'DN',
                label: 'Pantry'
            },
            {
                id: 'RA',
                label: 'Reception area'
            },
            {
                id: 'VP',
                label: 'Visitors parking'
            },
            {
                id: 'OP',
                label: 'Office partitions'
            },
            {
                id: 'SH',
                label: 'Core and Shell'
            },
            {
                id: 'CD',
                label: 'Children daycare'
            },
            {
                id: 'CL',
                label: 'Cleaning services'
            },
            {
                id: 'NH',
                label: 'Near Hotel'
            },
            {
                id: 'CR',
                label: 'Conference room'
            },
            {
                id: 'BL',
                label: 'View of Landmark'
            },
            {
                id: 'PR',
                label: 'Children Play Area'
            },
            {
                id: 'BH',
                label: 'Beach Access'
            }
        ];

        console.log(amenities.find(amenity => amenity.label === amenityName)?.id || amenityName)

        return amenities.find(amenity => amenity.label === amenityName)?.id || amenityName;
    }

    // Function to convert data URL to Blob
    function dataURLToBlob(dataURL) {
        const byteString = atob(dataURL.split(',')[1]);
        const arrayBuffer = new ArrayBuffer(byteString.length);
        const uintArray = new Uint8Array(arrayBuffer);
        for (let i = 0; i < byteString.length; i++) {
            uintArray[i] = byteString.charCodeAt(i);
        }
        return new Blob([uintArray], {
            type: 'image/png'
        });
    }

    // Function to fetch a property
    async function fetchProperty(id) {
        const url = `https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.get?entityTypeId=1036&id=${id}`;
        const response = await fetch(url);
        const data = await response.json();
        if (data.result && data.result.item) {
            const property = data.result.item;

            // Management
            document.getElementById('reference').value = property.ufCrm5ReferenceNumber;
            document.getElementById('landlord_name').value = property.ufCrm5LandlordName;
            document.getElementById('landlord_email').value = property.ufCrm5LandlordEmail;
            document.getElementById('landlord_phone').value = property.ufCrm5LandlordContact;
            Array.from(document.getElementById('availability').options).forEach(option => {
                if (option.value == property.ufCrm5Availability) option.selected = true;
            });
            document.getElementById('available_from').value = formatInputDate(property.ufCrm5AvailableFrom);
            document.getElementById('contract_expiry').value = formatInputDate(property.ufCrm5ContractExpiryDate);

            // Specifications
            document.getElementById('title_deed').value = property.title;
            document.getElementById('size').value = property.ufCrm5Size;
            document.getElementById('unit_no').value = property.ufCrm5UnitNo;
            document.getElementById('bathrooms').value = property.ufCrm5Bathroom;
            document.getElementById('parkings').value = property.ufCrm5Parking;
            document.getElementById('total_plot_size').value = property.ufCrm5TotalPlotSize;
            document.getElementById('lot_size').value = property.ufCrm5LotSize;
            document.getElementById('buildup_area').value = property.ufCrm5BuildupArea;
            document.getElementById('layout_type').value = property.ufCrm5LayoutType;
            document.getElementById('project_name').value = property.ufCrm5ProjectName;
            document.getElementById('build_year').value = property.ufCrm5BuildYear;
            Array.from(document.getElementById('property_type').options).forEach(option => {
                if (option.value === property.ufCrm5PropertyType) option.selected = true;
            });
            Array.from(document.getElementById('offering_type').options).forEach(option => {
                if (option.value === property.ufCrm5OfferingType) option.selected = true;
            });
            Array.from(document.getElementById('bedrooms').options).forEach(option => {
                if (option.value == property.ufCrm5Bedroom) option.selected = true;
            });
            Array.from(document.getElementById('furnished').options).forEach(option => {
                if (option.value == property.ufCrm5Furnished) option.selected = true;
            });
            Array.from(document.getElementById('project_status').options).forEach(option => {
                if (option.value == property.ufCrm5ProjectStatus) option.selected = true;
            });
            Array.from(document.getElementById('sale_type').options).forEach(option => {
                if (option.value == property.ufCrm5SaleType) option.selected = true;
            });
            Array.from(document.getElementById('ownership').options).forEach(option => {
                if (option.value == property.ufCrm5Ownership) option.selected = true;
            });

            // Property Permit
            document.getElementById('rera_permit_number').value = property.ufCrm5ReraPermitNumber
            document.getElementById('dtcm_permit_number').value = property.ufCrm5DtcmPermitNumber
            document.getElementById('rera_issue_date').value = formatInputDate(property.ufCrm5ReraPermitIssueDate);
            document.getElementById('rera_expiration_date').value = formatInputDate(property.ufCrm5ReraPermitExpirationDate);

            // Pricing
            document.getElementById('price').value = property.ufCrm5Price;
            document.getElementById('payment_method').value = property.ufCrm5PaymentMethod;
            document.getElementById('downpayment_price').value = property.ufCrm5DownPaymentPrice;
            document.getElementById('service_charge').value = property.ufCrm5ServiceCharge;
            property.ufCrm5HidePrice == "Y" ? document.getElementById('hide_price').checked = true : document.getElementById('hide_price').checked = false;
            Array.from(document.getElementById('rental_period').options).forEach(option => {
                if (option.value == property.ufCrm5RentalPeriod) option.selected = true;
            });
            Array.from(document.getElementById('cheques').options).forEach(option => {
                if (option.value == property.ufCrm5NoOfCheques) option.selected = true;
            });
            Array.from(document.getElementById('financial_status').options).forEach(option => {
                if (option.value == property.ufCrm5FinancialStatus) option.selected = true;
            });

            // Title and Description
            document.getElementById('title_en').value = property.ufCrm5TitleEn;
            document.getElementById('description_en').textContent = property.ufCrm5DescriptionEn;
            document.getElementById('title_ar').value = property.ufCrm5TitleAr;
            document.getElementById('description_ar').textContent = property.ufCrm5DescriptionAr;
            document.getElementById('brochure_description_1').textContent = property.ufCrm5BrochureDescription;
            document.getElementById('brochure_description_2').textContent = property.ufCrm_5_BROCHURE_DESCRIPTION_2;

            document.getElementById('titleEnCount').textContent = document.getElementById('title_en').value.length;
            document.getElementById('descriptionEnCount').textContent = document.getElementById('description_en').textContent.length;
            document.getElementById('titleArCount').textContent = document.getElementById('title_ar').value.length;
            document.getElementById('descriptionArCount').textContent = document.getElementById('description_ar').textContent.length;
            document.getElementById('brochureDescription1Count').textContent = document.getElementById('brochure_description_1').textContent.length;
            document.getElementById('brochureDescription2Count').textContent = document.getElementById('brochure_description_2').textContent.length;

            // Location
            document.getElementById('pf_location').value = property.ufCrm5Location;
            document.getElementById('pf_city').value = property.ufCrm5City;
            document.getElementById('pf_community').value = property.ufCrm5Community;
            document.getElementById('pf_subcommunity').value = property.ufCrm5SubCommunity;
            document.getElementById('pf_building').value = property.ufCrm5Tower;
            document.getElementById('bayut_location').value = property.ufCrm5BayutLocation;
            document.getElementById('bayut_city').value = property.ufCrm5BayutCity;
            document.getElementById('bayut_community').value = property.ufCrm5BayutCommunity;
            document.getElementById('bayut_subcommunity').value = property.ufCrm5BayutSubCommunity;
            document.getElementById('bayut_building').value = property.ufCrm5BayutTower;

            if (property.ufCrm5Geopoints) {
                const [latitude, longitude] = property.ufCrm5Geopoints.split(',').map(coord => coord.trim());
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;
            }

            // Photos and Videos
            document.getElementById('video_tour_url').value = property.ufCrm5VideoTourUrl;
            document.getElementById('360_view_url').value = property.ufCrm_5_360_VIEW_URL;
            document.getElementById('qr_code_url').value = property.ufCrm5QrCodePropertyBooster;
            // Photos
            // Floor Plan

            // Portals
            property.ufCrm5PfEnable == "Y" ? document.getElementById('pf_enable').checked = true : document.getElementById('pf_enable').checked = false;
            property.ufCrm5BayutEnable == "Y" ? document.getElementById('bayut_enable').checked = true : document.getElementById('bayut_enable').checked = false;
            property.ufCrm5DubizzleEnable == "Y" ? document.getElementById('dubizzle_enable').checked = true : document.getElementById('dubizzle_enable').checked = false;
            property.ufCrm5WebsiteEnable == "Y" ? document.getElementById('website_enable').checked = true : document.getElementById('website_enable').checked = false;
            if (document.getElementById('dubizzle_enable').checked && document.getElementById('bayut_enable').value) {
                toggle_bayut_dubizzle.checked = true;
            }

            switch (property.ufCrm5Status) {
                case 'PUBLISHED':
                    document.getElementById('publish').checked = true;
                    break;
                case 'UNPUBLISHED':
                    document.getElementById('unpublish').checked = true;
                    break;
                case 'LIVE':
                    document.getElementById('live').checked = true;
                    break;
                case 'DRAFT':
                    document.getElementById('draft').checked = true;
                    break;
                case 'ARCHIVED':
                    document.getElementById('archive').checked = true;
                    break;
                case 'POCKET':
                    document.getElementById('pocket').checked = true;
                    break;
            }

            function ensureOptionExistsAndSelect(selectElementId, value, label) {
                const selectElement = document.getElementById(selectElementId);
                const existingOption = document.querySelector(`#${selectElementId} option[value="${value}"]`);

                if (!existingOption) {
                    const newOption = document.createElement('option');
                    newOption.value = value;
                    newOption.textContent = label || 'Unknown Option';
                    newOption.selected = true;
                    selectElement.appendChild(newOption);
                } else {
                    existingOption.selected = true;
                }
            }

            ensureOptionExistsAndSelect('listing_agent', property.ufCrm5AgentId, property.ufCrm5AgentName);
            ensureOptionExistsAndSelect('listing_owner', property.ufCrm5ListingOwner, property.ufCrm5ListingOwner);
            ensureOptionExistsAndSelect('developer', property.ufCrm5Developers, property.ufCrm5Developers);

            // Notes
            function addExistingNote(note) {
                const li = document.createElement("li");
                li.classList.add("text-gray-700", "p-2", "flex", "justify-between", "items-center", "mb-2", "bg-gray-100", "rounded-md");

                li.innerHTML = `
                    ${note} 
                    <button class="text-red-500 hover:text-red-700" onclick="removeNote(this)">×</button>
                `;

                document.getElementById("notesList").appendChild(li);
                updateNotesInput();
            }

            if (property.ufCrm5Notes.length > 0) {
                property.ufCrm5Notes.forEach(note => {
                    addExistingNote(note);
                });
            }

            // Amenities
            function addExistingAmenity(amenity) {
                if (!selectedAmenities.some(a => a.id === amenity)) {
                    selectedAmenities.push({
                        id: amenity,
                        label: getAmenityName(amenity)
                    });
                }

                const li = document.createElement("li");
                li.classList.add("text-gray-700", "p-2", "flex", "justify-between", "items-center", "mb-2", "bg-gray-100", "rounded-md");

                li.innerHTML = `
                    ${getAmenityName(amenity)} 
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removeAmenity('${amenity}')">×</button>
                `;

                document.getElementById("selectedAmenities").appendChild(li);
                updateAmenitiesInput();
            }

            if (property.ufCrm5Amenities && property.ufCrm5Amenities.length > 0) {
                property.ufCrm5Amenities.forEach(amenity => {
                    addExistingAmenity(amenity);
                });
            }


            return property;

        } else {
            console.error('Invalid property data:', data);
            document.getElementById('property-details').textContent = 'Failed to load property details.';
        }
    }

    // Function to check if any property is selected
    function isPropertySelected() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        return propertyIds && propertyIds.length > 0;
    }

    // Function to select and add properties to agent transfer form
    function selectAndAddPropertiesToAgentTransfer() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (!isPropertySelected()) {
            return alert('Please select at least one property.');
        }

        document.getElementById('transferAgentPropertyIds').value = propertyIds.join(',');

        const agentModal = new bootstrap.Modal(document.getElementById('transferAgentModal'));
        agentModal.show();
    }

    // Function to select and add properties to owner transfer form
    function selectAndAddPropertiesToOwnerTransfer() {
        var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
        var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

        if (!isPropertySelected()) {
            return alert('Please select at least one property.');
        }

        document.getElementById('transferOwnerPropertyIds').value = propertyIds.join(',');


        const ownerModal = new bootstrap.Modal(document.getElementById('transferOwnerModal'));
        ownerModal.show();
    }

    // Function to calculate square meters
    function sqftToSqm(sqft) {
        const sqm = sqft * 0.092903;
        return parseFloat(sqm.toFixed(2));
    }

    async function getNewReference(offeringType) {
        const prefix = (offeringType === "RR" || offeringType === "CR") ? "BWC-R-" : "BWC-S-";
        const url = 'https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.list?entityTypeId=1036&order[id]=desc&select[]=ufCrm5ReferenceNumber';

        try {
            const response = await fetch(url);
            const data = await response.json();

            const seriesItems = data.result.items.filter(item =>
                item.ufCrm5ReferenceNumber.startsWith(prefix)
            );

            if (!seriesItems.length) {
                return `${prefix}000001`;
            }

            const lastReference = seriesItems[0].ufCrm5ReferenceNumber;
            const regex = new RegExp(`^${prefix}(\\d{6})$`);
            const match = lastReference.match(regex);

            const nextNumber = match ?
                String(parseInt(match[1], 10) + 1).padStart(6, '0') :
                '000001';

            return `${prefix}${nextNumber}`;
        } catch (error) {
            console.error('Error fetching reference:', error);
            return null;
        }
    }

    function downloadImages(propertyId, event) {
        // Create and show a toast notification
        const toastContainer = document.createElement('div');
        toastContainer.style.position = 'fixed';
        toastContainer.style.bottom = '1rem';
        toastContainer.style.right = '1rem';
        toastContainer.style.zIndex = '1050';

        const toast = document.createElement('div');
        toast.className = 'toast show';
        toast.style.minWidth = '300px';

        // Store the clicked element
        const clickedElement = event.target.closest('a'); // Get the parent <a> tag



        // Disable the link while downloading
        clickedElement.style.pointerEvents = 'none';

        toast.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto">Downloading Images</strong>
            <button type="button" class="btn-close" onclick="this.closest('.toast').remove()"></button>
        </div>
        <div class="toast-body">
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                <span>Preparing your download...</span>
            </div>
            <div class="text-muted small mt-1">This may take a few moments depending on the number of images.</div>
        </div>
    `;

        toastContainer.appendChild(toast);
        document.body.appendChild(toastContainer);

        // Rest of your existing code, but replace all event.target with clickedElement
        const loadingElement = document.createElement('span');
        loadingElement.className = 'ms-2 spinner-border spinner-border-sm';
        clickedElement.appendChild(loadingElement);

        // Disable the link while downloading
        clickedElement.style.pointerEvents = 'none';

        fetch(`download-images.php?id=${propertyId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // Get the filename from the Content-Disposition header if available
                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = `property_images_${propertyId}.zip`;
                if (contentDisposition) {
                    const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                    if (filenameMatch) {
                        filename = filenameMatch[1];
                    }
                }

                // Check if it's a ZIP file
                const contentType = response.headers.get('Content-Type');
                if (contentType !== 'application/zip') {
                    // If PHP returns an error message instead of ZIP
                    return response.text().then(text => {
                        throw new Error(text || 'Invalid response from server');
                    });
                }

                return response.blob().then(blob => ({
                    blob,
                    filename
                }));
            })
            .then(({
                blob,
                filename
            }) => {
                // Create and trigger download
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);

                // Update toast message for successful download
                toast.querySelector('.toast-body').innerHTML = `
                <div class="text-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Download complete!
                </div>
            `;

                // Remove toast after a short delay
                setTimeout(() => {
                    toast.remove();
                }, 1500);

                // Cleanup
                loadingElement.remove();
                clickedElement.style.pointerEvents = 'auto';
            })
            .catch(error => {
                console.error('Download failed:', error);

                // Update toast message for error
                toast.querySelector('.toast-body').innerHTML = `
                <div class="text-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message || 'Download failed. Please try again.'}
                </div>
            `;

                // Keep error message visible longer
                setTimeout(() => {
                    toast.remove();
                }, 3000);

                // Cleanup
                loadingElement.remove();
                clickedElement.style.pointerEvents = 'auto';
            });
    }
</script>

</body>

</html>