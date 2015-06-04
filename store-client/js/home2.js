/**
 * Created by Sergio Esteves on 5/31/2015.
 */
$(document).ready(function(){
    loadBooks();

    dialog = $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 300,
        width: 340,
        modal: true,
        buttons: {
            "Create New Order": newOrder,
            Cancel: function() {
                dialog.dialog( "close" );
            }
        },
        close: function() {
            $('#name').val('');
            $('#email').val('');
            $('#quantity').val('');
            $('#street').val('');
            dialog.dialog( "close" );
        }
    });

    form = dialog.find( "form" ).on( "submit", function( event ) {
        event.preventDefault();
        newOrder();
    });
});


function loadBooks() {
    //pedido all livros
    $.ajax({
        url: "http://api.store.dev/book/list",
    }).done(function (data) {
        var i = 0;
        //inserir linha a cada 4
        $("#maincontainer").empty();
        $("#maincontainer").append('<div class="row text-center" id="row0">');
        var lastrow = 0;
        for (i = 0; i < data.length; i++) {
            var title = data[i]['title'];
            var description = data[i]['description'];
            var photo = data[i]['photo'];
            var id = data[i]['id'];
            var price = data[i]['price'];

            $("#row"+lastrow).append(
                '<div class="col-md-3 col-sm-6 hero-feature book">' +
                '<div class="thumbnail">'+
                '<img src="'+photo+'" style="height: 250px;" alt="">'+
                '<div class="caption">'+
                '<h3>'+title+'</h3>'+
                '<p>'+description+'</p>'+
                '<p>'+price+'</p>'+
                '<p>'+
                '<a id='+id+' class="btn btn-primary btnbuy">Buy Now!</a>'+
                '</p>'+
                '</div>'+
                '</div>'+
                '</div>'
            );

            $(".book .btnbuy").click(function(){
                console.log('aqui');
                var id = $(this).attr('id');
                dialog.attr('data-book-id',id);
                dialog.dialog( "open" );
            })

            if(i+1 % 3 == 0)
            {
                $("#maincontainer").append('</div>');
                $("#maincontainer").append('<div class="row text-center" id="row'+i+'">');
                lastrow = lastrow + 1;
            }

        }
    });

};

function newOrder()
{
   var id = $('#dialog-form').attr("data-book-id");

   var name = $('#name').val();
   var email = $('#email').val();
   var quantity = $('#quantity').val();
   var street = $('#street').val();

    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    if(!re.test(email))
    {
        alert('Wrong Email!');
        return;
    }

    $.ajax({
        method: "POST",
        data: { book : id , clientName : name , email : email , quantity: quantity ,address: street},
        url: "http://api.store.dev/order/new"

    }).done(function (data) {
        alert('Sucess! You will be contact later');
        dialog.dialog( "close" );
    }).fail(function (msg) {

        alert('Something was wrong! '+msg.responseText);
    });
};

