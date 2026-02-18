<?php
// materials/theory.php
require_once '../includes/config.php';
// Include the new library manually
require_once '../includes/Parsedown.php';
require_once '../templates/header.php';

// --- Handle Logic ---
$generatedContent = "";
$errorMsg = "";
$topic = "";
$language = "Gujarati"; // Default
$fontClass = "lang-gujarati";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = trim($_POST['topic']);
    $language = $_POST['language'];

    // Determine Font Class
    switch ($language) {
        case 'Hindi':
            $fontClass = 'lang-hindi';
            break;
        case 'English':
            $fontClass = 'lang-english';
            break;
        default:
            $fontClass = 'lang-gujarati';
            break;
    }

    if (!empty($topic) && defined('GEMINI_API_KEY')) {

        // Optimized Prompt
        $promptText = "Act as an expert teacher. Create comprehensive, factually accurate, and easy-to-understand reading material for students on the topic \"$topic\" in $language. " .
            "Structure requirements:\n" .
            "1. **Title:** Start immediately with a main Title using Markdown H1 (# Title).\n" .
            "2. **Structure:** Use clearly defined sub-topics using Markdown H2 (## Subtopic).\n" .
            "3. **Depth & Clarity:** Explain all key concepts in depth. Cover EVERY major concept, sub-topic, and theoretical detail. Use real-world examples or analogies.\n" .
            "4. **Terminology:** If the output language is not English, include the **English technical term in parentheses** immediately after the translated term for clarity.\n" .
            "5. **Formatting:** Use bullet points for lists (start lines with * ). If a comparison is useful, MUST use a Markdown table.\n" .
            "6. **Self-Practice:** Include a section '## Practice Questions' at the end.\n" .
            "7. **Visual Aids:** At the very bottom, add a section '## Teacher\'s Visual Aids'.\n" .
            "Format strictly as Markdown. Do not include introductory text.";

        $data = [
            "contents" => [["parts" => [["text" => $promptText]]]],
            "generationConfig" => [
                "maxOutputTokens" => (int)GEMINI_MAX_TOKENS,
                "temperature" => 0.7
            ]
        ];

        $json_data = json_encode($data);
        $url = GEMINI_API_URL . "?key=" . GEMINI_API_KEY;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);

        if (!curl_errno($ch)) {
            $decoded = json_decode($response, true);
            if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
                $rawMarkdown = $decoded['candidates'][0]['content']['parts'][0]['text'];

                // --- THE FIX: USE PARSEDOWN ---
                $Parsedown = new Parsedown();
                $Parsedown->setSafeMode(true); // Security feature
                $generatedContent = $Parsedown->text($rawMarkdown);
            } else {
                $errorMsg = "Error: API refused content. Check Quota or Model.";
            }
        } else {
            $errorMsg = 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
    }
}
?>

<div id="loader" class="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text">Generating comprehensive material...<br>This may take 15-30 seconds.</div>
</div>

<section class="hero" style="padding: 2rem 1rem; min-height: auto;">
    <h1><i class="fas fa-book-open"></i> Lesson Note Generator</h1>
    <p>Professional theory notes in Gujarati, Hindi, or English.</p>
</section>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--shadow); margin-bottom: 30px;">
        <form method="POST" action="" onsubmit="document.getElementById('loader').style.display = 'flex';">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="grid-column: span 2;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Lesson Topic</label>
                    <input type="text" name="topic" value="<?php echo htmlspecialchars($topic); ?>"
                        placeholder="Ex: Indian Constitution, Laws of Motion" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                </div>

                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Language</label>
                    <select name="language" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                        <option value="Gujarati" <?php if ($language == 'Gujarati') echo 'selected'; ?>>Gujarati</option>
                        <option value="Hindi" <?php if ($language == 'Hindi') echo 'selected'; ?>>Hindi</option>
                        <option value="English" <?php if ($language == 'English') echo 'selected'; ?>>English</option>
                    </select>
                </div>

                <div style="display: flex; align-items: end;">
                    <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer;">
                        <i class="fas fa-magic"></i> Generate Notes
                    </button>
                </div>
            </div>
        </form>
        <?php if ($errorMsg): ?>
            <p style="color: red; margin-top: 10px;"><?php echo $errorMsg; ?></p>
        <?php endif; ?>
    </div>

    <?php if ($generatedContent): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3>Preview</h3>
            <div style="display: flex; gap: 10px;">
                <button onclick="exportToDocx()" class="btn-primary" style="background: #2980b9; border: none; cursor: pointer;">
                    <i class="fas fa-file-word"></i> Download DOCX
                </button>
                <button onclick="exportToPDF()" class="btn-primary" style="background: #e74c3c; border: none; cursor: pointer;">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </button>
            </div>
        </div>

        <div class="paper-container">
            <div class="a4-page content-output <?php echo $fontClass; ?>">
                <?php echo $generatedContent; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- JS Libraries for DOCX -->
<script src="https://unpkg.com/html-docx-js/dist/html-docx.js"></script>
<script src="https://unpkg.com/file-saver/dist/FileSaver.min.js"></script>

<!-- JS Libraries for PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function exportToDocx() {
        // 1. Get the content
        var contentBlock = document.querySelector('.content-output');
        if (!contentBlock) {
            alert("No content to generate!");
            return;
        }

        // 2. Prepare the HTML structure for Word
        // We explicitly set the charset and add the CSS directly so Word understands the styling.
        var css = `
        <style>
            body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; }
            h1 { font-size: 24pt; color: #1a1a1a; text-align: center; font-weight: bold; margin-bottom: 20px; }
            h2 { font-size: 16pt; color: #0f3460; border-bottom: 1px solid #ddd; margin-top: 20px; font-weight: bold; }
            h3 { font-size: 14pt; color: #444; margin-top: 15px; font-weight: bold; }
            p { margin-bottom: 12px; text-align: justify; }
            ul { margin-bottom: 15px; }
            li { margin-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th { background-color: #0f3460; color: white; padding: 10px; border: 1px solid #000; }
            td { border: 1px solid #000; padding: 10px; }
            /* Language Fallbacks for Windows */
            .lang-gujarati { font-family: 'Shruti', 'Nirmala UI', 'Arial Unicode MS', serif; }
            .lang-hindi { font-family: 'Mangal', 'Nirmala UI', 'Arial Unicode MS', serif; }
        </style>
    `;

        var htmlContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            ${css}
        </head>
        <body>
            ${contentBlock.innerHTML}
        </body>
        </html>
    `;

        // 3. Convert to DOCX Blob
        // 'html-docx-js' handles the complex XML packaging for us
        var converted = htmlDocx.asBlob(htmlContent);

        // 4. Save the file
        // Generate a clean filename based on the topic
        var topicName = "<?php echo $topic ? htmlspecialchars($topic) : 'Lesson_Plan'; ?>";
        var cleanName = topicName.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        saveAs(converted, cleanName + '_material.docx');
    }

    function exportToPDF() {
        // 1. Select the content
        var element = document.querySelector('.content-output');

        if (!element) {
            alert("No content to generate!");
            return;
        }

        // 2. Configure filename
        var topicName = "<?php echo $topic ? htmlspecialchars($topic) : 'Lesson_Plan'; ?>";
        var cleanName = topicName.replace(/[^a-z0-9]/gi, '_').toLowerCase();

        // 3. Clone the element to modify styles for PDF only (without affecting screen)
        var clone = element.cloneNode(true);

        // FORCE Styling on the clone to ensure it fits A4
        clone.style.width = '100%';
        clone.style.height = 'auto'; // Allow it to grow
        clone.style.overflow = 'visible'; // Show all content
        clone.style.maxHeight = 'none';

        // Add specific PDF-friendly font sizes
        clone.style.fontSize = '12pt';
        clone.style.lineHeight = '1.5';

        // We need to append the clone to the body to render it, but hide it from view
        // However, html2pdf works best if the element is visible during capture.
        // So we use a temporary container off-screen.
        var container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.top = '0';
        container.style.width = '190mm'; // Slightly less than A4 width to prevent side cut-off
        container.appendChild(clone);
        document.body.appendChild(container);

        // 4. PDF Options
        var opt = {
            margin: [7, 0, 7, 0], // 15mm margins
            filename: cleanName + '_material.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2, // Higher scale = sharper text
                useCORS: true, // thorough rendering
                scrollY: 0 // Start from top
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            },
            // KEY FIX: Smart Page Breaks
            pagebreak: {
                mode: ['avoid-all', 'css', 'legacy']
            }
        };

        // 5. Generate and Clean up
        html2pdf().set(opt).from(clone).save().then(function() {
            document.body.removeChild(container); // Remove the temporary container
        });
    }
</script>

<?php require_once '../templates/footer.php'; ?>