<script>
// Add this function for the update modal image preview
function previewUpdateImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('updateImagePreview');
        output.src = reader.result;
    }
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Update the openUpdateModal function to include the image upload handler
function openUpdateModal(tagid) {
    console.log('Opening modal for tagid:', tagid);
    
    const modalElement = document.getElementById('updateModal');
    if (!modalElement) {
        console.error('Modal element not found');
        return;
    }

    const modal = new bootstrap.Modal(modalElement, {
        keyboard: true,
        backdrop: true
    });
    
    $.ajax({
        url: 'fetch_vacuno_data.php',
        type: 'GET',
        data: { tagid: tagid },
        dataType: 'json',
        success: function(data) {
            console.log('Data received:', data);
            
            if (data.error) {
                console.error('Data error:', data.error);
                return;
            }

            // Update image previews with correct paths
            // Main image
            const updateImagePreview = document.getElementById('updateImagePreview');
            if (updateImagePreview) {
                if (data.image && data.image.trim() !== '') {
                    updateImagePreview.src = data.image;
                } else {
                    updateImagePreview.src = 'images/default_image.png';
                }
            }
            
            // Image 2
            const updateImage2Preview = document.getElementById('updateImage2Preview');
            if (updateImage2Preview) {
                if (data.image2 && data.image2.trim() !== '') {
                    updateImage2Preview.src = data.image2;
                } else {
                    updateImage2Preview.src = 'images/default_image.png';
                }
            }
            
            // Image 3
            const updateImage3Preview = document.getElementById('updateImage3Preview');
            if (updateImage3Preview) {
                if (data.image3 && data.image3.trim() !== '') {
                    updateImage3Preview.src = data.image3;
                } else {
                    updateImage3Preview.src = 'images/default_image.png';
                }
            }
            
            // Video
            const updateVideoPreview = document.getElementById('updateVideoPreview');
            if (updateVideoPreview) {
                if (data.video && data.video.trim() !== '') {
                    const videoSource = updateVideoPreview.querySelector('source');
                    if (videoSource) {
                        videoSource.src = data.video;
                        updateVideoPreview.load();
                    }
                } else {
                    const videoSource = updateVideoPreview.querySelector('source');
                    if (videoSource) {
                        videoSource.src = '';
                        updateVideoPreview.load();
                    }
                }
            }

            // Helper function to safely set form values
            const setFieldValue = (id, value) => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value || '';
                    console.log(`Set ${id} to:`, value);
                } else {
                    console.warn(`Element not found: ${id}`);
                }
            };

            // Populate form fields
            setFieldValue('updateNombre', data.nombre);
            setFieldValue('updateTagid', data.tagid);
            setFieldValue('updateFechaNacimiento', data.fecha_nacimiento);
            setFieldValue('updateGenero', data.genero);
            setFieldValue('updateRaza', data.raza);
            setFieldValue('updateEtapa', data.etapa);
            setFieldValue('updateGrupo', data.grupo);
            setFieldValue('updateEstatus', data.estatus);
            setFieldValue('updateFechaCompra', data.fecha_compra);

            // Show the modal
            modal.show();
            
            // Initialize carousel
            setTimeout(() => {
                if (document.getElementById('updateImagePreviewCarousel')) {
                    new bootstrap.Carousel(document.getElementById('updateImagePreviewCarousel'), {
                        interval: 3000
                    });
                }
            }, 500); // Small delay to ensure modal is fully shown
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            console.error('Response:', xhr.responseText);
            alert('Error al cargar los datos del animal: ' + error);
        }
    });
}
</script>

<!-- Add this script for image preview -->
<script>
</script> 