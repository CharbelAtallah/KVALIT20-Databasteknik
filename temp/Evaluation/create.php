<?php

/***************************************************************
 *
 *                       Kursutvärdering
 *
 ***************************************************************/

require_once 'db.php';


$stmt = $conn->prepare("SELECT * FROM courses");
$stmt->execute();
$courses = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT * FROM questions");
$stmt->execute();
$questions = $stmt->fetchAll();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

    $student_name = empty($_POST['name']) ?  "Anonym" : $_POST['name'];
    $course_id    = $_POST['course'];
    $answers      = $_POST['answers']; // en array som innehåller alla svar

    // Skapa en ny student
    $stmt = $conn->prepare("INSERT INTO students (name) VALUES (:name)");
    $stmt->bindParam(':name', $student_name);
    $stmt->execute();
    $student_id = $conn->lastInsertId();

    // Skapa en ny utvärdering
    $stmt = $conn->prepare("INSERT INTO surveys (course, student) 
                            VALUES (:course, :student )");
    $stmt->bindParam(':course', $course_id);
    $stmt->bindParam(':student', $student_id);
    $stmt->execute();
    $survey_id = $conn->lastInsertId();

    // Infoga alla svar 
    foreach ($answers as $question_id => $answer) {
        $stmt = $conn->prepare("INSERT INTO answers (survey_id, question_id, answer) 
                            VALUES (:survey_id, :question_id, :answer )");
        $stmt->bindParam(':survey_id', $survey_id);
        $stmt->bindParam(':question_id', $question_id);
        $stmt->bindParam(':answer', $answer);
        $stmt->execute();
    }

    echo "<h1>New record created successfully</h1>";
}
?>

<form action="#" method="post" class="row">

    <div class="col-xl-6 my-3">
        <label for="name"><h5>Skriv ditt namn eller lämna tomt för att svara anonymt</h5></label>
        <input type="text" id="name" name="name" class="form-control w-50">
    </div>

    <div class="col-xl-6 my-3">
        <label for="course"><h5>Välj en kurs från listan</h5></label>
        <select id="course" name="course" class="form-select w-50" required>
            <option value="">-- Välj kurs --</option>
            <?php foreach ($courses as $key => $course) { ?>
                <option value="<?php echo $course['course_id'] ?>"><?php echo $course['name'] ?></option>
            <?php } ?>
        </select>
    </div>

    <br>

    <?php foreach ($questions as $key => $question) { ?>
        <h5><?php echo $question['question'] ?></h5>
        <textarea name="<?php echo "answers[$question[question_id]]" ?>" cols="30" rows="5" class="mb-3"></textarea>
    <?php } ?>

    <div class="col-md-4">
        <input type="submit" class="form-control mt-2 btn btn-primary" value="Skicka">
    </div>

</form>