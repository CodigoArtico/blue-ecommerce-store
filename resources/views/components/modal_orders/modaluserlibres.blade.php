<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tu código aquí, por ejemplo, agregar event listeners
    });

    function mostrarOrden(id) {
        const modal = document.getElementById('ordersModaluserlibre');
        if (!modal) {
            console.error('El modal no se encontró en el DOM.');
            return;
        }

        // Mostrar el modal
        modal.classList.remove('hidden');

        // Mostrar el ícono de carga
        document.getElementById('loadingSpinnerlibres').classList.remove('hidden');
        document.getElementById('ordersListlibreslibres').innerHTML = ''; // Limpiar la lista

        // Hacer la solicitud para obtener la orden por ID
        fetch(`/orden/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ocultar el ícono de carga
                document.getElementById('loadingSpinnerlibres').classList.add('hidden');

                if (data.success) {
                    mostrarOrdenEnModal(data.orden);
                } else {
                    document.getElementById('ordersListlibreslibres').innerHTML = '<p>Orden no encontrada.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loadingSpinnerlibres').classList.add('hidden');
            });
    }

    function mostrarOrdenEnModal(order) {
        const ordersListlibres = document.getElementById(
            'ordersListlibreslibres'); // Asegúrate que este ID sea correcto
        ordersListlibres.innerHTML = ''; // Limpiar la lista antes de agregar nuevos elementos

        const productos = order.orden_productos || [];
        const orderItem = document.createElement('li');
        orderItem.classList.add('p-4', 'bg-gray-100', 'rounded-lg', 'shadow-md', 'mb-4'); // Agregué un margen inferior

        orderItem.innerHTML = `
            <div class="p-4 bg-white rounded-lg shadow-md transition-transform transform hover:scale-105">
                <div class="flex justify-between items-center mb-4 p-2 bg-gray-50 rounded-lg shadow-sm">
                    <p class="text-sm font-semibold text-gray-800">Orden:
                        ${order.estado === 'completado' ? 
                            '<i class="fa-solid fa-circle-check text-green-500"></i>' : ''
                        }
                        ${order.estado === 'procesando' ? 
                            '<i class="fa-solid fa-spinner fa-spin-pulse text-blue-400"></i>' : ''
                        }
                        ${order.estado === 'pendiente' ? 
                            '<i class="fa-solid fa-spinner fa-spin-pulse text-yellow-500"></i>' : ''
                        }
                        ${order.estado === 'cancelado' ? 
                            '<i class="fa-solid fa-circle-xmark text-red-500"></i>' : ''
                        } <span class="text-blue-600 font-bold">${order.codigo_orden}</span></p>
                    <p class="text-sm font-semibold text-gray-800">Total: <span class="text-green-600 font-bold">S/ ${order.total}</span></p>
                </div>
                <p class="text-sm text-gray-600 mb-2">
                    <strong>Estado:</strong> 
                    <span class="${getEstadoColorClass(order.estado)}">
                        ${order.estado}
                    </span>
                    ${order.identificador_web_venta ? `
                        <button type="button" class="btn btn-primary icon ml-2" title="Imprimir en formato Ticket"
                            onclick="abrirTicket('${order.identificador_web_venta}'); return false;">
                            <i class="fa-solid fa-receipt"></i> Ticket
                        </button>
                    ` : ''}
                </p>
                <p class="text-sm text-gray-600">
                    <strong>Fecha: <i class="fa-solid fa-calendar-days" style="color: #000000;"></i></strong> 
                    ${new Date(order.created_at).toLocaleString('es-ES', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric', 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        second: '2-digit' 
                    })}
                </p>
                <ul class="mt-2 space-y-2">
                    ${productos.map(producto => `
                        <li class="p-2 bg-gray-50 rounded-lg shadow-sm">
                            <p class="font-semibold">Prod: <span class="text-gray-800">${producto.producto.nombre}</span></p>
                            <p class="text-sm text-gray-600">Cantidad: <span class="text-gray-800">${producto.cantidad}</span></p>
                            <p class="text-sm text-gray-600">Precio: <span class="text-green-600">S/ ${producto.precio}</span></p>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
        ordersListlibres.appendChild(orderItem);
    }

    // Función auxiliar para obtener la clase de color del estado
    function getEstadoColorClass(estado) {
        switch (estado) {
            case 'pendiente':
                return 'text-yellow-500';
            case 'procesando':
                return 'text-blue-400';
            case 'completado':
            case 'online':
                return 'text-green-500';
            case 'cancelado':
                return 'text-red-500';
            default:
                return 'text-gray-500'; // Clase por defecto
        }
    }

    function cerrarModalPedidoslibres() {
        const modal = document.getElementById('ordersModaluserlibre');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>

<!-- Modal para mostrar la orden -->
<div id="ordersModaluserlibre" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-80 h-2/4 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Detalles de la Orden</h2>
            <button onclick="cerrarModalPedidoslibres()" class="text-gray-500 hover:text-gray-700">
                <!-- Ícono de cerrar aquí -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <h6 class="text-sm text-gray-600 font-medium mb-2">En unos momentos nos pondremos en contacto contigo...</h6>
        <!-- Ícono de carga -->
        <div id="loadingSpinnerlibres" class="absolute inset-0 flex justify-center items-center hidden">
            <i class="fas fa-spinner fa-spin text-gray-900 text-3xl"></i> <!-- Icono de carga -->
        </div>
        <ul id="ordersListlibreslibres" class="space-y-4">
            <!-- Aquí se cargará la orden dinámicamente -->
        </ul>
    </div>
</div>
