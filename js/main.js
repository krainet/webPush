/**
 * Created by ramon on 25/08/16.
 */

$("#sendPush").click(function() {

    var postData = {
        action : 'send_push',
        title : $('#inputNotiTitle').val(),
        message : $('#inputNotiBody').val(),
        icon : $('#inputNotiIcon').val(),
        tag : 'myTagExample',
        api_secret : $('#inputApiSecret').val(),
        regID : $('#token').val()

    };


    $.ajax({
        url: 'api.php',
        type: 'post',
        dataType: 'json',

        success: function (data) {
            console.log('Post response to API');
            console.log(data);
        },
        data: postData
    });

});

