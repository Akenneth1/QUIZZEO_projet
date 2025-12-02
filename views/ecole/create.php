<h2>Créer un Quiz</h2>
<form method="post" action="create_quiz.php">
    <label>Titre du quiz:</label>
    <input type="text" name="title" required><br><br>

    <h3>Questions</h3>
    <div>
        <label>Question 1:</label>
        <input type="text" name="questions[0][text]" required><br>

        <label>Type de question:</label>
        <select name="questions[0][type]" required>
            <option value="qcm">QCM</option>
            <option value="libre">Libre</option>
        </select><br><br>

  
        <div>
            <label>Réponses (QCM):</label><br>
            <input type="text" name="questions[0][answers][]" placeholder="Réponse A"><br>
            <input type="text" name="questions[0][answers][]" placeholder="Réponse B"><br>
            <input type="text" name="questions[0][answers][]" placeholder="Réponse C"><br>
            <input type="text" name="questions[0][answers][]" placeholder="Réponse D"><br>

            <label>Bonne réponse (index):</label>
            <input type="number" name="questions[0][correct]" min="0" max="3"><br>
        </div>

 
        <div>
            <label>Réponse attendue (Libre):</label>
            <input type="text" name="questions[0][expected]"><br>
        </div>

        <label>Points:</label>
        <input type="number" name="questions[0][points]" min="1" value="1"><br>
    </div>

    <button type="submit">Créer</button>
</form>