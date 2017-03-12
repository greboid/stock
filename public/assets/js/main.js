$.fn.select2.defaults.set( "theme", "bootstrap" );
$("#location").select2({
    placeholder: "Location",
    allowClear: true
});
$("#site").select2({
    placeholder: "Site",
    allowClear: true
});
$("#category").select2({
    placeholder: "Category",
    allowClear: true
});
$("#parent").select2({
    placeholder: "Parent",
    allowClear: true
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
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-danger');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-danger');
    },
    errorClass: 'offset-4 form-control-feedback',
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    },
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
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-danger');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-danger');
    },
    errorClass: 'offset-4 form-control-feedback',
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    },
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
