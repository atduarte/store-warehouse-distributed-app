/**
 * Created by Sergio Esteves on 5/31/2015.
 */
$(document).ready(function(){
    loadBooks();
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
            console.log(title);
            $("#row"+lastrow).append(
                '<div class="col-md-3 col-sm-6 hero-feature">' +
                '<div class="thumbnail">'+
                '<img src="'+photo+'" style="height: 250px;" alt="">'+
                '<div class="caption">'+
                '<h3>'+title+'</h3>'+
                '<p>'+description+'</p>'+
                '<p>'+
                '<a href="#" class="btn btn-primary">Buy Now!</a>'+
                '</p>'+
                '</div>'+
                '</div>'+
                '</div>'
            );

            if(i+1 % 3 == 0)
            {
                $("#maincontainer").append('</div>');
                $("#maincontainer").append('<div class="row text-center" id="row'+i+'">');
                lastrow = lastrow + 1;
            }
        }
    });
};