{{-- ABRIR TICKET DE LOS PEDIDOS --}}
<script>
     // Función modificada para manejar la apertura de tickets de forma diferente en dispositivos móviles
     function abrirTicket(identificador) {
        const esMovil = /Mobi|Android|iPhone|iPad|iPod|Opera Mini/i.test(navigator.userAgent);
        
        if (esMovil) {
            // Si es móvil, abrir el enlace directamente en una nueva pestaña
            window.location.href = `/venta/${identificador}/ticket`;
        } else {
            // Si no es móvil, abrir en una ventana emergente
            abrirVentanaPDFTicket(`/venta/${identificador}/ticket`);
        }
    }

    // Función para abrir la ventana emergente en escritorio
    function abrirVentanaPDFTicket(url) {
        // Ancho y alto para la ventana emergente
        var width = 400;
        var height = 600;

        // Calcular posición central para la ventana emergente
        var left = (screen.width - width) / 2;
        var top = (screen.height - height) / 2;

        window.open(url, '_blank', `width=${width},height=${height},top=${top},left=${left}`);
    }
</script>


{{-- mostrar los pedidos del usuario  autenticado --}}
<script>
    function mostrarPedidosUserNoAutenticados() {
        // Mostrar el modal
        document.getElementById('ordersModal').classList.remove('hidden');
        // Deshabilitar el scroll en el cuerpo
        document.body.style.overflow = 'hidden';
        // Mostrar el ícono de carga
        document.getElementById('loadingSpinner').classList.remove('hidden');
        document.getElementById('ordersList').innerHTML = ''; // Limpiar la lista

        // Obtener los IDs de las órdenes desde el localStorage (si existen)
        const ordenIds = JSON.parse(localStorage.getItem('ordenIds')) || [];
        // console.log(ordenIds); // Verifica que los IDs de las órdenes están en el array

        if (ordenIds.length === 0) {
            // Si no hay IDs de órdenes guardados, mostrar mensaje
            document.getElementById('loadingSpinner').classList.add('hidden');
            document.getElementById('ordersList').innerHTML = '<p>No tienes órdenes realizadas.</p>';
            return;
        }

        // Realizar la solicitud para obtener las órdenes de los IDs almacenados
        fetch("{{ route('usuario.ordenes.no-autenticados') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    orden_ids: ordenIds
                })
            })
            .then(response => response.json())
            .then(data => {
                // Ocultar el ícono de carga
                document.getElementById('loadingSpinner').classList.add('hidden');

                if (data.success && data.orders.length > 0) {
                    const ordersList = document.getElementById('ordersList');
                    ordersList.innerHTML = ''; // Limpiar la lista antes de agregar nuevos elementos

                    data.orders.forEach(order => {
                        const productos = order.orden_productos ||
                    []; // Asegúrate de acceder a 'orden_productos'
                        const orderItem = document.createElement('li');
                        orderItem.classList.add('p-4', 'bg-gray-100', 'rounded-lg', 'shadow-md');

                        // Definir la clase de color según el estado
                        let estadoColorClass;
                        switch (order.estado) {
                            case 'pendiente':
                                estadoColorClass = 'text-yellow-500';
                                break;
                            case 'procesando':
                                estadoColorClass = 'text-blue-400';
                                break;
                            case 'completado':
                            case 'online':
                                estadoColorClass = 'text-green-500';
                                break;
                            case 'cancelado':
                                estadoColorClass = 'text-red-500';
                                break;
                            default:
                                estadoColorClass = 'text-gray-500'; // Clase por defecto
                        }

                        orderItem.innerHTML = `
                        <div class="p-4 bg-white rounded-lg shadow-md transition-transform transform hover:scale-105 cursor-pointer" onclick="toggleOrderDetails(${order.id})">
                            <div class="flex justify-between items-center mb-4 p-2 bg-gray-50 rounded-lg shadow-md">
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
                                    }
                                    <span class="text-blue-600 font-bold">${order.codigo_orden}</span>
                                </p>
                                <p class="text-sm font-semibold text-gray-800">Total: 
                                    <span class="text-green-600 font-bold">S/ ${order.total}</span>
                                </p>
                            </div>
                            <div id="order-details-${order.id}" class="hidden mt-2">
                                <p class="text-sm text-gray-600 mb-2">
                                    <strong>Estado:</strong>
                                    <span class="${estadoColorClass}">
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
                                            <p class="text-sm font-semibold">Prod: <span class="text-gray-800">${producto.producto.nombre}</span></p>
                                            <p class="text-sm text-gray-600">Cant: <span class="text-gray-800">${producto.cantidad}</span></p>
                                            <p class="text-sm text-gray-600">Precio: <span class="text-green-600">S/ ${producto.precio}</span></p>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        </div>
                    `;
                        ordersList.appendChild(orderItem);
                    });
                } else {
                    const ordersList = document.getElementById('ordersList');
                    ordersList.innerHTML = '<p>No tienes órdenes realizadas.</p>';
                }

            })
            .catch(error => {
                console.error('Error:', error);
                // Ocultar el ícono de carga en caso de error
                document.getElementById('loadingSpinner').classList.add('hidden');
            });
    }

    // usuarios autenticados- todos sus pedidos 
    function mostrarPedidos() {
        // Mostrar el modal
        document.getElementById('ordersModal').classList.remove('hidden');
        // Deshabilitar el scroll en el cuerpo
        document.body.style.overflow = 'hidden';
        // Mostrar el ícono de carga
        document.getElementById('loadingSpinner').classList.remove('hidden');
        document.getElementById('ordersList').innerHTML = ''; // Limpiar la lista

        // Realizar la solicitud para obtener las órdenes del usuario autenticado
        fetch("{{ route('usuario.ordenes') }}", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ocultar el ícono de carga
                document.getElementById('loadingSpinner').classList.add('hidden');

                if (data.success && data.orders.length > 0) {
                    const ordersList = document.getElementById('ordersList');
                    ordersList.innerHTML = ''; // Limpiar la lista antes de agregar nuevos elementos

                    data.orders.forEach(order => {
                        const productos = order.orden_productos ||
                    []; // Asegúrate de acceder a 'orden_productos'
                        const orderItem = document.createElement('li');
                        orderItem.classList.add('p-4', 'bg-gray-100', 'rounded-lg', 'shadow-md');

                        // Definir la clase de color según el estado
                        let estadoColorClass;
                        switch (order.estado) {
                            case 'pendiente':
                                estadoColorClass = 'text-yellow-500';
                                break;
                            case 'procesando':
                                estadoColorClass = 'text-blue-400';
                                break;
                            case 'completado':
                            case 'online':
                                estadoColorClass = 'text-green-500';
                                break;
                            case 'cancelado':
                                estadoColorClass = 'text-red-500';
                                break;
                            default:
                                estadoColorClass = 'text-gray-500'; // Clase por defecto
                        }

                        orderItem.innerHTML = `
                            <div class="p-4 bg-white rounded-lg shadow-md transition-transform transform hover:scale-105 cursor-pointer">
                                <div class="flex justify-between items-center mb-4 p-2 bg-gray-50 rounded-lg shadow-md"  onclick="toggleOrderDetails(${order.id})">
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
                                        }
                                        <span class="text-blue-600 font-bold">${order.codigo_orden}</span>
                                    </p>
                                    <p class="text-sm font-semibold text-gray-800">Total: 
                                        <span class="text-green-600 font-bold">S/ ${order.total}</span>
                                    </p>
                                </div>
                                <div id="order-details-${order.id}" class="hidden mt-2">
                                    <p class="text-sm text-gray-600 mb-2">
                                        <strong>Estado:</strong>
                                        <span class="${estadoColorClass}">
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
                                                <p class="text-sm font-semibold">Prod: <span class="text-gray-800">${producto.producto.nombre}</span></p>
                                                <p class="text-sm text-gray-600">Cant: <span class="text-gray-800">${producto.cantidad}</span></p>
                                                <p class="text-sm text-gray-600">Precio: <span class="text-green-600">S/ ${producto.precio}</span></p>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        `;

                        ordersList.appendChild(orderItem);
                    });
                } else {
                    const ordersList = document.getElementById('ordersList');
                    ordersList.innerHTML = '<p>No tienes órdenes realizadas.</p>';
                }

            })
            .catch(error => {
                console.error('Error:', error);
                // Ocultar el ícono de carga en caso de error
                document.getElementById('loadingSpinner').classList.add('hidden');
            });
    }

    function cerrarModalPedidos() {
        document.getElementById('ordersModal').classList.add('hidden');

        // Restaurar el scroll del cuerpo
        document.body.style.overflow = '';
    }

    function toggleOrderDetails(orderId) {
        const orderDetails = document.getElementById(`order-details-${orderId}`);
        if (orderDetails) { // Asegurarse de que el elemento existe
            if (orderDetails.classList.contains('hidden')) {
                // Si está oculto, mostrarlo
                orderDetails.classList.remove('hidden');
            } else {
                // Si está visible, ocultarlo
                orderDetails.classList.add('hidden');
            }
        } else {
            console.error(`No se encontró el elemento con el ID: order-details-${orderId}`);
        }
    }
</script>
<style>
    /* Estilizar el scrollbar con bordes redondeados */
    #ordersList {
        scrollbar-width: thin;
        /* Controla el ancho del scrollbar */
        scrollbar-color: #b1b1b1 #f3f4f6;
        /* Establece los colores del scrollbar */
    }

    #ordersList::-webkit-scrollbar {
        width: 8px;
        /* Establece el ancho del scrollbar */
        height: 8px;
        /* Establece el alto (para el scrollbar horizontal) */
    }

    #ordersList::-webkit-scrollbar-thumb {
        background-color: #b1b1b1;
        /* Color del pulgar del scrollbar */
        border-radius: 10px;
        /* Bordes redondeados en el pulgar */
    }

    #ordersList::-webkit-scrollbar-track {
        background-color: #f3f4f6;
        /* Color de la pista del scrollbar */
        border-radius: 10px;
        /* Bordes redondeados en la pista */
    }
</style>

<!-- Modal para mostrar las órdenes del usuario autenticado -->
<div id="ordersModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-80 h-3/4 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Mis Órdenes</h2>
            <button onclick="cerrarModalPedidos()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Ícono de carga -->
        <div id="loadingSpinner" class="absolute inset-0 flex justify-center items-center hidden">
            <i class="fas fa-spinner fa-spin text-gray-900 text-3xl"></i> <!-- Icono de carga -->
        </div>

        <!-- Lista de órdenes (con scroll vertical) -->
        <ul id="ordersList"
            class="space-y-4 overflow-y-auto flex-1 max-h-[calc(100%-96px)] rounded-lg scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
            <!-- Aquí se cargarán las órdenes dinámicamente -->
        </ul>
    </div>
</div>
