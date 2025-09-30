$(document).ready(function() {
    $('.formEliminar').submit(function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Estás seguro?",
            text: "Se va a eliminar un registro!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: "#d33",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Si, eliminar!",
            cancelButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    })
})
