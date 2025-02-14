<div id="CityPopup" class="position-absolute mt-2 p-2 bg-white shadow z-index-1000 d-none" style="width: 300px;">
    <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
        <p class="form-label pb-2 text-secondary border-bottom">Result</p>
        <div id="resultContainer_city" class="list-group"></div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('city');
        const popup = document.getElementById('CityPopup');
        const resultContainer = document.getElementById('resultContainer_city');

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            if (query.length >= 2) {
                popup.classList.remove('d-none');
                popup.style.top = (searchInput.getBoundingClientRect().top + (window.pageYOffset || document.documentElement.scrollTop) - 170) + 'px';
                popup.style.left = (searchInput.getBoundingClientRect().left + (window.pageXOffset || document.documentElement.scrollLeft) - 300) + 'px';
                searchItems(query);
            } else {
                popup.classList.add('d-none');
                resultContainer.innerHTML = '';
            }
        });

        const searchItems = (query) => {
            const webhookUrl = 'https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.list';
            const data = {
                "entityTypeId": 1098,
                "select": ["id", "ufCrm33City"],
                "filter": {
                    "%ufCrm33City": query
                }
            };

            fetch(webhookUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    resultContainer.innerHTML = '';

                    if (data.error) {
                        console.error('Error:', data.error);
                        resultContainer.innerHTML = '<p>Error fetching data.</p>';
                        return;
                    }

                    let items = data.result.items.map(item => item.ufCrm33City).filter(Boolean);
                    let uniqueItems = [...new Set(items)]; // Remove duplicates

                    if (uniqueItems.length > 0) {
                        uniqueItems.forEach(city => {
                            const itemElement = document.createElement('li');
                            itemElement.classList.add('list-group-item');
                            itemElement.style.cursor = 'pointer';
                            itemElement.innerHTML = city;
                            itemElement.addEventListener('click', function() {
                                searchInput.value = city;
                                popup.classList.add('d-none');
                                resultContainer.innerHTML = '';
                            });
                            resultContainer.appendChild(itemElement);
                        });
                    } else {
                        resultContainer.innerHTML = '<p>No items found.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultContainer.innerHTML = '<p>Error fetching data.</p>';
                });
        };
    });
</script>