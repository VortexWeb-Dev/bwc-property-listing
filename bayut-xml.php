<?php
require 'utils/index.php';

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = 'https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r';
$entityTypeId = 1036;
$fields = [
    'id',
    'ufCrm5ReferenceNumber',
    'ufCrm5PermitNumber',
    'ufCrm5ReraPermitNumber',
    'ufCrm5DtcmPermitNumber',
    'ufCrm5OfferingType',
    'ufCrm5PropertyType',
    'ufCrm5HidePrice',
    'ufCrm5RentalPeriod',
    'ufCrm5Price',
    'ufCrm5ServiceCharge',
    'ufCrm5NoOfCheques',
    'ufCrm5City',
    'ufCrm5Community',
    'ufCrm5SubCommunity',
    'ufCrm5Tower',
    'ufCrm5BayutCity',
    'ufCrm5BayutCommunity',
    'ufCrm5BayutSubCommunity',
    'ufCrm5BayutTower',
    'ufCrm5TitleEn',
    'ufCrm5TitleAr',
    'ufCrm5DescriptionEn',
    'ufCrm5DescriptionAr',
    'ufCrm5TotalPlotSize',
    'ufCrm5Size',
    'ufCrm5Bedroom',
    'ufCrm5Bathroom',
    'ufCrm5AgentId',
    'ufCrm5AgentName',
    'ufCrm5AgentEmail',
    'ufCrm5AgentPhone',
    'ufCrm5AgentPhoto',
    'ufCrm5BuildYear',
    'ufCrm5Parking',
    'ufCrm5Furnished',
    'ufCrm_5_360_VIEW_URL',
    'ufCrm5PhotoLinks',
    'ufCrm5FloorPlan',
    'ufCrm5Geopoints',
    'ufCrm5Latitude',
    'ufCrm5Longitude',
    'ufCrm5AvailableFrom',
    'ufCrm5VideoTourUrl',
    'ufCrm5Developers',
    'ufCrm5ProjectName',
    'ufCrm5ProjectStatus',
    'ufCrm5ListingOwner',
    'ufCrm5Status',
    'ufCrm5PfEnable',
    'ufCrm5BayutEnable',
    'ufCrm5DubizzleEnable',
    'ufCrm5SaleType',
    'ufCrm5WebsiteEnable',
    'updatedTime',
    'ufCrm5Amenities'
];

$properties = fetchAllProperties($baseUrl, $entityTypeId, $fields, 'bayut');

if (count($properties) > 0) {
    $xml = generateBayutXml($properties);
    echo $xml;
} else {
    echo '<?xml version="1.0" encoding="UTF-8"?><list></list>';
}
