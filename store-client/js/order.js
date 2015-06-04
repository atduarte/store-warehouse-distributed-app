    $(document).ready(function(){
        var baseUrl = 'http://api.store.dev';

        function getUrlParameter(sParam)
        {
            var sPageURL = window.location.search.substring(1);
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++)
            {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == sParam)
                {
                    return sParameterName[1];
                }
            }
        }

        function createOrders(orders){
            var container = $('.table tbody');
            for(var i = 0 ; i < orders.length ; i++){
                var template = $('#order-template').clone(true,true);
                template.find('.id').text(orders[i]['id']);
                template.find('.title').text(orders[i]['title']);
                template.find('.quantity').text(orders[i]['quantity']);
                template.find('.client').text(orders[i]['client']);
                template.find('.address').text(orders[i]['address']);
                template.find('.email').text(orders[i]['email']);
                template.find('.state').text(orders[i]['state']);
                template.find('.dispatchingTime').text(orders[i]['dispatchingTime']);
                template.find('.price').text(orders[i]['price']);
                template.removeClass('hidden');
                container.append(template);
            }
        }


        $.ajax({
            url: baseUrl + "/order/list?email=" + getUrlParameter('email'),
            method: 'GET'
        }).done(function(data){
            createOrders(data);
        }).error(function(){

        })

    });

