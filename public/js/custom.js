const Toast = Swal.mixin({
    toast:true,
    position:'top-end',
    showConfirmButton:false,
    timer:3000,
    timerProgressBar:true,
    didOpen:(toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

function toastSuccess(title) {
    Toast.fire({
        icon:'success',
        title:title
    })
}

function toastError(title, f) {
    Toast.fire({
        icon:'error',
        title:title
    })
}

function toastInfo(title) {
    Toast.fire({
        icon:'info',
        title:title
    })
}