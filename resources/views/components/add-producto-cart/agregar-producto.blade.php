{{-- ----------------Funcion Para el Carrito------------------------ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar el clic en los botones de agregar al carrito
        document.querySelectorAll('.add-to-cart-button').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event
            .preventDefault(); // Evitar que el formulario se envíe de manera tradicional

                let form = button.closest('form');
                let productoId = form.getAttribute('data-producto-id');
                let cantidad = form.querySelector('input[name="cantidad"]').value;
                let csrfToken = form.querySelector('input[name="_token"]').value;

                // Hacer la solicitud AJAX
                fetch('/carrito/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            producto_id: productoId,
                            cantidad: cantidad
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Aquí manejas la respuesta del servidor
                        if (data.success) {
                            reproducirSonidoSuccess();
                            mostrarToast('success', data.message);

                            // Actualizar el contador de productos en el carrito
                            let cartCountElement = document.querySelector('#cart-count');
                            if (cartCountElement) {
                                cartCountElement.textContent = data.cartCount;  // Actualizamos el texto con el nuevo count
                                // Si el carrito está vacío, ocultamos el punto (opcional)
                                if (data.cartCount > 0) {
                                    cartCountElement.classList.remove('hidden');
                                } else {
                                    cartCountElement.classList.add('hidden');
                                }
                            }
                        } else {
                            // Manejar el error si el producto no se pudo agregar al carrito
                            reproducirSonidoAlert();
                            mostrarToast('info', data.message);
                        }
                    })
                    .catch(error => {
                        reproducirSonidoError();
                        mostrarToast('error', data.message);
                    });
            });
        });
    });
</script>
