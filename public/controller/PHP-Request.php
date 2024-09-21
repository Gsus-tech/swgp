<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_GET['change-selectedProject'])){
        $selectedProject = $_POST['selectedProject'] ?? null;

        if ($selectedProject) {
            $_SESSION['projectSelected'] = $selectedProject;

            // Respuesta en formato JSON
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}