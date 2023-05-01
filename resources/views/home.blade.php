@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                @php
                $messages = [
                    'trial' => [
                    ],
                    'demo' => [
                        [
                            'title' => 'Switch User Roles',
                            'body' => '<p>For demo users only, you have the ability to <b>switch the current user role</b> to experience the platform from each user\'s point of view.<br>
                            Check at the top-right user dropdown and you will see the 3 available roles to switch to.</p>'
                        ],
                    ],
                    'admin' => [
                        [
                            'title' => 'Manage Users',
                            'body' => '<p><b>Approve or Reject</b> users to the platform based on their profile details.
                            <br>Users can freely register with their preferred role but administrators are the ones that will <b>validate them and allow them to navigate the platform.</b></p>'
                        ],
                        [
                            'title' => 'Delete Users',
                            'body' => '<p>The deletion of users after they have finished with their examination is also available in the <b>Users</b> tab.
                            <br> <b>You can undo the deletion</b> anytime you want to be able to see the user in your users list again.</p>'
                        ],
                        [
                            'title' => 'Manage Courses',
                            'body' => '<p>Create and update courses and then <b>allow users to request access</b> on them. Courses with exams will not be able to be deleted.
                            <br>Admins approve user requests on courses in order for them to view the course exams.</p>'
                        ],
                    ],
                    'professor' => [
                        [
                            'title' => 'Segments & Question Types',
                            'body' => '<p>Each Test consists of <b>multiple Segments ordered by you</b>. Segments help you consist small groups of questions (commonly used for same topic) and each segment contains <b>the questions</b> for the student along <b>with their points</b> assigned to them. Types of Questions:
                                <br>- Single Choice Select
                                <br>- Multiple Choice Select
                                <br>- Correspondence
                                <br>- Free Text (autocorrect optionally)'
                        ],
                        [
                            'title' => 'Exam Creation',
                            'body' => '<p>After creating your segments you can <b>create an exam and attach your existing segments</b>. Select examination date and time, duration and description.
                            <br> You can also <b>update the segments</b> after you\'ve created the exam.'],
                        [
                            'title' => 'Invite Students directly on exam',
                            'body' => '<p>After you publish it, you will be able to invite students directly on your examination, bypassing for that single exam the approval procedure of users in the platform (Invited students will only be able to see the specific exam).
                                <br><i>If you want students to be available for multiple examinations they can all signup and request access to your course (Requires administrator approval).</i></p>'
                        ],
                        [
                            'title' => 'Starting Examination',
                            'body' => '<p>Press the <b>"Start"</b> button to initiate the clock. When Starting Exam the clock will <b>countdown 30’’ for students to prepare</b> before the segments appear on the screen. After those 30’’ the examination will start and <b>everyone’s page will refresh</b> to show the contents of the examination.
                            <br> The clock will start counting down with the exam’s duration.
                            <br><b>Students will be able to submit their answers unrelated of the expiring clock</b>, thus providing the ability to professors to control the exact examination\'s duration.</p>'
                        ],
                        [
                            'title' => 'Finishing Examination',
                            'body' => '<p>Press the <b>"Finish"</b> button to end the examination. When Finishing Exam the clock will countdown the last 30’’ for the student to <b>finalise their answers and submit for review</b>.
                            <br>After those seconds the examination will be <b>locked and no other answers can be submitted</b> to the system.</p>'
                        ],
                        [
                            'title' => 'Grading Examination',
                            'body' => '<p>Assign the equivalent amount of points to each segment question by viewing each student\'s answers and press <b>"Publish Grades"</b>.
                            <br>When using autocorrect questions, you will be able to <b>automatically correct all student\'s answers</b> by pressing <b>"Auto Calculate Grades"</b>.</p>'
                        ],
                        [
                            'title' => 'Publishing Exam Results',
                            'body' => '<p>After all students are graded, you can publish examination\'s grades and inform students with their grades.
                            <br>This action will also <b>email students and the professor</b> publishing the test with all <b>student’s results</b> and general statistics for all test.</p>'
                        ]
                    ],
                    'student' => [
                        [
                            'title' => 'Invited Students',
                            'body' => '<p>Students that have accepted the invitation can <b>only preview the single course they\'ve been invited to</b>.
                            <br>You can <b>re-access accepted invitations by using the same invitation email</b> that came first by the professor.</p>'
                        ],
                        [
                            'title' => 'Attend Examination',
                            'body' => '<p>Make sure to be online on the date and time of examination and <b>keep track of professor\'s updates around the exam</b> in order to be able to take the exam.
                            <br>Professor will manually start and finish the exam and <b>the clock will be indicative of the exam\'s duration</b>.</p>'
                        ],
                        [
                            'title' => 'Extra 30 seconds',
                            'body' => '<p>On Start and finish action from the professor, you will be given 30 seconds in the beginning to <b>prepare for the test</b>.<br>Upon finishing you will be given another 30 seconds to <b>finalize your answers</b>.</p>'
                        ],
                        [
                            'title' => 'Examination Results',
                            'body' => '<p>You will be <b>informed by the professors with an email</b> of the exams\'s results when all students are graded.</p>'
                        ],
                    ],
                ];
                    foreach($messages as $type => $list){
                        for($i=0;$i<count($list);$i++){
                            $messages[$type][$i]['tags'] = [$type];
                        }
                    }
                    $mode = \App\Util\Demo::getModeFromSessionIfAny();
                    $modeMessages = is_null($mode) ? [] : $messages[$mode];
                    $commits = array_merge($modeMessages,$messages[\Illuminate\Support\Facades\Auth::user()->role]);
               @endphp
                @foreach($commits as $commit)
                    @include('includes.commit',$commit)
                @endforeach

            </div>
        </div>
    </div>
@endsection
