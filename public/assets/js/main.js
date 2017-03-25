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
$("#sitesearch").select2({
    placeholder: "Select site(s)",
    allowClear: true,
    dropdownAutoWidth : true
});
$("#locationsearch").select2({
    placeholder: "Select location(s)",
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
  var itemid = button.data('id')
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

$('#editUserModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var userid = button.data('userid')
  var username = button.data('username')
  var name = button.data('name')
  var email = button.data('email')
  var active = button.data('active')
  var modal = $(this)
  console.log('User ID: '+userid)
  modal.find('#editID').val(userid)
  modal.find('#editUsername').val(username)
  modal.find('#editName').val(name)
  modal.find('#editEmail').val(email)
  if (active == 1) {
    modal.find('#activey').attr("checked", "checked")
    modal.find('#activen').removeAttr("checked", "checked")
  } else {
    modal.find('#activey').removeAttr("checked", "checked")
    modal.find('#activen').attr("checked", "checked")
  }
});
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
      var itemsearch = $('#itemsearch').val().toLowerCase();
      var locationsearch = $('#locationsearch').val();
      var sitesearch = $('#sitesearch').val();
      var min = parseInt($('#mincount').val());
      var max = parseInt($('#maxcount').val());

      var itemSet = (itemsearch == '' ? true : data[0].toLowerCase().includes(itemsearch));
      var locationSet = (locationsearch.length == 0 ? true : locationsearch.includes(data[2]));
      var siteSet = (sitesearch.length == 0 ? true : sitesearch.includes(data[1]));
      var minCount = (isNaN(min) ? true : parseInt(data[3]) >= min);
      var maxCount = (isNaN(max) ? true : parseInt(data[3]) <= max);

      return itemSet && locationSet && siteSet && minCount && maxCount
    }
);
var table = $('#stock').DataTable({
  ordering: true,
  dom: 'rtp',
});
$('#itemsearch').keyup( function() {
  table.draw();
});
$( '#locationsearch, #sitesearch, #mincount, #maxcount').change(function() {
  table.draw();
});
$('#itemsearchform, #mincountform, #maxcountform').bind('reset', function() {
  setTimeout(function(){
        table.draw();
    }, 200);
});
$('.itemcountbutton').click( function() {
  var row = $(this).closest('tr');
  var newCount = parseInt(row.find('input').val()) + parseInt($(this).val());
  var itemID = row.data('itemid');
  var data = '{"newcount":"'+newCount+'"}';
  $.ajax({
         'method': 'POST',
         'dataType': "json",
         'url': '/edit/item/'+itemID,
         'data': data
  }).done(function() {
    row.find('input').val(newCount);
  });
  return false
});
$('.itemcount').change(function(){
  var row = $(this).closest('tr');
  var newCount = parseInt($(this).val());
  var itemID = row.data('itemid');
  var data = '{"newcount":"'+newCount+'"}'
  $.ajax({
         'method': 'POST',
         'dataType': "json",
         'url': '/edit/item/'+itemID,
         'data': data
  })
});
