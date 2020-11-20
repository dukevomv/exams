const taskHandlerIdPrefix = ".task-wrap.task-wrap-correspondence#panel-task-"

function getCorrespondenceTaskAnswersAndElements(taskId) {
  let task = $(taskHandlerIdPrefix + taskId);
  let answers = {};
  task.find('.choice-wrap').each(function () {
    answers[$(this).find('input.side-a').val()] = {
      value: $(this).find('input.side-b').val(),
      element: $(this).find('input.side-b'),
    }
  });
  return answers;
}

$(".choice-side-b a").click(function (e) {
  e.preventDefault()
  const sideB = $(this).text();
  const parent = $(this).closest('.input-group');
  const sideA = parent.find('input.side-a').val();
  const taskId = parent.closest('.task-wrap').attr('data-task-id');

  let taskAnswersAndElements = getCorrespondenceTaskAnswersAndElements(taskId);
  let taskAnswers = {};
  Object.keys(taskAnswersAndElements).forEach(a => {
    if (taskAnswersAndElements[a].value === sideB) {
      taskAnswersAndElements[a].element.val('');
    }
    taskAnswers[a] = taskAnswersAndElements[a].value;
  });
  parent.find('input.side-b').val(sideB);
  taskAnswers[sideA] = sideB

  fixSelectedOptionsInDropdown(taskId,taskAnswers);
});

function fixSelectedOptionsInDropdown(taskId,taskAnswers) {
  $(taskHandlerIdPrefix + taskId + ' .panel-body .choice-wrap').each(function () {
    const valueB = $(this).find('input.side-b').val()

    const choiceIsEmpty = (valueB === '');

    $(this).find('input.side-b').val(choiceIsEmpty ? '' : valueB);
    $(this).find('button.choice-button .text-overflow').text(choiceIsEmpty ? 'Option' : valueB)
    if (choiceIsEmpty) {
      $(this).find('button.choice-button').removeClass('btn-primary').addClass('btn-default')
    } else {
      $(this).find('button.choice-button').removeClass('btn-default').addClass('btn-primary')
    }

    const selected = Object.values(taskAnswers);
    $(this).find('.choice-side-b').removeClass('active').removeClass('selected').each(function () {
      const choiceB = $(this).find('a').text();
      if (valueB === choiceB) {
        $(this).addClass('active')
      } else if (selected.indexOf(choiceB) > -1) {
        $(this).addClass('selected')
      }
    })
  })
}