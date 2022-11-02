function editPackage(obj) {}

function addPackage() {
  var $ = jQuery;
  var min = $("#cfws_min_qty").val();
  var max = $("#cfws_max_qty").val();
  var type = $("#cfws_discount_type").val();
  var discount = $("#cfws_discount").val();
  //   console.log(typeof type);
  if (min == "" || max == "" || discount == "") {
    alert("Please fill all fields");
    return;
  }
  if (type === "null") {
    alert("Please select discount type");
    return;
  }

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

  $("#cfws_min_qty").val(null);
  $("#cfws_max_qty").val(null);
  $("#cfws_discount_type").val(null);
  $("#cfws_discount").val(null);
}

jQuery(document).ready(function ($) {
  $("#post").submit(function (e) {
    if ($('input[name="cfws_min_unit[]"]').length == 0) {
      e.preventDefault();
      alert("Please add atleast one package to continue");
    }
  });
});
