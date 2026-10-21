 {{-- REALIZAR ORDEN  --}}
 <script>
     function realizarOrden() {
         const nombre = document.getElementById('nombre').value.trim();
         const telefono = document.getElementById('telefono').value.trim();
         const direccion = document.getElementById('direccion').value.trim();

         // Validar que los campos no estén vacíos
         if (!nombre || !telefono || !direccion) {
             reproducirSonidoAlert();
             mostrarMensajeincompleto("Por favor, complete todos los campos requeridos."); // Mensaje de advertencia
             return; // Detener la ejecución si hay campos vacíos
         }

         const productos = [];
         @foreach ($carrito as $item)
             productos.push({
                 producto_id: {{ $item->producto->id }},
                 cantidad: {{ $item->cantidad }},
                 precio: {{ $item->producto->precioVentaReciente() }}
             });
         @endforeach

         const data = {
             nombre: nombre,
             telefono: telefono,
             direccion: direccion,
             productos: productos,
             carrito: 'yes'
         };

         fetch("{{ route('ordenes.store') }}", {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                 },
                 body: JSON.stringify(data)
             })
             .then(response => {
                 if (response.ok) {
                     return response.json();
                 }
                 throw new Error('Error en la solicitud');
             })
             .then(data => {
                 if (data['success-orden']) { // Cambiado a 'success-orden'
                     reproducirSonidoSuccess();
                     mostrarToast('success', data['success-orden']); // Mostrar mensaje de éxito
                     setTimeout1medio5sg(() => { //llamar para actualizar a los 1,5 segundo
                         console.log("Se ejecutó el callback y la página se recargará.");
                     }, 1500);
                 } else if (data.error) {
                     mostrarToast('error', data.error); // Mostrar mensaje de error, si existe
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 mostrarToast('error', 'Ocurrió un error al realizar la orden.'); // Mensaje en caso de error
             });
     }

     // CAMPOS INCOMPLETOS
     function mostrarMensajeincompleto(message) {
         const Toast = Swal.mixin({
             toast: true,
             position: "top-end",
             showConfirmButton: false,
             timer: 2000,
             timerProgressBar: true,
             didOpen: (toast) => {
                 toast.onmouseenter = Swal.stopTimer;
                 toast.onmouseleave = Swal.resumeTimer;
             }
         });

         Toast.fire({
             icon: "error",
             title: message,
         }).then(() => {
             // reproducirSonidoSuccess();
             // location.reload(); // Recarga la página
         });
     }
 </script>

 {{-- ELIMINAR EL PRODUCTO DEL CARRITO  --}}
 <script>
     function eliminarProducto(id) {
         Swal.fire({
             title: '¿Estás seguro?',
             text: 'No podrás deshacer esta acción.',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#d33',
             cancelButtonColor: '#3085d6',
             confirmButtonText: 'Sí, eliminarlo',
             cancelButtonText: 'Cancelar'
         }).then((result) => {
             if (result.isConfirmed) {
                 // Realizar la solicitud AJAX DELETE
                 fetch(`/carrito/${id}`, {
                         method: 'DELETE',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                 'content')
                         }
                     })
                     .then(response => {
                         // Verificar que la respuesta sea JSON
                         return response.json();
                     })
                     .then(data => {
                         if (data.success) {
                             reproducirSonidoSuccess();
                             mostrarToast('success', data.success);
                             // Recargar la página después de un corto tiempo
                             setTimeout(function() {
                                 location
                                     .reload(); // O actualizar solo el carrito si prefieres hacerlo
                             }, 1500);
                         } else if (data.error) {
                             Swal.fire('Error!', data.error, 'error');
                         }
                     })
                     .catch(error => {
                         console.error('Error:', error);
                         Swal.fire('Error', 'Hubo un problema al eliminar el producto', 'error');
                     });
             }
         });
     }
 </script>

 {{-- ACTUALIZAR LA CANTIDAD DEL CARRITO  --}}
 <script>
     function validarYActualizarCantidad(itemId) {
         const cantidadInput = document.getElementById(`cantidad_${itemId}`);
         let cantidad = cantidadInput.value.trim();

         // Si el campo está vacío, no hacemos nada
         if (cantidad === "") {
             return; // No actualizar, solo retorna
         }

         // Si la cantidad es menor que 1 (por ejemplo 0 o número negativo), la corregimos a 1
         if (cantidad <= 0 || isNaN(cantidad)) {
             cantidad = 1; // O puedes poner 0 si prefieres eliminar el producto
             cantidadInput.value = cantidad; // Actualiza el input con el valor corregido
         }

         // Solo realizamos la actualización si la cantidad es válida (mayor que 0)
         if (cantidad > 0) {
             // Realizar la solicitud AJAX para actualizar la cantidad en la base de datos
             fetch(`{{ route('carrito.update', '') }}/${itemId}`, {
                     method: 'PATCH',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: JSON.stringify({
                         cantidad: cantidad
                     })
                 })
                 .then(response => response.json())
                 .then(data => {
                     if (data.success) {
                         // Actualizamos el total del carrito con la nueva cantidad
                         const totalElement = document.getElementById(
                             'total-carrito'); // Asegúrate de tener un elemento con esta ID
                         totalElement.textContent = `Total: S/ ${data.total}`; // Actualiza el total

                         // Mostrar mensaje de éxito
                         mostrarToast('info', data.success);
                     } else {
                         // Si hay algún error
                         Swal.fire('Error', 'Hubo un problema al actualizar la cantidad', 'error');
                     }
                 })
                 .catch(error => {
                     console.error('Error:', error);
                     Swal.fire('Error', 'Ocurrió un error al intentar actualizar la cantidad', 'error');
                 });
         }
     }
 </script>
