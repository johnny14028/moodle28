$(function () {
    $("#alertar").click(function () {
        $.ajax({
            type: "POST",
            url: "index.php?cmd=Test&action=ajax",
            data: 'subject=addActivity',
            dataType: 'json',
            success: function (respuesta) {
                $('#divResult').html(respuesta.html);
            },
            error: function (object, response, otherObject) {
                alert(response);
            }
        });
    });
});