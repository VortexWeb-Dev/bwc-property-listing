<?php
require 'utils/index.php';

header('Content-Type: application/json; charset=UTF-8');

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
    'ufCrm5WebsiteEnable',
    'updatedTime'
];

$properties = fetchAllProperties($baseUrl, $entityTypeId, $fields,);

if (count($properties) > 0) {
    $json = generateWebsiteJson($properties);
    echo $json;
} else {
    echo json_encode([]);
}
