<?php

require __DIR__ . "/crest/crest.php";
require __DIR__ . "/crest/settings.php";
require __DIR__ . "/utils/index.php";

$type = $_GET['type'];
$id = $_GET['id'];

$response = CRest::call('crm.item.get', [
  "entityTypeId" => LISTINGS_ENTITY_TYPE_ID,
  "select" => [
    "ufCrm5PhotoLinks",
    "ufCrm5ReferenceNumber"
  ],
  "id" => $id
]);

$property = $response['result']['item'];

if (!$property) {
  die("Property not found.");
}

$imageLinks = $property['ufCrm5PhotoLinks'];

if (empty($imageLinks)) {
  die("No images found for this property.");
}

$tempDir = sys_get_temp_dir() . "/property_images_$property[ufCrm5ReferenceNumber]";
if (!file_exists($tempDir)) {
  mkdir($tempDir, 0777, true);
}

foreach ($imageLinks as $index => $imageUrl) {
  $imageUrl = trim($imageUrl);
  $imageContents = file_get_contents($imageUrl);
  if ($imageContents === false) {
    continue;
  }

  $imageName = "image_" . ($index + 1) . ".jpg";
  file_put_contents("$tempDir/$imageName", $imageContents);
}

$zipFileName = "property_images_$property[ufCrm5ReferenceNumber].zip";
$zipFilePath = sys_get_temp_dir() . "/$zipFileName";

$zip = new ZipArchive();
if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
  $files = scandir($tempDir);
  foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
      $zip->addFile("$tempDir/$file", $file);
    }
  }
  $zip->close();
} else {
  die("Failed to create ZIP file.");
}

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
header('Content-Length: ' . filesize($zipFilePath));

readfile($zipFilePath);

array_map('unlink', glob("$tempDir/*"));
rmdir($tempDir);
unlink($zipFilePath);

header('Location: index.php');
exit;
