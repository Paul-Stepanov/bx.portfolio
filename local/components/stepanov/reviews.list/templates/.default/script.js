let sendCommentButton = document.querySelectorAll('.component__submit-button');
sendCommentButton.forEach((element) => {
    element.addEventListener('click', (currentElement) => {
            currentElement.preventDefault();

            let formData = new FormData(currentElement.currentTarget.parentElement);

            formData.delete('sessid');

            let formDataValues = [];

            for (const value of formData.values()) {
                formDataValues.push(value);
            }

            let comment = {
                'name': formDataValues[0],
                'rating': formDataValues[1],
                'comment': formDataValues[2],
            };

            comment = JSON.stringify(comment);

            let request = BX.ajax.runComponentAction('stepanov:reviews.list',
                'ajaxAddComment', {
                    mode: 'class', data: {formData: comment},
                });

            request.then((response) => {
                alert('Комментарий успешно добавлен!');

            }, (reject) => {
                alert(reject.errors[0]['message']);
            });
        },
    );
});




