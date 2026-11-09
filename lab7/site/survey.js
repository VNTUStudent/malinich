$(function () {
    var form = $('form.survey-form');

    form.attr('novalidate', true);

    function showErrors(errors) {
        form.prev('.error-box').remove();
        var box = $('<div class="error-box"></div>');
        box.append('<strong>Будь ласка, виправте такі помилки:</strong>');
        var list = $('<ul></ul>');
        $.each(errors, function (i, error) {
            list.append($('<li></li>').text(error));
        });
        box.append(list);
        form.before(box);
    }

    form.on('submit', function (event) {
        event.preventDefault();
        form.prev('.error-box').remove();
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Надсилання...');

        var data = form.serialize() + '&action=survey';

        $.post('api.php', data, null, 'json')
            .done(function (result) {
                if (!result.ok) {
                    showErrors(result.errors);
                    submitBtn.prop('disabled', false).text('Надіслати анкету');
                    return;
                }
                var r = result.response;
                var box = $('<div class="success-box"></div>');
                box.append($('<h3></h3>').text(
                    'Дякуємо за участь в опитуванні, ' + r.name + '!'
                ));
                box.append('<p>Вашу відповідь надіслано AJAX-запитом'
                    + ' (сторінку не перезавантажено) та збережено у файлі'
                    + ' <code>' + result.filename + '</code> у папці'
                    + ' <code>survey/</code> та в базі даних.</p>');
                box.append($('<p></p>').html(
                    '<strong>Час та дата заповнення форми:</strong> '
                    + r.submitted_at
                ));
                box.append('<p><a class="btn" href="index.html">'
                    + 'На головну сторінку</a></p>');
                form.replaceWith(box);
            })
            .fail(function () {
                showErrors(['Сервер недоступний. Спробуйте пізніше.']);
                submitBtn.prop('disabled', false).text('Надіслати анкету');
            });
    });
});
