var $ = jQuery;
jQuery(document).ready(function ($) {
  if (cfws_obj.is_product) {
    $("#post").submit(function (e) {
      // alert($('input[name="cfws_unit_cost"]'));
      if ($('input[name="cfws_min_unit[]"]').length == 0) {
        e.preventDefault();
        alert("Please add atleast one package to continue");
      }
      if ($('input[name="cfws_unit_cost"]').val() == "") {
        e.preventDefault();
        alert("Please add unit cost to continue");
      }
      if ($('input[name="cfws_unit_quantity"]').val() == "") {
        e.preventDefault();
        alert("Please add unit quantity to continue");
      }
    });
  }
});

function editPackage(obj) {
  var min = jQuery(obj).parents("tr").children("td").eq(0).text();
  var max = jQuery(obj).parents("tr").children("td").eq(1).text();
  var type = jQuery(obj).parents("tr").children("td").eq(2).text();
  var discount = jQuery(obj).parents("tr").children("td").eq(3).text();
  fillFields(min, max, type, discount);
  var row_index = $(obj).parents("tr").index();

  $("#cfws_add_package").text("Save");
  $("#cfws_add_package").attr("onclick", "saveEdit(" + row_index + ")");
}
function deletePackage(obj) {
  confirm("Are you sure?") ? jQuery(obj).closest("tr").remove() : "";
}

function fillFields(min, max, type, discount) {
  $("#cfws_min_qty").val(min);
  $("#cfws_max_qty").val(max);
  $("#cfws_discount_type").val(type);
  $("#cfws_discount").val(discount);
}

function validateFields(min, max, type, discount) {
  if (
    min == "" ||
    max == "" ||
    discount == "" ||
    min <= 0 ||
    max <= 0 ||
    discount < 0
  ) {
    alert("Please fill all fields with valid input");
    return false;
  }
  if (type === "null") {
    alert("Please select discount type");
    return false;
  }
  return true;
}

function getFieldsValue() {
  var min = $("#cfws_min_qty").val();
  var max = $("#cfws_max_qty").val();
  var type = $("#cfws_discount_type").val();
  var discount = $("#cfws_discount").val();
  return [min, max, type, discount];
}

function createRow(tr, min, max, type, discount) {
  tr +=
    "<input type='hidden' min='1' name='cfws_min_unit[]' value='" +
    min +
    "'/><input type='hidden' name='cfws_max_unit[]' value='" +
    max +
    "'/><input type='hidden' name='cfws_discount_type[]' value='" +
    type +
    "'/><input type='hidden' name='cfws_discount[]' value='" +
    discount +
    "'/>";
  tr += "<td>" + min + "</td>";
  tr += "<td>" + max + "</td>";
  tr += "<td>" + type + "</td>";
  tr += "<td>" + discount + "</td>";
  tr +=
    "<td><button id='cfws_edit_button' type='button' onclick='editPackage(this)'>Edit</button><button id='cfws_delete_button' type='button' onclick='deletePackage(this)'>Delete</button></td>";
  return tr;
}

function saveEdit(index) {
  [min, max, type, discount] = getFieldsValue();
  var validate = validateFields(min, max, type, discount);
  if (validate == false) {
    return;
  }
  var tr = "";
  tr = createRow(tr, min, max, type, discount);

  $("#cfws_package_table tr").eq(index).html(tr);

  fillFields(null, null, null, null);

  $("#cfws_add_package").text("Add Package");
  $("#cfws_add_package").attr("onclick", "addPackage()");
}

function addPackage() {
  [min, max, type, discount] = getFieldsValue();

  var validate = validateFields(min, max, type, discount);
  if (validate == false) {
    return;
  }
  var tr = "<tr>";
  tr = createRow(tr, min, max, type, discount);

  $("#cfws_package_table").append(tr);

  fillFields(null, null, null, null);
}
