<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Property Details</title>
  <link rel="stylesheet" href="./assets/css/brochure.css">
</head>

<body>
  <div class="header">
    <h1 class="property-title">Springs 14 | 3M | Vastu-Compliant | Lake View</h1>
    <div class="price">AED 5,800,000</div>
    <p class="location">Springs 14, The Springs, Dubai</p>
  </div>

  <div class="image-grid">
    <img src="https://placehold.jp/400x800.png" height="400" width="800" alt="Lake View" class="main-image">
    <img src="https://placehold.jp/200x400.png" height="200" width="400" alt="Blue Kitchen" class="small-image">
    <img src="https://placehold.jp/200x400.png" height="200" width="400" alt="Living Room" class="small-image">
  </div>

  <div class="details-section">
    <h2 class="section-title">Property Details</h2>
    <div class="property-details">
      <p class="description">Luxurious 3M villa with scenic lake views and modern amenities. This Vastu-compliant property features a stunning blue kitchen, spacious living areas, and premium finishes throughout. The property includes multiple bedrooms, modern bathrooms, and a well-designed layout perfect for family living.</p>
    </div>
  </div>

  <div class="details-section">
    <h2 class="section-title">Facilities and Amenities</h2>
    <ul class="amenities-list">
      <li>✓ item 1</li>
      <li>✓ item 1</li>
      <li>✓ item 1</li>
      <li>✓ item 1</li>
      <li>✓ item 1</li>
      <li>✓ item 1</li>
      <li>✓ item 1</li>
      <li>✓ item 1</li>
    </ul>
  </div>

  <!-- <div class="details-section">
    <h2 class="section-title">Location</h2>
    <div class="mapouter">
      <div class="gmap_canvas"><iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=600&height=400&amp;hl=en&amp;q=Business Bay&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe><a href="https://sprunkin.com/">Sprunki Phases</a></div>
      <style>
        .mapouter {
          position: relative;
          text-align: right;
          width: 100%;
          height: 200px;
        }

        .gmap_canvas {
          overflow: hidden;
          background: none !important;
          width: 100%;
          height: 200px;
        }

        .gmap_iframe {
          width: 100% !important;
          height: 200px !important;
        }
      </style>
    </div>
  </div> -->

  <div class="container">
    <div class="agent-info">
      <div class="agent-photo">
        <img src="https://youtupia.com/thinkrealty/images/agent-placeholder.webp" alt="Agent Photo" height="40" width="40">
      </div>
      <div>
        <h3 class="agent-name">Property Agent</h3>
        <p class="agent-email-and-phone">Contact for more information</p>
      </div>
    </div>

    <div class="company-info">
      <h3 class="company-name">BWC Real Estate</h3>
      <p class="company-phone">+971 54 594 4440</p>
    </div>
  </div>


  <script src="./assets/js/brochure.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", async function() {
      const propertyId = window.location.search.split("id=")[1];

      async function fetchAndUpdatePropertyDetails() {
        try {
          const API_URL = `https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.get?entityTypeId=1036&id=${propertyId}`;
          const response = await fetch(API_URL);
          const data = await response.json();
          const property = await data.result.item;

          document.querySelector(".property-title").textContent = property.ufCrm5TitleEn ?? "No Title";
          document.querySelector(".price").textContent = formatPrice(property.ufCrm5Price) + ' AED | For ' + mapOfferingType(property.ufCrm5OfferingType) ?? "Not Available";
          document.querySelector(".location").textContent =
            (property.ufCrm5Location ?? "Unknown").replaceAll(" - ", ", ");
          document.querySelector(".description").innerHTML = property.ufCrm5DescriptionEn ?? "Not Available";
          document.querySelector(".agent-name").textContent = property.ufCrm5AgentName ?? "No Agent Name";
          document.querySelector(".agent-email-and-phone").textContent = property.ufCrm5AgentPhone ?? "No Agent Phone";
          document.querySelector('ul.amenities-list').innerHTML = property.ufCrm5Amenities.slice(0, 9).map(amenity => `<li>✓ ${getAmenityName(amenity)}</li>`).join('');
          // document.querySelector('iframe.gmap_iframe').src = `https://maps.google.com/maps?width=600&height=400&hl=en&q=${property.ufCrm5Community ?? property.ufCrm5City}&t=&z=14&ie=UTF8&iwloc=B&output=embed`;
          document.querySelector(".image-grid").children[0].src = `proxy.php?url=${encodeURIComponent(property.ufCrm5PhotoLinks[0])}` ?? "https://placehold.jp/400x800.png";
          document.querySelector(".image-grid").children[1].src = `proxy.php?url=${encodeURIComponent(property.ufCrm5PhotoLinks[1])}` ?? "https://placehold.jp/200x400.png";
          document.querySelector(".image-grid").children[2].src = `proxy.php?url=${encodeURIComponent(property.ufCrm5PhotoLinks[2])}` ?? "https://placehold.jp/200x400.png";
          document.querySelector(".agent-photo").children[0].src = property.ufCrm5AgentPhoto !== '' ? property.ufCrm5AgentPhoto : `proxy.php?url=${encodeURIComponent('https://youtupia.com/thinkrealty/images/agent-placeholder.webp')}`;

          return property;
        } catch (error) {
          console.error("Error fetching property details:", error);
        }
      }

      async function convertImagesToBase64() {
        const images = document.querySelectorAll("img");
        const promises = Array.from(images).map(img => {
          return new Promise((resolve, reject) => {
            const imgElement = new Image();
            imgElement.crossOrigin = "anonymous";
            imgElement.src = img.src;
            imgElement.onload = function() {
              const canvas = document.createElement("canvas");
              canvas.width = imgElement.width;
              canvas.height = imgElement.height;
              const ctx = canvas.getContext("2d");
              ctx.drawImage(imgElement, 0, 0);
              img.src = canvas.toDataURL("image/png");
              resolve();
            };
            imgElement.onerror = reject;
          });
        });
        await Promise.all(promises);
      }

      const property = await fetchAndUpdatePropertyDetails();
      await convertImagesToBase64();

      window.location.replace('./index.php')

      html2canvas(document.body, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
        width: 794,
        height: 1123,
        windowWidth: document.body.scrollWidth,
        windowHeight: document.body.scrollHeight,
      }).then(canvas => {
        const link = document.createElement("a");
        link.href = canvas.toDataURL("image/png");
        link.download = sanitizeFileName(`${property.ufCrm5TitleEn}`) + '.png';
        link.click();
      });
    });
  </script>

</body>

</html>