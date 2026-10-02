<?php

require_once __DIR__ . '/inc/functions.php';

global $pdo;


/*
|--------------------------------------------------------------------------
| Allowed Quiz Types
|--------------------------------------------------------------------------
*/

$allowedTypes = [
    'General',
    'Gambling',
    'Audio Visual'
];


/*
|--------------------------------------------------------------------------
| JSON Response Helper
|--------------------------------------------------------------------------
*/

function quizJsonResponse($data)
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| AJAX - QUESTION NUMBERS
|--------------------------------------------------------------------------
*/

if (
    isset($_GET['ajax']) &&
    $_GET['ajax'] === 'question_numbers'
) {

    try {

        $set = isset($_GET['set_no'])
            ? (int) $_GET['set_no']
            : 0;

        $type = isset($_GET['type'])
            ? trim($_GET['type'])
            : '';

        $questionNumbers = [];

        if (
            $set > 0 &&
            in_array($type, $allowedTypes, true)
        ) {

            $stmt = $pdo->prepare("
                SELECT question
                FROM tbl_quiz
                WHERE set_no = ?
                  AND type = ?
                ORDER BY question ASC
            ");

            $stmt->execute([
                $set,
                $type
            ]);

            $questions =
                $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($questions as $index => $question) {

                $questionNumbers[] =
                    $index + 1;

            }
        }

        quizJsonResponse([
            'success' => true,
            'questions' => $questionNumbers
        ]);

    } catch (Throwable $e) {

        quizJsonResponse([
            'success' => false,
            'questions' => [],
            'message' => 'Unable to load question numbers.',
            'error' => $e->getMessage()
        ]);
    }
}


/*
|--------------------------------------------------------------------------
| AJAX - QUESTION
|--------------------------------------------------------------------------
*/

if (
    isset($_GET['ajax']) &&
    $_GET['ajax'] === 'question'
) {

    try {

        $set = isset($_GET['set_no'])
            ? (int) $_GET['set_no']
            : 0;

        $type = isset($_GET['type'])
            ? trim($_GET['type'])
            : '';

        $questionNo = isset($_GET['question_no'])
            ? (int) $_GET['question_no']
            : 0;

        $result = null;

        if (
            $set > 0 &&
            in_array($type, $allowedTypes, true) &&
            $questionNo > 0
        ) {

            $stmt = $pdo->prepare("
                SELECT
                    question,
                    type,
                    media_file
                FROM tbl_quiz
                WHERE set_no = ?
                  AND type = ?
                ORDER BY question ASC
            ");

            $stmt->execute([
                $set,
                $type
            ]);

            $questions =
                $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (
                isset(
                    $questions[$questionNo - 1]
                )
            ) {

                $result =
                    $questions[$questionNo - 1];

            }
        }

        quizJsonResponse([
            'success' => true,
            'question' => $result
        ]);

    } catch (Throwable $e) {

        quizJsonResponse([
            'success' => false,
            'question' => null,
            'message' => 'Unable to load question.',
            'error' => $e->getMessage()
        ]);
    }
}


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

$pageTitle = loadLang('quiz');

$metaDescription =
    'Quiz Questions';

include __DIR__ . '/inc/header.php';


/*
|--------------------------------------------------------------------------
| BREADCRUMBS
|--------------------------------------------------------------------------
*/

$breadcrumbs = [
    [
        'label' => t('home'),
        'url' => BASE_URL
    ],
    [
        'label' => 'Quiz',
        'url' => ''
    ],
];

echo renderBreadcrumbs($breadcrumbs);


/*
|--------------------------------------------------------------------------
| GET SETS
|--------------------------------------------------------------------------
*/

$sets = [];

$stmt = $pdo->query("
    SELECT DISTINCT set_no
    FROM tbl_quiz
    ORDER BY set_no ASC
");

$sets =
    $stmt->fetchAll(PDO::FETCH_COLUMN);

?>


<div class="container py-4">


    <!-- =========================================================
         PAGE HEADER
    ========================================================== -->

    <div class="section-head mb-4">

        <div class="section-kicker">
            Quiz
        </div>

        <h1 class="section-title mb-2">
            Quiz
        </h1>

        <p class="text-muted mb-0">
            Select a set, question type and question number.
        </p>

    </div>


    <!-- =========================================================
         FILTERS
    ========================================================== -->

    <div
        class="card border-0 shadow-sm rounded-4 mb-4"
    >

        <div class="card-body p-4">

            <div class="row g-3">


                <!-- SET -->

                <div class="col-md-4">

                    <label
                        for="setNo"
                        class="form-label fw-semibold"
                    >
                        Set No.
                    </label>

                    <select
                        id="setNo"
                        class="form-select"
                    >

                        <option value="">
                            Select Set No.
                        </option>

                        <?php foreach ($sets as $set) { ?>

                            <option
                                value="<?php echo e($set); ?>"
                            >
                                Set <?php echo e($set); ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- TYPE -->

                <div class="col-md-4">

                    <label
                        for="quizType"
                        class="form-label fw-semibold"
                    >
                        Type
                    </label>

                    <select
                        id="quizType"
                        class="form-select"
                        disabled
                    >

                        <option value="">
                            Select Type
                        </option>

                        <?php foreach ($allowedTypes as $type) { ?>

                            <option
                                value="<?php echo e($type); ?>"
                            >
                                <?php echo e($type); ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- QUESTION NUMBER -->

                <div class="col-md-4">

                    <label
                        for="questionNo"
                        class="form-label fw-semibold"
                    >
                        Question Number
                    </label>

                    <select
                        id="questionNo"
                        class="form-select"
                        disabled
                    >

                        <option value="">
                            Select Question No.
                        </option>

                    </select>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         FULL SCREEN MODAL
    ========================================================== -->

    <div
        class="modal fade"
        id="questionModal"
        tabindex="-1"
        aria-labelledby="questionModalLabel"
        aria-hidden="true"
    >

        <div
            class="modal-dialog modal-fullscreen"
            style="
                margin: 0;
                width: 100%;
                max-width: 100%;
                height: 100vh;
                height: 100dvh;
            "
        >

            <div
                class="modal-content"
                style="
                    width: 100%;
                    height: 100vh;
                    height: 100dvh;
                    max-height: 100vh;
                    max-height: 100dvh;
                    border: 0;
                    border-radius: 0;
                    background: #ffffff;
                    overflow: hidden;
                "
            >


                <!-- =================================================
                     MODAL HEADER
                ================================================== -->

                <div
                    class="modal-header"
                    style="
                        flex-shrink: 0;
                        padding: 12px 25px;
                        min-height: 60px;
                        height: 60px;
                        border-bottom: 1px solid #eeeeee;
                        background: #ffffff;
                    "
                >

                    <h5
                        class="modal-title"
                        id="questionModalLabel"
                        style="
                            margin: 0;
                            font-size: 0.95rem;
                            font-weight: 600;
                            color: #555555;
                        "
                    >
                        Question
                    </h5>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <!-- =================================================
                     MODAL BODY
                ================================================== -->

                <div
                    class="modal-body"
                    style="
                        width: 100%;
                        height: calc(100% - 60px);
                        padding: 0;
                        margin: 0;
                        overflow: hidden;
                        display: flex;
                        flex-direction: column;
                        box-sizing: border-box;
                    "
                >


                    <!-- =============================================
                         QUESTION AREA
                    ============================================== -->

                    <div
                        id="questionResult"
                        style="
                            width: 100%;
                            flex: 1 1 auto;
                            min-height: 0;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 15px 5%;
                            box-sizing: border-box;
                            overflow: hidden;
                        "
                    >

                        <div
                            style="
                                width: 100%;
                                text-align: center;
                                color: #777777;
                            "
                        >
                            Select a question.
                        </div>

                    </div>


                    <!-- =============================================
                         TIMER AREA
                    ============================================== -->

                    <div
                        id="quizTimerPanel"
                        style="
                            width: 100%;
                            flex: 0 0 auto;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            padding: 5px 20px 12px;
                            background: #ffffff;
                            box-sizing: border-box;
                        "
                    >


                        <!-- TIMER -->

                        <div
                            id="quizTimerDisplay"
                            style="
                                font-size: 3.2rem;
                                line-height: 1;
                                font-weight: 700;
                                letter-spacing: 2px;
                                color: #222222;
                                text-align: center;
                                font-variant-numeric: tabular-nums;
                                margin-bottom: 8px;
                            "
                        >
                            05:00
                        </div>


                        <!-- PRESETS -->

                        <div
                            style="
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 5px;
                                margin-bottom: 6px;
                            "
                        >

                            <button
                                type="button"
                                class="quiz-timer-preset"
                                data-minutes="1"
                                title="1 minute"
                                style="
                                    width: 27px;
                                    height: 27px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 12px;
                                    cursor: pointer;
                                "
                            >1</button>

                            <button
                                type="button"
                                class="quiz-timer-preset"
                                data-minutes="2"
                                title="2 minutes"
                                style="
                                    width: 27px;
                                    height: 27px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 12px;
                                    cursor: pointer;
                                "
                            >2</button>

                            <button
                                type="button"
                                class="quiz-timer-preset"
                                data-minutes="3"
                                title="3 minutes"
                                style="
                                    width: 27px;
                                    height: 27px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 12px;
                                    cursor: pointer;
                                "
                            >3</button>

                            <button
                                type="button"
                                class="quiz-timer-preset"
                                data-minutes="4"
                                title="4 minutes"
                                style="
                                    width: 27px;
                                    height: 27px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 12px;
                                    cursor: pointer;
                                "
                            >4</button>

                            <button
                                type="button"
                                class="quiz-timer-preset"
                                data-minutes="5"
                                title="5 minutes"
                                style="
                                    width: 27px;
                                    height: 27px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 12px;
                                    cursor: pointer;
                                "
                            >5</button>

                        </div>


                        <!-- TIMER CONTROLS -->

                        <div
                            style="
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 5px;
                            "
                        >

                            <button
                                type="button"
                                id="timerStart"
                                title="Start"
                                aria-label="Start"
                                style="
                                    width: 30px;
                                    height: 30px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 12px;
                                    cursor: pointer;
                                "
                            >▶</button>

                            <button
                                type="button"
                                id="timerPause"
                                title="Pause"
                                aria-label="Pause"
                                style="
                                    width: 30px;
                                    height: 30px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 12px;
                                    cursor: pointer;
                                "
                            >⏸</button>

                            <button
                                type="button"
                                id="timerStop"
                                title="Stop"
                                aria-label="Stop"
                                style="
                                    width: 30px;
                                    height: 30px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 12px;
                                    cursor: pointer;
                                "
                            >■</button>

                            <button
                                type="button"
                                id="timerReset"
                                title="Reset"
                                aria-label="Reset"
                                style="
                                    width: 30px;
                                    height: 30px;
                                    padding: 0;
                                    border: 1px solid #dddddd;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    font-size: 14px;
                                    cursor: pointer;
                                "
                            >↻</button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function ()
    {

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const setNo =
            document.getElementById('setNo');

        const quizType =
            document.getElementById('quizType');

        const questionNo =
            document.getElementById('questionNo');

        const questionModalElement =
            document.getElementById('questionModal');

        const questionResult =
            document.getElementById('questionResult');

        const questionModalLabel =
            document.getElementById(
                'questionModalLabel'
            );


        /*
        |--------------------------------------------------------------------------
        | TIMER ELEMENTS
        |--------------------------------------------------------------------------
        */

        const timerDisplay =
            document.getElementById(
                'quizTimerDisplay'
            );

        const timerStart =
            document.getElementById(
                'timerStart'
            );

        const timerPause =
            document.getElementById(
                'timerPause'
            );

        const timerStop =
            document.getElementById(
                'timerStop'
            );

        const timerReset =
            document.getElementById(
                'timerReset'
            );

        const timerPresets =
            document.querySelectorAll(
                '.quiz-timer-preset'
            );


        /*
        |--------------------------------------------------------------------------
        | AJAX ENDPOINT
        |--------------------------------------------------------------------------
        */

        const ajaxEndpoint =
            <?php echo json_encode($_SERVER['SCRIPT_NAME']); ?>;


        /*
        |--------------------------------------------------------------------------
        | BOOTSTRAP MODAL
        |--------------------------------------------------------------------------
        */

        let questionModal = null;

        if (
            typeof bootstrap !== 'undefined' &&
            questionModalElement
        ) {

            questionModal =
                new bootstrap.Modal(
                    questionModalElement
                );

        }


        /*
        |--------------------------------------------------------------------------
        | ESCAPE HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value)
        {

            const div =
                document.createElement('div');

            div.textContent =
                value === null ||
                value === undefined
                    ? ''
                    : String(value);

            return div.innerHTML;

        }


        /*
        |--------------------------------------------------------------------------
        | ESCAPE ATTRIBUTE
        |--------------------------------------------------------------------------
        */

        function escapeAttribute(value)
        {

            return escapeHtml(value)
                .replace(
                    /"/g,
                    '&quot;'
                )
                .replace(
                    /'/g,
                    '&#039;'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | RESET QUESTION NUMBERS
        |--------------------------------------------------------------------------
        */

        function resetQuestionNumbers()
        {

            questionNo.innerHTML = `
                <option value="">
                    Select Question No.
                </option>
            `;

            questionNo.disabled = true;

        }


        /*
        |--------------------------------------------------------------------------
        | RESET TYPE
        |--------------------------------------------------------------------------
        */

        function resetType()
        {

            quizType.value = '';

            quizType.disabled = true;

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD QUESTION NUMBERS
        |--------------------------------------------------------------------------
        */

        async function loadQuestionNumbers()
        {

            const set =
                setNo.value;

            const type =
                quizType.value;


            if (!set || !type) {

                resetQuestionNumbers();

                return;

            }


            questionNo.innerHTML = `
                <option value="">
                    Loading questions...
                </option>
            `;

            questionNo.disabled = true;


            const url =
                ajaxEndpoint +
                '?ajax=question_numbers' +
                '&set_no=' +
                encodeURIComponent(set) +
                '&type=' +
                encodeURIComponent(type);


            try {

                const response =
                    await fetch(
                        url,
                        {
                            method: 'GET',
                            cache: 'no-cache',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            }
                        }
                    );


                const responseText =
                    await response.text();


                let data;

                try {

                    data =
                        JSON.parse(
                            responseText
                        );

                } catch (error) {

                    console.error(
                        'Invalid JSON:',
                        responseText
                    );

                    throw new Error(
                        'Server returned an invalid response.'
                    );

                }


                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Unable to load questions.'
                    );

                }


                if (!data.success) {

                    throw new Error(
                        data.message ||
                        'Unable to load questions.'
                    );

                }


                questionNo.innerHTML = `
                    <option value="">
                        Select Question No.
                    </option>
                `;


                if (
                    Array.isArray(
                        data.questions
                    ) &&
                    data.questions.length > 0
                ) {

                    data.questions.forEach(
                        function (number)
                        {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                number;

                            option.textContent =
                                'Question ' +
                                number;

                            questionNo.appendChild(
                                option
                            );

                        }
                    );

                    questionNo.disabled =
                        false;

                } else {

                    questionNo.innerHTML = `
                        <option value="">
                            No questions found
                        </option>
                    `;

                    questionNo.disabled = true;

                }


            } catch (error) {

                console.error(
                    'Question number error:',
                    error
                );


                questionNo.innerHTML = `
                    <option value="">
                        Error loading questions
                    </option>
                `;

                questionNo.disabled = true;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | TIMER VARIABLES
        |--------------------------------------------------------------------------
        */

        let timerSeconds = 300;

        let timerInitialSeconds = 300;

        let timerInterval = null;

        let timerRunning = false;


        /*
        |--------------------------------------------------------------------------
        | FORMAT TIMER
        |--------------------------------------------------------------------------
        */

        function formatTimer(seconds)
        {

            seconds =
                Math.max(
                    0,
                    seconds
                );


            const minutes =
                Math.floor(
                    seconds / 60
                );

            const remainingSeconds =
                seconds % 60;


            return (
                String(minutes).padStart(
                    2,
                    '0'
                ) +
                ':' +
                String(
                    remainingSeconds
                ).padStart(
                    2,
                    '0'
                )
            );

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE TIMER
        |--------------------------------------------------------------------------
        */

        function updateTimerDisplay()
        {

            timerDisplay.textContent =
                formatTimer(
                    timerSeconds
                );


            if (timerSeconds <= 10) {

                timerDisplay.style.color =
                    '#dc3545';

            } else {

                timerDisplay.style.color =
                    '#222222';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | STOP INTERVAL
        |--------------------------------------------------------------------------
        */

        function stopTimerInterval()
        {

            if (
                timerInterval !== null
            ) {

                clearInterval(
                    timerInterval
                );

                timerInterval = null;

            }

            timerRunning = false;

        }


        /*
        |--------------------------------------------------------------------------
        | START TIMER
        |--------------------------------------------------------------------------
        */

        function startTimer()
        {

            if (timerRunning) {

                return;

            }


            if (timerSeconds <= 0) {

                timerSeconds =
                    timerInitialSeconds;

                updateTimerDisplay();

            }


            timerRunning = true;


            timerInterval =
                setInterval(
                    function ()
                    {

                        if (
                            timerSeconds > 0
                        ) {

                            timerSeconds--;

                            updateTimerDisplay();

                        }


                        if (
                            timerSeconds <= 0
                        ) {

                            stopTimerInterval();

                            timerDisplay.style.color =
                                '#dc3545';

                        }

                    },
                    1000
                );

        }


        /*
        |--------------------------------------------------------------------------
        | PAUSE
        |--------------------------------------------------------------------------
        */

        function pauseTimer()
        {

            stopTimerInterval();

        }


        /*
        |--------------------------------------------------------------------------
        | STOP
        |--------------------------------------------------------------------------
        */

        function stopTimer()
        {

            stopTimerInterval();

            timerSeconds = 0;

            updateTimerDisplay();

        }


        /*
        |--------------------------------------------------------------------------
        | RESET
        |--------------------------------------------------------------------------
        */

        function resetTimer()
        {

            stopTimerInterval();

            timerSeconds =
                timerInitialSeconds;

            updateTimerDisplay();

        }


        /*
        |--------------------------------------------------------------------------
        | TIMER EVENTS
        |--------------------------------------------------------------------------
        */

        timerStart.addEventListener(
            'click',
            function ()
            {
                startTimer();
            }
        );


        timerPause.addEventListener(
            'click',
            function ()
            {
                pauseTimer();
            }
        );


        timerStop.addEventListener(
            'click',
            function ()
            {
                stopTimer();
            }
        );


        timerReset.addEventListener(
            'click',
            function ()
            {
                resetTimer();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | TIMER PRESETS
        |--------------------------------------------------------------------------
        */

        timerPresets.forEach(
            function (button)
            {

                button.addEventListener(
                    'click',
                    function ()
                    {

                        const minutes =
                            parseInt(
                                this.dataset.minutes,
                                10
                            );


                        if (
                            isNaN(minutes) ||
                            minutes <= 0
                        ) {

                            return;

                        }


                        stopTimerInterval();


                        timerInitialSeconds =
                            minutes * 60;

                        timerSeconds =
                            timerInitialSeconds;


                        updateTimerDisplay();

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | INITIAL TIMER
        |--------------------------------------------------------------------------
        */

        updateTimerDisplay();


        /*
        |--------------------------------------------------------------------------
        | LOAD QUESTION
        |--------------------------------------------------------------------------
        */

        async function loadQuestion()
        {

            const set =
                setNo.value;

            const type =
                quizType.value;

            const questionNumber =
                questionNo.value;


            if (
                !set ||
                !type ||
                !questionNumber
            ) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | SHOW MODAL
            |--------------------------------------------------------------------------
            */

            if (questionModal) {

                questionModal.show();

            }


            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            questionModalLabel.innerHTML = `

                <span
                    style="
                        font-size: 0.95rem;
                        font-weight: 600;
                        color: #555555;
                    "
                >
                    Question
                    ${escapeHtml(questionNumber)}
                </span>

                <span
                    style="
                        font-size: 0.8rem;
                        font-weight: 400;
                        color: #999999;
                        margin-left: 8px;
                    "
                >
                    ${escapeHtml(type)}
                </span>

            `;


            /*
            |--------------------------------------------------------------------------
            | LOADING
            |--------------------------------------------------------------------------
            */

            questionResult.innerHTML = `

                <div
                    style="
                        width: 100%;
                        height: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                    "
                >

                    <div>

                        <div
                            class="spinner-border"
                            role="status"
                            style="
                                width: 3rem;
                                height: 3rem;
                                margin-bottom: 15px;
                            "
                        >
                        </div>

                        <div
                            style="
                                color: #777777;
                            "
                        >
                            Loading question...
                        </div>

                    </div>

                </div>

            `;


            /*
            |--------------------------------------------------------------------------
            | AJAX URL
            |--------------------------------------------------------------------------
            */

            const url =
                ajaxEndpoint +
                '?ajax=question' +
                '&set_no=' +
                encodeURIComponent(set) +
                '&type=' +
                encodeURIComponent(type) +
                '&question_no=' +
                encodeURIComponent(
                    questionNumber
                );


            try {

                const response =
                    await fetch(
                        url,
                        {
                            method: 'GET',
                            cache: 'no-cache',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            }
                        }
                    );


                const responseText =
                    await response.text();


                let data;

                try {

                    data =
                        JSON.parse(
                            responseText
                        );

                } catch (error) {

                    console.error(
                        'Invalid JSON:',
                        responseText
                    );

                    throw new Error(
                        'Server returned an invalid response.'
                    );

                }


                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Unable to load question.'
                    );

                }


                if (!data.success) {

                    throw new Error(
                        data.message ||
                        'Unable to load question.'
                    );

                }


                if (!data.question) {

                    throw new Error(
                        'Question not found.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | DATA
                |--------------------------------------------------------------------------
                */

                const question =
                    data.question;

                const questionText =
                    question.question || '';

                const typeValue =
                    question.type || type;

                const mediaFile =
                    question.media_file || '';


                /*
                |--------------------------------------------------------------------------
                | QUESTION HTML
                |--------------------------------------------------------------------------
                */

                let html = `

                    <div
                        style="
                            width: 100%;
                            height: 100%;
                            min-height: 0;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            padding: 10px 30px;
                            box-sizing: border-box;
                            overflow: hidden;
                        "
                    >

                        <div
                            style="
                                width: 100%;
                                max-width: 1500px;
                                margin: 0 auto;
                                text-align: center;
                                font-size: clamp(
                                    2rem,
                                    3.8vw,
                                    4.2rem
                                );
                                line-height: 1.3;
                                font-weight: 600;
                                color: #202124;
                                word-break: break-word;
                                overflow-wrap: anywhere;
                            "
                        >

                            ${escapeHtml(questionText)}

                        </div>

                `;


                /*
                |--------------------------------------------------------------------------
                | AUDIO VISUAL
                |--------------------------------------------------------------------------
                */

                if (
                    typeValue === 'Audio Visual' &&
                    mediaFile
                ) {

                    const safeMedia =
                        escapeAttribute(
                            mediaFile
                        );

                    const extension =
                        mediaFile
                            .split('.')
                            .pop()
                            .toLowerCase();


                    /*
                    |--------------------------------------------------------------------------
                    | IMAGE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        [
                            'jpg',
                            'jpeg',
                            'png',
                            'gif',
                            'webp'
                        ].includes(extension)
                    ) {

                        html += `

                            <div
                                style="
                                    width: 100%;
                                    max-width: 1200px;
                                    margin-top: 15px;
                                    text-align: center;
                                    min-height: 0;
                                "
                            >

                                <img
                                    src="${safeMedia}"
                                    alt="Quiz Media"
                                    style="
                                        display: block;
                                        max-width: 100%;
                                        max-height: 28vh;
                                        width: auto;
                                        height: auto;
                                        object-fit: contain;
                                        margin: 0 auto;
                                        border-radius: 8px;
                                    "
                                >

                            </div>

                        `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VIDEO
                    |--------------------------------------------------------------------------
                    */

                    else if (
                        [
                            'mp4',
                            'webm',
                            'mov',
                            'avi'
                        ].includes(extension)
                    ) {

                        html += `

                            <div
                                style="
                                    width: 100%;
                                    max-width: 1100px;
                                    margin-top: 15px;
                                    min-height: 0;
                                "
                            >

                                <video
                                    controls
                                    style="
                                        display: block;
                                        width: 100%;
                                        max-height: 32vh;
                                        margin: 0 auto;
                                        border-radius: 8px;
                                        background: #000000;
                                    "
                                >

                                    <source
                                        src="${safeMedia}"
                                    >

                                    Your browser does not support
                                    video playback.

                                </video>

                            </div>

                        `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | AUDIO
                    |--------------------------------------------------------------------------
                    */

                    else if (
                        [
                            'mp3',
                            'wav',
                            'ogg',
                            'm4a'
                        ].includes(extension)
                    ) {

                        html += `

                            <div
                                style="
                                    width: 100%;
                                    max-width: 700px;
                                    margin-top: 15px;
                                "
                            >

                                <audio
                                    controls
                                    style="
                                        width: 100%;
                                    "
                                >

                                    <source
                                        src="${safeMedia}"
                                    >

                                    Your browser does not support
                                    audio playback.

                                </audio>

                            </div>

                        `;

                    }

                }


                html += `

                    </div>

                `;


                /*
                |--------------------------------------------------------------------------
                | SHOW QUESTION
                |--------------------------------------------------------------------------
                */

                questionResult.innerHTML =
                    html;


            } catch (error) {

                console.error(
                    'Question loading error:',
                    error
                );


                questionResult.innerHTML = `

                    <div
                        style="
                            width: 100%;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 20px;
                            box-sizing: border-box;
                        "
                    >

                        <div
                            style="
                                max-width: 600px;
                                width: 100%;
                                padding: 25px;
                                border-radius: 10px;
                                background: #fff0f0;
                                border: 1px solid #ffcccc;
                                color: #dc3545;
                                text-align: center;
                            "
                        >

                            <strong>
                                Error loading question
                            </strong>

                            <div
                                style="
                                    margin-top: 8px;
                                "
                            >
                                ${escapeHtml(
                                    error.message
                                )}
                            </div>

                        </div>

                    </div>

                `;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | SET CHANGE
        |--------------------------------------------------------------------------
        */

        setNo.addEventListener(
            'change',
            function ()
            {

                resetQuestionNumbers();


                if (this.value) {

                    quizType.disabled =
                        false;

                } else {

                    resetType();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | TYPE CHANGE
        |--------------------------------------------------------------------------
        */

        quizType.addEventListener(
            'change',
            function ()
            {

                resetQuestionNumbers();


                if (this.value) {

                    loadQuestionNumbers();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | QUESTION NUMBER CHANGE
        |--------------------------------------------------------------------------
        */

        questionNo.addEventListener(
            'change',
            function ()
            {

                if (this.value) {

                    loadQuestion();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | MODAL CLOSE
        |--------------------------------------------------------------------------
        */

        if (questionModalElement) {

            questionModalElement.addEventListener(
                'hidden.bs.modal',
                function ()
                {

                    pauseTimer();

                }
            );

        }

    }

);

</script>

<?php

include __DIR__ . '/inc/footer.php';

?>

