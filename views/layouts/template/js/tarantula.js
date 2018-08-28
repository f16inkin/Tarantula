/**
 * Created by Rain on 22.08.2018.
 */
function upload(target) {
    var form = document.forms[target];
    var formData = new FormData(form);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/start/path");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if(xhr.status == 200) {
                data = xhr.responseText;
                $('#wrapper').prepend('<div id="alert_window"><p>'+data+'</p></div>');
                //Плавно его убираю с глаз и! И! Удаляю его колбэк функцией
                $("#alert_window").delay(2000).fadeOut(3000, function () {
                    $(this).remove()
                });
            }
        }
    };
    xhr.send(formData);
}

function getParsedData() {
    $.ajax({
        type: "POST",
        url: "/start/path",
        cache: false,
        success:function (response) {
            $("#response").html(response);
        }
    });
}

function getDataByDate() {
    $.ajax({
        type: "POST",
        url: "/start/report",
        cache: false,
        success:function (response) {
            $("#response").html(response);
        }
    });
}