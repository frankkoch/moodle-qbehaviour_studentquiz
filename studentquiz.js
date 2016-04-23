$(document).ready(function() {
    $('.studentquiz_behaviour input[type="radio"]').on('click', function() {
        $.post('../../question/behaviour/studentquiz/save.php', { save: 'vote', questionid: $(this).attr('name').substr(1), rate: $(this).val() });
    });

    $('.studentquiz_behaviour .add_comment').on('click', function() {
        var $field = $('.studentquiz_behaviour textarea.add_comment_field');
        var question_id = $field.attr('name').substr(1);

        $.post('../../question/behaviour/studentquiz/save.php', { save: 'comment', questionid: question_id, text: $field.val() }, function() {
            $field.val('');
            get_comment_list(question_id);
        })
    });

    $('.studentquiz_behaviour .vote .rating .rateable').on('click', function() {
        var rate = $(this).attr('data-rate');
        $.post('../../question/behaviour/studentquiz/save.php', { save: 'vote', questionid: $(this).attr('data-questionid'), rate: rate }, function() {
            $('.studentquiz_behaviour .vote .rating span').each(function(index) {
                if ($(this).attr('data-rate') <= rate) {
                    $(this).removeClass('star-empty');
                    $(this).removeClass('star');
                    $(this).addClass('star');
                }
            });
        });
    });

    bind_buttons();
});

function bind_buttons() {
    $('.studentquiz_behaviour .show_more').on('click', function() {
        $('.studentquiz_behaviour .comment_list div').removeClass('hidden');
        $(this).addClass('hidden');
        $('.studentquiz_behaviour .show_less').removeClass('hidden');
    });

    $('.studentquiz_behaviour .show_less').on('click', function() {
        $('.studentquiz_behaviour .comment_list div').each(function(index) {
            if (index > 1 && !$(this).hasClass('button_controls')) {
                $(this).addClass('hidden');
            }
        });

        $(this).addClass('hidden');
        $('.studentquiz_behaviour .show_more').removeClass('hidden');
    });

    $('.studentquiz_behaviour .remove_action').on('click', function() {
        var question_id = $(this).attr('data-question_id');
        $.post('../../question/behaviour/studentquiz/remove.php', { id: $(this).attr('data-id') }, function() {
            get_comment_list(question_id);
        });
    });
}

function get_comment_list(question_id) {
    $.get('../../question/behaviour/studentquiz/comment_list.php?question_id=' + question_id, function(data) {
        $('div.comment_list').html(data);
        bind_buttons();
    });
}