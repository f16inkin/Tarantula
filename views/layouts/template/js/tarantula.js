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
    var subdivision_id = $("select[name='subdivision_id']").val();
    $.ajax({
        type: "POST",
        url: "/start/report",
        data: {"subdivision_id": subdivision_id},
        cache: false,
        success:function (response) {
            if (response == false) {
                $('#response').append('<div id="error_window">Запрашиваемые данные отсутствуют</div>');
            }else {
                $("#response").html(response);
            }
        }
    });
}

function getDataByXml() {
    var subdivision_id = $("select[name='subdivision_id']").val();
    $.ajax({
        type: "POST",
        url: "/start/add",
        data: {"subdivision_id": subdivision_id},
        cache: false,
        success:function (response) {
            //Парсинг пришедшего из контроллера сообщения.
            var res = JSON.parse(response);
            //Для каждого элемента массива вывожу сообщение о загрузке
            res.forEach(function(item) {
                $('#wrapper').append('<div id="'+item.window+'">'+item.message+'</div>');
                //Тут можно делать микро задержку после каждой итерации и получится эффект последовательного
                //вывода сообщений
            });
        }
    });
}