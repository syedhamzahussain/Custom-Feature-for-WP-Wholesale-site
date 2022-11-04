jQuery( document ).ready(function($) {
    $('.cfws_offered_price').hide(); 
    $(".qty").change(function(){
        var qty = $(this).val();
        jQuery.ajax({
            url: cfws_obj.ajaxurl,
            type : "get",
            data: {
                action : 'cfws_get_price_by_quantity_ajax',
                qty:qty,
                product_id:cfws_obj.product_id,
            },   
            success: function(result) {
                price = parseInt(result.price);                
                $('#cfws_product_quantity').html(qty);
                $('#cfws_product_unit_price').html(price);
                $('#cfws_product_total_price').html((qty*price).toFixed(2));
                qty = parseInt(qty); 
                max_package = parseInt(result.max_package); 
                if(max_package < qty){
                    $('.cfws_offered_price').show();
                    var offered_price = $('#offered_price').val();
                    if(offered_price == ''){

                        $('#cfws_product_total_price').html((qty*price).toFixed(2));
                    }
                    else{

                        $('#cfws_product_total_price').html((qty*offered_price).toFixed(2));
                        $('#cfws_product_unit_price').html((offered_price).toFixed(2));
                    }
                }
                else{
                    $('.cfws_offered_price').hide();
                    $('#cfws_product_total_price').html((qty*price).toFixed(2));

                }
            },
            error: function(error){
                console.log(error);
            } 
        })
    });
    $("#offered_price").change(function(){
        var qty = $('.qty').val();
        var offered_price = $(this).val();
        $('#cfws_product_unit_price').html(offered_price);
        $('#cfws_product_total_price').html(qty*offered_price);
    });
});