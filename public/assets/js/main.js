$("#location").select2({
    placeholder: "Location",
    allowClear: true
});
$("#site").select2({
    placeholder: "Site",
    allowClear: true
});
$("#category").select2({
    placeholder: "Site",
    allowClear: true
});
$.tablesorter.addParser({
    id: 'inputcount',
    is: function(s) {
        return false;
    },
    format: function(s) {
        var regex = new RegExp('(.*?)value=\"(.*?)\"(.*?)');
        var results = regex.exec(s.toLowerCase());
        return 0;
    },
    type: 'text'
});
$("#stock").tablesorter({
    headers: {
        3: {
            sorter:'inputcount'
        }
    }
});
