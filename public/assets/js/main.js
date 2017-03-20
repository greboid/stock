$.clearInput = function (area) {
    $(area).find('input[type=text], input[type=password], input[type=number], input[type=email], textarea').val('');
};
$.fn.select2.defaults.set( "theme", "bootstrap" );
jQuery.validator.setDefaults({
  highlight: function(element) {
        $(element).closest('.form-group').addClass('has-danger');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-danger');
    },
    errorClass: 'offset-4 form-control-feedback',
});

$("#location").select2({
    placeholder: "Location",
    allowClear: true,
    dropdownAutoWidth : true
});
$("#site").select2({
    placeholder: "Site",
    allowClear: true,
    dropdownAutoWidth : true
});
$("#category").select2({
    placeholder: "Category",
    allowClear: true,
    dropdownAutoWidth : true
});
$("#parent").select2({
    placeholder: "Parent",
    allowClear: true,
    dropdownAutoWidth : true
});
$.tablesorter.addParser({
    id: 'inputcount',
    is: function(s) {
        return false;
    },
    format: function(s, table, cell, cellIndex) {
        return cell.getElementsByTagName('input')[0].value;
    },
    type: 'numeric'
});
$("#stock").tablesorter({
    theme : "bootstrap",
    textExtraction: "complex",
    headers: {
        3: {
            sorter:'inputcount'
        },
        4: {
            sorter: false
        }
    }
});
$("#categories").tablesorter({
    theme : "bootstrap",
    textExtraction: "complex",
    headers: {
        2: {
            sorter: false
        }
    }
});
$("#items").tablesorter({
    theme : "bootstrap",
    textExtraction: "complex",
    headers: {
        4: {
            sorter: false
        }
    }
});
$("#locations").tablesorter({
    theme : "bootstrap",
    textExtraction: "complex",
    headers: {
        3: {
            sorter: false
        }
    }
});
$("#sites").tablesorter({
    theme : "bootstrap",
    textExtraction: "complex",
    headers: {
        2: {
            sorter: false
        }
    }
});
$("#users").tablesorter({
    theme : "bootstrap",
    textExtraction: "complex",
    headers: {
        4: {
            sorter: false
        }
    }
});

$("#profileDetailsForm").validate({
    rules: {
        name: {
            required: true,
        },
        email: {
            required: true,
            email: true,
            remote: {
                url: "/user/checkemail",
                data: {
                    username: function() {
                        return $( "#username" ).val();
                    }
                }
            }
        }
    },
});
$("#changePasswordForm").validate({
    rules: {
        newpassword: {
            required: true,
            minlength: 6,
        },
        confirmpassword: {
            equalTo: "#newpassword",
       }
    },
});
$("#addUserForm").validate({
    errorClass: 'offset-3 form-control-feedback',
    rules: {
        username: {
            required: true,
            remote: {
                url: "/user/checkusername",
            }
        },
        name: {
            required: true,
        },
        email: {
            required: true,
            email: true,
            remote: {
                url: "/user/checkemail",
                data: {
                    username: function() {
                        return $( "#username" ).val();
                    }
                }
            }
        },
    }
});
$("#verifyEmailForm").validate({
    rules: {
        newpassword: {
            required: true,
            minlength: 6,
        },
        confirmpassword: {
            equalTo: "#newpassword",
       }
    },
});
$('#addUserModal').on('hidden.bs.modal', function () {
    $.clearInput(this);
});

$('#editSiteModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var siteName = button.data('sitename')
  var siteID = button.data('siteid')
  var modal = $(this)
  modal.find('#editID').val(siteID)
  modal.find('#editName').val(siteName)
});

$('#editLocationModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var locationid = button.data('locationid')
  var locationname = button.data('locationname')
  var sitename = button.data('sitename')
  var modal = $(this)
  modal.find('#editName').val(locationname)
  modal.find('#editID').val(locationid)
  $("#editSite").find("option:contains('"+sitename+"')").attr("selected", "selected")
});

$('#editCategoryModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var categoryid = button.data('categoryid')
  var categoryname = button.data('categoryname')
  var categoryparent = button.data('categoryparent')
  var modal = $(this)
  modal.find('#editName').val(categoryname)
  modal.find('#editID').val(categoryid)
  if (categoryparent == "") {
    $("#editParent").find("option:contains('"+categoryparent+"')").prop("selected", false)
  } else {
    $("#editParent").find("option:contains('"+categoryparent+"')").attr("selected", "selected")
  }
});

$('#editItemModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var itemid = button.data('itemid')
  var itemname = button.data('itemname')
  var locationname = button.data('locationname')
  var categoryname = button.data('categoryname')
  var stockcount = button.data('stockcount')
  var modal = $(this)
  modal.find('#editID').val(itemid)
  modal.find('#editName').val(itemname)
  $('#editLocation option').each(function () { if ($(this).text() == locationname) { $(this).attr("selected", "selected") } });
  $('#editCategory option').each(function () { if ($(this).text().trim() == categoryname) { $(this).attr("selected", "selected") } });
  modal.find('#editCount').val(stockcount)
});
