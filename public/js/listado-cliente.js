document.addEventListener("DOMContentLoaded", function() {
    window.addEventListener('load', function() {
        let tabla = $('#cotizacionesTable').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "drawCallback": function() {
                setTimeout(function() {
                    document.documentElement.classList.add('listo');
                    const cargador = document.getElementById('cargador');
                    cargador.classList.add('desvanecer');
                    setTimeout(() => cargador.style.display = 'none', 300);
                }, 100);
            }
        });
    });
});
