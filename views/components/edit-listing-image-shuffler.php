<div class="text-center mb-3">
    <input type="file" class="d-none" id="photos" name="photos[]" accept="image/*" multiple>
    <label for="photos" class="dropzone p-4 d-block" id="dropzone">
        <div class="cursor-pointer p-12 flex justify-center bg-white border border-gray-300 rounded-xl" data-hs-file-upload-trigger>
            <div class="text-center">
                <span class="inline-flex justify-center items-center size-16 bg-gray-100 text-gray-800 rounded-full">
                    <svg class="shrink-0 size-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" x2="12" y1="3" y2="15"></line>
                    </svg>
                </span>
                <div class="mt-4 flex flex-wrap justify-center text-sm leading-6 text-gray-600">
                    <span class="pe-1 font-medium text-gray-800">Drop your file here or</span>
                    <span class="bg-white font-semibold text-blue-600 hover:text-blue-700 rounded-lg decoration-2 hover:underline">browse</span>
                </div>
                <p class="mt-1 text-xs text-gray-400">Pick a file up to 10MB.</p>
            </div>
        </div>
    </label>
    <p class="text-left text-xs text-red-500 font-semibold mt-2 hidden" id="photosMessage"></p>
</div>

<input type="hidden" id="selectedImages" name="selectedImages" />
<input type="hidden" id="existingPhotos" name="existingPhotos" />

<label class="block text-sm font-medium mb-2">New Photos</label>
<div id="newPhotoPreviewContainer" class="photoPreviewContainer mb-4"></div>

<label class="block text-sm font-medium mb-2">Existing Photos</label>
<div id="existingPhotoPreviewContainer" class="photoPreviewContainer mb-4"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let selectedFiles = [];
        const dropzone = document.getElementById("dropzone");
        const fileInput = document.getElementById("photos");
        const previewContainer = document.getElementById("newPhotoPreviewContainer");
        const selectedImagesInput = document.getElementById("selectedImages");
        const message = document.getElementById("photosMessage");

        function updatePreview() {
            previewContainer.innerHTML = "";
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgWrapper = document.createElement("div");
                    imgWrapper.classList.add("relative", "inline-block", "m-1");
                    imgWrapper.style.width = "100px";
                    imgWrapper.style.height = "100px";

                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.classList.add("rounded", "border", "border-gray-300", "w-full", "h-full", "object-cover");

                    const removeBtn = document.createElement("button");
                    removeBtn.innerHTML = "&times;";
                    removeBtn.classList.add("absolute", "top-0", "right-0", "bg-red-500", "text-white", "rounded-full", "w-5", "h-5", "flex", "items-center", "justify-center");
                    removeBtn.onclick = function() {
                        selectedFiles.splice(index, 1);
                        updatePreview();
                    };

                    imgWrapper.appendChild(img);
                    imgWrapper.appendChild(removeBtn);
                    previewContainer.appendChild(imgWrapper);
                };
                reader.readAsDataURL(file);
            });
            selectedImagesInput.value = JSON.stringify(selectedFiles.map(f => f.name));
        }

        dropzone.addEventListener("dragover", function(e) {
            e.preventDefault();
            dropzone.classList.add("border-blue-500");
        });

        dropzone.addEventListener("dragleave", function() {
            dropzone.classList.remove("border-blue-500");
        });

        dropzone.addEventListener("drop", function(e) {
            e.preventDefault();
            dropzone.classList.remove("border-blue-500");
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener("change", function(e) {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            let validFiles = Array.from(files).filter(file => file.size < 10 * 1024 * 1024);
            selectedFiles.push(...validFiles);
            message.classList.add("hidden");
            updatePreview();
        }
    });
</script>