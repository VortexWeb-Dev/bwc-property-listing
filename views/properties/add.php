<div class="w-4/5 mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <form class="w-full space-y-4" id="addPropertyForm" onsubmit="handleAddProperty(event)" enctype="multipart/form-data">

            <div class="mb-4">
                <button type="button" onclick="javascript:history.back()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1">
                    Back
                </button>
            </div>

            <!-- Management -->
            <?php include_once('views/components/add-property/management.php'); ?>
            <!-- Specifications -->
            <?php include_once('views/components/add-property/specifications.php'); ?>
            <!-- Property Permit -->
            <?php include_once('views/components/add-property/permit.php'); ?>
            <!-- Pricing -->
            <?php include_once('views/components/add-property/pricing.php'); ?>
            <!-- Title and Description -->
            <?php include_once('views/components/add-property/title.php'); ?>
            <!-- Amenities -->
            <?php include_once('views/components/add-property/amenities.php'); ?>
            <!-- Location -->
            <?php include_once('views/components/add-property/location.php'); ?>
            <!-- Photos and Videos -->
            <?php include_once('views/components/add-property/media.php'); ?>
            <!-- Floor Plan -->
            <?php include_once('views/components/add-property/floorplan.php'); ?>
            <!-- Documents -->
            <?php // include_once('views/components/add-property/documents.php'); 
            ?>
            <!-- Notes -->
            <?php include_once('views/components/add-property/notes.php'); ?>
            <!-- Portals -->
            <?php include_once('views/components/add-property/portals.php'); ?>
            <!-- Status -->
            <?php include_once('views/components/add-property/status.php'); ?>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="javascript:history.back()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1">
                    Cancel
                </button>
                <button type="submit" id="submitButton" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("offering_type").addEventListener("change", async function() {
        const offeringType = this.value;
        console.log(offeringType);

        if (offeringType == 'RR' || offeringType == 'CR') {
            document.getElementById("rental_period").setAttribute("required", true);
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental) <span class="text-danger">*</span>';
        } else {
            document.getElementById("rental_period").removeAttribute("required");
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental)';
        }

        const newReference = await getNewReference(offeringType);
        document.getElementById("reference").value = newReference;
    })

    async function addItem(entityTypeId, fields) {
        try {
            const response = await fetch(`https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.add?entityTypeId=${entityTypeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fields,
                }),
            });

            if (response.ok) {
                window.location.href = 'index.php?page=properties';
            } else {
                console.error('Failed to add item');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function handleAddProperty(e) {
        e.preventDefault();

        document.getElementById('submitButton').disabled = true;
        document.getElementById('submitButton').innerHTML = 'Submitting...';

        const form = document.getElementById('addPropertyForm');
        const formData = new FormData(form);
        const data = {};

        formData.forEach((value, key) => {
            data[key] = typeof value === 'string' ? value.trim() : value;
        });

        const agent = await getAgent(data.listing_agent);

        const fields = {
            "title": data.title_deed,
            "ufCrm5ReferenceNumber": data.reference,
            "ufCrm5OfferingType": data.offering_type,
            "ufCrm5PropertyType": data.property_type,
            "ufCrm5Price": data.price,
            "ufCrm5TitleEn": data.title_en,
            "ufCrm5DescriptionEn": data.description_en,
            "ufCrm5TitleAr": data.title_ar,
            "ufCrm5DescriptionAr": data.description_ar,
            "ufCrm5Size": data.size,
            "ufCrm5Bedroom": data.bedrooms,
            "ufCrm5Bathroom": data.bathrooms,
            "ufCrm5Parking": data.parkings,
            "ufCrm5Geopoints": `${data.latitude}, ${data.longitude}`,
            "ufCrm5PermitNumber": data.dtcm_permit_number,
            "ufCrm5RentalPeriod": data.rental_period,
            "ufCrm5Furnished": data.furnished,
            "ufCrm5TotalPlotSize": data.total_plot_size,
            "ufCrm5LotSize": data.lot_size,
            "ufCrm5BuildupArea": data.buildup_area,
            "ufCrm5LayoutType": data.layout_type,
            "ufCrm5ProjectName": data.project_name,
            "ufCrm5ProjectStatus": data.project_status,
            "ufCrm5Ownership": data.ownership,
            "ufCrm5Developers": data.developer,
            "ufCrm5BuildYear": data.build_year,
            "ufCrm5Availability": data.availability,
            "ufCrm5AvailableFrom": data.available_from,
            "ufCrm5PaymentMethod": data.payment_method,
            "ufCrm5DownPaymentPrice": data.downpayment_price,
            "ufCrm5NoOfCheques": data.cheques,
            "ufCrm5ServiceCharge": data.service_charge,
            "ufCrm5FinancialStatus": data.financial_status,
            "ufCrm5VideoTourUrl": data.video_tour_url,
            "ufCrm_5_360_VIEW_URL": data["360_view_url"],
            "ufCrm5QrCodePropertyBooster": data.qr_code_url,
            "ufCrm5Location": data.pf_location,
            "ufCrm5City": data.pf_city,
            "ufCrm5Community": data.pf_community,
            "ufCrm5SubCommunity": data.pf_subcommunity,
            "ufCrm5Tower": data.pf_building,
            "ufCrm5BayutLocation": data.bayut_location,
            "ufCrm5BayutCity": data.bayut_city,
            "ufCrm5BayutCommunity": data.bayut_community,
            "ufCrm5BayutSubCommunity": data.bayut_subcommunity,
            "ufCrm5BayutTower": data.bayut_building,
            "ufCrm5Status": data.status,
            "ufCrm5ReraPermitNumber": data.rera_permit_number,
            "ufCrm5ReraPermitIssueDate": data.rera_issue_date,
            "ufCrm5ReraPermitExpirationDate": data.rera_expiration_date,
            "ufCrm5DtcmPermitNumber": data.dtcm_permit_number,
            "ufCrm5ListingOwner": data.listing_owner,
            "ufCrm5LandlordName": data.landlord_name,
            "ufCrm5LandlordEmail": data.landlord_email,
            "ufCrm5LandlordContact": data.landlord_phone,
            "ufCrm5ContractExpiryDate": data.contract_expiry,
            "ufCrm5UnitNo": data.unit_no,
            "ufCrm5SaleType": data.sale_type,
            "ufCrm5BrochureDescription": data.brochure_description_1,
            "ufCrm_5_BROCHURE_DESCRIPTION_2": data.brochure_description_2,
            "ufCrm5HidePrice": data.hide_price == "on" ? "Y" : "N",
            "ufCrm5PfEnable": data.pf_enable == "on" ? "Y" : "N",
            "ufCrm5BayutEnable": data.bayut_enable == "on" ? "Y" : "N",
            "ufCrm5DubizzleEnable": data.dubizzle_enable == "on" ? "Y" : "N",
            "ufCrm5WebsiteEnable": data.website_enable == "on" ? "Y" : "N",
        };

        if (agent) {
            fields["ufCrm5AgentId"] = agent.ufCrm7AgentId;
            fields["ufCrm5AgentName"] = agent.ufCrm7AgentName;
            fields["ufCrm5AgentEmail"] = agent.ufCrm7AgentEmail;
            fields["ufCrm5AgentPhone"] = agent.ufCrm7AgentMobile;
            fields["ufCrm5AgentPhoto"] = agent.ufCrm7AgentPhoto;
            fields["ufCrm5AgentLicense"] = agent.ufCrm7AgentLicense;
        }

        // Notes
        const notesString = data.notes;
        if (notesString) {
            const notesArray = JSON.parse(notesString);
            if (notesArray) {
                fields["ufCrm5Notes"] = notesArray;
            }
        }

        // Amenities
        const amenitiesString = data.amenities;
        if (amenitiesString) {
            const amenitiesArray = JSON.parse(amenitiesString);
            if (amenitiesArray) {
                fields["ufCrm5Amenities"] = amenitiesArray;
            }
        }

        // Property Photos
        const photos = document.getElementById('selectedImages').value;
        if (photos) {
            const fixedPhotos = photos.replace(/\\'/g, '"');
            const photoArray = JSON.parse(fixedPhotos);
            const watermarkPath = 'assets/images/watermark.png';
            const uploadedImages = await processBase64Images(photoArray, watermarkPath);

            if (uploadedImages.length > 0) {
                fields["ufCrm5PhotoLinks"] = uploadedImages;
            }
        }

        // Floorplan
        const floorplan = document.getElementById('selectedFloorplan').value;
        if (floorplan) {
            const fixedFloorplan = floorplan.replace(/\\'/g, '"');
            const floorplanArray = JSON.parse(fixedFloorplan);
            const watermarkPath = 'assets/images/watermark.png';
            const uploadedFloorplan = await processBase64Images(floorplanArray, watermarkPath);

            if (uploadedFloorplan.length > 0) {
                fields["ufCrm5FloorPlan"] = uploadedFloorplan[0];
            }
        }

        // Documents
        // const documents = document.getElementById('documents')?.files;
        // if (documents) {
        //     if (documents.length > 0) {
        //         let documentUrls = [];

        //         for (const document of documents) {
        //             if (document.size > 10485760) {
        //                 alert('File size must be less than 10MB');
        //                 return;
        //             }
        //             const uploadedDocument = await uploadFile(document);
        //             documentUrls.push(uploadedDocument);
        //         }

        //         fields["ufCrm5Documents"] = documentUrls;
        //     }

        // }

        // Add to CRM
        addItem(1036, fields, '?page=properties');
    }

    window.addEventListener('load', async () => {
        const newReference = await getNewReference('RS');
        document.getElementById("reference").value = newReference;
    });
</script>