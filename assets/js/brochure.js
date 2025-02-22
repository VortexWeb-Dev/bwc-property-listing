function getAmenityName(amenityId) {
  const amenities = [
    {
      id: "GV",
      label: "Golf view",
    },
    {
      id: "CW",
      label: "City view",
    },
    {
      id: "NO",
      label: "North orientation",
    },
    {
      id: "SO",
      label: "South orientation",
    },
    {
      id: "EO",
      label: "East orientation",
    },
    {
      id: "WO",
      label: "West orientation",
    },
    {
      id: "NS",
      label: "Near school",
    },
    {
      id: "HO",
      label: "Near hospital",
    },
    {
      id: "TR",
      label: "Terrace",
    },
    {
      id: "NM",
      label: "Near mosque",
    },
    {
      id: "SM",
      label: "Near supermarket",
    },
    {
      id: "ML",
      label: "Near mall",
    },
    {
      id: "PT",
      label: "Near public transportation",
    },
    {
      id: "MO",
      label: "Near metro",
    },
    {
      id: "VT",
      label: "Near veterinary",
    },
    {
      id: "BC",
      label: "Beach access",
    },
    {
      id: "PK",
      label: "Public parks",
    },
    {
      id: "RT",
      label: "Near restaurants",
    },
    {
      id: "NG",
      label: "Near Golf",
    },
    {
      id: "AP",
      label: "Near airport",
    },
    {
      id: "CS",
      label: "Concierge Service",
    },
    {
      id: "SS",
      label: "Spa",
    },
    {
      id: "SY",
      label: "Shared Gym",
    },
    {
      id: "MS",
      label: "Maid Service",
    },
    {
      id: "WC",
      label: "Walk-in Closet",
    },
    {
      id: "HT",
      label: "Heating",
    },
    {
      id: "GF",
      label: "Ground floor",
    },
    {
      id: "SV",
      label: "Server room",
    },
    {
      id: "DN",
      label: "Pantry",
    },
    {
      id: "RA",
      label: "Reception area",
    },
    {
      id: "VP",
      label: "Visitors parking",
    },
    {
      id: "OP",
      label: "Office partitions",
    },
    {
      id: "SH",
      label: "Core and Shell",
    },
    {
      id: "CD",
      label: "Children daycare",
    },
    {
      id: "CL",
      label: "Cleaning services",
    },
    {
      id: "NH",
      label: "Near Hotel",
    },
    {
      id: "CR",
      label: "Conference room",
    },
    {
      id: "BL",
      label: "View of Landmark",
    },
    {
      id: "PR",
      label: "Children Play Area",
    },
    {
      id: "BH",
      label: "Beach Access",
    },
  ];

  return (
    amenities.find((amenity) => amenity.id === amenityId)?.label || amenityId
  );
}

function mapOfferingType(offeringType) {
  if (offeringType === "RS" || offeringType === "CS") {
    return "Sale";
  }
  return "Rent";
}

function formatPrice(price) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "AED",
  }).format(price);
}


function sanitizeFileName(fileName) {
  return fileName.replace(/[^a-zA-Z0-9]/g, "_");
}