<?php
// templates/header.php
// Ensure config is loaded if this file is included directly (fallback)
if (!defined('BASE_URL')) {
    // Attempt to find config based on common locations
    if (file_exists(__DIR__ . '/../includes/config.php')) {
        require_once __DIR__ . '/../includes/config.php';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ContentCraft | Your Teaching Assistant</title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/styles/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header>
        <nav class="navbar">
            <div class="logo-container">
                <img src="<?php echo BASE_URL; ?>assets/images/icon.png" alt="ContentCraft Logo" class="logo-img">
                <a href="<?php echo BASE_URL; ?>index.php" class="logo-text">Content<span>Craft</span></a>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>index.php" class="nav-link">Home</a>
                </li>

                <li class="nav-item dropdown" id="generateDropdown">
                    <div class="nav-link dropdown-toggle">Generate</div>
                    <div class="dropdown-menu">
                        <div class="dropdown-group">
                            <div class="group-title">Records</div>
                            <a href="<?php echo BASE_URL; ?>plans/lesson-plan.php" class="dropdown-item">Lesson Plan</a>
                            <a href="<?php echo BASE_URL; ?>plans/demonstration-plan.php" class="dropdown-item">Demonstration Plan</a>
                            <a href="<?php echo BASE_URL; ?>plans/graded-exercise.php" class="dropdown-item">Graded Exercise</a>
                        </div>
                        
                        <div class="dropdown-group">
                            <div class="group-title">Materials</div>
                            <a href="<?php echo BASE_URL; ?>materials/theory.php" class="dropdown-item">Lesson Note</a>
                            <a href="<?php echo BASE_URL; ?>materials/practical.php" class="dropdown-item">Practical Note</a>
                            <a href="<?php echo BASE_URL; ?>tools/mcq-generator.php" class="dropdown-item">MCQs</a>
                            <a href="<?php echo BASE_URL; ?>tools/descriptive-qs-generator.php" class="dropdown-item">Descriptive Questions</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>about.php" class="nav-link">About</a>
                </li>
            </ul>

            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>
    </header>