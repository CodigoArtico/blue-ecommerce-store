<script>
    // Verificar si el usuario está autenticado
    const isAuthenticated = @json(auth()->check());

    // FUNCION PARA DELIVERY
    $(document).ready(function() {
        $('#delivery-form').on('submit', function(e) {
            e.preventDefault(); // Evita la recarga de la página
            const formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                success: function(response) {
                    const message = response['success-orden'];
                    const ordenId = response['orden_id'];

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });

                    Toast.fire({
                        icon: 'success',
                        title: message,
                    }).then(() => {
                        // Si el usuario no está autenticado, guardar el ID de la orden en el localStorage
                        if (!isAuthenticated && ordenId) {
                            // Obtener los IDs previos de las ordenes
                            let ordenIds = JSON.parse(localStorage.getItem(
                                'ordenIds')) || [];

                            // Agregar el nuevo ID al array
                            ordenIds.push(ordenId);

                            // Guardar el array actualizado en localStorage
                            localStorage.setItem('ordenIds', JSON.stringify(
                                ordenIds));
                        }

                        // Mostrar el botón "Ver Orden" si se recibe el ID de la orden
                        if (ordenId) {
                            $('#ver-orden-btn').show().off('click').on('click',
                                function() {
                                    mostrarOrden(ordenId);
                                });
                        }
                        reproducirSonidoSuccess();
                    });

                    // Vaciar el formulario
                    $('#delivery-form')[0].reset();
                },
                error: function(xhr) {
                    // console.log(xhr.responseJSON);

                    // Manejo de errores
                    const errorMessage = xhr.responseJSON ? xhr.responseJSON.error :
                        'Ocurrió un error.';
                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        });
    });

    //FUNCION OBTENER PROMEDIO DE VOTOS DE EL SERVIDOR 
    $(document).ready(function() {
        
        const promedio = {{ number_format($producto->calificaciones->avg('puntuacion') ?? 4.5, 1) }};
        const votos = {{ $producto->calificaciones->count() > 0 ? $producto->calificaciones->count() : rand(7,17) }};

        // Actualizar el promedio y votos
        $('#average-rating').text('Promedio: ' + promedio + ' / 5');
        $('#rating-count').text('(' + votos + ' votos)');

        // Pintar las estrellas basadas en el promedio
        $('#rating-stars i').each(function(index) {
            if (index < Math.floor(promedio)) {
                $(this).removeClass('far').addClass('fas'); // Estrella llena
            } else if (index < promedio && index < Math.ceil(promedio)) {
                $(this).removeClass('far').addClass('fas').addClass('half'); // Estrella medio llena
            } else {
                $(this).removeClass('fas').addClass('far'); // Estrella vacía
            }
        });

        // Manejo del evento de clic en las estrellas para calificar
        $('#rating-stars i').on('click', function() {
            // Verificar si el usuario está autenticado
            if (!isAuthenticated) {
                togglePopup(); // Si no está autenticado, abrir el popup
                return; // Salir de la función, no hacer la calificación
            }
            const rating = $(this).data('star');
            const productoId = {{ $producto->id }};

            // Enviar la calificación al servidor usando AJAX
            $.ajax({
                url: '/calificar-producto',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    producto_id: productoId,
                    puntuacion: rating,
                },
                success: function(response) {
                    mostrarToast('warning', response.calificacion)
                    // Actualizar la calificación promedio
                    $('#average-rating').text('Promedio: ' + response.promedio + ' / 5');
                    $('#rating-count').text('(' + response.votos +
                        ' votos)'); // Aquí actualizamos el número de votos

                    // Resaltar las estrellas hasta la calificación seleccionada
                    $('#rating-stars i').each(function(index) {
                        if (index < rating) {
                            $(this).removeClass('far').addClass('fas');
                        } else {
                            $(this).removeClass('fas').addClass('far');
                        }
                    });
                },
                error: function(xhr) {
                    alert('Error al registrar la calificación.');
                }
            });
        });
    });
</script>