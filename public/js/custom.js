const Toast = Swal.mixin({
    toast:true,
    position:'top-end',
    showConfirmButton:false,
    timer:2500,
    timerProgressBar:true,
    didOpen:(toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

function toastSuccess(title, reload = false) {
    Toast.fire({
        icon:'success',
        title:title,
        didClose: () => {
            if(reload) window.location.reload();
        }
    });
}

function toastError(title, reload = false) {
    Toast.fire({
        icon:'error',
        title:title
    });

}

function toastInfo(title) {
    Toast.fire({
        icon:'info',
        title:title
    })
}