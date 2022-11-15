jQuery(document).ready(function ($) {
  $(".cfws_offered_price").hide();
  if(cfws_obj.is_single_page == true){
    $(".qty").change(function () {
      var qty = $(this).val();
      jQuery.ajax({
        url: cfws_obj.ajaxurl,
        type: "get",
        data: {
          action: "cfws_get_price_by_quantity_ajax",
          qty: qty,
          product_id: cfws_obj.product_id,
        },
        success: function (result) {
          price = parseFloat(result.price);
          $("#cfws_product_quantity").html(qty);
          $("#cfws_product_unit_price").html(price);
          $("#cfws_product_total_price").html((qty * price).toFixed(2));
          qty = parseInt(qty);
          max_package = parseInt(result.max_package);
          if (max_package < qty) {
            $(".cfws_offered_price").show();
            var offered_price = $("#offered_price").val();
            if (offered_price == "") {
              $("#cfws_product_total_price").html((qty * price).toFixed(2));
            } else {
              $("#cfws_product_total_price").html(
                (qty * offered_price).toFixed(2)
                );
              $("#cfws_product_unit_price").html(offered_price.toFixed(2));
            }
          } else {
            $(".cfws_offered_price").hide();
            $("#cfws_product_total_price").html((qty * price).toFixed(2));
          }
        },
        error: function (error) {
          console.log(error);
        },
      });
    });
  }

  $("#offered_price").change(function () {
    var qty = $(".qty").val();
    var offered_price = $(this).val();
    $("#cfws_product_unit_price").html(offered_price);
    $("#cfws_product_total_price").html(qty * offered_price);
  });
  $("#cfws_add_to_cart").click(function () {
    var qty = $(".qty").val();
    var price = $("#cfws_product_unit_price").html();
    // var offered_price = $("#cfws_product_unit_price").val();
    // console.
    
    if($('#offered_price').is(':visible')){
      var offered_price = $("#offered_price").val();
      console.log(offered_price);
      if(offered_price != ''){
        jQuery.ajax({
          url: cfws_obj.ajaxurl,
          type: "get",
          dataType : 'json',
          data: {
            action: "cfws_add_to_cart_ajax",
            qty: qty,
            price: price,
            product_id: cfws_obj.product_id,
            offered_price: offered_price,
          },
          success: function (result) {
          },
        }).done( function (response) {

          if( response.error != 'undefined' && response.error ){
                //some kind of error processing or just redirect to link
                // might be a good idea to link to the single product page in case JS is disabled
            return true;
          } else {
            window.location.href = cfws_obj.cart_page_url;
          }
        });
      }
      else{
        alert('Must have to fill Offered Price');
      }
    }
    else{
      jQuery.ajax({
        url: cfws_obj.ajaxurl,
        type: "get",
        dataType : 'json',
        data: {
          action: "cfws_add_to_cart_ajax",
          qty: qty,
          price: price,
          product_id: cfws_obj.product_id,
          offered_price: false,
        },
        success: function (result) {
        },
      }).done( function (response) {

        if( response.error != 'undefined' && response.error ){
              //some kind of error processing or just redirect to link
              // might be a good idea to link to the single product page in case JS is disabled
          return true;
        } else {
          window.location.href = cfws_obj.cart_page_url;
        }
      });
    }
    
  });
  if(window.location.href == cfws_obj.cart_page_url){
    $(".qty").change(function () {
      // console.log($(this).parent().parent().parent().find('.cfws_custom_cart_price').val());
      var product_id = $(this).parent().next('#product_id').val();
      var qty = $(this).val();
      var custom_price = $(this).parent().parent().parent().find('.cfws_custom_cart_price');

      jQuery.ajax({
        url: cfws_obj.ajaxurl,
        type: "get",
        data: {
          action: "cfws_check_offered_price_ajax",
          qty: qty,
          product_id: product_id,
        },
        success: function (result) {
          if(result){
            custom_price.attr('disabled',false);
            
          }
          else{
            custom_price.attr('disabled',true);
            jQuery.ajax({
              url: cfws_obj.ajaxurl,
              type: "get",
              data: {
                action: "cfws_get_price_by_quantity_ajax",
                qty: qty,
                product_id: product_id,
              },
              success: function (result) {
                price = parseFloat(result.price);
                custom_price.val(price);
              },
              error: function (error) {
                console.log(error);
              },
            });
          }
        },
        error: function (error) {
          console.log(error);
        },
      });
    });
  }
  $("input[name=payment_method]").change(function () {
    var payment_method = $(this).val();
    if(payment_method == 'bacs'){
      $('#cfws_payment_file').attr('required',true);
      $('#cfws_payment').show();
      $("#place_order").attr('type','button');
    }
    else{
      $('#cfws_payment_file').removeAttr('required');
      $('#cfws_payment').hide();
      $("#place_order").attr('type','submit');
    }
  });
  // var payment_method = $("input[name=payment_method]").val();
  // if(payment_method == 'bacs'){
  //   $('#cfws_payment_file').attr('required',true);
  //   $('#cfws_payment').show();
  //   $("#place_order").attr('type','button');
  // }
  // else{
  //   $('#cfws_payment_file').removeAttr('required');
  //   $('#cfws_payment').hide();
  //   $("#place_order").attr('type','submit');
  // }
  // $("#place_order").click(function (e) {
  //   if($('#cfws_payment_file').is(':visible')){
  //     if($('#cfws_payment_file').val() == ''){
  //       alert('Must have to attach file to Pay for Order');
  //       e.preventDefault();
  //     }
  //     else{
  //       var cfws_payment_file = $('#cfws_payment_file')[0].files[0];
  //       var order_id = $('#order_id').val();
  //       jQuery.ajax({
  //         url: cfws_obj.ajaxurl,
  //         type: "get",
  //         data: {
  //           action: "cfws_place_order_with_slip",
  //           order_id: order_id,
  //           cfws_payment_file: cfws_payment_file,
  //         },
  //         success: function (result) {
  //         },
  //       }).done( function (response) {
  //         console.log(response);
    
  //         // if( response.error != 'undefined' && response.error ){
  //         //     //some kind of error processing or just redirect to link
  //         //     // might be a good idea to link to the single product page in case JS is disabled
  //         //   return true;
  //         // } else {
  //         //   // console.log('test');
  //         //   console.log(response);
  //         //   // $("#place_order").attr('type','submit');
  //         //   // $( "#place_order" ).trigger( "click" );
  //         // }
  //       });

  //     }
  //   }

  // });
  $("#cfws_place_order").click(function () {
    var billing_address = $("input[name='billing_address']:checked").val();
    var shippping_address = $("input[name='shippping_address']:checked").val();
    jQuery.ajax({
      url: cfws_obj.ajaxurl,
      type: "get",
      data: {
        action: "cfws_place_order",
        billing_address: billing_address,
        shippping_address: shippping_address,
      },
      success: function (result) {
        console.log(result);
        // window.href = result;
      },
    }).done( function (response) {

      if( response.error != 'undefined' && response.error ){
            //some kind of error processing or just redirect to link
            // might be a good idea to link to the single product page in case JS is disabled
        return true;
      } else {
        window.location.href = response;
      }
    });
  });
});



jQuery(document).ready(function ($) {
  $(".accordion h1").click(function () {
    var id = this.id; /* getting heading id */

    /* looping through all elements which have class .accordion-content */
    $(".accordion-content").each(function () {
      if ($("#" + id).next()[0].id != this.id) {
        $(this).slideUp();
      }
    });

    $(this).next().toggle(); /* Selecting div after h1 */
  });
});

function defaultAddressSet(id, slug) {
  jQuery.ajax({
    url: cfws_obj.ajaxurl,
    type: "get",
    data: {
      action: "cfws_set_default_address_ajax",
      id: id,
      slug: slug,
    },
    success: function (result) {
      if (result == 1) {
        location.reload(true);
        alert("Successfully set the address as default " + slug);
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function deleteAddress(id) {
  jQuery.ajax({
    url: cfws_obj.ajaxurl,
    type: "get",
    data: {
      action: "cfws_delete_address_ajax",
      id: id,
    },
    success: function (result) {
      if (result == 1) {
        location.reload(true);
        alert("Address Deleted Successfully");
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}
