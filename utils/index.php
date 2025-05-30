<?php

require_once(__DIR__ . "/../crest/crest.php");
require_once(__DIR__ . "/../crest/crestcurrent.php");


function buildApiUrl($baseUrl, $entityTypeId, $fields, $start = 0)
{
    $selectParams = '';
    foreach ($fields as $index => $field) {
        $selectParams .= "select[$index]=$field&";
    }
    $selectParams = rtrim($selectParams, '&');
    return "$baseUrl/crm.item.list?entityTypeId=$entityTypeId&$selectParams&start=$start&filter[ufCrm5Status]=PUBLISHED";
}

function fetchAllProperties($baseUrl, $entityTypeId, $fields, $platform = null)
{
    $allProperties = [];
    $start = 0;

    try {
        while (true) {
            $apiUrl = buildApiUrl($baseUrl, $entityTypeId, $fields, $start);
            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);

            if (isset($data['result']['items'])) {
                $properties = $data['result']['items'];
                $allProperties = array_merge($allProperties, $properties);
            }

            // If there's no "next" key, we've fetched all data
            if (empty($data['next'])) {
                break;
            }

            $start = $data['next'];
        }

        if ($platform) {
            switch ($platform) {
                case 'pf':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm5PfEnable'] === 'Y';
                    });
                    break;
                case 'bayut':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm5BayutEnable'] === 'Y';
                    });
                    break;
                case 'dubizzle':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm5DubizzleEnable'] === 'Y';
                    });
                    break;
                case 'website':
                    $allProperties = array_filter($allProperties, function ($property) {
                        return $property['ufCrm5WebsiteEnable'] === 'Y';
                    });
                    break;
                default:
                    break;
            }
        }

        return $allProperties;
    } catch (Exception $e) {
        error_log('Error fetching properties: ' . $e->getMessage());
        return [];
    }
}

function getPropertyPurpose($property)
{
    return ($property['ufCrm5OfferingType'] == 'RR' || $property['ufCrm5OfferingType'] == 'CR') ? 'Rent' : 'Buy';
}

function getPropertyType($property)
{
    $property_types = array(
        "AP" => "Apartment",
        "BW" => "Bungalow",
        "CD" => "Compound",
        "DX" => "Duplex",
        "FF" => "Full floor",
        "HF" => "Half floor",
        "PH" => "Penthouse",
        "TH" => "Townhouse",
        "VH" => "Villa",
        "WB" => "Whole Building",
        "HA" => "Short Term / Hotel Apartment",
        "LC" => "Labor camp",
        "BU" => "Bulk units",
        "WH" => "Warehouse",
        "FA" => "Factory",
        "OF" => "Office space",
        "RE" => "Retail",
        "LP" => $property['ufCrm5OfferingType'] == 'CR' || $property['ufCrm5OfferingType'] == 'CS' ? "Commercial Plot" : "Residential Plot",
        "SH" => "Shop",
        "SR" => "Show Room",
        "SA" => "Staff Accommodation"
    );

    return $property_types[$property['ufCrm5PropertyType']] ?? '';
}

function getPermitNumber($property)
{
    if (!empty($property['ufCrm5PermitNumber'])) {
        return $property['ufCrm5PermitNumber'];
    }
    return $property['ufCrm5ReraPermitNumber'] ?? '';
}

function getFullAmenityName($shortCode)
{
    $amenityMap = [
        'BA' => 'Balcony',
        'BP' => 'Basement parking',
        'BB' => 'BBQ area',
        'AN' => 'Cable-ready',
        'BW' => 'Built in wardrobes',
        'CA' => 'Carpets',
        'AC' => 'Central air conditioning',
        'CP' => 'Covered parking',
        'DR' => 'Drivers room',
        'FF' => 'Fully fitted kitchen',
        'GZ' => 'Gazebo',
        'PY' => 'Private Gym',
        'PJ' => 'Jacuzzi',
        'BK' => 'Kitchen Appliances',
        'MR' => 'Maids Room',
        'MB' => 'Marble floors',
        'HF' => 'On high floor',
        'LF' => 'On low floor',
        'MF' => 'On mid floor',
        'PA' => 'Pets allowed',
        'GA' => 'Private garage',
        'PG' => 'Garden',
        'PP' => 'Swimming pool',
        'SA' => 'Sauna',
        'SP' => 'Shared swimming pool',
        'WF' => 'Wood flooring',
        'SR' => 'Steam room',
        'ST' => 'Study',
        'UI' => 'Upgraded interior',
        'GR' => 'Garden view',
        'VW' => 'Sea/Water view',
        'SE' => 'Security',
        'MT' => 'Maintenance',
        'IC' => 'Within a Compound',
        'IS' => 'Indoor swimming pool',
        'SF' => 'Separate entrance for females',
        'BT' => 'Basement',
        'SG' => 'Storage room',
        'CV' => 'Community view',
        'GV' => 'Golf view',
        'CW' => 'City view',
        'NO' => 'North orientation',
        'SO' => 'South orientation',
        'EO' => 'East orientation',
        'WO' => 'West orientation',
        'NS' => 'Near school',
        'HO' => 'Near hospital',
        'TR' => 'Terrace',
        'NM' => 'Near mosque',
        'SM' => 'Near supermarket',
        'ML' => 'Near mall',
        'PT' => 'Near public transportation',
        'MO' => 'Near metro',
        'VT' => 'Near veterinary',
        'BC' => 'Beach access',
        'PK' => 'Public parks',
        'RT' => 'Near restaurants',
        'NG' => 'Near Golf',
        'AP' => 'Near airport',
        'CS' => 'Concierge Service',
        'SS' => 'Spa',
        'SY' => 'Shared Gym',
        'MS' => 'Maid Service',
        'WC' => 'Walk-in Closet',
        'HT' => 'Heating',
        'GF' => 'Ground floor',
        'SV' => 'Server room',
        'DN' => 'Pantry',
        'RA' => 'Reception area',
        'VP' => 'Visitors parking',
        'OP' => 'Office partitions',
        'SH' => 'Core and Shell',
        'CD' => 'Children daycare',
        'CL' => 'Cleaning services',
        'NH' => 'Near Hotel',
        'CR' => 'Conference room',
        'BL' => 'View of Landmark',
        'PR' => 'Children Play Area',
        'BH' => 'Beach Access',
        'CO' => 'Children Pool',
        'SP' => 'Shared Pool',
    ];

    return $amenityMap[$shortCode] ?? $shortCode;
}

function formatDate($date)
{
    return $date ? date('Y-m-d H:i:s', strtotime($date)) : date('Y-m-d H:i:s');
}

function formatField($field, $value, $type = 'string')
{
    if (empty($value) && $value != 0) {
        return '';
    }

    switch ($type) {
        case 'date':
            return '<' . $field . '>' . formatDate($value) . '</' . $field . '>';
        default:
            return '<' . $field . '>' . htmlspecialchars($value) . '</' . $field . '>';
    }
}

function formatPriceOnApplication($property)
{
    $priceOnApplication = ($property['ufCrm5HidePrice'] === 'Y') ? 'Yes' : 'No';
    return formatField('price_on_application', $priceOnApplication);
}

function formatRentalPrice($property)
{
    if (empty($property['ufCrm5Price'])) {
        return '<price></price>';
    }

    $price = (int) $property['ufCrm5Price'];
    $rentalPeriod = $property['ufCrm5RentalPeriod'] ?? '';

    $minPrices = [
        'Y' => 10000, // Yearly rent
        'M' => 1000,  // Monthly rent
        'W' => 1000,  // Weekly rent
        'D' => 100,   // Daily rent
    ];

    if (isset($minPrices[$rentalPeriod]) && $price < $minPrices[$rentalPeriod]) {
        return '<price></price>'; // Leave empty tag if below minimum
    }

    // If it's a sales price, return directly
    if (!$rentalPeriod) {
        return "<price>{$price}</price>";
    }

    // Construct rental price XML dynamically (avoid empty tags)
    $rentalPrices = [];
    if ($rentalPeriod == 'Y') $rentalPrices[] = "<yearly>{$price}</yearly>";
    if ($rentalPeriod == 'M') $rentalPrices[] = "<monthly>{$price}</monthly>";
    if ($rentalPeriod == 'W') $rentalPrices[] = "<weekly>{$price}</weekly>";
    if ($rentalPeriod == 'D') $rentalPrices[] = "<daily>{$price}</daily>";

    return "<price>\n" . implode("\n", $rentalPrices) . "\n</price>";
}

function formatBedroom($property)
{
    return formatField('bedroom', ($property['ufCrm5Bedroom'] > 7) ? '7+' : $property['ufCrm5Bedroom']);
}

function formatBathroom($property)
{
    return formatField('bathroom', ($property['ufCrm5Bathroom'] > 7) ? '7+' : $property['ufCrm5Bathroom']);
}

function formatFurnished($property)
{
    $furnished = $property['ufCrm5Furnished'] ?? '';
    if ($furnished) {
        switch ($furnished) {
            case 'furnished':
                return formatField('furnished', 'Yes');
            case 'unfurnished':
                return formatField('furnished', 'No');
            case 'Partly Furnished':
                return formatField('semi-furnished', 'Partly');
            default:
                return '';
        }
    }
    return ''; // If no furnished value exists, return an empty string
}

function formatAgent($property)
{
    $xml = '<agent>';
    $xml .= formatField('id', $property['ufCrm5AgentId']);
    $xml .= formatField('name', $property['ufCrm5AgentName']);
    $xml .= formatField('email', $property['ufCrm5AgentEmail']);
    $xml .= formatField('phone', $property['ufCrm5AgentPhone']);
    $xml .= formatField('photo', $property['ufCrm5AgentPhoto'] ?? 'https://youtupia.com/thinkrealty/images/agent-placeholder.webp');
    $xml .= '</agent>';

    return $xml;
}

function formatPhotos($photos)
{
    if (empty($photos)) {
        return '';
    }

    $xml = '<photo>';
    foreach ($photos as $photo) {
        $xml .= '<url last_update="' . date('Y-m-d H:i:s') . '" watermark="Yes">' . htmlspecialchars($photo) . '</url>';
    }
    $xml .= '</photo>';

    return $xml;
}

function formatGeopoints($property)
{
    if (!empty($property['ufCrm5Latitude']) && !empty($property['ufCrm5Longitude'])) {
        $geopoints = $property['ufCrm5Latitude'] . ',' . $property['ufCrm5Longitude'];
    } else {
        $geopoints = $property['ufCrm5Geopoints'] ?? '';
    }
    return formatField('geopoints', $geopoints);
}

function formatCompletionStatus($property)
{
    $status = $property['ufCrm5ProjectStatus'] ?? '';
    switch ($status) {
        case 'Completed':
        case 'ready_secondary':
            return formatField('completion_status', 'completed');
        case 'offplan':
        case 'offplan_secondary':
            return formatField('completion_status', 'off_plan');
        case 'ready_primary':
            return formatField('completion_status', 'completed_primary');
        case 'offplan_primary':
            return formatField('completion_status', 'off_plan_primary');
        default:
            return '';
    }
}

function getLastUpdated()
{
    $response = CRest::call('crm.item.list', [
        'order' => ['updatedTime' => 'desc'],
        'select' => ['updatedTime'],
    ]);

    $isoDate = $response['result']['items'][0]['updatedTime'];
    $dateTime = new DateTime($isoDate);

    return $dateTime->format('Y-m-d H:i:s');
}

function generatePfXml($properties)
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<list last_update="' . getLastUpdated() . '" listing_count="' . count($properties) . '">';

    foreach ($properties as $property) {
        $xml .= '<property last_update="' . formatDate($property['updatedTime'] ?? '') . '" id="' . htmlspecialchars($property['id'] ?? '') . '">';

        $xml .= formatField('reference_number', $property['ufCrm5ReferenceNumber']);
        $xml .= formatField('permit_number', getPermitNumber($property));

        $xml .= formatField('dtcm_permit', $property['ufCrm5DtcmPermitNumber']);
        $xml .= formatField('offering_type', $property['ufCrm5OfferingType']);
        $xml .= formatField('property_type', $property['ufCrm5PropertyType']);
        $xml .= formatPriceOnApplication($property);
        $xml .= formatRentalPrice($property);

        $xml .= formatField('service_charge', $property['ufCrm5ServiceCharge']);
        $xml .= formatField('cheques', $property['ufCrm5NoOfCheques']);
        $xml .= formatField('city', $property['ufCrm5City']);
        $xml .= formatField('community', $property['ufCrm5Community']);
        $xml .= formatField('sub_community', $property['ufCrm5SubCommunity']);
        $xml .= formatField('property_name', $property['ufCrm5Tower']);

        $xml .= formatField('title_en', $property['ufCrm5TitleEn']);
        $xml .= formatField('title_ar', $property['ufCrm5TitleAr']);
        $xml .= formatField('description_en', $property['ufCrm5DescriptionEn']);
        $xml .= formatField('description_ar', $property['ufCrm5DescriptionAr']);

        $xml .= formatField('plot_size', $property['ufCrm5TotalPlotSize']);
        $xml .= formatField('size', $property['ufCrm5Size']);
        // $xml .= formatField('bedroom', $property['ufCrm5Bedroom']);
        $xml .= formatBedroom($property);
        $xml .= formatBathroom($property);

        $xml .= formatAgent($property);
        $xml .= formatField('build_year', $property['ufCrm5BuildYear']);
        $xml .= formatField('parking', $property['ufCrm5Parking']);
        $xml .= formatFurnished($property);
        $xml .= formatField('view360', $property['ufCrm_5_360_VIEW_URL']);
        $xml .= formatPhotos($property['ufCrm5PhotoLinks']);
        $xml .= formatField('floor_plan', $property['ufCrm5FloorPlan']);
        $xml .= formatGeopoints($property);
        $xml .= formatField('availability_date', $property['ufCrm5AvailableFrom'], 'date');
        $xml .= formatField('video_tour_url', $property['ufCrm5VideoTourUrl']);
        $xml .= formatField('developer', $property['ufCrm5Developers']);
        $xml .= formatField('project_name', $property['ufCrm5ProjectName']);
        $xml .= formatCompletionStatus($property);

        $xml .= '</property>';
    }

    $xml .= '</list>';
    return $xml;
}

function generateBayutXml($properties)
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<Properties last_update="' . getLastUpdated() . '" listing_count="' . count($properties) . '">';

    foreach ($properties as $property) {
        $xml .= '<Property id="' . htmlspecialchars($property['id'] ?? '') . '">';

        // Ensure proper CDATA wrapping and no misplaced closing tags
        $xml .= '<Property_Ref_No><![CDATA[' . ($property['ufCrm5ReferenceNumber'] ?? '') . ']]></Property_Ref_No>';
        $xml .= '<Permit_Number><![CDATA[' . getPermitNumber($property) . ']]></Permit_Number>';
        $xml .= '<Property_Status>live</Property_Status>';
        $xml .= '<Property_purpose><![CDATA[' . getPropertyPurpose($property) . ']]></Property_purpose>';
        $xml .= '<Property_Type><![CDATA[' . getPropertyType($property) . ']]></Property_Type>';
        $xml .= '<Property_Size><![CDATA[' . ($property['ufCrm5Size'] ?? '') . ']]></Property_Size>';
        $xml .= '<Property_Size_Unit>SQFT</Property_Size_Unit>';

        // Ensure proper condition for optional fields
        if (!empty($property['ufCrm5TotalPlotSize'])) {
            $xml .= '<plotArea><![CDATA[' . $property['ufCrm5TotalPlotSize'] . ']]></plotArea>';
        }

        $xml .= '<Bedrooms><![CDATA[' . (($property['ufCrm5Bedroom'] === 0) ? -1 : ($property['ufCrm5Bedroom'] > 10 ? "10+" : $property['ufCrm5Bedroom'])) . ']]></Bedrooms>';
        $xml .= '<Bathrooms><![CDATA[' . ($property['ufCrm5Bathroom'] ?? '') . ']]></Bathrooms>';

        $is_offplan = ($property['ufCrm5ProjectStatus'] === 'offplan_primary' || $property['ufCrm5ProjectStatus'] === 'offplan_secondary') ? 'Yes' : 'No';
        $xml .= '<Off_plan><![CDATA[' . $is_offplan . ']]></Off_plan>';

        $xml .= '<Portals>';
        if ($property['ufCrm5BayutEnable'] === 'Y') {
            $xml .= '<Portal>Bayut</Portal>';
        }
        if ($property['ufCrm5DubizzleEnable'] === 'Y') {
            $xml .= '<Portal>Dubizzle</Portal>';
        }
        $xml .= '</Portals>';

        $xml .= '<Property_Title><![CDATA[' . ($property['ufCrm5TitleEn'] ?? '') . ']]></Property_Title>';
        $xml .= '<Property_Description><![CDATA[' . ($property['ufCrm5DescriptionEn'] ?? '') . ']]></Property_Description>';

        if (!empty($property['ufCrm5TitleAr'])) {
            $xml .= '<Property_Title_AR><![CDATA[' . ($property['ufCrm5TitleAr'] ?? '') . ']]></Property_Title_AR>';
        }
        if (!empty($property['ufCrm5DescriptionAr'])) {
            $xml .= '<Property_Description_AR><![CDATA[' . ($property['ufCrm5DescriptionAr'] ?? '') . ']]></Property_Description_AR>';
        }

        $xml .= '<Price><![CDATA[' . ($property['ufCrm5Price'] ?? '') . ']]></Price>';

        if ($property['ufCrm5RentalPeriod'] == 'Y') {
            $xml .= '<Rent_Frequency>Yearly</Rent_Frequency>';
        } elseif ($property['ufCrm5RentalPeriod'] == 'M') {
            $xml .= '<Rent_Frequency>Monthly</Rent_Frequency>';
        } elseif ($property['ufCrm5RentalPeriod'] == 'W') {
            $xml .= '<Rent_Frequency>Weekly</Rent_Frequency>';
        } elseif ($property['ufCrm5RentalPeriod'] == 'D') {
            $xml .= '<Rent_Frequency>Daily</Rent_Frequency>';
        }

        if ($property['ufCrm5Furnished'] === 'furnished' || $property['ufCrm5Furnished'] === 'Yes') {
            $xml .= '<Furnished>Yes</Furnished>';
        } elseif ($property['ufCrm5Furnished'] === 'unfurnished' || $property['ufCrm5Furnished'] === 'No') {
            $xml .= '<Furnished>No</Furnished>';
        } elseif ($property['ufCrm5Furnished'] === 'semi-furnished' || $property['ufCrm5Furnished'] === 'Partly') {
            $xml .= '<Furnished>Partly</Furnished>';
        }

        if (!empty($property['ufCrm5SaleType'])) {
            $xml .= '<offplanDetails_saleType><![CDATA[' . ($property['ufCrm5SaleType'] ?? '') . ']]></offplanDetails_saleType>';

            if($property['ufCrm5SaleType'] === 'Resale') {
                $xml .= '<offplanDetails_originalPrice><![CDATA[' . ($property['ufCrm5Price'] ?? '') . ']]></offplanDetails_originalPrice>';
                $xml .= '<offplanDetails_amountPaid><![CDATA[' . ($property['ufCrm5Price'] ?? '') . ']]></offplanDetails_amountPaid>';
            }
        }

        $xml .= '<City><![CDATA[' . ($property['ufCrm5BayutCity'] ?: $property['ufCrm5City'] ?? '') . ']]></City>';
        $xml .= '<Locality><![CDATA[' . ($property['ufCrm5BayutCommunity'] ?: $property['ufCrm5Community'] ?? '') . ']]></Locality>';
        $xml .= '<Sub_Locality><![CDATA[' . ($property['ufCrm5BayutSubCommunity'] ?: $property['ufCrm5SubCommunity'] ?? '') . ']]></Sub_Locality>';
        $xml .= '<Tower_Name><![CDATA[' . ($property['ufCrm5BayutTower'] ?: $property['ufCrm5Tower'] ?? '') . ']]></Tower_Name>';

        $xml .= '<Listing_Agent><![CDATA[' . ($property['ufCrm5AgentName'] ?? '') . ']]></Listing_Agent>';
        $xml .= '<Listing_Agent_Phone><![CDATA[' . ($property['ufCrm5AgentPhone'] ?? '') . ']]></Listing_Agent_Phone>';
        $xml .= '<Listing_Agent_Email><![CDATA[' . ($property['ufCrm5AgentEmail'] ?? '') . ']]></Listing_Agent_Email>';

        $xml .= '<Images>';
        foreach ($property['ufCrm5PhotoLinks'] ?? [] as $image) {
            $xml .= '<Image last_update="' . date('Y-m-d H:i:s') . '"><![CDATA[' . $image . ']]></Image>';
        }
        $xml .= '</Images>';

        if (!empty($property['ufCrm5Amenities']) && is_array($property['ufCrm5Amenities'])) {
            $xml .= '<Features>';
            foreach ($property['ufCrm5Amenities'] as $amenity) {
                $fullName = getFullAmenityName(trim($amenity));
                $xml .= '<Feature><![CDATA[' . $fullName . ']]></Feature>';
            }
            $xml .= '</Features>';
        }

        $xml .= '</Property>';
    }

    $xml .= '</Properties>';
    return $xml;
}

function uploadFile($file, $isDocument = false)
{
    global $cloudinary;

    try {
        if (!file_exists($file)) {
            throw new Exception("File not found: " . $file);
        }

        $uploadResponse = $cloudinary->uploadApi()->upload($file, [
            'folder' => 'bwc-uploads',
            'resource_type' => $isDocument ? 'raw' : 'image',
        ]);

        return $uploadResponse['secure_url'];
    } catch (Exception $e) {
        error_log("Error uploading image: " . $e->getMessage());
        echo "Error uploading image: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        return false;
    }
}

function fetchCurrentUser()
{
    $response = CRestCurrent::call("user.current");
    return $response['result'];
}

function isAdmin($userId)
{
    $response = CRestCurrent::call("user.admin");

    $admins = [
        8, // VortexWeb,
        15, // Janhelva Caranto,
        1, // Taha Ali
        281, // Eloisa Castro,
        173 // Charmaine Cunado
    ];

    return $response['result'] || in_array($userId, $admins);
}


function generateWebsiteJson($properties)
{
    $json = json_encode([
        'properties' => $properties,
        'total' => count($properties)
    ]);

    return $json;
}
