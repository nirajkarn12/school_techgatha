<?php require_once('header.php'); ?>

<?php

if (isset($_GET['success'])) {

    $success_message = 'Quiz question is added successfully!';

} elseif (isset($_GET['updated'])) {

    $success_message = 'Quiz question is updated successfully!';

} elseif (isset($_GET['deleted'])) {

    $success_message = 'Quiz question is deleted successfully!';

}

?>

<section class="content-header">

    <div class="content-header-left">

        <h1>View Quiz Questions</h1>

    </div>


    <div class="content-header-right">

        <a href="quiz-upload.php"
           class="btn btn-success btn-sm">

            <i class="fa fa-upload"></i>
            Upload CSV

        </a>


        <a href="quiz-export.php"
           class="btn btn-warning btn-sm">

            <i class="fa fa-download"></i>
            Export CSV

        </a>


        <a href="quiz-add.php"
           class="btn btn-primary btn-sm">

            <i class="fa fa-plus"></i>
            Add Question

        </a>

    </div>

</section>


<section class="content">

    <div class="row">

        <div class="col-md-12">


            <?php if($error_message): ?>

                <div class="callout callout-danger">

                    <p>
                        <?php echo $error_message; ?>
                    </p>

                </div>

            <?php endif; ?>


            <?php if($success_message): ?>

                <div class="callout callout-success">

                    <p>
                        <?php echo $success_message; ?>
                    </p>

                </div>

            <?php endif; ?>


            <div class="box box-info">

                <div class="box-body table-responsive">

                    <table id="example1"
                           class="table table-bordered table-hover table-striped">

                        <thead>

                            <tr>

                                <th width="40">
                                    #
                                </th>

                                <th width="80">
                                    Set
                                </th>

                                <th>
                                    Question
                                </th>

                                <th>
                                    Answer
                                </th>

                                <th width="130">
                                    Type
                                </th>

                                <th width="170">
                                    Date
                                </th>

                                <th width="120">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php

                            $i = 0;


                            $statement = $pdo->prepare("
                                SELECT *
                                FROM tbl_quiz
                                ORDER BY set_no ASC, quiz_id DESC
                            ");


                            $statement->execute();


                            $result = $statement->fetchAll(
                                PDO::FETCH_ASSOC
                            );


                            foreach ($result as $row) {

                                $i++;

                            ?>

                            <tr>

                                <td>

                                    <?php echo $i; ?>

                                </td>


                                <!-- Set Number -->

                                <td>

                                    <span class="label label-primary">

                                        Set <?php
                                        echo (int)($row['set_no'] ?? 1);
                                        ?>

                                    </span>

                                </td>


                                <!-- Question -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['question'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </td>


                                <!-- Answer -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['answer'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </td>


                                <!-- Type -->

                                <td>

                                    <?php

                                    if (
                                        $row['type'] == 'Gambling'
                                    ) {

                                        $type_class = 'label-danger';

                                    } elseif (
                                        $row['type'] == 'General'
                                    ) {

                                        $type_class = 'label-success';

                                    } elseif (
                                        $row['type'] == 'Audio Visual'
                                    ) {

                                        $type_class = 'label-info';

                                    } else {

                                        $type_class = 'label-default';

                                    }

                                    ?>


                                    <span class="label <?php
                                        echo $type_class;
                                    ?>">

                                        <?php

                                        echo htmlspecialchars(
                                            $row['type'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                        ?>

                                    </span>

                                </td>


                                <!-- Date -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['created_at'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </td>


                                <!-- Action -->

                                <td>

                                    <a
                                        href="quiz-edit.php?id=<?php
                                        echo (int)$row['quiz_id'];
                                        ?>"
                                        class="btn btn-primary btn-xs">

                                        Edit

                                    </a>


                                    <a
                                        href="#"
                                        class="btn btn-danger btn-xs"
                                        data-href="quiz-delete.php?id=<?php
                                        echo (int)$row['quiz_id'];
                                        ?>"
                                        data-toggle="modal"
                                        data-target="#confirm-delete">

                                        Delete

                                    </a>

                                </td>

                            </tr>

                            <?php

                            }

                            ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- Delete Confirmation Modal -->

<div class="modal fade"
     id="confirm-delete"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


    <div class="modal-dialog">


        <div class="modal-content">


            <div class="modal-header">

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-hidden="true">

                    &times;

                </button>


                <h4
                    class="modal-title"
                    id="myModalLabel">

                    Delete Confirmation

                </h4>

            </div>


            <div class="modal-body">

                <p>

                    Are you sure you want to delete
                    this quiz question?

                </p>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-default"
                    data-dismiss="modal">

                    Cancel

                </button>


                <a
                    class="btn btn-danger btn-ok">

                    Delete

                </a>

            </div>

        </div>

    </div>

</div>


<?php require_once('footer.php'); ?>