$(document).ready(function(){
    var voteform = $('form[data-role="voteform"]');

    $('a[data-role="voteup"]').on('click', function(e){
        e.stopPropagation();
        var id = $(this).data('id');
        voteform.find('select#appbundle_vote_post').val(id);
        voteform.find('#appbundle_vote_up').prop('checked', true);

        voteform.submit();
    });

    $('a[data-role="votedown"]').on('click', function(e){
        e.stopPropagation();
        var id = $(this).data('id');
        voteform.find('select#appbundle_vote_post').val(id);
        voteform.find('#appbundle_vote_up').prop('checked', false);

        voteform.submit();
    });

});