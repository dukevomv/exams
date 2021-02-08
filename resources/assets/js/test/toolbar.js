const testsURL = baseURL+'/tests/'

$('body').scrollspy({ target: '#segment-list' })

$('.task-value').on('change',function(){
  testUtils.toggleButton($('#save-test'),'enable');
  testUtils.toggleButton($('#save-draft-test'),'enable');
});

$('#start-test').on('click',function(e){
  makeAjaxPost(testsURL+testData.test.id+'/'+'start',{_token:CSRF},function() {
    $('#start-test').removeClass('btn-success').addClass('btn-default').prop('disabled',false)
  });
});
$('#finish-test').on('click',function(e){
  makeAjaxPost(testsURL+testData.test.id+'/'+'finish',{},function() {
    $('#finish-test').removeClass('btn-danger').addClass('btn-default').prop('disabled',false)
  });
});
$('#publish-grade').on('click',function(e){
  makeAjaxPost(testsURL+testData.test.id+'/users/'+testData.test.for_student.id+'/publish-grade');
});
$('#auto-grade').on('click',function(e){
  makeAjaxPost(testsURL+testData.test.id+'/users/'+testData.test.for_student.id+'/auto-grade');
});
$('#auto-calculate-test').on('click',function(e){
  makeAjaxPost(testsURL+testData.test.id+'/auto-calculate');
});
$('#publish-test-grades').on('click',function(e){
  makeAjaxPost(testsURL+testData.test.id+'/publish-grades');
});

function makeAjaxPost(path,data = {},callback = null){
  const final = Object.assign({_token:CSRF},...data)
  $.post(path,final,function() {
    if(!callback){
      location.reload()
    } else{
      callback()
    }
  }).fail(function(e) {
    showValidatorErrors(e)
  })
}


//todo these are not working
// - saving test and reloading
$('#save-test').on('click',function(e){
  testUtils.saveTest(true);
});
$('#save-draft-test').on('click',function(e){
  testUtils.saveTest();
});

testUtils.toggleButton = function(button,action,title=''){
  switch(action){
    case 'disable':
      button.prop('disabled',true);
      button.addClass('btn-default');
      break;
    case 'enable':
      button.prop('disabled',false);
      button.removeClass('btn-default');
      break;
    default:
    //code
  }
  if(title !== '')
    button.text(title);
}

testUtils.saveTest = function(final= false){
  let answers=[];

  $("#test-student-segments .task-wrap").each(function(index) {
    let task_type = $(this).attr('data-task-type')
    answers.push(GetTaskAnswers($(this),task_type))
  });

  makeAjaxPost(testsURL+testData.test.id+'/'+'submit',{final: final?1:0,answers},function() {
    testUtils.toggleButton($('#save-test'),'enable','Submit'+(final?'':' (1)'));
    testUtils.toggleButton($('#save-draft-test'),'disable');

    if(final){
      testUtils.toggleButton($('#save-test'),'disable');
    }
  });

  function GetDOMValue(element){
    let data = {};
    element.find('.task-value').each(function(i) {
      if($(this).attr('data-value-prop')){
        if($(this).attr('data-value-prop') == 'checked'){
          data[$(this).attr('data-key')] = $(this).is(":checked") ? 1 : 0;
        }
      } else if($(this).attr('data-value')){
        data[$(this).attr('data-key')] = $(this).attr('data-value');
      }
    });
    return data;
  }

  function GetTaskAnswers(element, task_type){
    let task = {
      id          : element.attr('data-task-id'),
      type        : task_type,
    }
    switch(task_type) {
      case "rmc":
      case "cmc":
        task.data = []
        element.find('.task-list .task-choice').each(function(i) {
          let choice = GetDOMValue($(this));
          task.data.push(choice);
        })
        break;
      case "free_text":
        task.data = element.find('textarea').val();
        break;
      case "correspondence":
        task.data = []
        element.find('.choice-wrap').each(function(i) {
          let choice = {
            side_a        : $(this).find('input.side-a').val(),
            side_b        : $(this).find('input.side-b').val()
          }
          if(choice.side_a != '' && choice.side_b != '')   task.data.push(choice)
        })
        break;
      case "code":
        //todo: fix code task type input
        task.data.push({
          id          : element.find('.task-code input').val(),
          description : element.find('.task-code textarea').val()
        })
        break;
      default:
      //code block
    }
    return task
  }
}