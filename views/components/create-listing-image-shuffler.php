<div class="text-center mb-3">
    <input type="file" class="d-none" id="photos" name="photos[]" accept="image/*" multiple>
    <label for="photos" class="dropzone d-block" id="dropzone">
        <div class="cursor-pointer p-12 flex justify-center bg-white border border-gray-300 rounded-xl" data-hs-file-upload-trigger="">
            <div class="text-center">
                <span class="inline-flex justify-center items-center size-16 bg-gray-100 text-gray-800 rounded-full">
                    <svg class="shrink-0 size-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" x2="12" y1="3" y2="15"></line>
                    </svg>
                </span>

                <div class="mt-4 flex flex-wrap justify-center text-sm leading-6 text-gray-600">
                    <span class="pe-1 font-medium text-gray-800">
                        Drop your file here or
                    </span>
                    <span class="bg-white font-semibold text-blue-600 hover:text-blue-700 rounded-lg decoration-2 hover:underline">browse</span>
                </div>

                <p class="mt-1 text-xs text-gray-400">Pick a file up to 10MB.</p>
            </div>
        </div>
    </label>
    <p class="text-left text-xs text-red-500 font-semibold mt-2 hidden" id="photosMessage"></p>
</div>
<div id="photoPreviewContainer" class="photoPreviewContainer"></div>
<input type="hidden" id="selectedImages" name="selectedImages" />

<style>
    .photoPreviewContainer {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .slot {
        width: 100px;
        height: 100px;
        position: relative;
    }

    .slot img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .remove-btn {
        position: absolute;
        top: 2px;
        right: 2px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 14px;
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let imageLinks = [];
        let selectedFiles = [];
        const previewContainer = document.getElementById('photoPreviewContainer');
        const selectedImagesInput = document.getElementById('selectedImages');
        const photosInput = document.getElementById('photos');
        const errorMessage = document.getElementById('photosMessage');

        function handleFiles(files) {
            files = Array.from(files);

            files.forEach((file) => {
                if (file.size < 10 * 1024 * 1024 && !selectedFiles.some(f => f.name === file.name)) {
                    selectedFiles.push(file);
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imageLinks.push(e.target.result);
                        updatePhotoPreview();
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function updatePhotoPreview() {
            previewContainer.innerHTML = "";
            imageLinks.forEach((src, i) => {
                const slot = document.createElement('div');
                slot.classList.add('slot');

                const img = document.createElement('img');
                img.src = src;

                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = "&times;";
                removeBtn.classList.add("remove-btn");
                removeBtn.onclick = function() {
                    selectedFiles.splice(i, 1);
                    imageLinks.splice(i, 1);
                    updatePhotoPreview();
                };

                slot.appendChild(img);
                slot.appendChild(removeBtn);
                previewContainer.appendChild(slot);
            });
            updateSelectedImagesInput();
        }

        function updateSelectedImagesInput() {
            selectedImagesInput.value = JSON.stringify(imageLinks);
        }

        photosInput.addEventListener("change", function(event) {
            handleFiles(event.target.files);
        });

        const dropzone = document.querySelector('.dropzone');
        dropzone.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropzone.classList.add('border-blue-500');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-blue-500');
        });

        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            dropzone.classList.remove('border-blue-500');
            handleFiles(event.dataTransfer.files);
        });
    });
</script>