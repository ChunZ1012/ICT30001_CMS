var imgDict = []

$(function(){
    $("#page-cover").change(readImage);
    $(document).on('click', '.image-cancel', function() {
        let no = $(this).data('no');
        $(".preview-image.preview-show-"+no).remove();

        $.map(imgDict, function(f, idx){
            console.log(f.id + ", " + no - 1);
            if(f.id == no) imgDict.splice(idx, 1);
        });
    });
});

var num = 1;
function readImage() {
    if (window.File && window.FileList && window.FileReader) {
        var files = event.target.files; //FileList object
        for (let i = 0; i < files.length; i++) {
            var file = files[i];
            if (!file.type.match('image')) continue;
            
            var reader = new FileReader();
            reader.onload = function (e) {
                var f = e.target;

                saveToDict(num, f.result, file.name, file.type);
                addPreviewImage(f.result);
            };

            reader.readAsDataURL(file);
            // $("#page-image").val('');
        }
    } else {
        console.log('Browser not support');
    }
}

function addPreviewImage(src)
{
    var previewContainer = $("#preview-images-zone");
    var html = '<div class="preview-image preview-show-' + num + '">' +
    '<div class="image-cancel" id="image-cancel" data-no="' + num + '">x</div>' +
    '<div class="image-zone"><img id="pro-img-' + num + '" src="' + src + '"></div>'
    previewContainer.append(html)

    num += 1;
}

function saveToDict(fId, data, name = "", type = "")
{
    if(data.startsWith("http")) {
        var fname = data.split('/').pop();
        fetch(data).then((res) => {
            return res.blob();
        }).then((blob) => {
            var f = new File([blob], fname, { type: blob.type });
            imgDict.push({ id:fId, file:f });
        });
    }
    else {
        var bytesChars = atob(data.split(',')[1]);
        var bytesNum = new Array(bytesChars.length);
        
        for(var i = 0; i < bytesChars.length; i++) {
            bytesNum[i] = bytesChars.charCodeAt(i);
        }

        var bytesArray = new Uint8Array(bytesNum);
        var f = new File([bytesArray], name, { type : type });
        console.log(f)
        imgDict.push({ id:fId, file:f });
    }
}