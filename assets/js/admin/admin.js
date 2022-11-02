function addPackage() {
  var $ = jQuery;
  var min = $("#cfws_min_qty").val();
  var max = $("#cfws_max_qty").val();
  var type = $("#cfws_discount_type").val();
  var discount = $("#cfws_discount").val();

  var minInput =
    "<input type='hidden' name='cfws_min_unit[]' value='" + min + "'/>";
  var maxInput =
    "<input type='hidden' name='cfws_max_unit[]' value='" + max + "'/>";
  var typeInput =
    "<input type='hidden' name='cfws_discount_type[]' value='" + type + "'/>";
  var discountInput =
    "<input type='hidden' name='cfws_discount[]' value='" + discount + "'/>";

  $(".packages_table_group").append(minInput);
  $(".packages_table_group").append(maxInput);
  $(".packages_table_group").append(typeInput);

  $(".packages_table_group").append(discountInput);

  var tr = "<tr>";
  tr += "<td>" + min + "</td>";
  tr += "<td>" + max + "</td>";
  tr += "<td>" + type + "</td>";
  tr += "<td>" + discount + "</td>";

  $("#cfws_package_table").append(tr);
}
