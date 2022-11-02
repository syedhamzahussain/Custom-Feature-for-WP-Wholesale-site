function editPackage(obj) {
  var min = jQuery(obj).parents("tr").children("td").eq(0).text();
  var max = jQuery(obj).parents("tr").children("td").eq(1).text();
  var type = jQuery(obj).parents("tr").children("td").eq(2).text();
  var discount = jQuery(obj).parents("tr").children("td").eq(3).text();
  var $ = jQuery;
  $("#cfws_min_qty").val(min);
  $("#cfws_max_qty").val(max);
  $("#cfws_discount_type").val(type);
  $("#cfws_discount").val(discount);

  var row_index = $(obj).parents("tr").index();

  $("#cfws_add_package").text("Save");
  $("#cfws_add_package").attr("onclick", "saveEdit(" + row_index + ")");
}
function deletePackage(obj) {
  jQuery(obj).closest("tr").remove();
}

function saveEdit(index) {
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

  var tr = "";
  tr +=
    "<input type='hidden' name='cfws_min_unit[]' value='" +
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

  $("#cfws_package_table tr").eq(index).html(tr);

  $("#cfws_min_qty").val(null);
  $("#cfws_max_qty").val(null);
  $("#cfws_discount_type").val(null);
  $("#cfws_discount").val(null);

  $("#cfws_add_package").text("Add Package");
  $("#cfws_add_package").attr("onclick", "addPackage()");
}

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

  var tr = "<tr>";
  tr +=
    "<input type='hidden' name='cfws_min_unit[]' value='" +
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
  tr += "</tr>";
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
