document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.DataTable !== 'undefined' && document.getElementById('cotizacionesTable')) {
        $('#cotizacionesTable').DataTable({
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                lengthMenu: "Mostrar  _MENU_  registros por página",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando _START_ a _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip><"clear">',
           });
    }
});
