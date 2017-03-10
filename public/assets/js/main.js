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
    format: function(s, table, cell, cellIndex) {
        var regex = new RegExp('(.*?)value=\"(.*?)\"(.*?)');
        var results = regex.exec(s.toLowerCase());
        return cell.getElementsByTagName('input')[0].value;
    },
    type: 'numeric'
});
$("#stock").tablesorter({
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
