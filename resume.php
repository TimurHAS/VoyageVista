<?php
require_once 'includes/data.php';
$pageTitle = 'Résumé du projet';
include 'includes/header.php';
?>

<section class="center-page">
    <div class="resume-panel panel">

        <div class="resume-header">
            <a class="resume-back" href="index.php" aria-label="Retour">&#8592;</a>
            <h1 class="resume-title">Résumé</h1>
        </div>

        <table class="resume-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tâche</th>
                    <th>Difficulté</th>
                    <th>Impact</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Système de rôles</td>
                    <td><span class="diff-dot diff-moyen"></span> Moyen</td>
                    <td>Très important</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Conférence des dates</td>
                    <td><span class="diff-dot diff-simple"></span> Simple</td>
                    <td>Important</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Disponibilités hôtels</td>
                    <td><span class="diff-dot diff-simple"></span> Simple</td>
                    <td>Important</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Places activités</td>
                    <td><span class="diff-dot diff-simple"></span> Simple</td>
                    <td>Moyen</td>
                </tr>
            </tbody>
        </table>

        <div class="resume-footer">
            <p>Tu es à <strong>~65%</strong>. Ces 4 points te font atteindre 100%.</p>
            <p class="resume-question">Tu veux qu'on commence lequel ?</p>
        </div>

    </div>
</section>

<style>
.resume-panel {
    max-width: 580px;
    margin: 2.5rem auto;
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 2rem;
}

.resume-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.resume-back {
    font-size: 1.2rem;
    color: var(--blue);
    text-decoration: none;
    line-height: 1;
}

.resume-title {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text);
}

.resume-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.resume-table th {
    text-align: left;
    padding: 0.5rem 0.75rem;
    color: var(--gray-500);
    font-weight: 600;
    border-bottom: 1px solid var(--gray-200);
    white-space: nowrap;
}

.resume-table td {
    padding: 0.65rem 0.75rem;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}

.resume-table tr:last-child td {
    border-bottom: none;
}

.resume-table td:first-child {
    color: var(--gray-500);
    font-size: 0.85rem;
    width: 2rem;
}

.resume-table td:nth-child(3) {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    white-space: nowrap;
}

.diff-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.diff-simple  { background: #f0c040; }
.diff-moyen   { background: #e05c2a; }
.diff-difficile { background: #c74444; }

.resume-footer {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
    font-size: 0.92rem;
    color: var(--text);
}

.resume-footer p {
    margin: 0.25rem 0;
}

.resume-question {
    color: var(--gray-500);
    margin-top: 0.5rem !important;
}
</style>

<?php include 'includes/footer.php'; ?>
