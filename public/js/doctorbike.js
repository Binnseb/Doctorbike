var $marque = $('#moto_marque');

$marque.change(function() {

    var $form = $(this).closest('form');

    var data = {};
    data[$marque.attr('nom')] = $marque.val();
    console.log($marque);

    $.ajax({
        url : $form.attr('action'),
        type: $form.attr('method'),
        data : data,
        success: function(html) {

            $('#moto_modele').replaceWith(

                $(html).find('#moto_modele')
            );
        }
    });
});