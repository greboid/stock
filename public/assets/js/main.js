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
        var regex = new RegExp('(.*?)value=\"(.*?)\"(.*?)');
        var results = regex.exec(s.toLowerCase());
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
$('#addUserModal').on('hidden.bs.modal', function () {
    $.clearInput(this);
});
