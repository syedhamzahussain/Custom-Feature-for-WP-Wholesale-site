jQuery( document ).ready(function($) {
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
            success: function(price) {
                $('#cfws_product_quantity').html(qty);
                $('#cfws_product_unit_price').html(price);
                $('#cfws_product_total_price').html(qty*price);
            },
            error: function(error){
                console.log(error);
            } 
        })
    });
});