/*!
 * 官网：http://www.fircms.com/
 * Copyright 2013, poctsy
 * 请尊重原创，保留头部版权
 * 在保留版权的前提下可应用于个人或商业用途
 */

$(function() {
    startimagesnow();
    sortableinput();
    delectli();
    $('#sortable').sortable({
        sort: function(event, ui) {
            ui.item.attr('id', 'sortactive');
        }
    });
    $('#sortable').sortable({
        update: function(event, ui) {
            images = $('#Post_images').attr('value');
            $('#content').append('<input id=\"tembsort\"type=\"hidden\" >');
            $('#content').append('<input id=\"tembsort-last\"type=\"hidden\" >');
            image = images.split('{/img}');
            $.each(image, function(i, val) {
                if(val.indexOf($('#sortactive').find('img').attr('src')) == -1){
                    $('#tembsort').val($('#tembsort').attr('value') + val);
                }
            });
            tembsort=$('#tembsort').attr('value');
            tembsort = tembsort.split('{/text}');
            tembsort.splice($('#sortactive').index(), 0,
                '{img}{src}'+ $('#sortactive').find('img').attr('src')+'{/src}{text}'+$('#sortactive').find('input').attr('value')
            )
            $.each(tembsort, function(i, val) {
                if(val !=""){
                    $('#tembsort-last').val($('#tembsort-last').attr('value') + val+'{/text}{/img}');
                }
            });
            $('#Post_images').val($('#tembsort-last').val())
            $('#tembsort').attr('value', '');
            $('#tembsort-last').attr('value', '');
            $('#tembsort').remove();
            $('#tembsort-last').remove();
            ui.item.attr('id', '');
        }
    });
    $('#sortable').disableSelection();
});
function sortableinput() {
    $('#sortable input').focus(function() {
        $(this).css('background-color', '#FFFFCC');
    });
    $('#sortable input').blur(function() {
        if (this.value != 'undefined') {
            $(this).css('background-color', 'rgb(212, 240, 214);');
            citetext = this.value;
            imgsrc = $(this).prev().attr('src');
            images = $('#Post_images').attr('value');
            image = images.split('{/img}');
            $.each(image, function(i, val) {
                if (val.indexOf(imgsrc) >= 0) {
                    $('#Post_images').val(images.replace(val, '{img}{src}' + imgsrc + '{/src}{text}' + citetext + '{/text}'));
                }
            });
        }
        else {
            $(this).css('background-color', '');
        }
    });
}
function startimagesnow() {
    Images = $('#Post_images').attr('value');
    Imagesarray = Images.split('{/img}');
    div = $('#sortable');
    $.each(Imagesarray, function(i, val) {
        oneval = val.replace('{img}{src}', '<li class=\"ui-state-default\"><img src=\"');
        twoval = oneval.replace('{/src}{text}', '\"><input type=\"text\" value=\"');
        treeoval = twoval.replace('{/text}', '\"  ><span class=\"delectli\"></span></li>');
        div.append(treeoval);
    });
}
function delectli() {
    delect = $('.delectli').click(function() {
        imgsrc = $(this).prev().prev().attr('src');
        images = $('#Post_images').attr('value');
        image = images.split('{/img}');
        $.each(image, function(i, val) {
            if (val.indexOf(imgsrc) >= 0) {
                $('#Post_images').val(images.replace(val + '{/img}', ''));
            }
        });
        $(this).parent().remove();
    })

}
KindEditor.ready(function(K) {
    var images_editor = K.editor({
        imageSizeLimit:'3MB',
        'uploadJson': './index.php?r=admin/upload/kupload',
        'extraFileUploadParams':{
            'YII_CSRF_TOKEN':''+$("input[name='YII_CSRF_TOKEN']").val()+''
        }
    });
    K('#Post_selectImage').click(function() {
        images_editor.loadPlugin('multiimage', function() {
            images_editor.plugin.multiImageDialog({
                clickFn: function(urlList) {
                    var div = K('#sortable');
                    K.each(urlList, function(i, data) {
                        url = K.formatUrl(data.url, 'relative');
                        images = $('#Post_images').attr('value');
                        if (images.indexOf(url) < 0) {
                            div.append('<li class=\"ui-state-default\"><img src=\"' + url+ '\"><input type=\"text\" value=\"undefined\"  ><span class=\"delectli\"></span></li>');
                            $('#Post_images').val(images + '{img}{src}' + url + '{/src}{text}' + undefined + '{/text}{/img}');
                        }
                    });
                    sortableinput();
                    delectli();
                    images_editor.hideDialog();
                }
            });
        });
    });

});