const Toast = Swal.mixin({
    toast:true,
    position:'top-end',
    showConfirmButton:false,
    timer:2000,
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

function toastLoading(title = '', desc = '') {
    Swal.fire({
        title: title == '' ? 'Loading' : title,
        html: desc = '' ? 'Your request is processing.' : desc,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function setPasswordToggler(input) {
    $(function(){
        $("#" + input).click(function(e){
            e.preventDefault();

            $passInput = $(this).prev();
            $passIcon = $("#" + input + " > i:first-child");
            $passType = $passInput.attr("type");

            if($passType == "password") {
                $passInput.attr("type", "text");
                $passIcon.addClass("fa-eye-slash");
                $passIcon.removeClass("fa-eye");
            }
            else {
                $passInput.attr("type", "password");
                $passIcon.addClass("fa-eye");
                $passIcon.removeClass("fa-eye-slash");
            }
        });
    });
}