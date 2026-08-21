<?php

/* ==========================================================================
   AJAX WINNER SELECTION
   ========================================================================== */

if (
    isset($_POST['action']) &&
    $_POST['action'] === 'select_winner'
) {

    ob_start();
    require_once('header.php');
    ob_end_clean();

    header('Content-Type: application/json; charset=utf-8');

    try {

        /*
        |--------------------------------------------------------------------------
        | SELECT RANDOM WINNER FROM ALL NON-WINNER TICKETS
        |--------------------------------------------------------------------------
        */

        $statement = $pdo->prepare("
            SELECT
                ticket_id,
                ticket_number,
                child_name,
                guardian_name,
                contact_number,
                ticket_price,
                prize,
                class
            FROM tbl_lottery_ticket
            WHERE status != 'Winner'
            ORDER BY RAND()
            LIMIT 1
        ");

        $statement->execute();

        $winner = $statement->fetch(PDO::FETCH_ASSOC);

        /*
        |--------------------------------------------------------------------------
        | NO TICKETS LEFT
        |--------------------------------------------------------------------------
        */

        if (!$winner) {

            echo json_encode([
                'success' => false,
                'message' => 'No tickets remaining for the lucky draw.'
            ]);

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | MARK SELECTED TICKET AS WINNER
        |--------------------------------------------------------------------------
        */

        $update = $pdo->prepare("
            UPDATE tbl_lottery_ticket
            SET
                status = 'Winner',
                updated_at = NOW()
            WHERE ticket_id = ?
            AND status != 'Winner'
        ");

        $update->execute([
            $winner['ticket_id']
        ]);


        /*
        |--------------------------------------------------------------------------
        | VERIFY UPDATE
        |--------------------------------------------------------------------------
        */

        if ($update->rowCount() !== 1) {

            echo json_encode([
                'success' => false,
                'message' => 'Unable to mark the selected ticket as Winner.'
            ]);

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | GET ALL REMAINING NON-WINNER TICKETS
        |--------------------------------------------------------------------------
        */

        $ticketStatement = $pdo->prepare("
            SELECT
                ticket_id,
                ticket_number,
                prize
            FROM tbl_lottery_ticket
            WHERE status != 'Winner'
            ORDER BY ticket_id ASC
        ");

        $ticketStatement->execute();

        $remainingTickets =
            $ticketStatement->fetchAll(PDO::FETCH_ASSOC);


        /*
        |--------------------------------------------------------------------------
        | ADD WINNER TO WHEEL ARRAY
        |--------------------------------------------------------------------------
        */

        array_unshift(
            $remainingTickets,
            [
                'ticket_id'     => $winner['ticket_id'],
                'ticket_number' => $winner['ticket_number'],
                'prize'         => $winner['prize']
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | RETURN RESULT
        |--------------------------------------------------------------------------
        */

        echo json_encode([
            'success'        => true,
            'ticket_id'      => $winner['ticket_id'],
            'ticket_number'  => $winner['ticket_number'],
            'prize'          => $winner['prize'],
            'child_name'     => $winner['child_name'],
            'guardian_name'  => $winner['guardian_name'],
            'contact_number' => $winner['contact_number'],
            'ticket_price'   => $winner['ticket_price'],
            'class'          => $winner['class'],
            'tickets'        => $remainingTickets
        ]);

        exit;


    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);

        exit;
    }
}


/* ==========================================================================
   NORMAL PAGE
   ========================================================================== */

require_once('header.php');


try {

    /*
    |--------------------------------------------------------------------------
    | GET ALL AVAILABLE TICKETS (include prize)
    |--------------------------------------------------------------------------
    */

    $statement = $pdo->prepare("
        SELECT
            ticket_id,
            ticket_number,
            prize
        FROM tbl_lottery_ticket
        WHERE status != 'Winner'
        ORDER BY ticket_id ASC
    ");

    $statement->execute();

    $wheelTickets =
        $statement->fetchAll(PDO::FETCH_ASSOC);

    $available_count =
        count($wheelTickets);


} catch (Throwable $e) {

    $wheelTickets = [];

    $available_count = 0;

    $page_error =
        $e->getMessage();
}


/*
|--------------------------------------------------------------------------
| JSON FOR JAVASCRIPT
|--------------------------------------------------------------------------
*/

$wheelTicketsJson = json_encode(
    $wheelTickets,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP
);

?>

<style>

/* ==========================================================================
   PAGE + BACKGROUND (Dark Blue Gradient – keep as you like)
   ========================================================================== */

.lottery-spinner-page {
    width: 100%;
    height: calc(100vh - 120px);
    min-height: 0;

    padding: 10px 15px 30px;

    box-sizing: border-box;

    background:
        radial-gradient(
            circle at 50% 45%,
            #7066ff 0%,
            #4769ff 35%,
            #02113a 70%,
            #000000 100%
        );

    position: relative;

    overflow: hidden;

    display: flex;

    align-items: center;
    justify-content: center;
}


.lottery-spinner-page::before {

    content: '';

    position: absolute;

    inset: 0;

    background-image:
        radial-gradient(
            circle at 20% 30%,
            rgba(255,255,255,0.35) 0%,
            transparent 4%
        ),
        radial-gradient(
            circle at 80% 20%,
            rgba(255,255,255,0.25) 0%,
            transparent 3%
        ),
        radial-gradient(
            circle at 60% 70%,
            rgba(255,255,255,0.3) 0%,
            transparent 5%
        ),
        radial-gradient(
            circle at 30% 80%,
            rgba(255,255,255,0.2) 0%,
            transparent 4%
        ),
        radial-gradient(
            circle at 90% 60%,
            rgba(255,255,255,0.25) 0%,
            transparent 3%
        );

    pointer-events: none;

    z-index: 0;
}


/* ==========================================================================
   CONTAINER
   ========================================================================== */

.spinner-container {

    width: 100%;

    max-width: 1200px;

    height: 100%;

    margin: 0 auto;

    text-align: center;

    position: relative;

    z-index: 1;

    display: flex;

    flex-direction: column;

    justify-content: center;
}


/* ==========================================================================
   TITLE
   ========================================================================== */

.spinner-main-title {

    font-size: 34px;

    font-weight: 800;

    line-height: 1.2;

    color: #fff;

    text-shadow:
        0 2px 8px rgba(0,0,0,0.35);

    margin: 0 0 18px;
}


/* ==========================================================================
   MAIN LAYOUT
   ========================================================================== */

.wheel-and-display {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 40px;

    flex: 1 1 auto;

    min-height: 0;
}


/* ==========================================================================
   WHEEL AREA
   ========================================================================== */

.wheel-area {

    position: relative;

    flex: 1 1 auto;

    min-width: 0;

    height: auto;

    max-width:
        min(
            620px,
            72vw,
            64vh
        );

    aspect-ratio: 1 / 1;

    display: flex;

    align-items: center;

    justify-content: center;
}


/* ==========================================================================
   WHEEL  (Golden border and glow – exactly as in image)
   ========================================================================== */

.wheel {

    position: relative;

    width: 100%;

    height: 100%;

    border-radius: 50%;

    border: 22px solid #d4af37;

    box-sizing: border-box;

    background: #c0392b; /* fallback, will be overridden by JS */

    box-shadow:
        0 0 0 6px #b8860b,
        0 0 0 10px #f1c40f,
        0 12px 40px rgba(0,0,0,0.45),
        inset 0 0 30px rgba(0,0,0,0.15);

    transition:
        transform 30s
        cubic-bezier(0.08, 0.6, 0.1, 1);

    overflow: hidden;
}


.wheel::before {

    content: '';

    position: absolute;

    inset: -18px;

    border-radius: 50%;

    background:
        repeating-conic-gradient(
            from 0deg,
            #fff 0deg 4deg,
            transparent 4deg 15deg
        );

    -webkit-mask:
        radial-gradient(
            farthest-side,
            transparent calc(100% - 8px),
            #000 calc(100% - 8px)
        );

    mask:
        radial-gradient(
            farthest-side,
            transparent calc(100% - 8px),
            #000 calc(100% - 8px)
        );

    pointer-events: none;

    z-index: 15;

    opacity: 0.9;
}


/* ==========================================================================
   TICKET LABELS
   ========================================================================== */

.ticket-label {

    position: absolute;

    left: 50%;
    top: 50%;

    width: 100%;
    height: 100%;

    transform-origin: center center;

    pointer-events: none;

    z-index: 5;
}


.ticket-label span {

    position: absolute;

    left: 50%;

    top: 7%;

    transform:
        translateX(-50%)
        rotate(180deg);

    padding: 2px 1px;

    box-sizing: border-box;

    font-size: 14px;

    font-weight: 900;

    line-height: 1;

    text-align: center;

    text-shadow:
        1px 1px 3px rgba(0,0,0,0.6);

    white-space: nowrap;

    overflow: hidden;

    text-overflow: clip;

    pointer-events: none;
}


/* ==========================================================================
   WHEEL SECTOR LINES
   ========================================================================== */

.wheel-sector-line {

    position: absolute;

    width: 2px;

    height: 50%;

    top: 0;

    left:
        calc(50% - 1px);

    background:
        rgba(255,255,255,0.85);

    transform-origin:
        bottom center;

    z-index: 7;

    pointer-events: none;
}


/* ==========================================================================
   INNER RING
   ========================================================================== */

.wheel-inner-ring {

    position: absolute;

    width: 100%;

    height: 100%;

    border-radius: 50%;

    border:
        4px solid
        rgba(255,255,255,0.55);

    box-sizing: border-box;

    pointer-events: none;

    z-index: 8;
}


/* ==========================================================================
   CENTER
   ========================================================================== */

.wheel-center {

    position: absolute;

    width:
        clamp(
            110px,
            18vh,
            160px
        );

    height:
        clamp(
            110px,
            18vh,
            160px
        );

    left: 50%;
    top: 50%;

    transform:
        translate(-50%, -50%);

    border-radius: 50%;

    background:
        radial-gradient(
            circle at 35% 35%,
            #f7e8a0 0%,
            #d4af37 40%,
            #b8860b 75%,
            #8b6914 100%
        );

    border:
        10px solid #f1c40f;

    box-shadow:
        0 6px 22px rgba(0,0,0,0.45),
        inset 0 3px 8px rgba(255,255,255,0.4);

    display: flex;

    align-items: center;

    justify-content: center;

    z-index: 20;
}


.wheel-center-text {

    font-size:
        clamp(
            13px,
            1.9vh,
            18px
        );

    line-height: 1.25;

    font-weight: 900;

    color: #5c3b00;

    text-align: center;

    text-shadow:
        0 1px 1px
        rgba(255,255,255,0.4);
}


/* ==========================================================================
   POINTER  (golden)
   ========================================================================== */

.wheel-pointer {

    position: absolute;

    top: -4px;

    left: 50%;

    transform:
        translateX(-50%);

    width: 0;
    height: 0;

    border-left:
        32px solid transparent;

    border-right:
        32px solid transparent;

    border-top:
        68px solid #f1c40f;

    filter:
        drop-shadow(
            0 4px 10px
            rgba(0,0,0,0.6)
        );

    z-index: 50;
}


/* ==========================================================================
   RIGHT PANEL
   ========================================================================== */

.right-panel {

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 15px;

    flex-shrink: 0;

    width: 280px;
}


/* ==========================================================================
   LIVE DISPLAY
   ========================================================================== */

.live-display {

    width: 100%;

    background:
        linear-gradient(
            145deg,
            #1a1a1a,
            #0d0d0d
        );

    border:
        6px solid #d4af37;

    border-radius: 16px;

    box-shadow:
        0 0 0 3px #b8860b,
        0 10px 30px rgba(0,0,0,0.5),
        inset 0 0 20px rgba(0,0,0,0.6);

    padding: 20px 15px;

    text-align: center;

    position: relative;
}


.live-display::before {

    content: '';

    position: absolute;

    inset: 8px;

    border:
        1px solid
        rgba(212,175,55,0.3);

    border-radius: 10px;

    pointer-events: none;
}


.live-display-label {

    font-size: 14px;

    font-weight: 700;

    color: #f1c40f;

    letter-spacing: 2px;

    text-transform: uppercase;

    margin-bottom: 12px;

    text-shadow:
        0 0 8px
        rgba(241,196,15,0.5);
}


.live-display-screen {

    background: #001a00;

    border:
        3px solid #0a3d0a;

    border-radius: 8px;

    padding: 18px 10px;

    min-height: 70px;

    display: flex;

    align-items: center;

    justify-content: center;

    box-shadow:
        inset 0 0 25px
        rgba(0,0,0,0.8);

    position: relative;

    overflow: hidden;
}


.live-display-screen::after {

    content: '';

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            180deg,
            rgba(255,255,255,0.05) 0%,
            transparent 40%,
            transparent 60%,
            rgba(0,0,0,0.2) 100%
        );

    pointer-events: none;
}


#liveTicketNumber {

    font-family:
        'Courier New',
        monospace;

    font-size: 32px;

    font-weight: 900;

    color: #00ff41;

    text-shadow:
        0 0 5px #00ff41,
        0 0 15px #00ff41,
        0 0 25px rgba(0,255,65,0.5);

    letter-spacing: 1px;

    transition:
        transform 0.08s ease;

    word-break: break-all;

    line-height: 1.1;
}


#liveTicketNumber.pop {

    transform:
        scale(1.15);
}


.live-display-footer {

    margin-top: 14px;

    font-size: 12px;

    color: #aaa;

    letter-spacing: 1px;
}


/* ==========================================================================
   SPIN BUTTON
   ========================================================================== */

.spin-button {

    width: 100%;

    padding: 15px 20px;

    border: none;

    border-radius: 50px;

    background:
        linear-gradient(
            180deg,
            #e74c3c 0%,
            #c0392b 100%
        );

    color: #ffffff;

    font-size: 22px;

    font-weight: 800;

    cursor: pointer;

    box-shadow:
        0 8px 22px
        rgba(192,57,43,0.45);

    transition: all 0.2s;

    margin: 0;
}


.spin-button:hover {

    background:
        linear-gradient(
            180deg,
            #c0392b 0%,
            #a93226 100%
        );

    transform:
        translateY(-2px);
}


.spin-button:active {

    transform:
        translateY(1px);
}


.spin-button:disabled {

    background: #999;

    cursor: not-allowed;

    box-shadow: none;

    transform: none;
}


/* ==========================================================================
   ERROR
   ========================================================================== */

.spinner-error {

    display: none;

    max-width: 650px;

    margin: 15px auto;

    padding: 15px;

    background: #f8d7da;

    border:
        1px solid #f5c6cb;

    border-radius: 6px;

    color: #721c24;

    font-size: 16px;
}


/* ==========================================================================
   WINNER MODAL
   ========================================================================== */

#winnerModal {

    display: none !important;

    position: fixed !important;

    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;

    width: 100vw !important;
    height: 100vh !important;

    margin: 0 !important;
    padding: 0 !important;

    background:
        rgba(0,0,0,0.72) !important;

    z-index: 999999 !important;

    overflow: hidden !important;

    box-sizing: border-box !important;
}


#winnerModal.show {

    display: block !important;
}


#winnerModal .winner-box {

    position: absolute !important;

    top: 50% !important;
    left: 50% !important;

    right: auto !important;
    bottom: auto !important;

    margin: 0 !important;

    width: 90% !important;

    max-width: 700px !important;

    padding: 45px 30px !important;

    background: #ffffff !important;

    border:
        5px solid #28a745 !important;

    border-radius: 22px !important;

    box-sizing: border-box !important;

    text-align: center !important;

    box-shadow:
        0 20px 60px
        rgba(0,0,0,0.45) !important;

    z-index: 1000000 !important;

    transform:
        translate(-50%, -50%) !important;

    animation:
        winnerAppear 0.45s
        ease-out !important;
}


@keyframes winnerAppear {

    0% {

        opacity: 0;

        transform:
            translate(-50%, -50%)
            scale(0.70);
    }

    100% {

        opacity: 1;

        transform:
            translate(-50%, -50%)
            scale(1);
    }
}


#winnerModal .winner-close {

    position: absolute !important;

    top: 12px !important;
    right: 15px !important;

    width: 40px !important;
    height: 40px !important;

    margin: 0 !important;
    padding: 0 !important;

    border: none !important;

    border-radius: 50% !important;

    background: #eeeeee !important;

    color: #555 !important;

    font-size: 26px !important;

    font-weight: bold !important;

    line-height: 40px !important;

    text-align: center !important;

    cursor: pointer !important;
}


#winnerModal .winner-close:hover {

    background: #e74c3c !important;

    color: #ffffff !important;
}


#winnerModal .winner-title {

    font-size: 40px;

    font-weight: 900;

    color: #28a745;

    line-height: 1.2;

    margin: 0 0 20px;
}


#winnerModal .winner-ticket-number {

    font-size: 60px;

    font-weight: 900;

    color: #e74c3c;

    line-height: 1.1;

    margin-bottom: 10px;

    word-break: break-word;
}


#winnerModal .winner-name {

    font-size: 34px;

    font-weight: 700;

    color: #333;

    line-height: 1.3;

    margin-bottom: 15px;
}


#winnerModal .winner-class {

    font-size: 21px;

    font-weight: 600;

    color: #555;

    line-height: 1.4;
}


/* ==========================================================================
   CONFETTI
   ========================================================================== */

.confetti-container {

    position: fixed;

    top: 0;
    left: 0;

    width: 100vw;
    height: 100vh;

    pointer-events: none;

    z-index: 9999999;

    overflow: hidden;
}


.confetti {

    position: absolute;

    top: -40px;

    width: 10px;

    height: 18px;

    opacity: 1;

    animation:
        confettiFall
        3.5s
        linear
        forwards;
}


@keyframes confettiFall {

    0% {

        transform:
            translate3d(0,0,0)
            rotate(0deg);

        opacity: 1;
    }

    100% {

        transform:
            translate3d(
                var(--drift),
                110vh,
                0
            )
            rotate(900deg);

        opacity: 0;
    }
}


/* ==========================================================================
   RESPONSIVE
   ========================================================================== */

@media (max-width: 900px) {

    .wheel-and-display {
        gap: 25px;
    }

    .right-panel {
        width: 240px;
    }

    #liveTicketNumber {
        font-size: 26px;
    }
}


@media (max-width: 700px) {

    .lottery-spinner-page {

        height:
            calc(100vh - 100px);

        padding:
            8px 10px 25px;
    }


    .spinner-main-title {
        font-size: 26px;
    }


    .wheel-and-display {

        flex-direction: column;

        gap: 15px;
    }


    .wheel-area {

        max-width:
            min(92vw, 62vh);

        width: 100%;

        aspect-ratio: 1 / 1;
    }


    .right-panel {

        width: 90%;

        max-width: 320px;

        flex-direction: column;

        align-items: center;

        gap: 12px;
    }


    .wheel {

        border-width: 14px;
    }


    .wheel-center {

        width: 100px;

        height: 100px;

        border-width: 8px;
    }


    .wheel-center-text {
        font-size: 13px;
    }


    .wheel-pointer {

        border-left-width: 24px;

        border-right-width: 24px;

        border-top-width: 52px;
    }


    .live-display {
        width: 100%;
    }


    #liveTicketNumber {
        font-size: 28px;
    }


    .spin-button {

        padding: 13px 20px;

        font-size: 18px;
    }
}


@media (max-width: 700px) and (max-height: 700px) {

    .spinner-main-title {
        font-size: 22px;
    }


    .wheel {
        border-width: 10px;
    }


    .wheel-center {

        width: 85px;

        height: 85px;

        border-width: 6px;
    }


    .wheel-center-text {
        font-size: 12px;
    }


    .wheel-pointer {

        border-left-width: 20px;

        border-right-width: 20px;

        border-top-width: 44px;
    }


    .spin-button {

        padding: 11px 16px;

        font-size: 16px;
    }
}

</style>


<!-- ==========================================================================
     CONTENT HEADER
     ========================================================================== -->

<section class="content-header">

    <div class="content-header-left">

        <h1>Lottery Spinner</h1>

    </div>


    <div class="content-header-right">

        <a
            href="lottery.php"
            class="btn btn-default btn-sm"
        >
            Manage Lottery
        </a>

    </div>

</section>


<!-- ==========================================================================
     MAIN CONTENT
     ========================================================================== -->

<section class="content">

    <div class="lottery-spinner-page">

        <div class="spinner-container">


            <div class="spinner-main-title">
                Lottery Lucky Draw
            </div>


            <?php if (isset($page_error)) { ?>


                <div
                    class="spinner-error"
                    style="display:block;"
                >

                    <?php
                    echo htmlspecialchars(
                        $page_error
                    );
                    ?>

                </div>


            <?php } elseif ($available_count <= 0) { ?>


                <div class="alert alert-warning">

                    <strong>
                        No Tickets Remaining
                    </strong>

                    <br>

                    All tickets have already been
                    selected as winners.

                </div>


            <?php } else { ?>


                <div class="wheel-and-display">


                    <!-- ======================================================
                         WHEEL
                         ====================================================== -->

                    <div class="wheel-area">

                        <div class="wheel-pointer"></div>


                        <div
                            id="lotteryWheel"
                            class="wheel"
                        >

                            <div
                                id="wheelTickets"
                            ></div>


                            <div
                                id="wheelLines"
                            ></div>


                            <div
                                class="wheel-inner-ring"
                            ></div>


                            <div class="wheel-center">

                                <div class="wheel-center-text">

                                    LUCKY<br>
                                    DRAW

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ======================================================
                         RIGHT PANEL
                         ====================================================== -->

                    <div class="right-panel">


                        <div class="live-display">

                            <div class="live-display-label">
                                Now Pointing
                            </div>


                            <div class="live-display-screen">

                                <div
                                    id="liveTicketNumber"
                                >
                                    ----   <!-- ⬅️ Placeholder -->
                                </div>

                            </div>


                            <div class="live-display-footer">
                                LIVE TICKET
                            </div>

                        </div>


                        <button
                            type="button"
                            id="spinButton"
                            class="spin-button"
                            onclick="spinWheel()"
                        >
                            SPIN THE WHEEL
                        </button>


                    </div>

                </div>


                <div
                    id="spinnerError"
                    class="spinner-error"
                ></div>


            <?php } ?>


        </div>

    </div>

</section>


<!-- ==========================================================================
     WINNER MODAL
     ========================================================================== -->

<div id="winnerModal">

    <div class="winner-box">


        <button
            type="button"
            class="winner-close"
            onclick="closeWinnerModal()"
        >
            ×
        </button>


        <div class="winner-title">
            🎉 CONGRATULATIONS! 🎉
        </div>


        <div
            id="winnerTicket"
            class="winner-ticket-number"
        ></div>


        <div
            id="winnerName"
            class="winner-name"
        ></div>


        <div
            id="winnerClass"
            class="winner-class"
        ></div>


    </div>

</div>


<!-- ==========================================================================
     CONFETTI
     ========================================================================== -->

<div
    id="confettiContainer"
    class="confetti-container"
></div>


<!-- ==========================================================================
     AUDIO
     ========================================================================== -->

<audio
    id="spinnerSound"
    src="/school_techgatha/assets/music/spinner.mp3"
    preload="auto"
    loop
></audio>


<audio
    id="blastSound"
    src="/school_techgatha/assets/music/blast.mp3"
    preload="auto"
></audio>


<audio
    id="clapSound"
    src="/school_techgatha/assets/music/clap.mp3"
    preload="auto"
></audio>


<script>

/* ==========================================================================
   SOUND CONTROL
   ========================================================================== */

function playSpinnerSound() {

    const sound =
        document.getElementById(
            'spinnerSound'
        );

    if (!sound) {
        return;
    }

    sound.load();

    sound.currentTime = 0;

    sound.playbackRate = 1.0;

    sound.volume = 0.8;

    sound.play().catch(function(e) {

        console.warn(
            'Spinner sound play failed:',
            e
        );

    });
}


function updateSpinnerSpeed(progress) {

    const sound =
        document.getElementById(
            'spinnerSound'
        );

    if (!sound) {
        return;
    }

    const rate =
        1.0 -
        (progress * 0.9);

    sound.playbackRate =
        Math.max(
            0.1,
            rate
        );

    sound.volume =
        0.8 *
        (1 - progress * 0.3);
}


function stopSpinnerSound() {

    const sound =
        document.getElementById(
            'spinnerSound'
        );

    if (!sound) {
        return;
    }

    sound.pause();

    sound.currentTime = 0;

    sound.playbackRate = 1.0;

    sound.volume = 0.8;
}


function playBlastSound() {

    const sound =
        document.getElementById(
            'blastSound'
        );

    if (!sound) {
        return;
    }

    sound.currentTime = 0;

    sound.volume = 1.0;

    sound.play().catch(function(e) {

        console.warn(
            'Blast sound play failed:',
            e
        );

        sound.load();

        sound.play().catch(
            function() {}
        );

    });
}


function playClapSound() {

    const sound =
        document.getElementById(
            'clapSound'
        );

    if (!sound) {
        return;
    }

    sound.currentTime = 0;

    sound.volume = 0.8;

    sound.play().catch(function(e) {

        console.warn(
            'Clap sound play failed:',
            e
        );

        sound.load();

        sound.play().catch(
            function() {}
        );

    });
}


/* ==========================================================================
   INITIAL TICKETS
   ========================================================================== */

let wheelTickets =
    <?php
    echo $wheelTicketsJson ?: '[]';
    ?>;


/* ==========================================================================
   VARIABLES
   ========================================================================== */

let spinning = false;

let currentRotation = 0;

let animationFrameId = null;

let lastDisplayedNumber = '';


/* ==========================================================================
   BUILD WHEEL  – RED/WHITE SECTORS, SHOW PRIZE (NO $ SIGN)
   ========================================================================== */

function buildWheel(tickets) {

    const wheel =
        document.getElementById(
            'lotteryWheel'
        );

    const ticketContainer =
        document.getElementById(
            'wheelTickets'
        );

    const lineContainer =
        document.getElementById(
            'wheelLines'
        );


    if (
        !wheel ||
        !ticketContainer ||
        !lineContainer
    ) {
        return;
    }


    ticketContainer.innerHTML = '';

    lineContainer.innerHTML = '';


    if (
        !tickets ||
        tickets.length === 0
    ) {

        wheel.style.background =
            '#c0392b';

        return;
    }


    const sectorCount =
        tickets.length;


    const sectorAngle =
        360 / sectorCount;


    /*
    |--------------------------------------------------------------------------
    | CLASSIC RED & WHITE SECTORS (exactly like your image)
    |--------------------------------------------------------------------------
    */

    const colors = [
        '#e74c3c', // bright red
        '#ffffff'  // pure white
    ];


    /*
    |--------------------------------------------------------------------------
    | BUILD BACKGROUND SECTORS (conic gradient)
    |--------------------------------------------------------------------------
    */

    let gradientParts = [];


    for (
        let i = 0;
        i < sectorCount;
        i++
    ) {

        const start =
            i * sectorAngle;

        const end =
            (i + 1) * sectorAngle;


        const color =
            colors[i % colors.length];


        gradientParts.push(
            color +
            ' ' +
            start +
            'deg ' +
            end +
            'deg'
        );
    }


    wheel.style.background =
        'conic-gradient(' +
        gradientParts.join(',') +
        ')';

    // The golden border and box-shadow are in CSS, so they stay untouched.


    /*
    |--------------------------------------------------------------------------
    | DYNAMIC FONT SIZE
    |--------------------------------------------------------------------------
    */

    let fontSize = 14;

    let labelWidth = 90;


    if (sectorCount <= 30) {

        fontSize = 14;
        labelWidth = 90;

    } else if (sectorCount <= 50) {

        fontSize = 12;
        labelWidth = 75;

    } else if (sectorCount <= 100) {

        fontSize = 10;
        labelWidth = 60;

    } else if (sectorCount <= 150) {

        fontSize = 8;
        labelWidth = 48;

    } else if (sectorCount <= 200) {

        fontSize = 7;
        labelWidth = 42;

    } else if (sectorCount <= 300) {

        fontSize = 6;
        labelWidth = 36;

    } else {

        fontSize = 5;
        labelWidth = 30;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE LABEL FOR EVERY TICKET – SHOW PRIZE (NO $) OR TICKET NUMBER
    |--------------------------------------------------------------------------
    */

    tickets.forEach(function(
        ticket,
        index
    ) {

        const angle =
            (index * sectorAngle) +
            (sectorAngle / 2);


        const label =
            document.createElement(
                'div'
            );


        label.className =
            'ticket-label';


        // Text color: dark on white, white on red
        const isWhite = (index % 2 === 1);
        label.style.color =
            isWhite ? '#222' : '#ffffff';


        label.style.transform =
            'translate(-50%, -50%) ' +
            'rotate(' +
            angle +
            'deg)';


        const span =
            document.createElement(
                'span'
            );


        // Display prize if available, otherwise ticket number – but NO '$' sign
        let displayText = ticket.prize;
        if (!displayText || displayText.trim() === '') {
            displayText = ticket.ticket_number;
        } else {
            // Remove any '$' that might be stored, just show the number
            displayText = String(displayText).replace(/[$,]/g, '').trim();
        }

        span.textContent = displayText;


        span.style.fontSize =
            fontSize + 'px';

        span.style.fontWeight = '900';

        span.style.textShadow =
            '0 1px 4px rgba(0,0,0,0.6)';

        span.style.width =
            labelWidth + 'px';

        span.style.display = 'inline-block';

        span.style.textAlign = 'center';


        label.appendChild(span);


        ticketContainer.appendChild(
            label
        );

    });


    /*
    |--------------------------------------------------------------------------
    | CREATE SEPARATOR LINES
    |--------------------------------------------------------------------------
    */

    for (
        let i = 0;
        i < sectorCount;
        i++
    ) {

        const line =
            document.createElement(
                'div'
            );


        line.className =
            'wheel-sector-line';


        line.style.transform =
            'rotate(' +
            (i * sectorAngle) +
            'deg)';


        line.style.background =
            'rgba(255,255,255,0.5)';


        lineContainer.appendChild(
            line
        );
    }


    // Do NOT update live display here – keep placeholder.
}


/* ==========================================================================
   BUILD INITIAL WHEEL
   ========================================================================== */

buildWheel(
    wheelTickets
);


/* ==========================================================================
   FIND TICKET INDEX
   ========================================================================== */

function findTicketIndex(
    tickets,
    ticketNumber
) {

    return tickets.findIndex(
        function(ticket) {

            return String(
                ticket.ticket_number
            ) === String(
                ticketNumber
            );

        }
    );
}


/* ==========================================================================
   CALCULATE WINNER ROTATION
   ========================================================================== */

function calculateWinnerRotation(
    winnerIndex,
    totalTickets
) {

    if (
        !totalTickets ||
        totalTickets <= 0
    ) {
        return currentRotation;
    }


    const sectorAngle =
        360 / totalTickets;


    const winnerCenter =
        (winnerIndex * sectorAngle) +
        (sectorAngle / 2);


    let targetAngle =
        360 - winnerCenter;


    targetAngle =
        (targetAngle + 360) % 360;


    const fullSpins =
        360 * 18;


    const currentNormalized =
        (currentRotation % 360 + 360) % 360;


    let difference =
        targetAngle -
        currentNormalized;


    if (difference < 0) {

        difference += 360;
    }


    return (
        currentRotation +
        fullSpins +
        difference
    );
}


/* ==========================================================================
   GET CURRENT TICKET FROM ANGLE – returns prize (no $) or ticket number
   ========================================================================== */

function getTicketFromAngle(
    angle,
    tickets
) {

    if (
        !tickets ||
        tickets.length === 0
    ) {
        return '----';
    }


    const sectorCount =
        tickets.length;


    const sectorAngle =
        360 / sectorCount;


    let normalized =
        (
            (angle % 360) +
            360
        ) % 360;


    const pointerAngle =
        (360 - normalized) % 360;


    let index =
        Math.floor(
            pointerAngle /
            sectorAngle
        );


    if (
        index >= sectorCount
    ) {
        index = 0;
    }


    const ticket = tickets[index];
    if (!ticket) return '----';

    let display = ticket.prize;
    if (!display || display.trim() === '') {
        display = ticket.ticket_number;
    } else {
        display = String(display).replace(/[$,]/g, '').trim();
    }
    return display;
}


/* ==========================================================================
   UPDATE LIVE DISPLAY
   ========================================================================== */

function updateLiveTicketFromAngle(
    angle
) {

    const numberEl =
        document.getElementById(
            'liveTicketNumber'
        );


    if (!numberEl) {
        return;
    }


    const displayText =
        getTicketFromAngle(
            angle,
            wheelTickets
        );


    if (
        displayText !==
        lastDisplayedNumber
    ) {

        numberEl.textContent =
            displayText;


        lastDisplayedNumber =
            displayText;


        numberEl.classList.remove(
            'pop'
        );


        void numberEl.offsetWidth;


        numberEl.classList.add(
            'pop'
        );
    }
}


/* ==========================================================================
   LIVE TRACKING
   ========================================================================== */

function startLiveTracking(
    startAngle,
    endAngle,
    duration
) {

    const startTime =
        performance.now();


    function animate(currentTime) {

        if (!spinning) {
            return;
        }


        const elapsed =
            currentTime -
            startTime;


        const progress =
            Math.min(
                elapsed / duration,
                1
            );


        const eased =
            cubicBezier(
                0.08,
                0.6,
                0.1,
                1,
                progress
            );


        const currentAngle =
            startAngle +
            (
                (endAngle - startAngle) *
                eased
            );


        updateLiveTicketFromAngle(
            currentAngle
        );


        updateSpinnerSpeed(
            eased
        );


        if (progress < 1) {

            animationFrameId =
                requestAnimationFrame(
                    animate
                );
        }
    }


    animationFrameId =
        requestAnimationFrame(
            animate
        );
}


/* ==========================================================================
   CUBIC BEZIER
   ========================================================================== */

function cubicBezier(
    p1x,
    p1y,
    p2x,
    p2y,
    t
) {

    const cx =
        3 * p1x;


    const bx =
        3 * (p2x - p1x) -
        cx;


    const ax =
        1 - cx - bx;


    const cy =
        3 * p1y;


    const by =
        3 * (p2y - p1y) -
        cy;


    const ay =
        1 - cy - by;


    function sampleCurveX(t) {

        return (
            (ax * t + bx) *
            t +
            cx
        ) * t;
    }


    function sampleCurveY(t) {

        return (
            (ay * t + by) *
            t +
            cy
        ) * t;
    }


    let t2 = t;


    for (
        let i = 0;
        i < 8;
        i++
    ) {

        const x2 =
            sampleCurveX(t2) -
            t;


        if (
            Math.abs(x2) <
            0.001
        ) {
            break;
        }


        const d2 =
            (
                3 * ax * t2 +
                2 * bx
            ) * t2 +
            cx;


        if (
            Math.abs(d2) <
            1e-6
        ) {
            break;
        }


        t2 =
            t2 -
            x2 / d2;
    }


    return sampleCurveY(t2);
}


/* ==========================================================================
   SPIN WHEEL
   ========================================================================== */

function spinWheel() {

    if (spinning) {
        return;
    }


    spinning = true;


    const button =
        document.getElementById(
            'spinButton'
        );

    const wheel =
        document.getElementById(
            'lotteryWheel'
        );

    const errorBox =
        document.getElementById(
            'spinnerError'
        );


    if (
        !button ||
        !wheel
    ) {

        spinning = false;

        return;
    }


    button.disabled = true;

    button.innerHTML =
        'SPINNING...';


    if (errorBox) {

        errorBox.style.display =
            'none';
    }


    document
        .getElementById(
            'winnerModal'
        )
        .classList
        .remove('show');


    /*
    |--------------------------------------------------------------------------
    | PRE-PRIME BLAST
    |--------------------------------------------------------------------------
    */

    const blast =
        document.getElementById(
            'blastSound'
        );


    if (blast) {

        blast.load();

        blast.play()
            .then(function() {

                blast.pause();

                blast.currentTime = 0;

            })
            .catch(
                function() {}
            );
    }


    /*
    |--------------------------------------------------------------------------
    | PRE-PRIME CLAP
    |--------------------------------------------------------------------------
    */

    const clap =
        document.getElementById(
            'clapSound'
        );


    if (clap) {

        clap.load();

        clap.play()
            .then(function() {

                clap.pause();

                clap.currentTime = 0;

            })
            .catch(
                function() {}
            );
    }


    /*
    |--------------------------------------------------------------------------
    | START SPINNER SOUND
    |--------------------------------------------------------------------------
    */

    playSpinnerSound();


    /*
    |--------------------------------------------------------------------------
    | REQUEST RANDOM WINNER FROM SERVER
    |--------------------------------------------------------------------------
    */

    const formData =
        new URLSearchParams();


    formData.append(
        'action',
        'select_winner'
    );


    fetch(
        'spinner_ticket.php',
        {
            method: 'POST',

            headers: {
                'Content-Type':
                    'application/x-www-form-urlencoded'
            },

            body:
                formData.toString()
        }
    )


    .then(
        async function(response) {

            const text =
                await response.text();


            console.log(
                'Lottery server response:',
                text
            );


            try {

                return JSON.parse(
                    text
                );

            } catch (error) {

                throw new Error(
                    'Invalid server response: ' +
                    text
                );
            }

        }
    )


    .then(
        function(data) {


            if (!data.success) {

                showError(
                    data.message ||
                    'Unable to select winner.'
                );


                resetButton();

                stopSpinnerSound();

                return;
            }


            const winnerNumber =
                data.ticket_number;


            const tickets =
                data.tickets || [];


            const winnerIndex =
                findTicketIndex(
                    tickets,
                    winnerNumber
                );


            if (winnerIndex < 0) {

                showError(
                    'Winner ticket was not found on the wheel.'
                );


                resetButton();

                stopSpinnerSound();

                return;
            }


            wheelTickets =
                tickets;


            buildWheel(
                wheelTickets
            );


            const startRotation =
                currentRotation;


            const targetRotation =
                calculateWinnerRotation(
                    winnerIndex,
                    tickets.length
                );


            currentRotation =
                targetRotation;


            wheel.style.transform =
                'rotate(' +
                targetRotation +
                'deg)';


            startLiveTracking(
                startRotation,
                targetRotation,
                30000
            );


            setTimeout(
                function() {


                    updateLiveTicketFromAngle(
                        targetRotation
                    );


                    stopSpinnerSound();


                    playBlastSound();


                    setTimeout(
                        function() {

                            playClapSound();

                        },
                        500
                    );


                    showWinner(
                        data
                    );


                },
                30500
            );

        }
    )


    .catch(
        function(error) {

            console.error(
                'Lottery spinner error:',
                error
            );


            showError(
                error.message ||
                'Something went wrong. Please try again.'
            );


            resetButton();


            stopSpinnerSound();
        }
    );
}


/* ==========================================================================
   SHOW WINNER – show prize (no $) or ticket number
   ========================================================================== */

function showWinner(data) {

    let display = data.prize;
    if (!display || display.trim() === '') {
        display = data.ticket_number || '';
    } else {
        display = String(display).replace(/[$,]/g, '').trim();
    }

    document.getElementById(
        'winnerTicket'
    ).textContent = display;


    document.getElementById(
        'winnerName'
    ).textContent =
        data.child_name || '';


    const classElement =
        document.getElementById(
            'winnerClass'
        );


    if (
        data.class !== null &&
        data.class !== ''
    ) {

        classElement.textContent =
            'Class: ' +
            data.class;

    } else {

        classElement.textContent =
            '';
    }


    const modal =
        document.getElementById(
            'winnerModal'
        );


    modal.classList.add(
        'show'
    );


    createConfetti();


    const button =
        document.getElementById(
            'spinButton'
        );


    button.disabled =
        false;


    button.innerHTML =
        'SPIN AGAIN';


    spinning =
        false;


    if (animationFrameId) {

        cancelAnimationFrame(
            animationFrameId
        );

        animationFrameId =
            null;
    }
}


/* ==========================================================================
   CLOSE WINNER MODAL
   ========================================================================== */

function closeWinnerModal() {

    const modal =
        document.getElementById(
            'winnerModal'
        );


    modal.classList.remove(
        'show'
    );
}


document.addEventListener(
    'click',
    function(event) {

        const modal =
            document.getElementById(
                'winnerModal'
            );


        if (
            event.target === modal
        ) {

            closeWinnerModal();
        }
    }
);


/* ==========================================================================
   CONFETTI
   ========================================================================== */

function createConfetti() {

    const container =
        document.getElementById(
            'confettiContainer'
        );


    container.innerHTML =
        '';


    const colors = [

        '#ff4757',
        '#ffa502',
        '#2ed573',
        '#1e90ff',
        '#a55eea',
        '#ff6b81',
        '#3742fa',
        '#ff7f50',
        '#20bf6b',
        '#45aaf2'

    ];


    for (
        let i = 0;
        i < 300;
        i++
    ) {

        const piece =
            document.createElement(
                'div'
            );


        piece.className =
            'confetti';


        piece.style.left =
            Math.random() *
            100 +
            '%';


        piece.style.backgroundColor =
            colors[
                Math.floor(
                    Math.random() *
                    colors.length
                )
            ];


        piece.style.width =
            (
                Math.random() *
                10 +
                6
            ) +
            'px';


        piece.style.height =
            (
                Math.random() *
                18 +
                8
            ) +
            'px';


        piece.style.setProperty(
            '--drift',
            (
                Math.random() *
                400 -
                200
            ) +
            'px'
        );


        piece.style.animationDelay =
            (
                Math.random() *
                0.8
            ) +
            's';


        piece.style.animationDuration =
            (
                Math.random() *
                2 +
                3
            ) +
            's';


        if (
            Math.random() >
            0.5
        ) {

            piece.style.borderRadius =
                '50%';
        }


        container.appendChild(
            piece
        );
    }


    setTimeout(
        function() {

            container.innerHTML =
                '';

        },
        8200
    );
}


/* ==========================================================================
   SHOW ERROR
   ========================================================================== */

function showError(message) {

    const errorBox =
        document.getElementById(
            'spinnerError'
        );


    if (!errorBox) {
        return;
    }


    errorBox.textContent =
        message;


    errorBox.style.display =
        'block';
}


/* ==========================================================================
   RESET BUTTON
   ========================================================================== */

function resetButton() {

    const button =
        document.getElementById(
            'spinButton'
        );


    if (button) {

        button.disabled =
            false;


        button.innerHTML =
            'SPIN THE WHEEL';
    }


    spinning =
        false;


    if (animationFrameId) {

        cancelAnimationFrame(
            animationFrameId
        );

        animationFrameId =
            null;
    }
}

</script>


<?php require_once('footer.php'); ?>