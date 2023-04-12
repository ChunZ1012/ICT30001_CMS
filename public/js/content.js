const editorConfig = {
    buttonList: [
        ['undo', 'redo'],
        ['font', 'fontSize', 'formatBlock'],
        ['paragraphStyle', 'blockquote'],
        ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
        ['fontColor', 'hiliteColor', 'textStyle'],
        ['removeFormat'],
        '/',
        ['outdent', 'indent'],
        ['align', 'horizontalRule', 'list', 'lineHeight'],
        ['table', 'link', 'image', 'video', 'audio'],
        ['fullScreen', 'showBlocks', 'codeView'],
        ['preview', 'print'],
        ['save', 'template'],
    ],
    height: 'auto',
    width: 'auto',
    charCounter: true
};

var imgDict = [];
var editor;
var modalEditor;

$(function(){
    $("#page-cover").change(readImage);
    $(document).on('click', '.image-cancel', function() {
        let no = $(this).attr('data-no');
        $(".preview-image.preview-show-"+no).remove();

        $.map(imgDict, function(f, idx){
            if(f.id == no) imgDict.splice(idx, 1);
        });
    });

    $htmlModal = $("#modalImageContent");
    $bsModal = bootstrap.Modal.getOrCreateInstance($htmlModal);
    $no = -1;
    $data = null;

    $(document).on('click', '#image-edit-btn', function(e){
        e.preventDefault();

        $no = $(this).data('no');
        $htmlModal.attr('data-no', $no);
        $bsModal.show();
    });

    $htmlModal.bind('show.bs.modal', function(e){
        $no = $(this).attr('data-no');

        $data = imgDict.filter(el => el.id == $no);
        if($data != null && $data.length > 0) {
            $("#page-image-alt-text").val($data[0].altText);
            modalEditor.setContents($data[0].desc);  
        }
    });

    $("#page-image-save-btn").click(function(e){
        $hNo = $htmlModal.attr("data-no");

        if($hNo != $no) toastError("Invalid form data! Please refresh the page and try again!");

        else {
            $hData = imgDict.filter(el => el.id == $no);
            if($hData != null && $hData.length > 0) {
                if($.data($hData[0]) == $.data($data)) toastError("Corrupted dictionary! Please refresh the page!");

                else {
                    let dict = imgDict[imgDict.findIndex(el => el.id == $no)];
                    dict.altText = $("#page-image-alt-text").val();
                    dict.desc = modalEditor.getContents(true);

                    $bsModal.hide();
                }
            }
            else toastError("Cannot find the specific data! Please refresh the page!")
        }
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
                saveToDict(num, f.result, "", "", file.name, file.type);
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
    var html =  '<div class="preview-image preview-show-' + num + '">' +
    '<div class="image-cancel" data-no="' + num + '">x</div>' +
    '<div class="image-zone"><img id="pro-img-' + num + '" src="' + src + '"></div>' +
    '<div class="tools-edit-image"><a href="javascript:void(0)" data-no="' + num + '" class="btn btn-light btn-edit-image" id="image-edit-btn">Edit</a></div>' +
    '</div>';
    previewContainer.append(html)

    num += 1;
}

function saveToDict(fId, data, altText = "", desc = "", name = "", type = "")
{
    // Fetch from server
    if(data.startsWith("http")) {
        var fname = data.split('/').pop();
        fetch(data).then((res) => {
            return res.blob();
        }).then((blob) => {
            var f = new File([blob], fname, { type: blob.type });
            imgDict.push({ id:fId, file:f, altText:altText, desc:desc });
        });
    }
    // Newly added
    else {
        var bytesChars = atob(data.split(',')[1]);
        var bytesNum = new Array(bytesChars.length);
        
        for(var i = 0; i < bytesChars.length; i++) {
            bytesNum[i] = bytesChars.charCodeAt(i);
        }

        var bytesArray = new Uint8Array(bytesNum);
        var f = new File([bytesArray], name, { type : type });
        imgDict.push({ id:fId, file:f, altText:'', desc:'' });
    }
}