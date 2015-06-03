$(document).ready(function(){
    var baseUrl = 'http://api.store.dev/app_dev.php';
    var bookTemplate = $('#book-template');


    var createBooks = function(books){
        var container = $('#book-container').empty();

        for(var i = 0 ; i < books.length;i++){
            var newBook = bookTemplate.clone(true,true);
            newBook.find('.title'       ).text(books[i].title);
            newBook.find('.description' ).text(books[i].description);
            newBook.find('.stock'       ).text(books[i].stock);
            newBook.find('.image'       ).attr('src',books[i].photo);
            newBook.attr('id',null);
            newBook.data('id',books[i].id);
            newBook.removeClass('hidden');
            container.append(newBook);
        }
    };

    var fetchBooks = function(){
        $.ajax({
            url: baseUrl + "/book/list",
            method: 'GET'
        })
            .done(function(data){
                createBooks(data);
            })
            .error(function(){

            });
    };

    $('.book .erase').click(function(){
        $.ajax({
            method: 'POST',
            url: baseUrl + '/book/delete',
            data: {book: $(this).parent('.book').data('id')}
        }).done(function(){
            fetchBooks();
        }).error(function(){

        });
    });

    $('#newBook').click(function(){
        $(".newbook input").val('');
        $(".newbook").modal('show');
    });

    $('.newbook .save').click(function(){
        var a = {
            title:          $(this).find('#new-book-title').val(),
            description:    $(this).find('#new-book-description').val(),
            photo :         $(this).find('#new-book-url').val(),
            stock :         $(this).find('#new-book-stock').val()
        };
        console.log(a);
        $.ajax({
            url: baseUrl + '/book/add',
            method: 'POST',
            data: {
                title:          $('#new-book-title').val(),
                description:    $('#new-book-description').val(),
                photo :         $('#new-book-url').val(),
                stock :         $('#new-book-stock').val()
            }
        }).done(function(){
            fetchBooks();
            $(".newbook").modal('hide');
        }).error(function(){

        });
    });

    fetchBooks();
});