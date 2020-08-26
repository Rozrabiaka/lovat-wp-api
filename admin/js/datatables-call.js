$(document).ready( function () {
    $('#lovat-api-generated-keys').DataTable();
} );

$('#myAlert').on('closed.bs.alert', function () {
    $('.alert').alert('close');
})

$(".close-lovat-alert").click(function() {
    $(this)
        .parent(".lovat-alert")
        .fadeOut();
});